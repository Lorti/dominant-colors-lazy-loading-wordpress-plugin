<?php

class ClassTest extends WP_UnitTestCase {

	function test_define_public_hooks() {

		$name = 'Dominant_Colors_Lazy_Loading';

		$plugin = $this->getMockBuilder( $name )
		               ->disableOriginalConstructor()
		               ->getMock();

		$plugin->expects( $this->once() )
		       ->method( 'define_public_hooks' );

		$reflectedClass = new ReflectionClass( $name );
		$constructor    = $reflectedClass->getConstructor();
		$constructor->invoke( $plugin );

	}

	function test_disable_public_hooks_on_amp_sites() {

		function is_amp_endpoint() {
			return true;
		}

		$name = 'Dominant_Colors_Lazy_Loading';

		$plugin = $this->getMockBuilder( $name )
		               ->disableOriginalConstructor()
		               ->getMock();

		$plugin->expects( $this->never() )
		       ->method( 'define_public_hooks' );

		$reflectedClass = new ReflectionClass( $name );
		$constructor    = $reflectedClass->getConstructor();
		$constructor->invoke( $plugin );

	}

}
