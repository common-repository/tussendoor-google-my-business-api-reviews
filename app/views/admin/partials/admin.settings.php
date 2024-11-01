<?php 
use Tussendoor\GmbReviews\Plugin;
use Tussendoor\GmbReviews\Models\Accounts;
use Tussendoor\GmbReviews\Models\Locations;

$settings = Plugin::config('settings');

$accountModel = new Accounts();
$accounts = $accountModel->getFromSettings();

$locationModel = new Locations();
$locations = ($accountModel->hasSelectedAccount() ? $locationModel->getLocationsFromAccount($accountModel->getSelectedAccountEndpoint()) : []); ?>

<div id="<?php esc_attr_e(Plugin::config('plugin.tag')); ?>_settings" class="app_settings">

    <div class="tab-title mb-2">
        <h3><?php esc_html_e('Settings', 'gmb-reviews'); ?></h3>
    </div>
        
    <div class="dashboard_part">
        <p class="m-0">
            <?php esc_html_e('Choose the account that manages the location of which you want to retrieve the reviews.', 'gmb-reviews'); ?>
        </p>
        
        <p>
            <?php printf(
                __('If you have added location groups in %sGoogle My Business%s you will see these groups between your accounts.', 'gmb-reviews'),
                '<a href="' . Plugin::config('google.locations_url') . '" target="_blank" rel="noopener">',
                '</a>'
            ); ?>
            <?php esc_html_e('The locations you\'ve added in a group will only be available under this group.', 'gmb-reviews'); ?>
        </p>
        
        <div class="input-group mb-3">
            <div class="input-group-prepend w-25">
                <label class="input-group-text w-100 fw-light border-0 custom-bg-light" for="account"><?php esc_html_e('Account', 'gmb-reviews'); ?></label>
            </div>
            <select class="select-account custom-select border-0 w-75" id="account" name="selected_account" placeholder="- <?php esc_html_e('Choose account', 'gmb-reviews'); ?> -">
                <?php if (empty($accounts)): ?>
                    <option value="" selected="true" disabled="disabled">- <?php esc_html_e('Sync account data first', 'gmb-reviews'); ?> -</option>
                <?php else: ?>
                    <option value="" selected="true" disabled="disabled">- <?php esc_html_e('Choose account', 'gmb-reviews'); ?> -</option>
                    
                    <?php foreach ($accounts as $account): ?>
                        <option <?php echo (isset($account['selected']) && (bool) $account['selected']) ? 'selected="true"' : ''; ?> value="<?php esc_attr_e($account['endpoint']); ?>"><?php esc_attr_e($account['name']); ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="input-group mb-3">
            <div class="input-group-prepend w-25">
                <label class="input-group-text w-100 fw-light border-0 custom-bg-light" for="location"><?php esc_html_e('Location', 'gmb-reviews'); ?></label>
            </div>
            <select class="select-location custom-select border-0 w-75" id="location" name="selected_location" placeholder="- <?php esc_html_e('Choose account first', 'gmb-reviews'); ?> -">
                <?php if (! (bool) $accountModel->hasSelectedAccount()): ?>
                    <option value="" selected="true" disabled="disabled">- <?php esc_html_e('Choose account first', 'gmb-reviews'); ?> -</option>
                <?php else: ?>
                    <option value="" selected="true" disabled="disabled">- <?php esc_html_e('Choose location', 'gmb-reviews'); ?> -</option>
                    
                    <?php foreach ($locations as $location): ?>
                        <option <?php echo (isset($location['selected']) && (bool) $location['selected']) ? 'selected="true"' : ''; ?> value="<?php esc_attr_e($location['endpoint']); ?>"><?php esc_attr_e($location['name']); ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        
        
        <div class="button_wrapper m20t">
            <button type="text" class="btn btn-primary btn-sm js--sync-accounts">
                <i class="me-1 fa-solid fa-arrows-rotate"></i>
                <?php if (empty($accounts)): ?>
                    <?php esc_html_e('Sync', 'gmb-reviews'); ?>
                <?php else: ?>
                    <?php esc_html_e('Force new sync', 'gmb-reviews'); ?>
                <?php endif; ?>
            </button>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="me-1 fa-solid fa-floppy-disk"></i>
                <?php esc_html_e('Save', 'gmb-reviews'); ?>
            </button>
        </div>
    </div>

</div>