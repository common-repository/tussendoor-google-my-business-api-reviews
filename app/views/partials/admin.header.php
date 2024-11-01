<?php
use Tussendoor\GmbReviews\Plugin; ?>

<div id="<?php echo Plugin::config('plugin.tag'); ?>_dashboard"> <?php /* closed in footer */ ?>
    <div class="bg-light p-5 gmb"> <?php /* closed in footer */ ?>
        <div class="custom-main-card card shadow-none border-0 custom-min-view-height-90 m-0 p-5"> <?php /* closed in footer */ ?>
            
            <div class="card-header border-bottom-0 p-0 d-flex align-items-center flex-wrap">
                <div class="header-title">
                    <?php if (!empty($args['badge'])): ?>
                        <div class="d-flex align-items-center flex-wrap">
                            <h1 class="card-title d-block float-none fw-bold">
                                <?php echo esc_html($args['title']); ?>
                            </h1>
                            <div class="badge bg-<?php echo esc_attr($args['badge']['class']); ?> rounded-pill fw-light ms-2">
                                <?php echo esc_html($args['badge']['text']); ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <h1 class="card-title d-block float-none fw-bold">
                            <?php echo esc_html($args['title']); ?>
                        </h1>
                    <?php endif; ?>
                    <h6 class="card-subtitle mb-2 mt-1 d-block text-primary fw-lighter"><?php echo esc_html($args['description']); ?></h6>
                </div>
                
                <?php if (!empty($args['back']) && $args['back'] === true): ?>
                    <button
                    class="btn btn-sm btn-primary ms-auto"
                    onclick="window.history.go(-1); return false;">
                        <i class="fa fa-chevron-left"></i>
                        <?php esc_html_e('Back', 'gmb-reviews'); ?>
                    </button>
                <?php elseif (!empty($args['backUrl'])): ?>
                    <a href="<?php echo esc_url($args['backUrl']); ?>" class="btn btn-sm btn-primary ms-auto">
                        <i class="fa fa-chevron-left"></i>
                        <?php esc_html_e('Back', 'gmb-reviews'); ?>
                    </a>
                <?php endif; ?>

                <hr class="wp-header-end w-100">
            </div>