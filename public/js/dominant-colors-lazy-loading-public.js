(function() {
	'use strict';

	var reStyle = /background: #[a-f0-9]{6};/;

	var check = function () {
		var images = document.getElementsByClassName( 'dcll-placeholder' );
		var viewport = document.documentElement.clientHeight || window.innerHeight;
		var updated = false;

		[].forEach.call( images, function ( image ) {

			var rect = image.getBoundingClientRect();

			if ( viewport - rect.top > 0 ) {
				show( image );
				updated = true;
			}

		} );

		if (updated) {
			check();
		}
	};

	var show = function ( image ) {
		if ( image.hasAttribute( 'style' ) ) {
			image.addEventListener( 'load', function () {
				image.setAttribute( 'style', image.getAttribute( 'style' ).replace( reStyle, '' ) );
			} );
		}

		image.src = image.getAttribute( 'data-src' );
		image.removeAttribute( 'data-src' );

		if ( image.hasAttribute( 'data-srcset' ) ) {
			image.srcset = image.getAttribute( 'data-srcset' );
			image.removeAttribute( 'data-srcset' );
		}

		image.classList.remove( 'dcll-placeholder' );
	};

	window.addEventListener( 'load', check, false );
	window.addEventListener( 'scroll', check, false );
	window.addEventListener( 'resize', check, false );
	document.body.addEventListener( 'post-load', check, false );
})();
