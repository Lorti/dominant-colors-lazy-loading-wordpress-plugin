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
            $item.html(ajax_object.calculating_string);

            var data = {
                'action': 'recalculate_dominant_color_post_meta',
                'nonce': ajax_object.ajax_nonce,
                'attachment-id': $item.attr('data-attachment-id')
            };

            $.post(ajax_object.ajax_url, data, function (response) {
                if (response.success) {
                    success++;
                    $item.html(ajax_object.success_string);
                } else {
                    error++;
                    $item.html(ajax_object.error_string);
                }

                var count = $items.length - success;
                if (count !== 1) {
                    $status.html(ajax_object.status_message_plural.replace('{{count}}', count));
                } else {
                    $status.html(ajax_object.status_message_singular);
                }

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
                return ajax_object.success_message;

            }
            if (!success && error) {
                return ajax_object.error_message;
            }
            if (success && error) {
                return ajax_object.result_message.replace('{{success}}', success).replace('{{error}}', error);
            }
        };

        $button.on('click', function () {
            $button.attr('disabled', true);
            calculate(0);
        });

    });

})(jQuery);
