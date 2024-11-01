<?php namespace Tussendoor\GmbReviews\Models;

use Tussendoor\GmbReviews\Plugin;
use Tussendoor\GmbReviews\Helpers\Setting;

class Accounts
{
    public $settings;

    public function __construct()
    {
        $this->settings = get_option(Plugin::config('plugin.settings'), []);
    }
    
    /**
     * Save accounts to the plugin settings
     *
     * @param  array $accountData
     * @return void
     */
    public function saveToSettings(array $accountData)
    {
        $setting['accounts'] = $accountData;
        Setting::savePluginSetting($setting);
    }
    
    /**
     * Get accounts from the plugin settings
     *
     * @return array
     */
    public function getFromSettings() : array
    {
        return isset($this->settings['accounts']) ? $this->settings['accounts'] : [];
    }
    
    /**
     * Save the selected account by setting the selected attribute to true
     * Set the selected attribute of all other accounts to false
     * 
     * Method gets called from the wp_ajax_save method from App()
     *
     * @param  string $selectedEndpoint
     * @return void
     */
    public function saveSelectedAccount(string $selectedEndpoint)
    {
        $accountData = [];
        $accounts    = $this->getFromSettings();

        foreach ($accounts as $account) {
            if ($account['endpoint'] === $selectedEndpoint) {
                $account['selected'] = true;
            } else {
                $account['selected'] = false;
            }
            $accountData[] = $account;
        }

        $this->saveToSettings($accountData);
    }
    
    /**
     * Container method to get information from the selected account
     * Only used within this class
     *
     * @param  string $key The information we want to retrieve
     * @return string
     */
    private function getSelectedAccount(string $key) : string
    {
        $accounts = $this->getFromSettings();
        foreach ($accounts as $account) {
            if (isset($account['selected']) && $account['selected']) {
                return $account[$key];
            }
        }

        return '';
    }
    
    /**
     * Simple method that checks if an account has been selected allready
     *
     * @return bool
     */
    public function hasSelectedAccount() : bool
    {
        return !empty($this->getSelectedAccount('endpoint'));
    }
    
    /**
     * Get the endpoint of the selected account
     *
     * @return string
     */
    public function getSelectedAccountEndpoint() : string
    {
        return $this->getSelectedAccount('endpoint');
    }
    
    /**
     * Get the name of the selected account
     *
     * @return string
     */
    public function getSelectedAccountName() : string
    {
        return $this->getSelectedAccount('name');
    }
}