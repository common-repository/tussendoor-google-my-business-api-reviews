<?php namespace Tussendoor\GmbReviews\Controllers;

use Tussendoor\GmbReviews\Plugin;
use Tussendoor\GmbReviews\Helpers\Cookie;
use Tussendoor\GmbReviews\Helpers\Request;

class DashboardController extends Controller
{
    public $accessCodeUri;
    public $clientID;
    public $clientSecret;
    public $credentialsName;
    public $credentialsUrl;
    public $hasExpired;
    public $manualUrl;
    public $authExpired;

    public function __construct()
    {
        $this->setupFromSettingsConfig();
        $this->setupFromPluginConfig();
        $this->setupFromGoogleConfig();

        $this->getAccessCodeUri();
    }
    
    /**
     * Setup public properties that we use in our views
     */
    private function setupFromSettingsConfig()
    {
        $this->clientID      = Plugin::config('settings.client_id');
        $this->clientSecret  = Plugin::config('settings.client_secret');
        $this->authExpired   = Plugin::config('settings.authorization_expired');
        
        if ($this->authExpired) {
            $this->adminNotice(__('The access to the Google My Business API has been expired, please grant access again.', 'gmb-reviews'), '', 3);
        }
    }

    /**
     * Setup public properties that we use in our views
     */
    private function setupFromGoogleConfig()
    {
        $this->credentialsName = Plugin::config('google.credentials_name');
        $this->credentialsUrl  = Plugin::config('google.credentials_url');
    }

    /**
     * Setup public properties that we use in our views
     */
    private function setupFromPluginConfig()
    {
        $this->manualUrl = get_bloginfo('language') === 'nl' ? Plugin::config('tussendoor.manual') : Plugin::config('tussendoor.manual_en');
    }
    
    /**
     * Method for constructing the uri we use for requesting the access code
     */
    protected function getAccessCodeUri()
    {
        if (! Plugin::hasSetting('client_id')) return;

        // get state cookie
        $state = Cookie::get('state');

        $this->accessCodeUri = add_query_arg([
            'redirect_uri'           => Plugin::config('plugin.redirect_uri'),
            'scope'                  => Plugin::config('google.gmb_scope'),
            'client_id'              => Plugin::config('settings.client_id'),
            'state'                  => $state,
            'prompt'                 => 'consent',
            'access_type'            => 'offline',
            'response_type'          => 'code',
            'include_granted_scopes' => 'true',
        ], Plugin::config('endpoints.access_code'));
    }
    
    /**
     * Show a save button when there are no client credentials know
     * Makes sure a user can save these credentials
     * 
     * @see dashboard.php
     * @return bool
     */
    public function showSaveButton() : bool
    {
        return ! Plugin::hasSetting('client_id') || ! Plugin::hasSetting('client_secret');
    }

    /**
     * Show a granting or revoking access buttons
     * Makes sure a user can grant or revoke access to the api by the app
     * 
     * @see dashboard.php
     * @return bool
     */
    public function showAccessButtons() : bool
    {
        return Plugin::hasSetting('client_id') && Plugin::hasSetting('client_secret') && !empty($this->accessCodeUri);
    }

    /**
     * Makes sure a user can only access the settings after granting access to the GMB API
     * 
     * @see admin.php
     * @return bool
     */
    public function showSettingsTab() : bool
    {
        return Plugin::hasSetting('client_id') && Plugin::hasSetting('client_secret') && Plugin::hasSetting('authorization');
    }

    /**
     * Makes sure a user can only access the creator tab after selecting a location
    *
    * @see admin.php
    * @return bool
    */
    public function showCreatorTab() : bool
    {
        if (! Plugin::hasSetting('hasSelectedLocation')) return false;

        return Plugin::config('settings.hasSelectedLocation');
    }

    /**
     * Hide granting access button
     * Makes sure a user can only grant access when the app does not have access yet
     * 
     * @see dashboard.php
     * @return bool
     */
    public function hideGrantAccessButton() : bool
    {
        return $this->pluginHasAccess();
    }

    /**
     * Hide revoking access button
     * Makes sure a user can only revoke access when the app does have access
     * 
     * @see dashboard.php
     * @return bool
     */
    public function hideRevokeAccessButton() : bool
    {
        return ! Plugin::hasSetting('authorization') || ! Plugin::hasSetting('google_code');

    }
    
    /**
     * Checks if the plugin has access to the API
     * 
     * @see dashboard.php
     * @return bool
     */
    public function pluginHasAccess() : bool
    {
        return Plugin::hasSetting('client_id') && Plugin::hasSetting('client_secret') && Plugin::hasSetting('authorization') && Plugin::hasSetting('google_code');
    }

    /**
     * Helper to check whether or not to activate a tab on load
     *
     * @param  string $tab
     * @return bool
     */
    public function isActive(string $tab) : bool
    {
        $request = Request::fromGlobal();

        if (!$request->has('tab') || $request->isEmpty('tab')) {
            return $tab === 'dashboard';
        }

        if (strpos($request->getString('tab'), 'creator-tab') !== false && $tab === 'creative-tab') {
            return true;
        }
        
        return $request->getString('tab') === $tab . '-tab';
    }
    
    /**
     * Return published pages to fill dropdown on settings page
     *
     * @return array
     */
    public function getPages() : array
    {
        $args = [
            'sort_order'    => 'asc',
            'sort_column'   => 'post_title',
            'post_type'     => 'page',
            'post_status'   => 'publish'
        ];
        $pages = get_pages($args);

        return $pages;
    }
    
    /**
     * Check if the current page in the dropdown is the selected page
     *
     * @param  string $id The ID of the current page in the loop
     * @return bool
     */
    public function isSelectedPage(string $id) : bool
    {
        return Plugin::hasSetting('reviews_page_id') && Plugin::config('settings.reviews_page_id') === $id;
    }
}