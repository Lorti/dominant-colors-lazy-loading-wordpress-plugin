<?php

class AdminTest extends WP_UnitTestCase {

	protected $admin;
	protected $image;

	public function setUp() {
		parent::setUp();
		$plugin      = new Dominant_Colors_Lazy_Loading();
		$this->admin = new Dominant_Colors_Lazy_Loading_Admin( $plugin->get_plugin_name(), $plugin->get_version() );
		$this->image = realpath( dirname( __FILE__ ) ) . '/test.jpg';
	}

	function test_calculate_dominant_color() {

		$expected = 'ca7a7b';
		$actual   = $this->admin->calculate_dominant_color( $this->image );
		$this->assertEquals( $expected, $actual );

	}

	function test_calculate_tiny_thumbnails() {

		$expected = [
			'3x3' => 'R0lGODlhAwADAPMAAIN5cXtwbKWWlYlPTpBSVcx7geJQV+1WX/FUXQAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAAAAAAALAAAAAADAAMAAAQHEAQxSDEHRQA7',
			'4x4' => 'R0lGODlhBAAEAPMAAIyCd42FgYZ5c6ugn21cU15MS3JbXcykqKtHSsBUW8pZYO5pcutOVvZVXvhVXvFNVSH5BAAAAAAALAAAAAAEAAQAAAQMEAQxSDEHJbVYc08EADs=',
			'5x5' => 'R0lGODlhBQAFAPQAAIF4baSblox/eo5/eK6ioYN5a2tjXktFRY2Agsi5vF44NHtKSmw+QqVkae2OltROVOhbY+pdZvFdZvZaZOlLUvRSWfhTXPZRW+5HTwAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAAAAAAALAAAAAAFAAUAAAUVIBAIA1EYB5IoC9M4DxRJE1VZFxYCADs='
		];
		$actual   = $this->admin->calculate_tiny_thumbnails( $this->image );
		$this->assertEquals( $expected, $actual );

	}

	function test_add_dominant_color_post_meta() {

		$id = $this->factory->attachment->create_object( $this->image, 0, array(
			'post_mime_type' => 'image/jpeg',
			'post_type'      => 'attachment'
		) );

		$actual = $this->admin->add_dominant_color_post_meta( $id );
		$this->assertEquals( 'ca7a7b', $actual );

		$actual = get_post_meta( $id, 'dominant_color', true );
		$this->assertEquals( 'ca7a7b', $actual );

		$actual = get_post_meta( $id, 'tiny_thumbnails', true );
		$this->assertEquals( 'a:3:{s:3:"3x3";s:120:"R0lGODlhAwADAPMAAIN5cXtwbKWWlYlPTpBSVcx7geJQV+1WX/FUXQAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAAAAAAALAAAAAADAAMAAAQHEAQxSDEHRQA7";s:3:"4x4";s:128:"R0lGODlhBAAEAPMAAIyCd42FgYZ5c6ugn21cU15MS3JbXcykqKtHSsBUW8pZYO5pcutOVvZVXvhVXvFNVSH5BAAAAAAALAAAAAAEAAQAAAQMEAQxSDEHJbVYc08EADs=";s:3:"5x5";s:204:"R0lGODlhBQAFAPQAAIF4baSblox/eo5/eK6ioYN5a2tjXktFRY2Agsi5vF44NHtKSmw+QqVkae2OltROVOhbY+pdZvFdZvZaZOlLUvRSWfhTXPZRW+5HTwAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAAAAAAALAAAAAAFAAUAAAUVIBAIA1EYB5IoC9M4DxRJE1VZFxYCADs=";}', $actual );

	}

	function test_query_images_without_dominant_colors() {

		$ids = array();

		foreach ( range( 1, 3 ) as $i ) {
			$attachment_id = $this->factory->attachment->create_object( "image-$i.jpg", 0, array(
				'post_mime_type' => 'image/jpeg',
				'post_type'      => 'attachment'
			) );
			$ids[]         = $attachment_id;
			sleep( 1 );
		}

		rsort( $ids );
		$ids = array_values( $ids );

		$expected = (object) array(
			'total' => '3',
			'ids'   => array_map( 'strval', $ids )
		);
		$actual   = $this->admin->query_images_without_dominant_colors( 3 );

		$this->assertEquals( $expected, $actual );

	}

}
