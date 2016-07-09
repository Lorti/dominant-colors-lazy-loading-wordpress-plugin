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

		wp_localize_script( $this->plugin_name, 'ajax_object', array(
			'ajax_url'                => admin_url( 'admin-ajax.php' ),
			'ajax_nonce'              => wp_create_nonce( 'recalculate_dominant_color_post_meta' ),

			'success_message'         => __( 'All dominant colors have been calculated successfully.', 'dominant-colors-lazy-loading' ),
			'error_message'           => __( 'All attempts seem to have failed. Please make sure that the attachment files exist.', 'dominant-colors-lazy-loading' ),
			'result_message'          => __( '{{success}} color(s) calculated, but {{error}} attempt(s) failed.', 'dominant-colors-lazy-loading' ),

			'status_message'          => __( '{{count}} of {{total}} missing dominant colors calculated.', 'dominant-colors-lazy-loading' ),
			'attachment_message'      => __( 'Calculation for {{attachment}} failed.', 'dominant-colors-lazy-loading' ),
			'patience_message'        => __( 'Please be patient while the calculation is in progress. This can take a while if your server is slow or if you have many images.', 'dominant-colors-lazy-loading' ),

			'ajax_error'        => __( 'An unexpected error has occurred, please reload the page and restart the calculation.', 'dominant-colors-lazy-loading' )
		) );
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

		$imagick = class_exists( 'Imagick', false );

		$active_tab = 'placeholders';
		if ( isset( $_GET['tab'] ) ) {
			$active_tab = $_GET['tab'];
		}

		include_once 'partials/dominant-colors-lazy-loading-admin-header.php';

		if ( $active_tab == 'placeholders') {
			include_once 'partials/dominant-colors-lazy-loading-admin-placeholders.php';
		} else {
			$attachments = $this->query_images_without_dominant_colors();
			include_once 'partials/dominant-colors-lazy-loading-admin-calculation.php';
		}

	}

	/**
	 * Query the database for the total amount of missing dominant colors
	 * and return a batch of IDs for calculation.
	 *
	 * @since   0.5.4
	 *
	 * @param int $limit
	 *
	 * @return object
	 */
	public function query_images_without_dominant_colors( $limit = 1024 ) {

		$limit = intval( $limit );
		global $wpdb;

		$total_sql = "SELECT COUNT(*) AS count FROM $wpdb->posts as posts 
			LEFT JOIN $wpdb->postmeta as meta ON ( posts.ID = meta.post_id AND meta.meta_key = 'dominant_color' ) 
			WHERE posts.post_mime_type LIKE 'image/%' 
			AND meta.post_id IS NULL 
			AND posts.post_type = 'attachment'";

		$chunk_sql = "SELECT ID as id FROM $wpdb->posts as posts 
			LEFT JOIN $wpdb->postmeta as meta ON ( posts.ID = meta.post_id AND meta.meta_key = 'dominant_color' ) 
			WHERE posts.post_mime_type LIKE 'image/%' 
			AND meta.post_id IS NULL 
			AND posts.post_type = 'attachment'
			GROUP BY posts.ID
			ORDER BY posts.post_date DESC
			LIMIT 0, $limit";

		$total = $wpdb->get_row( $total_sql )->count;
		$ids   = $wpdb->get_col( $chunk_sql );

		return (object) compact( 'total', 'ids' );
	}

	/**
	 * Ajax action for retrieving the next batch of ids.
	 *
	 * @since   0.5.4
	 */
	public function next_batch_of_attachment_ids () {
		if ( ! current_user_can( 'manage_options' ) ||
		     ! wp_verify_nonce( $_REQUEST['nonce'], 'recalculate_dominant_color_post_meta' )
		) {
			wp_die();
		}

		$query = $this->query_images_without_dominant_colors();
		wp_send_json( $query );
	}

	/**
	 * Register all related settings of this plugin
	 *
	 * @since  0.3.0
	 */
	public function register_settings() {

		add_settings_section(
			'dominant_colors_general',
			__( 'Placeholders', 'dominant-colors-lazy-loading' ),
			array( $this, 'dominant_colors_general_callback' ),
			$this->plugin_name
		);

		add_settings_field(
			'dominant_colors_placeholder_format',
			__( 'Format', 'dominant-colors-lazy-loading' ),
			array( $this, 'dominant_colors_placeholder_format_callback' ),
			$this->plugin_name,
			'dominant_colors_general',
			array( 'label_for' => 'dominant_colors_placeholder_format' )
		);

		add_settings_field(
			'dominant_colors_placeholder_fallback',
			__( 'Fallback', 'dominant-colors-lazy-loading' ),
			array( $this, 'dominant_colors_placeholder_fallback_callback' ),
			$this->plugin_name,
			'dominant_colors_general',
			array( 'label_for' => 'dominant_colors_placeholder_fallback' )
		);

		register_setting( $this->plugin_name, 'dominant_colors_placeholder_format' );
		register_setting( $this->plugin_name, 'dominant_colors_placeholder_fallback', array( $this, 'sanitize_hex_color' ) );

	}

	/**
	 * @since  0.3.0
	 */
	public function dominant_colors_general_callback() {
		echo '<p>' . __( 'If you want to preserve the aspect ratio of responsive images enable SVG placeholders.<br>If you care about transferred bytes, browser compatibility or preserve the aspect ratio yourself use GIF placeholders.', 'dominant-colors-lazy-loading' ) . '</p>';
	}

	/**
	 * @since  0.3.0
	 */
	public function dominant_colors_placeholder_format_callback() {
		$format = get_option( 'dominant_colors_placeholder_format' );
		?>
		<fieldset>
			<label>
				<input type="radio" name="dominant_colors_placeholder_format"
				       value="<?php echo Dominant_Colors_Lazy_Loading::FORMAT_SVG; ?>" <?php checked( $format, Dominant_Colors_Lazy_Loading::FORMAT_SVG ); ?>>
				<?php _e( 'SVG (More bytes, preserves image aspect ratio)', 'dominant-colors-lazy-loading' ); ?>
			</label>
			<br>
			<label>
				<input type="radio" name="dominant_colors_placeholder_format"
				       value="<?php echo Dominant_Colors_Lazy_Loading::FORMAT_GIF; ?>" <?php checked( $format, Dominant_Colors_Lazy_Loading::FORMAT_GIF ); ?>>
				<?php _e( 'GIF (Less bytes, compatible with ancient browsers)', 'dominant-colors-lazy-loading' ); ?>
			</label>
		</fieldset>
		<?php
	}

	/**
	 * @since  0.4.0
	 */
	public function dominant_colors_placeholder_fallback_callback() {
		$fallback = get_option( 'dominant_colors_placeholder_fallback' );
		echo '#<input type="text" name="dominant_colors_placeholder_fallback" id="dominant_colors_placeholder_fallback" value="' . $fallback . '" placeholder="bada55">';
	}

	/**
	 * @since  0.4.0
	 *
	 * @param $color
	 *
	 * @return mixed
	 */
	public function sanitize_hex_color( $color ) {
		if ( preg_match('/^[a-f0-9]{6}$/', $color ) )
			return $color;
	}

	/**
	 * Calculates the dominant color of an attachment and saves it as post meta.
	 *
	 * @since   0.1.0
	 *
	 * @param $post_id
	 * @return string|WP_Error
	 */
	public function add_dominant_color_post_meta( $post_id ) {
		if ( wp_attachment_is_image( $post_id )) {

			if ( ! class_exists( 'Imagick', false ) )
				return;

			$path = get_attached_file( $post_id );

			try {
				$dominant_color = $this->calculate_dominant_color( $path );
				update_post_meta( $post_id, 'dominant_color', $dominant_color );
				return $dominant_color;
			}
			catch ( Exception $e ) {
				return new WP_Error( 'invalid_image', $e->getMessage(), $path );
			}

		}
	}

	/**
	 * Ajax action for calculating the missing dominant color of an image attachment.
	 *
	 * @since   0.5.0
	 */
	public function recalculate_dominant_color_post_meta() {
		if ( ! current_user_can( 'manage_options' ) ||
		     ! wp_verify_nonce( $_REQUEST['nonce'], 'recalculate_dominant_color_post_meta' )
		) {
			wp_die();
		}

		$attachment_id = intval( $_POST['attachment-id'] );
		$result        = $this->add_dominant_color_post_meta( $attachment_id );
		wp_send_json( array(
			'success' => is_string( $result ),
			'title' => get_the_title ( $attachment_id )
		) );
	}

	/**
	 * Calculates the dominant color of an image.
	 *
	 * @since   0.5.2
	 *
	 * @param $path
	 *
	 * @return string
	 */
	public function calculate_dominant_color( $path ) {
		$image = new Imagick( $path );
		$image->resizeImage( 256, 256, Imagick::FILTER_QUADRATIC, 1 );
		$image->quantizeImage( 1, Imagick::COLORSPACE_RGB, 0, false, false );
		$image->setFormat( 'RGB' );

		return substr( bin2hex( $image ), 0, 6 );
	}

	/**
	 * Calculates tiny thumbnails of an image.
	 *
	 * @since   0.6.0
	 *
	 * @param $path
	 *
	 * @return string
	 */
	public function calculate_tiny_thumbnails( $path ) {
		$three = new Imagick( $path );
		$three->resizeImage( 3, 3, Imagick::FILTER_QUADRATIC, 1 );
		$three->setFormat( 'GIF' );
		$four = new Imagick( $path );
		$four->resizeImage( 4, 4, Imagick::FILTER_QUADRATIC, 1 );
		$four->setFormat( 'GIF' );
		$five = new Imagick( $path );
		$five->resizeImage( 5, 5, Imagick::FILTER_QUADRATIC, 1 );
		$five->setFormat( 'GIF' );
		return array(
			'3x3' => base64_encode( $three ),
			'4x4' => base64_encode( $four ),
			'5x5' => base64_encode( $five )
		);
	}

}
