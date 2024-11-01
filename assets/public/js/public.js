jQuery(document).ready(function($) {
    publicModalFunctions();

    function publicModalFunctions() {
        var $modal = $('#public-modal');
        if (!$modal.length) {
            return;
        }

        var $html = $('html'),
            $counter = $modal.find('.counter'),
            time = $counter.text()
            duration = time * 1000;

        $html.addClass('gmb_r_no-scroll');

        $({ Counter: time }).animate({Counter: 0}, {
            duration: duration,
            easing: 'linear',
            step: function() {
                $counter.text(Math.ceil(this.Counter));
            },
            complete: function() {
                $counter.text(0);
                window.location.replace(PublicController.admin_page);
            }
        });
    }
});