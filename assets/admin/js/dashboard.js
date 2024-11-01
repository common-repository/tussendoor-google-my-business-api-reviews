jQuery(document).ready(function($) {

    $('[data-toggle="tooltip"]').tooltip();

    $(document).on('click', '.open-modal', function(e) {
        e.preventDefault();
    });
    
    $(document).on('click', '.nav-item a.tab-link', function() {

        let url = window.location.href;
        let baseUrl = url.split('?')[0]

        let target = $(this).attr('id');
        let searchParams = new URLSearchParams(window.location.search)

        searchParams.set('tab', target);

        let newUrl = baseUrl + '?' + searchParams.toString();
        history.pushState({}, null, newUrl);

    });

    $(document).on('click', '.notice-dismiss.custom', function() {
        $(this).parent().remove();
    });
    

    $(document).on('submit', '#gmb_r_admin', function(e) {
        e.preventDefault();
        var form = this;
        var formdata = new FormData(form);

        $.ajax({
            url: ajaxurl,
            data: formdata,
            cache: false,
            processData: false,
            contentType: false,
            type: 'POST',
            beforeSend: function() {
                $('button[type="submit"]', form).prop('disabled', true);
                $('button[type="submit"] i', form).removeClass('fa-save').addClass('fa-spinner fa-spin');
            },
            success: function(response) {
                location.replace(location.href);
            },
        }).done(function(data) {
            $('button[type="submit"]', form).removeClass('btn-primary').addClass('btn-success');
        }).fail(function(jqXHR, textStatus) {
            $('button[type="submit"]', form).removeClass('btn-primary').addClass('btn-danger');
        }).always(function() {
            $('button[type="submit"] i', form).removeClass('fa-spinner fa-spin').addClass('fa-save');

            setTimeout(function() {
                $('button[type="submit"]', form).prop('disabled', false);
                $('button[type="submit"]', form).removeClass('btn-success btn-danger').addClass('btn-primary');
            }, 2000);
        });
    });

    $(document).on('click', '.js--empty_cache', function(e) {
        e.preventDefault();
        var formdata = new FormData();

        var button = $(this);
        var action = $('#gmb_r_admin input[name="action"]').val();

        formdata.append('action', action);
        formdata.append('empty_cache', true);

        $.ajax({
            url: ajaxurl,
            data: formdata,
            cache: false,
            processData: false,
            contentType: false,
            type: 'POST',
            beforeSend: function() {
                button.prop('disabled', true).addClass('text-muted');
                $('i', button).removeClass('fa-trash-alt').addClass('fa-spinner fa-spin');
                $('span', button).text($('span', button).data('loading'));
            },
        }).done(function(data) {
            button.removeClass('text-muted').addClass('text-success');
            $('span', button).text($('span', button).data('done'));
        }).fail(function(jqXHR, textStatus) {
            button.removeClass('text-muted').addClass('text-danger');
            $('span', button).text($('span', button).data('fail'));
        }).always(function() {
            $('i', button).removeClass('fa-spinner fa-spin').addClass('fa-trash-alt');

            setTimeout(function() {
                button.prop('disabled', false);
                button.removeClass('text-muted text-success text-danger');
                $('span', button).text($('span', button).data('always'));
            }, 2000);
        });
    });

    $(document).on('click', '.js--register', function(e) {
        e.preventDefault();
        var formdata = new FormData();

        var button = $(this);
        var action = $('#gmb_r_admin input[name="action"]').val();
        var key    = $('#gmb_r_admin #license_code').val();

        formdata.append('action', action);
        formdata.append('key', key);
        formdata.append('register', true);

        $.ajax({
            url: ajaxurl,
            data: formdata,
            cache: false,
            processData: false,
            contentType: false,
            type: 'POST',
            beforeSend: function() {
                button.prop('disabled', true);
                $('i', button).removeClass('fa-save').addClass('fa-spinner fa-spin');
            },
        }).done(function(data) {
            button.removeClass('btn-primary').addClass('btn-success');
            setTimeout(function() {
                $('#register').modal('toggle');
            }, 1000);
        }).fail(function(jqXHR, textStatus) {
            button.removeClass('btn-primary').addClass('btn-warning');
        }).always(function() {
            $('i', button).removeClass('fa-spinner fa-spin').addClass('fa-save');

            setTimeout(function() {
                button.prop('disabled', false);
                button.removeClass('btn-success btn-warning').addClass('btn-primary');
            }, 2000);
        });
    });

    $(document).on('click', '.js--revoke-access', function(e) {
        e.preventDefault();
        var button = $(this);

        $.ajax({
            url: ajaxurl,
            data: {
                action: 'gmb_r_revoke_access',
            },
            type: 'POST',
            beforeSend: function() {
                button.prop('disabled', true);
                $('i', button).removeClass('fa-times').addClass('fa-spinner fa-spin');
            },
        }).done(function(data) {
            button.removeClass('btn-warning').addClass('btn-success');

            var wordpressNotice = data.data;
            $('<div class="notice notice-success is-dismissible is-dismissible mx-0 py-0 rounded"> <p>' + wordpressNotice + '</p> </div>').insertAfter('#gmb_r_admin .p-header-end');

            $('#gmb_r_admin #grant_access').toggleClass('hidden');
            $('#gmb_r_admin #revoke_access').toggleClass('hidden');

            setTimeout(function() {
                $('#revoke').modal('toggle');
                button.removeClass('btn-success').addClass('btn-warning');
            }, 1000);

            window.location.replace(Dashboard.admin_page);
        }).fail(function(jqXHR, textStatus, errorThrown) {
            button.removeClass('btn-warning').addClass('btn-danger');

            var wordpressNotice = jqXHR.responseJSON.data;
            $('<div class="notice notice-error is-dismissible is-dismissible mx-0 py-0 rounded"> <p>' + wordpressNotice + '</p> </div>').insertAfter('#gmb_r_admin .p-header-end');

            $('#gmb_r_admin #grant_access').toggleClass('hidden');
            $('#gmb_r_admin #revoke_access').toggleClass('hidden');

            setTimeout(function() {
                $('#revoke').modal('toggle');
            }, 1000);

        }).always(function() {
            $('i', button).removeClass('fa-spinner fa-spin').addClass('fa-times');

            setTimeout(function() {
                button.prop('disabled', false);
                button.removeClass('btn-success btn-danger').addClass('btn-warning');
            }, 2000);
        });
    });

    $(document).on('click', '.js--sync-accounts, #settings-tab.lazyload', function(e) {
        e.preventDefault();
        var button = $('.js--sync-accounts');

        $('#settings-tab.lazyload').removeClass('lazyload');

        $.ajax({
            url: ajaxurl,
            data: {
                action: 'gmb_r_sync_accounts',
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                button.prop('disabled', true);
                $('i', button).addClass('fa-spin');
            },
            success: function(response) {
                var accounts = response.data.accounts,
                    $account = $('#gmb_r_settings select#account'),
                    $location = $('#gmb_r_settings select#location'),
                    accountPlaceholderText = $account.attr('placeholder');
                    locationPlaceholderText = $location.attr('placeholder');

                // add correct placeholder text after syncing the accounts and select the first option
                $account.find('option:first').text(accountPlaceholderText).prop('selected', true);
                $location.find('option:first').text(locationPlaceholderText).prop('selected', true);

                // ensure there are no duplicate accounts shown in the dropdown
                // and remove old locations
                $account.find('option').not(':first').remove();
                $location.find('option').not(':first').remove();

                $.each(accounts, function(index, account) {
                    $account.append($("<option />").val(account.endpoint).text(account.name));
                });
            }
        }).done(function(response) {
            button.removeClass('btn-primary').addClass('btn-success');

            var wordpressNotice = response.data.notice;
            $('<div class="notice notice-success is-dismissible is-dismissible mx-0 py-0 rounded"> <p>' + wordpressNotice + '</p> </div>').insertAfter('#gmb_r_admin .p-header-end');

            setTimeout(function() {
                button.removeClass('btn-success').addClass('btn-primary');
            }, 1000);
        }).fail(function(jqXHR, textStatus, errorThrown) {
            button.removeClass('btn-primary').addClass('btn-danger');

            var wordpressNotice = jqXHR.responseJSON.data;
            $('<div class="notice notice-error is-dismissible is-dismissible mx-0 py-0 rounded"> <p>' + wordpressNotice + '</p> </div>').insertAfter('#gmb_r_admin .p-header-end');

        }).always(function() {
            $('i', button).removeClass('fa-spin');

            setTimeout(function() {
                button.prop('disabled', false);
                button.removeClass('btn-success btn-danger').addClass('btn-primary');
            }, 2000);
        });
    });

    $(document).on('change', '#gmb_r_settings select#account', function(e) {
        e.preventDefault();

        var $account       = $(this),
            $select        = $('#gmb_r_settings select#location'),
            chosenAccount  = $account.val();

        // return early when there is no value
        if (!$.trim(chosenAccount).length) {
            return;
        }

        $.ajax({
            url: ajaxurl,
            data: {
                action  : 'gmb_r_sync_locations',
                account : chosenAccount,
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                $account.prop('disabled', true);
                $select.prop('disabled', true);
            },
            success: function(response) {

                var locations = response.data.locations,
                    placeholderText = $select.attr('placeholder');

                // add correct placeholder text after syncing the accounts and select the first option
                $select.find('option:first').text(placeholderText).prop('selected', true);

                // ensure there are no duplicate or old locations shown in the dropdown
                $select.find('option').not(':first').remove();

                $.each(locations, function(index, location) {
                    $select.append($("<option />").val(location.endpoint).text(location.name));
                });
                
            }
        }).done(function(response) {
            // silence is golden?
        }).fail(function(jqXHR, textStatus, errorThrown) {

            var wordpressNotice = jqXHR.responseJSON.data;
            $('<div class="notice notice-error is-dismissible is-dismissible mx-0 py-0 rounded"> <p>' + wordpressNotice + '</p> </div>').insertAfter('#gmb_r_admin .p-header-end');

        }).always(function() {
            $account.prop('disabled', false);
            $select.prop('disabled', false);
        });
    });
});
