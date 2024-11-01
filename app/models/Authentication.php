<?php namespace Tussendoor\GmbReviews\Models;

use Tussendoor\GmbReviews\Plugin;
use Tussendoor\GmbReviews\Helpers\Setting;

class Authentication
{
    public $settings;
    
    public function __construct()
    {
        $this->settings = get_option(Plugin::config('plugin.settings'), []);
    }
    
    /**
     * Save authorization to the plugin settings
     *
     * @param  array $authData
     * @return void
     */
    public function saveToMainSettings(array $authData)
    {
        Setting::savePluginSetting($authData);
    }

    /**
     * Save authorization to the plugin settings
     *
     * @param  array $authData
     * @return void
     */
    public function saveToSettings(array $authData)
    {
        $setting['authorization'] = $authData;
        Setting::savePluginSetting($setting);
    }
    
    /**
     * Get all authorization information from the plugin settings
     *
     * @return array
     */
    public function getAllFromSettings() : array
    {
        return isset($this->settings['authorization']) ? $this->settings['authorization'] : [];
    }

    /**
     * Get all authorization information from the plugin settings
     *
     * @return string
     */
    public function getFromSettings(string $key) : string
    {
        return isset($this->settings['authorization'][$key]) ? $this->settings['authorization'][$key] : '';
    }

}