(function ($) {
	'use strict';

	$( function () {

		var $status = $( '.js-status-message' );
		var $progress = $( '.js-progress-bar' );
		var $button = $( '.js-calculation-button' );

		var total = window.attachment_total;
		var ids = window.attachment_ids;

		var runs = 0;
		var success = 0;
		var error = 0;

		var calculate = function () {
			var id = ids.shift();

			var data = {
				'action': 'recalculate_dominant_color_post_meta',
				'nonce': ajax_object.ajax_nonce,
				'attachment-id': id
			};

			$.post( ajax_object.ajax_url, data, function ( response ) {

				if ( response.success ) {
					success++;
				} else {
					error++;
				}

				runs++;

				$status.html( status( runs, total ) );
				$progress.attr( 'value', runs );

				if ( ids.length ) {
					calculate();
				} else {
					if ( runs !== total ) {
						next();
					} else {
						$status.html( result( success, error ) );
					}
				}

			} );
		};

		var next = function () {
			var data = {
				'action': 'next_batch_of_attachment_ids',
				'nonce': ajax_object.ajax_nonce
			};

			$.post( ajax_object.ajax_url, data, function ( response ) {

				if ( response.ids ) {
					ids = response.ids;
					calculate();
				} else {
					$status.html( ajax_object.ajax_error );
				}

			} );
		};

		var status = function ( runs, total ) {
			return ajax_object.status_message.replace( '{{count}}', runs ).replace( '{{total}}', total ) + '<br>' + ajax_object.patience_message;
		};

		var result = function ( success, error ) {
			if ( success && ! error ) {
				return ajax_object.success_message;
			}
			if ( ! success && error ) {
				return ajax_object.error_message;
			}
			if ( success && error ) {
				return ajax_object.result_message.replace( '{{success}}', success ).replace( '{{error}}', error );
			}
		};

		$button.on( 'click', function () {
			$button.attr( 'disabled', true );
			$status.html( status( runs, total ) );
			calculate();
		} );

	} );

})(jQuery);
