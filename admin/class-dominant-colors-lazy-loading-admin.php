<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      0.1.0
 *
 * @package    Dominant_Colors_Lazy_Loading
 * @subpackage Dominant_Colors_Lazy_Loading/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Dominant_Colors_Lazy_Loading
 * @subpackage Dominant_Colors_Lazy_Loading/admin
 * @author     Manuel Wieser <office@manuelwieser.com>
 */
class Dominant_Colors_Lazy_Loading_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/dominant-colors-lazy-loading-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/dominant-colors-lazy-loading-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Add an options page under the Settings sub menu
	 *
	 * @since  0.1.0
	 */
	public function add_options_page() {

		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Dominant Colors Lazy Loading Settings', 'dominant-colors-lazy-loading' ),
			__( 'Dominant Colors Lazy Loading', 'dominant-colors-lazy-loading' ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_options_page' )
		);

	}

	/**
	 * Render the options page for plugin
	 *
	 * @since  0.1.0
	 */
	public function display_options_page() {
		include_once 'partials/dominant-colors-lazy-loading-admin-display.php';
	}

	/**
	 * Calculates the dominant color of an image attachment and saves it as post meta.
	 *
	 * @since   0.1.0
	 *
	 * @param $post_id
	 * @return void|WP_Error
	 */
	public function add_dominant_color_post_meta( $post_id ) {
		if ( wp_attachment_is_image( $post_id )) {

			if ( ! class_exists( 'Imagick', false ) )
				return;

			$path = wp_get_attachment_image_src( $post_id )[0];

			try {
				$image = new Imagick( $path );
				$image->resizeImage( 250, 250, Imagick::FILTER_GAUSSIAN, 1 );
				$image->quantizeImage( 1, Imagick::COLORSPACE_RGB, 0, false, false );
				$image->setFormat( 'RGB' );
				update_post_meta( $post_id, 'dominant_color', substr(bin2hex($image), 0, 6));
			}
			catch ( Exception $e ) {
				return new WP_Error( 'invalid_image', $e->getMessage(), $path );
			}

		}

	}

}
