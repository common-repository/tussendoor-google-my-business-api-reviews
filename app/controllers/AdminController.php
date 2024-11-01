<?php namespace Tussendoor\GmbReviews\Controllers;

use Tussendoor\GmbReviews\Plugin;

class AdminController extends Controller
{

    protected $pluginFile;

    public function register()
    {
        $this->pluginFile = Plugin::config('plugin.dir') . '/' . Plugin::config('plugin.dir') . '.php';

        $this->addNotices();
        add_action('after_plugin_row', [$this, 'pluginViewNotice'], 10, 3);
    }

    /**
     * Method for displaying an admin notice - returns true on succes
     *
     * @param  string   $message    The message for in the notice
     * @param  bool     $state      The state of the notice 1 = succes; 2 = failure; 3 = warning; 4 = info
     * @param  bool     $global     If we want to show to notice globally 
     * @return bool
     */
    public function pluginViewNotice($pluginFile, $pluginData, $status)
    {
        if ($pluginFile != $this->pluginFile) return false;

        // If WordPress admin has language set to Dutch use upgrade_url any 
        // other language should use upgrade_url_en
        $upgrade_url = (get_user_locale() === 'nl_NL') ? Plugin::config('tussendoor.upgrade_url') : Plugin::config('tussendoor.upgrade_url_en');

        $message = sprintf(
            __('%s: The support for the %s plugin will end on %s. Upgrade to the Premium version %s if you would like to keep using the features of this plugin.', 'gmb-reviews'),
            '<strong>' . __('NOTICE', 'gmb-reviews') . '</strong> ',
            '<strong>' . Plugin::config('plugin.name') . '</strong> ',
            Plugin::config('plugin.eol_date'),
            '<a href="' . $upgrade_url . '" target="_blank" style="text-decoration: underline;">' . __('here', 'gmb-reviews') . '</a>',
        );

        echo '<tr class="plugin-update-tr active" id="gmb-reviews-update-tr">
            <td colspan="4" class="colspanchange">
                <div class="notice inline notice-warning notice-alt"
                    ><p>' . $message . '</p>
                </div>
            </td>
        </tr>';
    }
    
    /**
     * Method for adding notices for when information is missing
     */
    private function addNotices()
    {
        if (empty(get_bloginfo('name'))) {
            $message = sprintf(
                __('The use of the field %s in the %sGeneral Settings%s of WordPress is required.', 'gmb-reviews'),
                '<i>' . __('Site Title') . '</i>',
                '<a href="' . admin_url('options-general.php') . '" target="_blank">',
                '</a>'
            );
            $this->adminNotice($message, '', 2, false);
        }

        if (empty(get_bloginfo('description'))) {
            $message = sprintf(
                __('The use of the field %s in the %sGeneral Settings%s of WordPress is recommended for better results.', 'gmb-reviews'),
                '<i>' . __('Tagline') . '</i>',
                '<a href="' . admin_url('options-general.php') . '" target="_blank">',
                '</a>'
            );
            $this->adminNotice($message, '', 3, false);
        }

        if (!is_writable(dirname(Plugin::config('plugin.cachepath')))) {
            $this->adminNotice('<strong>Error:</strong> ' . __('The uploads folder is not writable! This will result in unexpected results.', 'gmb-reviews'), '', 2, false);
        }

        $activePlugins = apply_filters('active_plugins', get_option('active_plugins'));
        if (in_array('gmb-reviews-premium/gmb-reviews-premium.php', $activePlugins)) {
            $message = sprintf(
                __('Disable the premium %s plugin if you want to use the free version.', 'gmb-reviews'),
                Plugin::config('plugin.name'),
            );
            $this->adminNotice('<strong>Error:</strong> ' . $message, '', 2, true);
        }

        $upgrade_url = (get_user_locale() === 'nl_NL') ? Plugin::config('tussendoor.upgrade_url') : Plugin::config('tussendoor.upgrade_url_en');
        $message = sprintf(
            __('%s: The support for the %s plugin will end on %s. Upgrade to the Premium version %s if you would like to keep using the features of this plugin.', 'gmb-reviews'),
            '<strong>' . __('NOTICE', 'gmb-reviews') . '</strong> ',
            '<strong>' . Plugin::config('plugin.name') . '</strong> ',
            Plugin::config('plugin.eol_date'),
            '<a href="' . $upgrade_url . '" target="_blank" style="text-decoration: underline;">' . __('here', 'gmb-reviews') . '</a>',
        );
        $this->adminNotice($message, '', 3, true);
    }
}