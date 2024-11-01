<?php use Tussendoor\GmbReviews\Plugin; ?>

<div class="modal public-modal" id="public-modal" aria-hidden="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-none border-0">
            <div class="modal-header border-0">
                <h4 class="modal-title"><?php esc_html_e('Permission denied!', 'gmb-reviews'); ?></h4>
            </div>
            <div class="modal-body border-0">
                <strong><?php esc_html_e('Something went wrong when requesting access to the Google My Business API', 'gmb-reviews'); ?></strong>
                <p class="blue">
                    <?php esc_html_e('You will be redirected to the admin page in', 'gmb-reviews'); ?> 
                    <span class="counter">5</span>
                </p>
                <p class="small blue">
                    <?php printf(
                        __('Contact %sTussendoor B.V.%s if this error persists.', 'gmb-reviews'),
                        '<a href="' . Plugin::config('tussendoor.contact') . '" target="_blank">',
                        '</a>'
                    ); ?>    
                </p>
            </div>
        </div>
    </div>
</div>