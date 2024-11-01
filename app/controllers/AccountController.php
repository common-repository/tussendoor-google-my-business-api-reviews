<?php namespace Tussendoor\GmbReviews\Controllers;

use Tussendoor\GmbReviews\Plugin;
use Tussendoor\GmbReviews\Models\Accounts;

use Tussendoor\Http\Client;
use Tussendoor\Http\RequestException;

class AccountController extends LocationsController
{
    private $uri;
    private $auth;
    protected $pluginSettings;
    protected $accounts;
    
    /**
     * The button to fire this functionality is only visible when access is granted
     * Authorization will be available then and that is why we do not need many checks here
     */
    public function register()
    {
        $this->setup();
        $this->addActions();
    }

    protected function setup()
    {
        $this->auth     = Plugin::config('settings.authorization');
        $this->uri      = Plugin::config('endpoints.accounts');
        $this->client   = new Client();
    }

    protected function addActions()
    {
        add_action('wp_ajax_'.Plugin::config('plugin.tag').'_sync_accounts', [$this, 'requestAccountNames']);
    }
    
    /**
     * We get too much information from requestAccounts() this method simplifies the information
     * This method also saves the accounts to the settings of the plugin
     * All corresponding locations will also be saved
     * 
     * Method gets called by an ajax call that gets fired by a click of the user
     *
     * @return void
     */
    public function requestAccountNames()
    {
        try {
            $accounts = $this->requestAccounts();
        } catch (\Throwable $exception) {
            wp_send_json_error($exception->getMessage(), 500);
        }

        $accountData = [];
        foreach ($accounts as $account) {
            $accountData[] = [
                'name'      => $account['accountName'],
                'endpoint'  => $account['name'],
                'selected'  => false,
            ];
        };

        if (empty($accountData)) {
            wp_send_json_error(__('Unable to find accounts with the given client credentials.', 'gmb-reviews'), 404);
        }

        $this->saveAccounts($accountData);
        $this->saveLocationsFromAccounts($accountData);

        $response['accounts'] = $accountData;
        $response['notice'] = __('Successfully synced account data!', 'gmb-reviews');

        wp_send_json_success($response, 200);
    }
    
    /**
     * Request all accounts corresponding with the client credentials of the user
     * When there are no (valid) credentials we throw an error that is catched in requestAccountNames()
     *
     * @return array
     */
    protected function requestAccounts() : array
    { 
        try {
            $result = $this->client->get($this->uri)->headers([
                'Content-Type'  => 'application/json',
                'Authorization' => $this->auth['token_type'] . ' ' . $this->auth['access_token'],
            ])->send();
        } catch (\Throwable $exception) {
            wp_send_json_error($exception->getMessage(), 500);
        } catch (RequestException $exception) {
            wp_send_json_error($exception->getErrors(), 500);
        }

        if ($result->getStatusCode() != '200') {
            // The requested URL was not found on this server
            // Request had invalid authentication credentials or token type
            $this->adminNotice(__('Something went wrong while accessing the Google API, please contact Tussendoor B.V. if the error persists.', 'gmb-reviews'), 'TSD004-' . $result->getStatusCode(), 2);
        }

        $body       = json_decode($result->getBody(), true);
        $accounts   = (!empty($body) && !empty($body['accounts']) ? $body['accounts'] : []);
        
        if (!is_array($accounts)) return [];

        return $accounts;
    }
    
    /**
     * Save all found accounts to the settings
     *
     * @param  array $accountData
     * @return void
     */
    public function saveAccounts(array $accountData)
    {
        if (empty($accountData)) {
            return;
        }

        $accounts = new Accounts();
        $accounts->saveToSettings($accountData);
    }
}