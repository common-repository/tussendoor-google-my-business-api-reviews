<?php namespace Tussendoor\GmbReviews\Helpers;

use Tussendoor\GmbReviews\Plugin;

class Setting
{    
    /**
     * Get value from the database
     *
     * @param  string $name
     * @return array
     */
    public static function get(string $name) : array
    {
        return get_option($name, []);
    }

    /**
     * Save or update option in the database
     *
     * @param  string $name of the setting
     * @param  array $values to be saved
     * @return bool
     */
    public static function save(string $name, array $values) : bool
    {
        $settings = get_option($name, []);
        $settings = array_replace_recursive($settings, $values);
        
        return update_option($name, $settings, true);
    }
    
    /**
     * Delete value from database
     *
     * @param  string $name The name of the option to delete
     * @return bool
     */
    public static function delete(string $name) : bool
    {
        return delete_option($name);
    }

    /**
     * Save values to the plugin settings outside of the plugin dashboard
     *
     * @param  array $values The values to save
     * @return bool
     */
    public static function savePluginSetting(array $values) : bool
    {
        $settings = get_option(Plugin::config('plugin.settings'), []);
        $settings = array_replace_recursive($settings, $values);

        if (update_option(Plugin::config('plugin.settings'), $settings, true)) {
            return Plugin::loadOptions();
        }

        return false;
    }

    /**
     * Delete values from the plugin settings outside of the plugin dashboard
     *
     * @param  array $values The array keys to delete
     * @return bool
     */
    public static function deletePluginSetting(array $settingKeys) : bool
    {
        $settings = get_option(Plugin::config('plugin.settings'), []);

        foreach ($settingKeys as $key) {
            if (array_key_exists($key, $settings)) unset($settings[$key]);
        }
    
        if (update_option(Plugin::config('plugin.settings'), $settings, true)) {
            return Plugin::loadOptions();
        }
    
        return false;
    }

    /**
     * Delete all authorization values from the plugin settings
     *
     * @return bool
     */
    public static function deleteAuthorization() : bool
    {
        // Check if there is / was autohirzation in the first place
        if (!Plugin::hasSetting('client_id') || !Plugin::hasSetting('client_secret') || !Plugin::hasSetting('authorization') || !Plugin::hasSetting('google_code')) {
            return false;
        }

        $deletableSettings = [
            'google_code',
            'authorization',
        ];
        
        return self::deletePluginSetting($deletableSettings);
    }
}