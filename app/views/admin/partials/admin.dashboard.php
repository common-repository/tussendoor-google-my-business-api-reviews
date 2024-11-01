<div id="tab-dashboard">
    <div class="tab-title mb-3">
        <h3><?php esc_html_e('Google My Business information', 'gmb-reviews'); ?></h3>
    </div>

    <div class="information_wrapper">
        <p><?php esc_html_e('The following information is retrieved from your', 'gmb-reviews'); ?> <a href="<?php esc_attr_e($dashboard->credentialsUrl); ?>" target="_blank" rel="noopener"><?php esc_attr_e($dashboard->credentialsName); ?>.</a></p>
        <div class="input-group mb-3">
            <div class="input-group-prepend w-25">
                <label class="input-group-text w-100 fw-light border-0 custom-bg-light" for="client_id"><?php esc_html_e('Client ID', 'gmb-reviews'); ?></label>
            </div>
            <input class="border-0 w-75" type="text" name="client_id" value="<?php esc_attr_e($dashboard->clientID); ?>" <?php echo (bool) $dashboard->pluginHasAccess() ? 'disabled="disabled"' : ''; ?>>
        </div>
        
        <div class="input-group mb-3">
            <div class="input-group-prepend w-25">
                <label class="input-group-text w-100 fw-light border-0 custom-bg-light" for="client_secret"><?php esc_html_e('Client Secret', 'gmb-reviews'); ?></label>
            </div>
            <input class="border-0 w-75" type="password" name="client_secret" value="<?php echo esc_attr($dashboard->clientSecret) ? esc_attr($dashboard->clientSecret) : ''; ?>" <?php echo (bool) $dashboard->pluginHasAccess() ? 'disabled="disabled"' : ''; ?>>
        </div>
        
        <div id="save_client" class="input_wrapper <?php echo (bool) $dashboard->showSaveButton() ? '' : 'hidden'; ?>">
            <button type="submit" class="btn btn-sm btn-primary m20t">
                <i class="me-1 fa-solid fa-floppy-disk"></i>
                <?php esc_html_e('Save', 'gmb-reviews'); ?>
            </button>
        </div>
    </div>

    <?php if ((bool) $dashboard->showAccessButtons()): ?>
        <div id="grant_access" class="input_wrapper mt-5 <?php echo ((bool) $dashboard->hideGrantAccessButton()) ? 'hidden' : ''; ?>">
    
            <div class="alert alert-warning">
                <?php if ((bool) $dashboard->authExpired): ?>
                    <p class="text-danger mb-1"><strong><?php esc_html_e('The access has been expired!', 'gmb-reviews'); ?></strong></p>
                <?php endif; ?>
                <p class="m-0"><?php esc_html_e('The plugin needs access to your Google My Business API, click on the button below to grant this access.', 'gmb-reviews'); ?></p>
                <p class="m-0"><?php esc_html_e('Be sure to add the current website URL to the Authorized redirect URIs of your', 'gmb-reviews'); ?> <a href="<?php esc_attr_e($dashboard->credentialsUrl); ?>"><?php esc_attr_e($dashboard->credentialsName); ?>.</a></p>
            </div>
    
            <a class="btn btn-sm btn-primary m-0" href="<?php esc_attr_e($dashboard->accessCodeUri); ?>">
                <i class="me-1 fa-solid fa-check"></i>
                <?php esc_html_e('Grant access', 'gmb-reviews'); ?>
            </a>
        </div>
        
        <div id="revoke_access" class="input_wrapper <?php echo ((bool) $dashboard->hideRevokeAccessButton()) ? 'hidden' : ''; ?>">
            <p class="m20t m-0"><?php esc_html_e('You have granted access to the Google My Business API with these credentials, click on the button below to revoke this access.', 'gmb-reviews'); ?></p>
            <p class="m10b m-0"><?php esc_html_e('After revoking the access you will be able to enter new credentials.', 'gmb-reviews'); ?></p>
            <button type="text" class="btn btn-sm btn-warning open-modal d-flex align-items-center mt-2" data-bs-toggle="modal" data-bs-target="#revoke">
                <i class="me-2 fa-solid fa-xmark"></i>
                <span class="text"><?php esc_html_e('Revoke access', 'gmb-reviews'); ?></span>
            </button>
        </div>
    <?php endif; ?>

</div>

<?php $dashboard->printModal('admin', 'admin.modal.revoke'); ?>