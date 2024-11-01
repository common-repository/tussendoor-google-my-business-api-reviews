<?php use Tussendoor\GmbReviews\Plugin; ?>

            <div class="card-footer px-lg-2 px-1 px-md-3 pt-5 bg-transparent border-0">

                <div class="card-footer-content d-flex">
                    <div class="logo">
                        <?php echo file_get_contents(Plugin::config('plugin.path').'/assets/admin/img/logo-tsd-liggend.svg'); ?>
                    </div>
                    <div class="ms-auto d-flex align-items-center">
                        <a class="text-muted fw-light text-decoration-none" href="<?php echo Plugin::config('tussendoor.website'); ?>" target="_blank"><?php echo Plugin::config('tussendoor.website_short'); ?></a>
                        <p class="text-muted mx-3 my-0">&#8226;</p>
                        <a class="btn btn-md btn-primary rounded-pill fw-light custom-button-width" href="<?php echo Plugin::config('tussendoor.contact'); ?>" target="_blank"><?php esc_html_e('Help', 'gmb-reviews'); ?></a>
                    </div>
                </div>

            </div>

        </div> <?php /* header: custom-main-card card shadow-none custom-min-view-height-90 m-0 */ ?>
    </div> <?php /* header: bg-light p-5 */ ?>
</div> <?php /* header: plugin.tag_dashboard */ ?>