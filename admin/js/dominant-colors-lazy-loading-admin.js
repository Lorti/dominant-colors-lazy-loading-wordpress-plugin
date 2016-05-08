(function ($) {
	'use strict';

	 var $status;
	 var $progress;
	 var $button;

	 var total;
	 var ids;

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

			$status.html( status() );
			$progress.attr( 'value', runs );

			if ( ids.length ) {
				calculate();
			} else {
				if ( runs !== total ) {
					next();
				} else {
					$status.html( result() );
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

	var status = function () {
		return ajax_object.status_message.replace( '{{count}}', runs ).replace( '{{total}}', total ) + '<br>' + ajax_object.patience_message;
	};

	var result = function () {
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

	var listeners = function () {
		$button.on( 'click', function () {
			$button.attr( 'disabled', true );
			$status.html( status() );
			calculate();
		} );
	};

	$( function () {
		$status = $( '.js-status-message' );
		$progress = $( '.js-progress-bar' );
		$button = $( '.js-calculation-button' );

		total = window.attachment_total;
		ids = window.attachment_ids;

		listeners();
	} );

})(jQuery);
