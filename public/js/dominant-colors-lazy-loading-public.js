(function() {
	'use strict';

	var check = function () {
		var images = document.getElementsByClassName( 'lazy' );
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
		image.src = image.getAttribute( 'data-src' );
		image.removeAttribute( 'data-src' );

		if ( image.hasAttribute( 'data-srcset' ) ) {
			image.srcset = image.getAttribute( 'data-srcset' );
			image.removeAttribute( 'data-srcset' );
		}

		image.classList.remove( 'lazy' );
	};

	window.addEventListener( 'load', check, false );
	window.addEventListener( 'scroll', check, false );
	window.addEventListener( 'resize', check, false );
	document.body.addEventListener( 'post-load', check, false );
})();
