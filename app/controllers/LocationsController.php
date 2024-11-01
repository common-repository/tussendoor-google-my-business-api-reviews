<?php namespace Tussendoor\GmbReviews\Controllers;

use Tussendoor\Http\Client;
use Tussendoor\GmbReviews\Plugin;
use Tussendoor\Http\RequestException;

use Tussendoor\GmbReviews\Helpers\Request;
use Tussendoor\GmbReviews\Models\Accounts;
use Tussendoor\GmbReviews\Models\Locations;

class LocationsController extends Controller
{
    private $uri;
    private $auth;
    protected $accounts;
    protected $selectedAccount;

    public function __construct()
    {
        $this->client    = new Client();
        $this->accounts  = new Accounts();
        $this->locations = new Locations();
        $this->auth      = Plugin::config('settings.authorization');

        if ($this->accounts->hasSelectedAccount()) {
            $this->selectedAccount = $this->accounts->getSelectedAccountEndpoint();
            $this->uri             = str_replace('accounts/$accountID', $this->selectedAccount, Plugin::config('endpoints.locations'));
        }
    }

    public function register()
    {
        add_action('wp_ajax_'.Plugin::config('plugin.tag').'_sync_locations', [$this, 'requestLocations']);
    }

    /**
     * Request all locations corresponding to the given accounts and save them to the settings
     * 
     * Method gets called from AccountController::62
     *
     * @param  array $accounts
     * @return void
     */
    protected function saveLocationsFromAccounts(array $accounts)
    {
        if (empty($this->auth)) return;

        $locationsData = [];
        foreach ($accounts as $account) {
            $uri = str_replace('accounts/$accountID', $account['endpoint'], Plugin::config('endpoints.locations'));
           
            $foundLocations = $this->requestLocationData($uri, $this->auth);
            $foundLocations = $this->transformLocationData($foundLocations, $account['endpoint']);

            $locationsData = array_merge($locationsData, $foundLocations);
        }

        $this->locations->saveToSettings($locationsData);
    }
    
    /**
     * Request location corresponding to the selected account
     * 
     * Method gets called on change event of the account dropdown
     *
     * @return void
     */
    public function requestLocations()
    {
        $request = Request::fromGlobal();

        if (empty($this->auth) || !$request->has('account')) {
            wp_send_json_error(__('Unable to find locations, please select an account or add the correct authentication.', 'gmb-reviews'), 404);
        };

        $account = $request->getString('account');
        $response['locations'] = $this->locations->getLocationsFromAccount($account);

        if (empty($response['locations'])) {
            wp_send_json_error(__('Unable to find locations corresponding to this account with the given client credentials. Please check if you granted access with the correct Google account.', 'gmb-reviews'), 404);
        }

        wp_send_json_success($response, 200);
    }
    
    /**
     * Request locations
     * 
     * @since 1.1.0 Since the use of the new Locations API endpoint we added the query arguments "pageSize" and "readMask"
     *
     * @param  string $uri  The uri that is composed with the given / chosen / saved account
     * @param  mixed  $auth The authentication corresponding to the client credentials
     * @return array
     */
    private function requestLocationData(string $uri, array $auth) : array
    {
        try {

            $endpoint = add_query_arg([
                'pageSize'  => 100,
                'readMask'  => 'name,title',
            ], $uri);

            $result = $this->client->get($endpoint)->headers([
                'Content-Type'  => 'application/json',
                'Authorization' => $auth['token_type'] . ' ' . $auth['access_token'],
            ])->send();

        } catch (\Throwable $exception) {
            wp_send_json_error($exception->getMessage(), 500);
        } catch (RequestException $exception) {
            wp_send_json_error($exception->getErrors(), 500);
        }

        if ($result->getStatusCode() != '200') {
            // todo - shows after page refresh. Maybe early wp_send_json_error()?
            $this->adminNotice(__('Something went wrong while accessing the Google API, please contact Tussendoor B.V. if the error persists.', 'gmb-reviews'), 'TSD005-' . $result->getStatusCode(), 2);
        }
        
        $body = json_decode($result->getBody(), true);
        if (empty($body) || empty($body['locations'])) return [];
    
        return $body['locations'];
    }
    
    /**
     * We receive a lot of information from google so we transform it to a more usefull array
     * The method also makes sure the parent account gets added to the location data so we can find to matching pairs more easily
     * 
     * @since 1.1.0 Since the use of the new Locations API endpoint we request the location title instead of locationName
     *
     * @param  array  $locationData The information from Google
     * @param  string $parent       The parent account
     * @return array
     */
    private function transformLocationData(array $locationData, string $parent) : array
    {
        $locations = [];
        foreach ($locationData as $location) {
            $locations[] = [
                'name'      => $location['title'],
                'endpoint'  => $location['name'],
                'parent'    => $parent,
                'selected'  => false,
            ];
        };

        return $locations;
    }
}