<?php

class PublicTest extends WP_UnitTestCase {

	protected $public;

	public function setUp() {
		parent::setUp();
		$plugin       = new Dominant_Colors_Lazy_Loading();
		$this->public = new Dominant_Colors_Lazy_Loading_Public( $plugin->get_plugin_name(), $plugin->get_version() );
	}

	function test_replace_source_with_dominant_color() {
		$image = '<img class="alignnone size-medium wp-image-123" src="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-200x300.png" alt="Cats" width="200" height="300" />';

		$expected = '<img class="alignnone size-medium wp-image-123 lazy" src="data:image/gif;base64,R0lGODlhAQABAIABANrHuQAAACwAAAAAAQABAAACAkQBADs=" data-src="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-200x300.png" alt="Cats" width="200" height="300" />';

		$actual = $this->public->replace_source_with_dominant_color( $image, 'dac7b9' );
		$this->assertEquals( $expected, $actual );
	}

	function test_replace_source_with_dominant_color_responsive_images() {
		$image = '<img class="alignnone size-medium wp-image-123" src="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-300x129.jpg" alt="cats" width="300" height="129" srcset="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-300x129.jpg 300w, http://local.wordpress.dev/wp-content/uploads/2015/05/cats-768x329.jpg 768w, http://local.wordpress.dev/wp-content/uploads/2015/05/cats-1024x439.jpg 1024w" sizes="(max-width: 300px) 100vw, 300px" />';

		$expected = '<img class="alignnone size-medium wp-image-123 lazy" src="data:image/gif;base64,R0lGODlhAQABAIABAHNkWAAAACwAAAAAAQABAAACAkQBADs=" data-src="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-300x129.jpg" alt="cats" width="300" height="129" data-srcset="http://local.wordpress.dev/wp-content/uploads/2015/05/cats-300x129.jpg 300w, http://local.wordpress.dev/wp-content/uploads/2015/05/cats-768x329.jpg 768w, http://local.wordpress.dev/wp-content/uploads/2015/05/cats-1024x439.jpg 1024w" sizes="(max-width: 300px) 100vw, 300px" />';

		$actual = $this->public->replace_source_with_dominant_color( $image, '736458' );
		$this->assertEquals( $expected, $actual );
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

		$expected = [ ];
		foreach ( $ids as $i => $id ) {
			$expected[ $urls[ $i ] ] = $id;
		}

		$actual = $this->public->get_gallery_attachment_ids( $post_id );
		$this->assertEquals( $expected, $actual );

	}
}
