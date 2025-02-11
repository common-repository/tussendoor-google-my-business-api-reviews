<?php

return [
    'tussendoor' => [
        'name'              => 'Tussendoor B.V.',
        'street'            => 'Sixmastraat 66-B',
        'address'           => '8932 PA Leeuwarden',
        'email'             => 'info@tussendoor.nl',
        'tel'               => '058 711 0916',
        'website'           => 'https://tussendoor.nl',
        'website_short'     => 'tussendoor.nl',
        'contact'           => 'https://www.tussendoor.nl/contact',
        'manual'            => 'https://tussendoor.nl/handleidingen/wordpress-gebruik/google-my-business-review-credentials-aanvragen',
        'manual_en'         => 'https://tussendoor.nl/handleidingen/wordpress-gebruik/request-google-my-business-review-credentials',
        'pro_url'           => 'https://tussendoor.nl/wordpress-plugins/gmb-google-my-business-reviews',
        'upgrade_url'       => 'https://www.tussendoor.nl/update-gmb-plugin',
        'upgrade_url_en'    => 'https://www.tussendoor.nl/update-gmb-plugin-en',
    ],
    'plugin' => [
        'name'              => 'Google My Business API Reviews',
        'nameshort'         => 'Google Reviews',
        'tag'               => 'gmb_r',
        'settings'          => 'gmb_r_settings',
        'version'           => '1.1.2',
        'path'              => dirname(__DIR__),
        'viewpath'          => dirname(__DIR__).'/app/views/',
        'url'               => plugin_dir_url(__DIR__),
        'assets'            => plugin_dir_url(__DIR__).'assets/',
        'dir'               => plugin_basename(dirname(__DIR__)),
        'lang'              => plugin_basename(dirname(__DIR__)) . '/assets/languages',
        'dashboard_url'     => admin_url('admin.php?page=gmb_r'),
        'settings_url'      => admin_url('admin.php?page=gmb_r&tab=settings-tab'),
        'widget_url'        => admin_url('admin.php?page=gmb_r&tab=creator-tab'),
        'info_url'          => admin_url('admin.php?page=gmb_r&tab=info-tab'),
        'php_minimum'       => '7.4',
        'cachepath'         => wp_upload_dir()['basedir'] . '/gmb_r/cache/',
        'redirect_uri'   => sanitize_url($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']),
        'eol_date'          => __('1 february 2024', 'gmb-reviews'),
    ],
    'endpoints' => [
        'revoke'            => 'https://oauth2.googleapis.com/revoke',
        'auth'              => 'https://oauth2.googleapis.com/token',
        'refresh'           => 'https://oauth2.googleapis.com/token',
        'access_code'       => 'https://accounts.google.com/o/oauth2/v2/auth',
        'accounts'          => 'https://mybusinessaccountmanagement.googleapis.com/v1/accounts',
        'locations'         => 'https://mybusinessbusinessinformation.googleapis.com/v1/accounts/$accountID/locations',
        'reviews'           => 'https://mybusiness.googleapis.com/v4/accounts/$accountID/locations/$locationID/reviews',
        'base'              => 'https://mybusiness.googleapis.com/v4/',
    ],
    'google' => [
        'gmb_scope'         => 'https://www.googleapis.com/auth/business.manage',
        'locations_url'     => 'https://business.google.com/locations',
        'credentials_url'   => 'https://console.cloud.google.com/apis/credentials',
        'credentials_name'  => 'OAuth 2.0 Client ID',
    ]
];
