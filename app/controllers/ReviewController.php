<?php namespace Tussendoor\GmbReviews\Controllers;

use Tussendoor\Http\Client;
use Tussendoor\GmbReviews\Plugin;
use Tussendoor\Http\RequestException;
use Tussendoor\GmbReviews\Helpers\Cache;
use Tussendoor\GmbReviews\Models\Reviews;
use Tussendoor\GmbReviews\Models\Accounts;
use Tussendoor\GmbReviews\Models\Locations;

class ReviewController extends Controller
{
    public $data;

    protected $uri;
    protected $auth;
    protected $model;
    protected $client;
    protected $accounts;
    protected $locations;
    protected string $selectedLocation;

    private $execute = false;

    public function __construct()
    {
        $this->accounts  = new Accounts();
        $this->locations = new Locations();

        if (Plugin::hasSetting('authorization') && $this->locations->hasSelectedLocation()) {
            $this->client   = new Client();
            $this->auth     = Plugin::config('settings.authorization');

            $this->selectedLocation = $this->locations->getSelectedLocationEndpoint();
            $this->uri = $this->getReviewEndpoint();

            $this->execute = true;
        }
        
    }

    public function register()
    {
        $this->cacheReviewData();
    }
    
    /**
     * Construct the review endpoint
     * 
     * @since 1.1.0
     *
     * @return string
     */
    private function getReviewEndpoint()
    {
        $selectedAccount = $this->accounts->getSelectedAccountEndpoint();
        $endpoint = str_replace(
            [
                'accounts/$accountID', 
                'locations/$locationID',
            ], 
            [
                $selectedAccount, 
                $this->selectedLocation,
            ], 
            Plugin::config('endpoints.reviews')
        );
        return $endpoint;
    }

    /**
     * Set model with cached data
     *
     * @return object
     */
    public function getCached() : object
    {
        $reviews = new Reviews();

        $cache = $this->getCachedReviews();
        $reviews->setData($cache);

        return $reviews;
    }
    
    /**
     * Cache reviews on load
     * Only cache again if the old cache is older than 2 hours
     *
     * @return void
     */
    protected function cacheReviewData()
    {
        if (! $this->execute) return;
        
        $filename   = Cache::getFileName($this->selectedLocation);
        $jsonCache  = Cache::getJsonCache('reviews-' . $filename);
        $cache      = json_decode($jsonCache, true);
        
        // If cache is still available, we do not need to set this again
        // Cache is only available for 2 hours
        if (!empty($jsonCache) && is_array($cache)) {
            return;
        }

        // Cache a maximum of 20 reviews
        $uri = add_query_arg('pageSize', '20', $this->uri);

        try {
            $result = $this->requestReviewData($uri, $this->auth);
        } catch (\Throwable $exception) {
            $this->adminNotice($exception->getMessage(), '', 2);
        }
        
        if (empty($result)) return;
        
        $reviews = $this->anonymizeReviews($result);
        Cache::setJsonCache('reviews-' . $filename, $reviews);
    }
    
    /**
     * Return the cached reviews from our cache folder based on the selected location
     *
     * @return array
     */
    public function getCachedReviews() : array
    {
        if (! $this->execute) return [];
        
        $this->selectedLocation = $this->locations->getSelectedLocationEndpoint();
        $filename               = Cache::getFileName($this->selectedLocation);
        
        $jsonCache   = Cache::getJsonCache('reviews-' . $filename);
        $reviewCache = json_decode($jsonCache, true);
        
        if (! empty($jsonCache) && is_array($reviewCache)) {
           return $reviewCache;
        }

        return [];
    }

    /**
     * Get all the data from the request and add it to an array
     *
     * @param  string $uri
     * @param  array $auth
     * @return null|array
     */
    protected function requestReviewData(string $uri, array $auth) : ?array
    {
        if (! $this->execute) return null;

        try {
            $result = $this->client->get($uri)->headers([
                'Content-Type: application/json',
                'Authorization' => $auth['token_type'] . ' ' . $auth['access_token'],
            ])->send();
        } catch (\Throwable $exception) {
            wp_send_json_error($exception->getMessage(), 500);
        } catch (RequestException $exception) {
            wp_send_json_error($exception->getErrors(), 500);
        }

        if ($result->getStatusCode() != '200') {
            // Possible login with wrong account. Example: oAuth2 is at info@test.nl and login via Google promt is with information@test.nl
            $this->adminNotice(__('Something went wrong while accessing the Google API, please contact Tussendoor B.V. if the error persists.', 'gmb-reviews'), 'TSD006-' . $result->getStatusCode(), 2);
        }

        $body = json_decode($result->getBody(), true);
        if (empty($body) || empty($body['reviews'])) return null;

        return $this->anonymizeReviews($body);
    }
    
    /**
     * Before we save the reviews to the cache we want to anonymize the names when the reviewer selected this
     * The isAnonymous key is not allways set, but when the reviewer want to be anonymous this attribute is true
     *
     * @param  array $reviews
     * @return array
     */
    private function anonymizeReviews(array $reviews) : array
    {
        $anonymizedReviews = [];
        foreach ($reviews['reviews'] as $review) {
            if (!isset($review['reviewer']['displayName'])) {
                $review['reviewer']['displayName'] = __('Anonymous', 'gmb-reviews');
            }
            if (isset($review['reviewer']['isAnonymous']) && $review['reviewer']['isAnonymous']) {
                $review['reviewer']['displayName'] = __('Anonymous', 'gmb-reviews');
            }
            $anonymizedReviews[] = $review;
        }

        $reviews['reviews'] = $anonymizedReviews;
        return $reviews;
    }
}