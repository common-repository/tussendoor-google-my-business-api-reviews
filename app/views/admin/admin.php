<?php
use Tussendoor\GmbReviews\Plugin;
use Tussendoor\GmbReviews\Controllers\DashboardController as Dashboard;
    
$dashboard = new Dashboard(); ?>

<?php $args = [
    'title'         => esc_html(Plugin::config('plugin.name')),
    'description'   => esc_html__('Add a star rating to your website in the organic search results in Google and show your reviews with a widget or shortcode!', 'gmb-reviews'),
    'badge'         => [
        'text'  => Plugin::config('plugin.version'),
        'class' => 'success',
    ]
]; ?>

<?php $dashboard->template('partials/admin.header', compact('args')); ?>

<form id="<?php esc_attr_e(Plugin::config('plugin.tag')); ?>_admin" class="card-body p-0 mt-3" action="<?php esc_attr_e(admin_url('admin-ajax.php')); ?>" method="post">
    <input type="hidden" name="action" value="<?php esc_attr_e(Plugin::config('plugin.tag')); ?>_save">
    <section class="card-inner-body pt-lg-2 d-lg-flex flex-nowrap gap-4">
        <div class="tab-navigation pr-lg-3">
            <ul class="nav nav-tabs border-0 flex-lg-column" role="tablist">
                <li class="nav-item p-lg-2 mb-md-0 mb-sm-0 mb-0 border-light" role="presentation">
                    <a class="nav-link tab-link border-0 m-0 fw-light <?php echo (bool) $dashboard->isActive('dashboard') ? 'active' : ''; ?>" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard" href="#dashboard" role="tab" aria-controls="dashboard" aria-selected="<?php echo (bool) $dashboard->isActive('dashboard') ? 'true' : 'false'; ?>">
                        <?php esc_html_e('Dashboard', 'gmb-reviews'); ?>
                    </a>
                </li>
                <?php if ((bool) $dashboard->showSettingsTab()): ?>
                    <li class="nav-item p-lg-2 mb-md-0 mb-sm-0 mb-0 border-light" role="presentation">
                        <a class="nav-link tab-link border-0 m-0 fw-light <?php echo (bool) $dashboard->isActive('settings') ? 'active' : ''; ?> <?php echo ! (bool) Plugin::hasSetting('accounts') ? 'lazyload' : ''; ?>" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" href="#settings" role="tab" aria-controls="settings" aria-selected="<?php echo (bool) $dashboard->isActive('settings') ? 'true' : 'false'; ?>">
                            <?php esc_html_e('Settings', 'gmb-reviews'); ?>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if ((bool) $dashboard->showCreatorTab()): ?>
                    <li class="nav-item p-lg-2 mb-md-0 mb-sm-0 mb-0 border-light" role="presentation">
                        <a class="nav-link tab-link border-0 m-0 fw-light <?php echo (bool) $dashboard->isActive('creator') ? 'active' : ''; ?>" id="creator-tab" data-bs-toggle="tab" data-bs-target="#creator" href="#creator" role="tab" aria-controls="creator" aria-selected="<?php echo (bool) $dashboard->isActive('creator') ? 'true' : 'false'; ?>">
                            <?php esc_html_e('Widget', 'gmb-reviews'); ?>
                        </a>
                    </li>
                <?php endif; ?>
                <li class="nav-item p-lg-2 mb-md-0 mb-sm-0 mb-0 border-light" role="presentation">
                    <a class="nav-link tab-link border-0 m-0 fw-light <?php echo (bool) $dashboard->isActive('info') ? 'active' : ''; ?>" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" href="#info" role="tab" aria-controls="info" aria-selected="<?php echo (bool) $dashboard->isActive('info') ? 'true' : 'false'; ?>">
                        <?php esc_html_e('Info', 'gmb-reviews'); ?>
                    </a>
                </li>
            </ul>
        </div>
        <div class="tab-content pr-lg-3 py-lg-0 py-4">
            <div class="tab-pane fade <?php echo (bool) $dashboard->isActive('dashboard') ? 'active show' : ''; ?>" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
                <?php require Plugin::config('plugin.viewpath').'admin/partials/admin.dashboard.php'; ?>
            </div>
            <?php if ((bool) $dashboard->showSettingsTab()): ?>
                <div class="tab-pane fade <?php echo (bool) $dashboard->isActive('settings') ? 'active show' : ''; ?>" id="settings" role="tabpanel" aria-labelledby="settings-tab">
                    <?php require Plugin::config('plugin.viewpath').'admin/partials/admin.settings.php'; ?>
                </div>
            <?php endif; ?>
            <?php if ((bool) $dashboard->showCreatorTab()): ?>
                <div class="tab-pane fade <?php echo (bool) $dashboard->isActive('creator') ? 'active show' : ''; ?>" id="creator" role="tabpanel" aria-labelledby="creator-tab">
                    <?php require Plugin::config('plugin.viewpath').'admin/partials/admin.creator.php'; ?>
                </div>
            <?php endif; ?>
            <div class="tab-pane fade <?php echo (bool) $dashboard->isActive('info') ? 'active show' : ''; ?>" id="info" role="tabpanel" aria-labelledby="info-tab">
                <?php require Plugin::config('plugin.viewpath').'admin/partials/admin.info.php'; ?>
            </div>
        </div>
    </section>
</form>

<?php $dashboard->template('partials/admin.footer'); ?>