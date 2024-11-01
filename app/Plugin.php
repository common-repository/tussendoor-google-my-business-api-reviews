<?php namespace Tussendoor\GmbReviews;

class Plugin
{
    protected static $app, $config;

    public static function boot()
    {
        static::$config = new \Adbar\Dot;
        self::loadConfig(dirname(__FILE__, 2).'/config/config.php');
        self::loadOptions();
        self::$app = new App();
    }

    public static function loadConfig($config)
    {
        if (!file_exists($config)) {
            return;
        }

        self::$config->merge(require $config);
        return true;
    }

    public static function loadOptions()
    {
        self::$config->merge([
            'settings' => get_option(Plugin::config('plugin.settings'), []),
        ]);
        return true;
    }

    public static function config($key, $value = null)
    {
        if ($value) {
            return self::$config->set($key, $value);
        }
        return self::$config->get($key);
    }

    public static function hasSetting($key)
    {
        return ! self::$config->isEmpty('settings.' . $key);
    }

    public static function hasConfig($key)
    {
        return ! self::$config->isEmpty($key);
    }
}
