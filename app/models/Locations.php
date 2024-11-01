<?php namespace Tussendoor\GmbReviews\Models;

use Tussendoor\GmbReviews\Plugin;
use Tussendoor\GmbReviews\Helpers\Setting;

class Locations
{
    public $settings;

    public function __construct()
    {
        $this->settings = get_option(Plugin::config('plugin.settings'), []);
    }

    /**
     * Save locations to the plugin settings
     *
     * @param  array $locationData
     * @return void
     */
    public function saveToSettings(array $locationData) {
        $setting['locations'] = $locationData;
        Setting::savePluginSetting($setting);
    }
    
    /**
     * Get locations from the plugin settings
     *
     * @return array
     */
    public function getFromSettings()
    {
        return isset($this->settings['locations']) ? $this->settings['locations'] : [];
    }
    
    /**
     * Simple method to check if the current account has locations
     *
     * @param  string $selectedAccount
     * @return bool
     */
    public function currentAccountHasLocations(string $selectedAccount) : bool
    {
        return !empty($this->getLocationsFromAccount($selectedAccount));
    }
    
    /**
     * Get all the locations from the selected account
     *
     * @param  string $selectedAccount
     * @return array
     */
    public function getLocationsFromAccount(string $selectedAccount) : array
    {   
        $locationData = [];
        $locations    = $this->getFromSettings();
        foreach ($locations as $location) {
            if ($location['parent'] === $selectedAccount) {
                $locationData[] = $location;
            }
        }

        return $locationData;
    }
    
    /**
     * Save the selected location by setting the selected attribute to true
     * Set the selected attribute of all other locations to false
     * 
     * Method gets called from the wp_ajax_save method from App()
     *
     * @param  string $selectedEndpoint
     * @return void
     */
    public function saveSelectedLocation(string $selectedEndpoint)
    {
        $locationData = [];
        $locations    = $this->getFromSettings();

        $selected = false;
        foreach ($locations as $location) {
            if ($location['endpoint'] === $selectedEndpoint) {
                $location['selected'] = true;
                $selected = true;
            } else {
                $location['selected'] = false;
            }
            $locationData[] = $location;
        }

        $this->saveToSettings($locationData);

        Setting::savePluginSetting(['hasSelectedLocation' => $selected]);
    }
        
    /**
     * Container method to get information from the selected location
     * Only used within this class
     *
     * @param  string $key The information we want to retrieve
     * @return string
     */
    private function getSelectedLocation(string $key) : string
    {
        $locations = $this->getFromSettings();
        foreach ($locations as $location) {
            if (isset($location['selected']) && $location['selected']) {
                return $location[$key];
            }
        }

        return '';
    }
        
    /**
     * Simple method that checks if an location has been selected allready
     *
     * @return bool
     */
    public function hasSelectedLocation() : bool
    {
        return !empty($this->getSelectedLocation('endpoint'));
    }
        
    /**
     * Get the endpoint of the selected location
     *
     * @return string
     */
    public function getSelectedLocationEndpoint() : string
    {
        return $this->getSelectedLocation('endpoint');
    }
    
    /**
     * Get the name of the selected location
     *
     * @return string
     */
    public function getSelectedLocationName() : string
    {
        return $this->getSelectedLocation('name');
    }
}