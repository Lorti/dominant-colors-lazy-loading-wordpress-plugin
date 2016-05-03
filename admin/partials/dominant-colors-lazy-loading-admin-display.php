<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      0.1.0
 *
 * @package    Dominant_Colors_Lazy_Loading
 * @subpackage Dominant_Colors_Lazy_Loading/admin/partials
 */
?>

<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<form action="options.php" method="post">
		<?php
			settings_fields( $this->plugin_name );
			do_settings_sections( $this->plugin_name );
			submit_button();
		?>
	</form>

	<?php if( ! class_exists( 'Imagick', false ) ) : ?>
		
		<h3><?php _e( 'ImageMagick PHP extension was not detected', 'dominant-colors-lazy-loading' ); ?></h3>
		
	<?php else : ?> 

	<?php if ( count( $attachments ) ): ?>

		<h3<?php _e( 'Status', 'dominant-colors-lazy-loading' ); ?></h3>

		<p class="js-status-message">
      
      <?php printf( _n( '%s image currently has no dominant color assigned.', '%s images currently have no dominant color assigned.', count( $attachments ), 'dominant-colors-lazy-loading' ), count( $attachments ) ); ?>
      
      <br />
      
      <?php _e( 'Do you want to calculate now?', 'dominant-colors-lazy-loading'); ?>

		</p>
		<p><input class="button-secondary js-calculation-button"
		          type="button"
		          value="<?php _e( 'Calculate', 'dominant-colors-lazy-loading' ); ?>"
		          data-ajax-url="<?php echo $ajax_url; ?>"
		          data-ajax-nonce="<?php echo $ajax_nonce; ?>"></p>

		<table>
			<?php foreach ( $attachments as $attachment ): ?>
				<tr>
					<td>
						<?php echo $attachment->post_name; ?>
					</td>
					<td class="js-attachment-id" data-attachment-id="<?php echo $attachment->ID; ?>">
						<?php _e( 'Pending', 'dominant-colors-lazy-loading' ); ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>

	<?php endif; ?>
<?php endif; ?>
</div>
