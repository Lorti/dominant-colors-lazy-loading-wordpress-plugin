<?php

class AdminTest extends WP_UnitTestCase {

	protected $admin;

	public function setUp() {
		parent::setUp();
		$plugin      = new Dominant_Colors_Lazy_Loading();
		$this->admin = new Dominant_Colors_Lazy_Loading_Admin( $plugin->get_plugin_name(), $plugin->get_version() );
	}

	function test_calculate_dominant_color() {

		$path     = realpath( dirname( __FILE__ ) ) . '/test.jpg';
		$expected = 'ca7a7b';
		$actual   = $this->admin->calculate_dominant_color( $path );
		$this->assertEquals( $expected, $actual );

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
