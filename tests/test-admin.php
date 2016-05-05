<?php

class AdminTest extends WP_UnitTestCase {

	protected $admin;

	public function setUp() {
		parent::setUp();
		$plugin       = new Dominant_Colors_Lazy_Loading();
		$this->admin = new Dominant_Colors_Lazy_Loading_Admin( $plugin->get_plugin_name(), $plugin->get_version() );
	}

	function test_calculate_dominant_color() {

		$path = realpath(dirname(__FILE__)) . '/test.jpg';
		$expected = 'ca7a7b';
		$actual = $this->admin->calculate_dominant_color( $path );
		$this->assertEquals( $expected, $actual );

	}

}
