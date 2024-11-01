<div class="modal fade" id="revoke" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-none border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title"><?php esc_html_e('Warning! Revoking acces.', 'gmb-reviews'); ?></h5>
                <i class="fa-solid fa-xmark p-2" role="button" data-bs-dismiss="modal" aria-label="Close"></i>
            </div>
            <div class="modal-body border-0">
                <p class="m-0"><strong><?php esc_html_e('Are you sure you want to revoke the access to the API?', 'gmb-reviews'); ?></strong></p>
                <p><?php esc_html_e('After revoking the access you wont be able to use the functionalities of this plugin without regranting access. By revoking the access you will also delete your Client ID and Client Secret.', 'gmb-reviews'); ?></p>
            </div>
            <div class="modal-footer border-0 mt-3 justify-content-end">
                <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal"><?php esc_html_e('Close and keep access', 'gmb-reviews'); ?></button>
                <button type="button" class="btn btn-sm btn-danger js--revoke-access"><i class="me-1 fa-solid fa-xmark"></i> <?php esc_html_e('Revoke access', 'gmb-reviews'); ?></button>
            </div>
        </div>
    </div>
</div>