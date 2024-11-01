<?php namespace Tussendoor\GmbReviews\Controllers;

use Tussendoor\Http\Client;
use Tussendoor\GmbReviews\Plugin;
use Tussendoor\Http\RequestException;

use Tussendoor\GmbReviews\Helpers\Cookie;
use Tussendoor\GmbReviews\Helpers\Request;
use Tussendoor\GmbReviews\Helpers\Setting;
use Tussendoor\GmbReviews\Models\Authentication;

class AccessController extends Controller
{
    protected $clientID;
    protected $clientSecret;

    public function register()
    {
        $this->setup();
        $this->addActions();
        $this->saveAccessCode();
    }

    private function setup()
    {
        if (Plugin::hasSetting('client_id')
            && Plugin::hasSetting('client_secret')
            && Plugin::hasSetting('google_code')
        ) {
        
            $this->clientID     = Plugin::config('settings.client_id');
            $this->clientSecret = Plugin::config('settings.client_secret');
            $this->client       = new Client();
        
            try {
                $this->saveAuthenticationInfo();
            } catch (\Throwable $exception) {
                $this->adminNotice($exception->getMessage(), '', 2);
            }
            
        }
    }

    public function addActions()
    {
        add_action('wp_ajax_'.Plugin::config('plugin.tag').'_revoke_access', [$this, 'revokeAccess']);
    }
    
    /**
     * Save the access code that is send back to us by google in the redirect url as google_code
     */
    public function saveAccessCode()
    {
        $request = Request::fromGlobal();
        
        if (
            !$request->has('code') 
            || ( !$request->has('state') || !Cookie::has('state') || $request->getString('state') != Cookie::getString('state') ) // See App()->setStateCookie()
            || ( !$request->has('scope') || $request->getString('scope') != Plugin::config('google.gmb_scope') )
        ) return;

        $code = $request->getString('code');
        $codeSetting = [
            'google_code' => $code
        ];

        Setting::savePluginSetting($codeSetting);
    }
    
    /**
     * Revoke the granted access to this plugin on a ajax call
     */
    public function revokeAccess()
    {   
        if (! Plugin::hasSetting('authorization.access_token')) {
            wp_send_json_error(__('It looks like the plugin does not have access yet! Access token missing.', 'gmb-reviews'), 404);
        }

        try {
            $result = $this->client->post(Plugin::config('endpoints.revoke'), [
                'token' => Plugin::config('settings.authorization.access_token')
            ])->send();
        } catch (\Throwable $exception) {
            wp_send_json_error($exception->getMessage(), 500);
        } catch (RequestException $exception) {
            wp_send_json_error($exception->getErrors(), 500);
        }

        if ($result->getStatusCode() != '200') {
            // Could be an invalid token;
            // Could be "The requested URL was not found on this server"
            $this->adminNotice(__('Something went wrong while accessing the Google API, please contact Tussendoor B.V. if the error persists.', 'gmb-reviews'), 'TSD003-' . $result->getStatusCode(), 2);
            wp_send_json_error(__('Something went wrong while accessing the Google API, please contact Tussendoor B.V. if the error persists.', 'gmb-reviews'), 500);
        }

        // delete saved reviews in the cache
        $this->emptyCache(true);

        // For now we do delete all settings, but maybe the plugin will get new settings later on
        // we do not know if these settings are deletable
        $deletableSettings = [
            'client_id',
            'client_secret',
            'google_code',
            'authorization',
            'accounts',
            'locations',
            'hasSelectedLocation',
        ];

        Setting::deletePluginSetting($deletableSettings);

        wp_send_json_success(__('Successfully revoked access to the Google My Business API.', 'gmb-reviews'));
    }
    
    /**
     * Obtain the authentication token using the refresh token
     * Do this everytime the authentication expires - we check the expiration with a transient
     */
    private function saveAuthenticationInfo()
    {
        if (get_transient(Plugin::config('plugin.tag') . '_get_new_authentication_info') 
            && Plugin::hasSetting('authorization')
        ) {
            return Plugin::config('settings.authorization');
        }
        
        $authentication = new Authentication();
        $refreshToken    = $this->getRefreshToken($authentication);

        if (empty($refreshToken)) return;

        try {
            $result = $this->client->post(Plugin::config('endpoints.auth'), [
                'refresh_token' => $refreshToken,
                'client_id'     => $this->clientID,
                'client_secret' => $this->clientSecret,
                'redirect_uri'  => Plugin::config('plugin.redirect_uri'),
                'grant_type'    => 'refresh_token',
            ])->send();
        } catch (\Throwable $exception) {
            wp_send_json_error($exception->getMessage(), 500);
        } catch (RequestException $exception) {
            wp_send_json_error($exception->getErrors(), 500);
        }

        if ($result->getStatusCode() != '200') {
            $this->authorizationExpired();
            $this->adminNotice(__('Something went wrong while accessing the Google API, please re-authorise the access and contact Tussendoor B.V. if the error persists.', 'gmb-reviews'), 'TSD002-' . $result->getStatusCode(), 2);
        }

        $body = json_decode($result->getBody(), true);

        if (empty($body) || !isset($body['access_token'])) return '';
        
        set_transient(Plugin::config('plugin.tag') . '_get_new_authentication_info', Plugin::config('plugin.tag') . '_expiration_control', $body['expires_in']);
        
        $previousAuthorization = Plugin::hasSetting('authorization') ? Plugin::config('settings.authorization') : [];
        $authorizationArray    = array_replace($previousAuthorization, $body);
        
        $authentication->saveToSettings($authorizationArray);
        
        Setting::deletePluginSetting(['authorization_expired']);
    }
    
    /**
     * Obtain the refresh token that allows us to always and automatically retrieve the authentication
     * Only do this once
     *
     * @return string
     */
    private function getRefreshToken(Authentication $authentication)
    {
        // if token is known and set return this token
        if (Plugin::hasSetting('authorization.refresh_token')) {
            return Plugin::config('settings.authorization.refresh_token');
        }

        try {
            $result = $this->client->post(Plugin::config('endpoints.refresh'), [
                'code'          => Plugin::config('settings.google_code'),
                'client_id'     => $this->clientID,
                'client_secret' => $this->clientSecret,
                'redirect_uri'  => Plugin::config('plugin.redirect_uri'),
                'grant_type'    => 'authorization_code',
            ])->send();
        } catch (\Throwable $exception) {
            wp_send_json_error($exception->getMessage(), 500);
        } catch (RequestException $exception) {
            wp_send_json_error($exception->getErrors(), 500);
        }

        if ($result->getStatusCode() != '200') {
            $this->authorizationExpired();
            $this->adminNotice(__('Something went wrong while accessing the Google API, please re-authorise the access and contact Tussendoor B.V. if the error persists.', 'gmb-reviews'), 'TSD001-' . $result->getStatusCode(), 2);
        }

        $body = json_decode($result->getBody(), true);

        if (empty($body) || !isset($body['refresh_token'])) return '';

        $authentication->saveToSettings($body);

        return (string) $body['refresh_token'];
    }
}