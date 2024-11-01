<?php namespace Tussendoor\GmbReviews;

use Tussendoor\GmbReviews\Helpers\Cookie;
use Tussendoor\GmbReviews\Helpers\Notice;
use Tussendoor\GmbReviews\Helpers\Request;
use Tussendoor\GmbReviews\Models\Accounts;
use Tussendoor\GmbReviews\Models\Locations;

class App
{
    function __construct()
    {
        $this->init();
        $this->addActions();
        $this->addFilters();
        $this->registerControllers();
    }

    public function init()
    {
        $this->createCacheFolder();
        Cookie::init();
    }

    public function addActions()
    {
        add_action('admin_menu', [$this, 'adminMenu'], 100);
        add_action('admin_bar_menu', [$this, 'adminBar'], 999);
        add_action('wp_enqueue_scripts', [$this, 'adminEnqueueStyle']);
        add_action('admin_enqueue_scripts', [$this, 'adminEnqueueScripts']);

        add_action('wp_ajax_'.Plugin::config('plugin.tag').'_save', [$this, 'wpAjaxSave']);

        add_action('init', [new Notice, 'display'], 99);
        add_action('init', [$this, 'LoadTranslations']);
    }

    public function addFilters()
    {
        add_filter('wp_kses_allowed_html', [$this, 'allowSvgInHTML']);
    }
    
    /**
     * Add our menu page
     */
    public function adminMenu()
    {
        add_menu_page(
            Plugin::config('plugin.name'),
            Plugin::config('plugin.nameshort'),
            'publish_posts',
            Plugin::config('plugin.tag'),
            function()
            {
                require Plugin::config('plugin.viewpath').'admin/admin.php';
            },
            'dashicons-star-filled'
        );
    }
    
     /**
     * Add our menu page to the admin bar
     */
    public function adminBar($wp_admin_bar) {

        $parent = Plugin::config('plugin.tag');

        $customAdminNodes = [
            [
                'id'    => $parent,
                'title' => '<i class="ab-icon '.$parent.'_icon"></i><span class="ab-label">' . Plugin::config('plugin.nameshort') . '</span>',
                'href'  => Plugin::config('plugin.dashboard_url'),
            ],
            [
                'parent' => $parent,
                'id'     => $parent . '_dashboard',
                'title'  => __('Dashboard', 'gmb-reviews'),
                'href'   => Plugin::config('plugin.dashboard_url'),
            ],
        ];

        if (Plugin::hasSetting('client_id') && Plugin::hasSetting('client_secret') && Plugin::hasSetting('authorization')) {
            $customAdminNodes[] = [
                'parent' => $parent,
                'id'     => $parent . '_settings',
                'title'  => __('Settings', 'gmb-reviews'),
                'href'   => Plugin::config('plugin.settings_url'),
            ];
        }
        
        if (Plugin::hasSetting('hasSelectedLocation') && Plugin::config('settings.hasSelectedLocation')) {
            $customAdminNodes[] = [
                'parent' => $parent,
                'id'     => $parent . '_widget',
                'title'  => __('Widget', 'gmb-reviews'),
                'href'   => Plugin::config('plugin.widget_url'),
            ];
        }

        $customAdminNodes[] = [
            'parent' => $parent,
            'id'     => $parent . '_info',
            'title'  => __('Info', 'gmb-reviews'),
            'href'   => Plugin::config('plugin.info_url'),
        ];

        $customAdminNodes[] = [
            'parent' => $parent,
            'id'     => $parent . '_gopro',
            'title'  => '<i class="ab-icon '.$parent.'_pro_icon"></i><span class="ab-label">' . __('Premium!', 'gmb-reviews'),
            'href'   => Plugin::config('tussendoor.pro_url'),
            'meta'   => [
                'rel'    => 'noopener',
                'target' => '_blank',
            ],
        ];
            
        foreach ($customAdminNodes as $node) {
            $wp_admin_bar->add_node($node);
        }
    }

    /**
     * Enqueue assets that should be enqueued on frontend and backend
     */
    public function adminEnqueueStyle() {
        wp_enqueue_style(
            Plugin::config('plugin.tag').'_admin_css',
            Plugin::config('plugin.url').'assets/admin/css/admin.css',
            null,
            Plugin::config('plugin.version')
        );
    }
    
