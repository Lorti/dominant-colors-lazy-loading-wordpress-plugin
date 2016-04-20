<?php

class SampleTest extends WP_UnitTestCase {

	protected $plugin_name;
	protected $version;

	public function setUp() {
		$plugin            = new Dominant_Colors_Lazy_Loading();
		$this->plugin_name = $plugin->get_plugin_name();
		$this->version     = $plugin->get_version();
	}

	function test_replace_source_with_dominant_color() {
		$image = '<img class="alignnone size-medium wp-image-123" src="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-200x300.png" alt="Cats" width="200" height="300" />';

		$expected = '<img class="alignnone size-medium wp-image-123 lazy" src="data:image/gif;base64,R0lGODlhAQABAIABANrHuQAAACwAAAAAAQABAAACAkQBADs=" data-src="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-200x300.png" alt="Cats" width="200" height="300" />';

		$plugin_public = new Dominant_Colors_Lazy_Loading_Public( $this->plugin_name, $this->version );
		$actual        = $plugin_public->replace_source_with_dominant_color( $image, 'dac7b9' );

		$this->assertEquals( $expected, $actual );
	}

	function test_replace_source_with_dominant_color_responsive_images() {
		$image = '<img class="alignnone size-medium wp-image-123" src="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-300x129.jpg" alt="cats" width="300" height="129" srcset="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-300x129.jpg 300w, http://local.wordpress.dev/wp-content/uploads/2015/05/cats-768x329.jpg 768w, http://local.wordpress.dev/wp-content/uploads/2015/05/cats-1024x439.jpg 1024w" sizes="(max-width: 300px) 100vw, 300px" />';

		$expected = '<img class="alignnone size-medium wp-image-123 lazy" src="data:image/gif;base64,R0lGODlhAQABAIABAHNkWAAAACwAAAAAAQABAAACAkQBADs=" data-src="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-300x129.jpg" alt="cats" width="300" height="129" data-srcset="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-300x129.jpg 300w, http://local.wordpress.dev/wp-content/uploads/2015/05/cats-768x329.jpg 768w, http://local.wordpress.dev/wp-content/uploads/2015/05/cats-1024x439.jpg 1024w" sizes="(max-width: 300px) 100vw, 300px" />';

		$plugin_public = new Dominant_Colors_Lazy_Loading_Public( $this->plugin_name, $this->version );
		$actual        = $plugin_public->replace_source_with_dominant_color( $image, '736458' );

		$this->assertEquals( $expected, $actual );
	}
}
