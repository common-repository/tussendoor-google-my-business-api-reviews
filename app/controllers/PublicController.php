<?php namespace Tussendoor\GmbReviews\Controllers;

use Tussendoor\GmbReviews\Plugin;
use Tussendoor\GmbReviews\Helpers\Cookie;
use Tussendoor\GmbReviews\Helpers\Request;

class PublicController extends Controller
{
    public function register()
    {
        $this->addActions();
    }

    public function addActions()
    {
        add_action('wp_enqueue_scripts', [$this, 'loadPublicAssets']);
        add_action('wp_footer', [$this, 'showCodeModal']);
    }
    
    public function loadPublicAssets()
    {
        if (!$this->continue()) return;

        wp_enqueue_script(
            Plugin::config('plugin.tag').'_public_script',
            Plugin::config('plugin.assets').'public/js/public.js',
            ['jquery'],
            Plugin::config('plugin.version')
        );

        wp_localize_script(
            Plugin::config('plugin.tag').'_public_script', 
            'PublicController', 
            array('admin_page' => Plugin::config('plugin.dashboard_url')),
        );

        wp_enqueue_style(
            Plugin::config('plugin.tag').'_public_style',
            Plugin::config('plugin.assets').'public/css/public.css',
            null,
            Plugin::config('plugin.version')
        );
    }
    
    /**
     * Show a modal with information for the user
     * The information depends on the response from Google
     *
     * @return string html
     */
    public function showCodeModal()
    {
        if (!$this->continue()) return;

        $request = Request::fromGlobal();

        if ($request->getString('state') != Cookie::getString('state')) {
            return $this->printModal('public', 'public.modal.state-failed');
        }

        if ($request->has('error')) {
            return $this->printModal('public', 'public.modal.access-denied');
        }

        return $this->printModal('public', 'public.modal.success');
    }

    
    /**
     * Simple check to know if code should be executed
     * Only execute when there is a code set, state is set and when the scope is correct
     *
     * @return bool
     */
    private function continue() : bool
    {
        $request = Request::fromGlobal();

        return ((
            // If access is grantend
            $request->has('code') 
            && $request->has('state') 
            && Cookie::has('state')
            && ( $request->has('scope') && $request->getString('scope') === Plugin::config('google.gmb_scope') )
        ) || (
            // If access is denied and we have an error
            $request->has('error')
            && $request->has('state') 
        ));
    }
}