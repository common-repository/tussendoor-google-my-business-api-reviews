<?php 
use Tussendoor\GmbReviews\Helpers\Request;
$request = Request::fromGlobal(); ?>

<div class="modal public-modal" id="public-modal" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?php esc_html_e('Something went wrong!', 'gmb-reviews'); ?></h4>
                <h5><?php esc_html_e('Error:', 'gmb-reviews'); ?> "<?php echo $request->getString('error'); ?>"</h5>
            </div>
            <div class="modal-body">
                <strong><?php esc_html_e('Something went wrong when granting access to the Google My Business API.', 'gmb-reviews'); ?></strong>
                <p><?php esc_html_e('Please try again.', 'gmb-reviews'); ?></p>
                <p class="blue m10t">
                    <?php esc_html_e('You will be redirected to the admin page in', 'gmb-reviews'); ?> 
                    <span class="counter">5</span>
                </p>
            </div>
        </div>
    </div>
</div>