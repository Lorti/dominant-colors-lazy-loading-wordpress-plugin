<div class="wrap">

	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<?php if ( ! $imagick ): ?>

		<div class="notice notice-error is-dismissible">
			<p><?php _e( 'ImageMagick PHP extension was not detected.', 'dominant-colors-lazy-loading' ); ?></p>
		</div>
		
	<?php endif; ?>

	<h2 class="nav-tab-wrapper">

		<a href="?page=dominant-colors-lazy-loading&tab=placeholders"
		   class="nav-tab <?php if ( $active_tab == 'placeholders' ) {
			   echo 'nav-tab-active';
		   } ?>">
			<?php _e( 'Placeholders', 'dominant-colors-lazy-loading' ); ?>
		</a>

		<a href="?page=dominant-colors-lazy-loading&tab=calculation"
		   class="nav-tab <?php if ( $active_tab == 'calculation' ) {
			   echo 'nav-tab-active';
		   } ?>">
			<?php _e( 'Calculation', 'dominant-colors-lazy-loading' ); ?></a>
		</a>
		
	</h2>

</div>
