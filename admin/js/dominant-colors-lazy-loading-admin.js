(function ($) {
    'use strict';

    $(function () {

        var $status = $('.js-status-message');
        var $button = $('.js-calculation-button');
        var $items = $('.js-attachment-id');

        var items = $items.length;
        var success = 0;
        var error = 0;

        var calculate = function (index) {
            var $item = $items.eq(index);
            $item.html('Calculating…');

            var data = {
                'action': 'recalculate_dominant_color_post_meta',
                'nonce': $button.attr('data-ajax-nonce'),
                'attachment-id': $item.attr('data-attachment-id')
            };

            $.post($button.attr('data-ajax-url'), data, function (response) {
                if (response.success) {
                    success++;
                    $item.html('<strong>Success</strong>');
                } else {
                    error++;
                    $item.html('<strong>Error</strong>');
                }

                $status.html($items.length - success + ' images currently have no dominant color assigned.');

                items--;
                if (items > 0) {
                    calculate(index + 1);
                } else {
                    $status.html(result(success, error));
                }
            });
        };

        var result = function (success, error) {
            if (success && !error) {
                return 'All dominant colors have been calculated successfully.'
            }
            if (!success && error) {
                return 'All attempts seem to have failed. Do you have the ImageMagick PHP extension installed?'
            }
            if (success && error) {
                return success + ' color(s) calculated, but ' + error + ' attempt(s) seem to have failed.'
            }
        };

        $button.on('click', function () {
            $button.attr('disabled', true);
            calculate(0);
        });

    });

})(jQuery);