    /**
     * Enqueue assets on our admin page
     *
     * @param string $hook
     */
    public function adminEnqueueScripts($hook) {

        $this->adminEnqueueStyle();

        // Only enqueue admin assets on our own admin page
        if (strpos($hook, Plugin::config('plugin.tag')) !== false) {
            $min = WP_DEBUG ? '.min' : '';

            wp_enqueue_script(
                Plugin::config('plugin.tag').'_bootstrap_script',
                Plugin::config('plugin.url').'vendor/twbs/bootstrap/dist/js/bootstrap.bundle'.$min.'.js',
                null,
                Plugin::config('plugin.version')
            );
            wp_enqueue_script(
                Plugin::config('plugin.tag').'_dashboard_script',
                Plugin::config('plugin.url').'assets/admin/js/dashboard.js',
                null,
                Plugin::config('plugin.version')
            );
            wp_localize_script(
                Plugin::config('plugin.tag').'_dashboard_script', 
                'Dashboard', 
                array(
                    'admin_page'    => Plugin::config('plugin.dashboard_url'),
                    'settings_page' => Plugin::config('plugin.settings_url'),
                ),
            );

            wp_enqueue_style(
                Plugin::config('plugin.tag').'_fontawesome_css',
                Plugin::config('plugin.url').'vendor/fortawesome/font-awesome/css/all'.$min.'.css',
                null,
                Plugin::config('plugin.version')
            );
            wp_enqueue_style(
                Plugin::config('plugin.tag').'_bootstrap_css',
                Plugin::config('plugin.url').'vendor/twbs/bootstrap/dist/css/bootstrap'.$min.'.css',
                null,
                Plugin::config('plugin.version')
            );
            wp_enqueue_style(
                Plugin::config('plugin.tag').'_dashboard_css',
                Plugin::config('plugin.url').'assets/admin/css/dashboard.css',
                null,
                Plugin::config('plugin.version')
            );

        }
    }
    
    /**
     * We add svg's in our shortcodes and widget
     * When escaping we accept the SVG's here
     *
     * @param  mixed $tags
     * @return array
     */
    public function allowSvgInHTML($tags)
    {
        $tags['svg'] = array(
            'class'             => true,
            'xmlns'             => true,
            'fill'              => true,
            'viewbox'           => true,
            'role'              => true,
            'aria-hidden'       => true,
            'aria-labelledby'   => true,
            'focusable'         => true,
            'width'             => true,
            'height'            => true,
        );
        $tags['path'] = array(
            'd'     => true,
            'fill'  => true,
        );
        $tags['g'] = array(
            'fill'  => true,
        );
        $tags['title'] = array(
            'title'  => true,
        );
        return $tags;
    }
    
    /**
     * Load plugin translations.
     */
    public function LoadTranslations()
    {
        load_plugin_textdomain('gmb-reviews', false, Plugin::config('plugin.lang'));
    }
    
    /**
     * Multiple functions to fire on save
     */
    public function wpAjaxSave() {
        if (!current_user_can('administrator')) {
            wp_send_json_error();
        }

        $request = Request::fromGlobal();
        $request->remove('action');

        if ($request->has('empty_cache')) {
            $request->remove('empty_cache');
            $this->emptyCache(true);
        }

        if ($request->has('selected_account')) {
            $selectedEndpoint = $request->getString('selected_account');
            (new Accounts)->saveSelectedAccount($selectedEndpoint);

            // do not save the value itself to the settings
            $request->remove('selected_account');
        }

        if ($request->has('selected_location')) {
            $selectedEndpoint = $request->getString('selected_location');
            (new Locations)->saveSelectedLocation($selectedEndpoint);
        
            // do not save the value itself to the settings
            $request->remove('selected_location');
        }
        
        if ($this->save($request)) {
            wp_send_json_success();
        }

        wp_send_json_error();
    }
    
    /**
     * Remove all files in our cache folder
     * Delete transient to enforce requesting new authentication information after saving client credentials
     
     */
    protected function emptyCache($deleteTransients = true)
    {
        if ($deleteTransients) {
            delete_transient(Plugin::config('plugin.tag') . '_get_new_authentication_info');
            delete_transient(Plugin::config('plugin.tag') . '_notices');
        }

        $cachePath = Plugin::config('plugin.cachepath');
        $caches = glob($cachePath . '*');

        foreach($caches as $file) {
            if(is_file($file)) {
                unlink($file); 
            }
        }

        wp_send_json_success();
    }
    
    /**
     * Save settings to the options table and
     * Merge the settings to our config file
     *
     * @param  Request $request
     * @return bool
     */
    public function save(Request $request) : bool
    {
        // remove whitespaces around all the values in the $values variable
        $values   = array_map('trim', $request->all());
        $settings = get_option(Plugin::config('plugin.settings'), []);
        $settings = array_replace_recursive($settings, $values);

        if (update_option(Plugin::config('plugin.settings'), $settings, true)) {
            return Plugin::loadOptions();
        }

        return false;
    }

    public function registerControllers()
    {
        (new Controllers\AdminController)->register();
        (new Controllers\PublicController)->register();
        (new Controllers\AccessController)->register();
        (new Controllers\ReviewController)->register();
        (new Controllers\AccountController)->register();
        (new Controllers\CreatorController)->register();
        (new Controllers\LocationsController)->register();
        (new Controllers\StructuredDataController)->register();
    }

    public function createCacheFolder()
    {
        if (!file_exists(Plugin::config('plugin.cachepath'))) {
            mkdir(Plugin::config('plugin.cachepath'), 0777, true);
        }
    }
}
