<?php

class PublicTest extends WP_UnitTestCase {

	protected $public;

	public function setUp() {
		parent::setUp();
		$plugin       = new Dominant_Colors_Lazy_Loading();
		$this->public = new Dominant_Colors_Lazy_Loading_Public( $plugin->get_plugin_name(), $plugin->get_version() );
	}

	function test_get_gallery_attachment_ids() {

		$ids  = array();
		$urls = array();

		foreach ( range( 1, 3 ) as $i ) {
			$attachment_id = $this->factory->attachment->create_object( "image-$i.jpg", 0, array(
				'post_mime_type' => 'image/jpeg',
				'post_type'      => 'attachment'
			) );
			$ids[]         = $attachment_id;
			$urls[]        = 'http://' . WP_TESTS_DOMAIN . '/wp-content/uploads/' . "image-$i.jpg";
		}

		$content = sprintf( '[gallery ids="%s"]', join( ',', $ids ) );
		$post_id = $this->factory->post->create( array( 'post_content' => $content ) );

		$expected = array();
		foreach ( $ids as $i => $id ) {
			$expected[ $urls[ $i ] ] = $id;
		}

		$actual = $this->public->get_gallery_attachment_ids( $post_id );
		$this->assertEquals( $expected, $actual );

	}

	function test_replace_source_with_dominant_color_gif_format() {

		$image = '<img class="alignnone size-medium wp-image-123" src="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-200x300.png" alt="Cats" width="200" height="300" />';

		$expected = '<img class="alignnone size-medium wp-image-123 dcll-image dcll-placeholder" src="data:image/gif;base64,R0lGODlhAQABAIABANrHuQAAACwAAAAAAQABAAACAkQBADs=" data-src="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-200x300.png" alt="Cats" width="200" height="300" />';

		$actual = $this->public->replace_source_with_dominant_color( $image, 'dac7b9', Dominant_Colors_Lazy_Loading::FORMAT_GIF );
		$this->assertEquals( $expected, $actual );

	}

	function test_replace_source_with_dominant_color_svg_format() {

		$image = '<img class="alignnone size-medium wp-image-123" src="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-200x300.png" alt="Cats" width="200" height="300" />';

		$expected = '<img class="alignnone size-medium wp-image-123 dcll-image dcll-placeholder" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMDAgMzAwIj48L3N2Zz4=" data-src="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-200x300.png" style="background: #dac7b9;" alt="Cats" width="200" height="300" />';

		$actual = $this->public->replace_source_with_dominant_color( $image, 'dac7b9', Dominant_Colors_Lazy_Loading::FORMAT_SVG );
		$this->assertEquals( $expected, $actual );

	}

	function test_replace_source_with_dominant_color_wrapped_format() {

		$image = '<img class="alignnone size-medium wp-image-123" src="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-200x300.png" alt="Cats" width="200" height="300" />';

		$expected = '<div class="dcll-wrapper" style="padding-top: 150%;"><img class="alignnone size-medium wp-image-123 dcll-image dcll-placeholder" src="data:image/gif;base64,R0lGODlhAQABAIABANrHuQAAACwAAAAAAQABAAACAkQBADs=" data-src="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-200x300.png" alt="Cats" width="200" height="300" /></div>';

		$actual = $this->public->replace_source_with_dominant_color( $image, 'dac7b9', Dominant_Colors_Lazy_Loading::FORMAT_WRAPPED );
		$this->assertEquals( $expected, $actual );

		$image = '<img class="alignnone size-medium wp-image-123" src="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-200x200.png" alt="Cats" width="200" height="200" />';

		$expected = '<div class="dcll-wrapper" style="padding-top: 100%;"><img class="alignnone size-medium wp-image-123 dcll-image dcll-placeholder" src="data:image/gif;base64,R0lGODlhAQABAIABANrHuQAAACwAAAAAAQABAAACAkQBADs=" data-src="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-200x200.png" alt="Cats" width="200" height="200" /></div>';

		$actual = $this->public->replace_source_with_dominant_color( $image, 'dac7b9', Dominant_Colors_Lazy_Loading::FORMAT_WRAPPED );
		$this->assertEquals( $expected, $actual );

		$image = '<img class="alignnone size-medium wp-image-123" src="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-300x200.png" alt="Cats" width="300" height="200" />';

		$expected = '<div class="dcll-wrapper" style="padding-top: 66.667%;"><img class="alignnone size-medium wp-image-123 dcll-image dcll-placeholder" src="data:image/gif;base64,R0lGODlhAQABAIABANrHuQAAACwAAAAAAQABAAACAkQBADs=" data-src="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-300x200.png" alt="Cats" width="300" height="200" /></div>';

		$actual = $this->public->replace_source_with_dominant_color( $image, 'dac7b9', Dominant_Colors_Lazy_Loading::FORMAT_WRAPPED );
		$this->assertEquals( $expected, $actual );

	}

