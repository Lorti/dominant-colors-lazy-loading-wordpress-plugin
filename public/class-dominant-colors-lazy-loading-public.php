<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      0.1.0
 *
 * @package    Dominant_Colors_Lazy_Loading
 * @subpackage Dominant_Colors_Lazy_Loading/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Dominant_Colors_Lazy_Loading
 * @subpackage Dominant_Colors_Lazy_Loading/public
 * @author     Manuel Wieser <office@manuelwieser.com>
 */
class Dominant_Colors_Lazy_Loading_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 *
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Dominant_Colors_Lazy_Loading_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Dominant_Colors_Lazy_Loading_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'css/dominant-colors-lazy-loading-public.css', array(), $this->version,
			'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Dominant_Colors_Lazy_Loading_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Dominant_Colors_Lazy_Loading_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/dominant-colors-lazy-loading-public.js',
			array(), $this->version, true );

	}

	/**
	 * Replace images with placeholders in the content.
	 *
	 * @since 0.1.0
	 *
	 * @param $content
	 *
	 * @return string
	 */
	public function filter( $content ) {

		// Are we currently on an AMP URL?
		$is_amp_endpoint = function_exists( 'is_amp_endpoint' ) && is_amp_endpoint();

		if ( ! preg_match_all( '/<img [^>]+>/', $content, $matches ) || $is_amp_endpoint ) {
			return $content;
		}

		global $post;

		$selected_images = $attachment_ids = array();
		$gallery_images  = $this->get_gallery_attachment_ids( $post->ID );

		foreach ( $matches[0] as $image ) {

			$attachment_id = null;

			if ( preg_match( '/wp-image-([0-9]+)/i', $image, $class_id ) ) {

				$attachment_id = absint( $class_id[1] );

			} else if ( preg_match( '/src="([^"]+)"/', $image, $image_src ) ) {
				if ( array_key_exists( $image_src[1], $gallery_images ) ) {
					$attachment_id = $gallery_images[ $image_src[1] ];
				} else {
					$image_parts = array();

					// Delete size from URL (`-300x300`)
					preg_match( '/(.*)-(?!.*\1)([0-9]+x[0-9]+)(\..*)($|\n)/', $image_src[1], $image_parts );
					$clean_src = $image_src[1];
					if ( isset( $image_parts[1] ) && isset( $image_parts[3] ) ) {
						$clean_src = $image_parts[1] . $image_parts[3];
					}
					$attachment_id = absint( attachment_url_to_postid( $clean_src ) );
				}
			}

			if ( isset( $attachment_id ) ) {
				$selected_images[ $image ]        = $attachment_id;
				$attachment_ids[ $attachment_id ] = true;
			}
		}

		if ( count( $attachment_ids ) > 1 ) {
			update_meta_cache( 'post', array_keys( $attachment_ids ) );
		}

		$format = get_option( 'dominant_colors_placeholder_format', Dominant_Colors_Lazy_Loading::FORMAT_SVG );

		foreach ( $selected_images as $image => $attachment_id ) {
			if ( $format === Dominant_Colors_Lazy_Loading::FORMAT_GIF ||
			     $format === Dominant_Colors_Lazy_Loading::FORMAT_SVG ||
			     $format === Dominant_Colors_Lazy_Loading::FORMAT_WRAPPED
			) {
				$dominant_color = get_post_meta( $attachment_id, 'dominant_color', true );
			} else {
				$tiny_thumbnails = get_post_meta( $attachment_id, 'tiny_thumbnails', true );
				if ( ! empty( $tiny_thumbnails ) ) {
					$tiny_thumbnails = unserialize( $tiny_thumbnails );
					$dominant_color  = $tiny_thumbnails[ $format ];
				}
			}
			if ( empty( $dominant_color ) ) {
				$dominant_color = get_option( 'dominant_colors_placeholder_fallback' );
			}
			if ( ! empty( $dominant_color ) ) {
			    $noscript = get_option( 'dominant_colors_placeholder_noscript', false );
				$content = str_replace( $image,
					$this->replace_source_with_dominant_color( $image, $dominant_color, $format, $noscript ), $content );
			}
		}

		return $content;

	}

	/**
	 * Replace an image in the theme with a placeholder.
	 *
	 * @since 0.4.0
	 *
	 * @param $image
	 * @param $attachment_id
	 * @param $format
	 *
	 * @return string
	 */
	public function theme_filter( $image, $attachment_id, $format = null ) {

		// Are we currently on an AMP URL?
		$is_amp_endpoint = function_exists( 'is_amp_endpoint' ) && is_amp_endpoint();

		if ( ! preg_match_all( '/<img [^>]+>/', $image, $matches ) || $is_amp_endpoint ) {
			return $image;
		}

		if ( ! is_null( $format ) ) {
			if ( $format !== Dominant_Colors_Lazy_Loading::FORMAT_GIF &&
			     $format !== Dominant_Colors_Lazy_Loading::FORMAT_SVG &&
			     $format !== Dominant_Colors_Lazy_Loading::FORMAT_WRAPPED ) {
				$format = null;
			}
		}

		if ( is_null( $format ) ) {
			$format = get_option( 'dominant_colors_placeholder_format', Dominant_Colors_Lazy_Loading::FORMAT_SVG );
		}

		$dominant_color = get_post_meta( $attachment_id, 'dominant_color', true );

		if ( empty( $dominant_color ) ) {
			$dominant_color = get_option( 'dominant_colors_placeholder_fallback' );
		}

		if ( ! empty( $dominant_color ) ) {
            $noscript = get_option( 'dominant_colors_placeholder_noscript', false );
			$image = $this->replace_source_with_dominant_color( $image, $dominant_color, $format, $noscript );
		}

		return $image;

	}

	/**
	 * Returns an array with the image URLs as keys and the post IDs as values.
	 *
	 * @since 0.2.0
	 *
	 * @param $post_id
	 *
	 * @return array
	 */
	public function get_gallery_attachment_ids( $post_id ) {

		$galleries = get_post_galleries( $post_id, false );

		$image_ids_by_url = array();

		foreach ( $galleries as $gallery ) {
			$ids  = explode( ',', $gallery['ids'] );
			$urls = $gallery['src'];

			foreach ( $urls as $key => $url ) {
				$image_ids_by_url[ $url ] = $ids[ $key ];
			}
		}

		return $image_ids_by_url;

	}

	/**
	 * Replace `src` and `srcset` with placeholders and return the altered element.
	 *
	 * @since 0.1.0
	 *
	 * @param $image
	 * @param $color
	 * @param $format
	 * @param $noscript
	 *
	 * @return string
	 */
	public function replace_source_with_dominant_color( $image, $color, $format, $noscript = false ) {
		if ( empty( $color ) ) {
			return $image;
		}

		if ( preg_match('/class="[^"]*\bdisable-dcll\b[^"]*"/', $image)) { //if the class disable-dcll is present
			return $image;
		}

		$image_src = preg_match( '/src="([^"]+)"/', $image, $match_src ) ? $match_src[1] : '';

		if ( ! $image_src ) {
			return $image;
		}

		if ( preg_match( '/src="data/', $image ) ) {
			return $image;
		}

        $noscript_element = '';
		if ($noscript) {
            $noscript_element = sprintf('<noscript>%s</noscript>', $image);
        }

		$image = str_replace( 'srcset', 'data-srcset', $image );

		if ( preg_match( '/class="/', $image ) ) {
			$image = preg_replace( '/class="(.*?)"/', 'class="$1 dcll-image dcll-placeholder"', $image );
		} else {
			$image = str_replace( '<img', '<img class="dcll-image dcll-placeholder"', $image );
		}

		$image_width        = intval( preg_match( '/width="(\d+)"/', $image, $match_width ) ? $match_width[1] : 1 );
		$image_height       = intval( preg_match( '/height="(\d+)"/', $image, $match_height ) ? $match_height[1] : 1 );
		$image_aspect_ratio = round( ( $image_height / $image_width ) * 100, 3 );

		if ( $format === Dominant_Colors_Lazy_Loading::FORMAT_SVG ) {

			$svg         = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %s %s"></svg>';
			$placeholder = 'data:image/svg+xml;base64,' . base64_encode( sprintf( $svg, $image_width, $image_height ) );

			return str_replace( $match_src[0],
				sprintf( 'src="%s" data-src="%s" style="background: #%s;"', $placeholder, $image_src, $color ),
				$image ) . $noscript_element;

		} else {

			$isFallbackColor = strlen( $color ) === 6;

			if ( $format === Dominant_Colors_Lazy_Loading::FORMAT_GIF ||
			     $format === Dominant_Colors_Lazy_Loading::FORMAT_SVG ||
			     $format === Dominant_Colors_Lazy_Loading::FORMAT_WRAPPED ||
			     $isFallbackColor
			) {
				$placeholder = $this->create_gif_placeholder( $color );
			} else {
				$placeholder = 'data:image/gif;base64,' . $color;
			}

			$image = str_replace( $match_src[0], sprintf( 'src="%s" data-src="%s"', $placeholder, $image_src, $color ),
				$image );

			if ( $format === Dominant_Colors_Lazy_Loading::FORMAT_WRAPPED ) {
				return sprintf( '<div class="dcll-wrapper" style="padding-top: %s%%;">%s</div>', $image_aspect_ratio,
					$image );
			}

			return $image . $noscript_element;
		}

	}

	/**
	 * Creates a single pixel GIF in the specified color and returns it as base64-encoded data URI.
	 *
	 * @since 0.6.0
	 *
	 * @param $color
	 *
	 * @return string
	 */
	public function create_gif_placeholder( $color ) {
		$header                    = '474946383961';
		$logical_screen_descriptor = '01000100800100';
		$image_descriptor          = '2c000000000100010000';
		$image_data                = '0202440100';
		$trailer                   = '3b';

		$gif = implode( array(
			$header,
			$logical_screen_descriptor,
			$color,
			'000000',
			$image_descriptor,
			$image_data,
			$trailer
		) );

		$placeholder = 'data:image/gif;base64,' . base64_encode( hex2bin( $gif ) );

		return $placeholder;
	}

}
