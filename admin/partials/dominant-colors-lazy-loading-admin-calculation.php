<div class="wrap">

	<h3><?php _e( 'Calculation', 'dominant-colors-lazy-loading' ); ?></h3>

	<?php if ( $attachments->total ): ?>
		
		<p class="js-status-message">
			<?php printf( _n( '%s image has no dominant color assigned or is missing tiny thumbnails.', '%s images have no dominant color assigned or are missing tiny thumbnails.', $attachments->total, 'dominant-colors-lazy-loading' ), $attachments->total ); ?>
			<br>
			<?php _e( 'Do you want to calculate now?', 'dominant-colors-lazy-loading' ); ?>
		</p>

		<progress class="js-progress-bar" value="0" max="<?php echo $attachments->total; ?>"></progress>

		<ul class="js-error-list"></ul>

		<?php if ( $imagick ): ?>
			<p><input type="button" class="button-secondary js-calculation-button"
			          value="<?php _e( 'Calculate', 'dominant-colors-lazy-loading' ); ?>"></p>
		<?php endif; ?>

		<script type="text/javascript">
			window.attachment_total = <?php echo $attachments->total; ?>;
			window.attachment_ids = <?php echo json_encode( $attachments->ids ); ?>;
		</script>

	<?php else: ?>

		<?php _e( 'All dominant colors have been calculated successfully.', 'dominant-colors-lazy-loading' ); ?>

	<?php endif; ?>

</div>