	function test_replace_source_with_thumbnails() {

		$image = '<img class="alignnone size-medium wp-image-123" src="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-200x300.png" alt="Cats" width="200" height="300" />';

		$expected = '<img class="alignnone size-medium wp-image-123 dcll-image dcll-placeholder" src="data:image/gif;base64,R0lGODlhAQABAIABANrHuQAAACwAAAAAAQABAAACAkQBADs=" data-src="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-200x300.png" alt="Cats" width="200" height="300" />';

		$actual = $this->public->replace_source_with_dominant_color( $image, 'dac7b9', Dominant_Colors_Lazy_Loading::FORMAT_GIF_3x3 );
		$this->assertEquals( $expected, $actual );

		$expected = '<img class="alignnone size-medium wp-image-123 dcll-image dcll-placeholder" src="data:image/gif;base64,R0lGODlhAwADAPMAAIN5cXtwbKWWlYlPTpBSVcx7geJQV+1WX/FUXQAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAAAAAAALAAAAAADAAMAAAQHEAQxSDEHRQA7" data-src="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-200x300.png" alt="Cats" width="200" height="300" />';

		$actual = $this->public->replace_source_with_dominant_color( $image, 'R0lGODlhAwADAPMAAIN5cXtwbKWWlYlPTpBSVcx7geJQV+1WX/FUXQAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAAAAAAALAAAAAADAAMAAAQHEAQxSDEHRQA7', Dominant_Colors_Lazy_Loading::FORMAT_GIF_3x3 );
		$this->assertEquals( $expected, $actual );

		$expected = '<img class="alignnone size-medium wp-image-123 dcll-image dcll-placeholder" src="data:image/gif;base64,R0lGODlhBAAEAPMAAIyCd42FgYZ5c6ugn21cU15MS3JbXcykqKtHSsBUW8pZYO5pcutOVvZVXvhVXvFNVSH5BAAAAAAALAAAAAAEAAQAAAQMEAQxSDEHJbVYc08EADs=" data-src="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-200x300.png" alt="Cats" width="200" height="300" />';

		$actual = $this->public->replace_source_with_dominant_color( $image, 'R0lGODlhBAAEAPMAAIyCd42FgYZ5c6ugn21cU15MS3JbXcykqKtHSsBUW8pZYO5pcutOVvZVXvhVXvFNVSH5BAAAAAAALAAAAAAEAAQAAAQMEAQxSDEHJbVYc08EADs=', Dominant_Colors_Lazy_Loading::FORMAT_GIF_4x4 );
		$this->assertEquals( $expected, $actual );

		$expected = '<img class="alignnone size-medium wp-image-123 dcll-image dcll-placeholder" src="data:image/gif;base64,R0lGODlhBQAFAPQAAIF4baSblox/eo5/eK6ioYN5a2tjXktFRY2Agsi5vF44NHtKSmw+QqVkae2OltROVOhbY+pdZvFdZvZaZOlLUvRSWfhTXPZRW+5HTwAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAAAAAAALAAAAAAFAAUAAAUVIBAIA1EYB5IoC9M4DxRJE1VZFxYCADs=" data-src="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-200x300.png" alt="Cats" width="200" height="300" />';

		$actual = $this->public->replace_source_with_dominant_color( $image, 'R0lGODlhBQAFAPQAAIF4baSblox/eo5/eK6ioYN5a2tjXktFRY2Agsi5vF44NHtKSmw+QqVkae2OltROVOhbY+pdZvFdZvZaZOlLUvRSWfhTXPZRW+5HTwAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAAAAAAALAAAAAAFAAUAAAUVIBAIA1EYB5IoC9M4DxRJE1VZFxYCADs=', Dominant_Colors_Lazy_Loading::FORMAT_GIF_5x5 );
		$this->assertEquals( $expected, $actual );

	}

	function test_replace_source_with_dominant_color_responsive_images() {

		$image = '<img class="alignnone size-medium wp-image-123" src="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-300x129.jpg" alt="cats" width="300" height="129" srcset="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-300x129.jpg 300w, http://local.wordpress.dev/wp-content/uploads/2015/05/cats-768x329.jpg 768w, http://local.wordpress.dev/wp-content/uploads/2015/05/cats-1024x439.jpg 1024w" sizes="(max-width: 300px) 100vw, 300px" />';

		$expected = '<img class="alignnone size-medium wp-image-123 dcll-image dcll-placeholder" src="data:image/gif;base64,R0lGODlhAQABAIABAHNkWAAAACwAAAAAAQABAAACAkQBADs=" data-src="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-300x129.jpg" alt="cats" width="300" height="129" data-srcset="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-300x129.jpg 300w, http://local.wordpress.dev/wp-content/uploads/2015/05/cats-768x329.jpg 768w, http://local.wordpress.dev/wp-content/uploads/2015/05/cats-1024x439.jpg 1024w" sizes="(max-width: 300px) 100vw, 300px" />';

		$actual = $this->public->replace_source_with_dominant_color( $image, '736458', 'gif' );
		$this->assertEquals( $expected, $actual );

	}

	function test_dominant_colors_custom_filter() {

		$id = $this->factory->attachment->create_object( 'image.jpg', 0, array(
			'post_mime_type' => 'image/jpeg',
			'post_type'      => 'attachment'
		) );

		update_post_meta( $id, 'dominant_color', 'dac7b9' );

		$original_image = '<img class="alignnone size-medium wp-image-123" src="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-200x300.png" alt="Cats" width="200" height="300" />';

		$expected_gif = '<img class="alignnone size-medium wp-image-123 dcll-image dcll-placeholder" src="data:image/gif;base64,R0lGODlhAQABAIABANrHuQAAACwAAAAAAQABAAACAkQBADs=" data-src="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-200x300.png" alt="Cats" width="200" height="300" />';

		$expected_svg = '<img class="alignnone size-medium wp-image-123 dcll-image dcll-placeholder" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMDAgMzAwIj48L3N2Zz4=" data-src="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-200x300.png" style="background: #dac7b9;" alt="Cats" width="200" height="300" />';

		$actual_gif = apply_filters( 'dominant_colors', $original_image, $id, Dominant_Colors_Lazy_Loading::FORMAT_GIF );
		$actual_svg = apply_filters( 'dominant_colors', $original_image, $id );

		$this->assertEquals( $expected_gif, $actual_gif );
		$this->assertEquals( $expected_svg, $actual_svg );

	}

}
