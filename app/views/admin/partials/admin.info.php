<?php
use Tussendoor\GmbReviews\Plugin;?>


<div id="tab-info">
    <div class="tab-title mb-3">
        <h3><?php esc_html_e('Installation', 'gmb-reviews'); ?></h3>
    </div>
    <div id="info_table">
        <div class="input-group mb-3 align-items-center">
            <div class="input-group-prepend w-25">
                <label class="input-group-text w-100 fw-light border-0 custom-bg-light" for="version"><?php esc_html_e('Version', 'gmb-reviews'); ?></label>
            </div>
            <p id="version" class="mb-0 ms-2"><?php esc_html_e(Plugin::config('plugin.version')); ?></p>
        </div>
        <div class="input-group mb-3 align-items-center">
            <div class="input-group-prepend w-25">
                <label class="input-group-text w-100 fw-light border-0 custom-bg-light align-items-center" for="php">
                    <?php esc_html_e('PHP', 'gmb-reviews'); ?>
                    <span class="has-tip dashicons dashicons-editor-help ms-2 text-primary" data-toggle="tooltip" data-placement="top" title="<?php esc_html_e('Minimum PHP version:', 'gmb-reviews'); ?> <?php esc_attr_e(Plugin::config('plugin.php_minimum')); ?>"></span>
                </label>
            </div>
            <p id="php" class="mb-0 ms-2 <?php echo (int) phpversion() < (float) Plugin::config('plugin.php_minimum') ? 'td-notice version_notice' : 'td-notice valid'; ?>"><?php esc_html_e(phpversion()); ?></p>
        </div>        
        <div class="input-group mb-3 align-items-center">
            <div class="input-group-prepend w-25">
                <label class="input-group-text w-100 fw-light border-0 custom-bg-light" for="cache"><?php esc_html_e('Cache', 'gmb-reviews'); ?></label>
            </div>
            <p id="cache" class="mb-0 ms-2">
                <?php $url = add_query_arg('empty_cache', null, admin_url('admin-ajax.php')); ?>
                <td>
                    <a href="<?php esc_attr_e($url); ?>" class="js--empty_cache">
                        <i class="me-1 fa-regular fa-trash-can"></i>
                        <span data-always="<?php esc_html_e('Clear cache', 'gmb-reviews'); ?>" data-loading="<?php esc_html_e('Clearing cache', 'gmb-reviews'); ?>" data-done="<?php esc_html_e('Cache cleared!', 'gmb-reviews'); ?>" data-fail="<?php esc_html_e('Clearing cache failed :(', 'gmb-reviews'); ?>">
                            <?php esc_html_e('Clear cache', 'gmb-reviews'); ?>
                        </span>
                    </a>
                </td>
            </p>
        </div>

    </div>
</div>