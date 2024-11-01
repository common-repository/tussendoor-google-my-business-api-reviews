<?php namespace Tussendoor\GmbReviews\Controllers;

use Tussendoor\GmbReviews\Plugin;
use Tussendoor\GmbReviews\Helpers\Widget;

class CreatorController extends ReviewController
{   

    public $reviews;

    const DEFAULT_WIDGET_NAME = 'Default Review Widget';

    public function __construct()
    {
        parent::__construct();
        $this->reviews = $this->getCached();
    }

    public function register()
    {
        $this->addActions();
        $this->addShortcode();
    }

    public function addActions()
    {
        add_action('widgets_init', [$this, 'widgetDefault']);
        add_action('wp_enqueue_scripts', [$this, 'loadPublicAssets']);
        add_action('admin_enqueue_scripts', [$this, 'loadAdminAssets']);
    }

    public function addShortcode()
    {
        add_shortcode(Plugin::config('plugin.tag').'_default', [$this, 'shortcodeWidget']);
    }

    public function loadAdminAssets()
    {
        if (! $this->isAdminPage()) return;

        wp_enqueue_style(
            Plugin::config('plugin.tag').'_defaults_style',
            Plugin::config('plugin.assets').'public/css/defaults.css',
            null,
            Plugin::config('plugin.version')
        );
    }

    public function loadPublicAssets()
    {
        wp_enqueue_style(
            Plugin::config('plugin.tag').'_defaults_style',
            Plugin::config('plugin.assets').'public/css/defaults.css',
            null,
            Plugin::config('plugin.version')
        );
    }
        
    /**
     * Used to insert the stars SVG
     *
     * @return string
     */
    public function stars() : string
    {
        ob_start(); ?>
        <div class="rating_wrapper">
            <div class="average_rating">
                <div class="rating rating__filled" style="width:<?php esc_attr_e($this->reviews->getRatingPercentage()); ?>%">
                    <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 99 14.25">
                        <path d="M91.5 11.45l4.64 2.8-1.23-5.27L99 5.43l-5.39-.46L91.5 0l-2.11 4.97-5.39.46 4.09 3.55-1.23 5.27 4.64-2.8z"></path>
                        <path data-name="Path" d="M7.5 11.45l4.63 2.8-1.23-5.27L15 5.43l-5.39-.46L7.5 0 5.39 4.97 0 5.43l4.09 3.55-1.22 5.27 4.63-2.8zM28.5 11.45l4.63 2.8-1.22-5.27L36 5.43l-5.39-.46L28.5 0l-2.11 4.97-5.39.46 4.09 3.55-1.23 5.27 4.64-2.8zM49.5 11.45l4.63 2.8-1.22-5.27L57 5.43l-5.39-.46L49.5 0l-2.11 4.97-5.39.46 4.09 3.55-1.22 5.27 4.63-2.8zM70.5 11.45l4.64 2.8-1.23-5.27L78 5.43l-5.39-.46L70.5 0l-2.11 4.97-5.39.46 4.09 3.55-1.23 5.27 4.64-2.8z"></path>
                    </svg>
                </div>
                <div class="rating rating__empty">
                    <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 99 14.25">
                        <path d="M99 5.43L93.61 5 91.5 0l-2.11 5-5.39.43L88.09 9l-1.23 5.27 4.64-2.8 4.64 2.8L94.91 9zm-7.5 4.62l-2.82 1.7.75-3.21-2.49-2.16 3.28-.28 1.28-3 1.28 3 3.29.28-2.49 2.17.75 3.21z"></path>
                        <path data-name="Shape" d="M78 5.43L72.61 5 70.5 0l-2.11 5-5.39.43L67.09 9l-1.23 5.27 4.64-2.8 4.64 2.8L73.91 9zm-7.5 4.62l-2.82 1.7.75-3.21-2.49-2.16 3.28-.28 1.28-3 1.28 3 3.29.28-2.49 2.17.75 3.21zM15 5.43L9.61 5 7.5 0 5.39 5 0 5.43 4.09 9l-1.23 5.25 4.64-2.8 4.64 2.8L10.91 9zm-7.5 4.62l-2.82 1.7.75-3.21-2.49-2.16 3.28-.28 1.28-3 1.28 3 3.29.28-2.49 2.17.75 3.21zM57 5.43L51.61 5 49.5 0l-2.11 5-5.39.43L46.09 9l-1.23 5.27 4.64-2.8 4.64 2.8L52.91 9zm-7.5 4.62l-2.82 1.7.75-3.21-2.49-2.16 3.28-.28 1.28-3 1.28 3 3.29.28-2.49 2.17.75 3.21zM36 5.43L30.61 5 28.5 0l-2.11 5-5.39.43L25.09 9l-1.23 5.27 4.64-2.8 4.64 2.8L31.91 9zm-7.5 4.62l-2.82 1.7.75-3.21-2.49-2.16 3.28-.28 1.28-3 1.28 3 3.29.28-2.49 2.17.75 3.21z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <?php return ob_get_clean();
    }

    /**
     * Used to register the default widget
     *
     * @return void
     */
    public function widgetDefault() : void
    {
        $id   = Plugin::config('plugin.tag') . '_default';
        $name = self::DEFAULT_WIDGET_NAME;

        $content = $this->defaultWidgetContent();

        $widget = new Widget($id, $name);
        $widget->setContent($content);

        register_widget($widget);
    }

    private function defaultWidgetContent() : string
    {
        ob_start(); ?>

        <div id="<?php esc_attr_e(Plugin::config('plugin.tag')); ?>_content" class="widget widget_block default">
            <div class="star_wrapper">
                <?php echo wp_kses_post($this->stars()); ?>
                <div class="average_wrapper">
                    <span><?php echo esc_attr($this->reviews->getRatingValue()) . ' / ' . esc_attr($this->reviews->getBestRating()); ?></span>
                </div>
            </div>
        </div>

        <?php return ob_get_clean();
    }

    public function shortcodeWidget() : string
    {
        ob_start();
        echo wp_kses_post($this->defaultWidgetContent());
        return ob_get_clean();
    }
}