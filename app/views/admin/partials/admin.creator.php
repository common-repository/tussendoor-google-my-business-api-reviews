<?php use Tussendoor\GmbReviews\Plugin; 
use Tussendoor\GmbReviews\Controllers\CreatorController; ?>

<div id="creator_defaults">

    <?php $message = sprintf(
        __('Find the default widget in the widget settings by searching: %s', 'gmb-reviews'),
        esc_attr(CreatorController::DEFAULT_WIDGET_NAME)
    ); ?>
    
    <div class="input-group mb-5 d-block">
        <p id="description" class="mb-1 p-2 custom-bg-light rounded d-flex align-items-center">
            <?php esc_html_e('Default Review Widget', 'gmb-reviews'); ?>
            <span class="has-tip dashicons dashicons-editor-help text-primary ms-2" data-toggle="tooltip" data-placement="top" title="<?php echo $message; ?>"></span>
        </p>
        <p class="w-100 fw-light border-0 text-wrap text-left m-0"><?php echo do_shortcode('['.esc_attr(Plugin::config('plugin.tag')).'_default]'); ?></p>
    </div>
    
    <div class="input-group d-block">
        <p id="description" class="mb-1 p-2 custom-bg-light rounded d-flex align-items-center">
            <?php esc_html_e('Shortcode', 'gmb-reviews'); ?>
            <span class="has-tip dashicons dashicons-editor-help text-primary ms-2" data-toggle="tooltip" data-placement="top" title="<?php esc_html_e('Shortcodes can be used everywhere in your theme or page builder.', 'gmb-reviews'); ?>"></span>
        </p>
        <p class="w-100 fw-light border-0 text-wrap text-left mb-2">[<?php esc_attr_e(Plugin::config('plugin.tag')); ?>_default]</p>
        <p class="w-100 fw-light border-0 text-wrap text-left m-0"><?php echo do_shortcode('['.esc_attr(Plugin::config('plugin.tag')).'_default]'); ?></p>
    </div>
</div>