<?php

class WC_Example_Extension_Price_Emoji_Test extends WP_UnitTestCase {

	public function setUp() {
		if ( ! class_exists( 'WC_Example_Extension_Price_Emoji' ) ) {
			require_once( trailingslashit( plugin_dir_path( wc_ee_instance()->file ) ) . 'includes/class-wc-example-extension-price-emoji.php' );
		}
	}

	public function test_price_to_emoji() {
		// Free!
		$this->assertEquals( '😍', WC_Example_Extension_Price_Emoji::price_to_emoji( 0 ) );
		$this->assertEquals( '😍', WC_Example_Extension_Price_Emoji::price_to_emoji( '0.00' ) );

		// Good price.
		$this->assertEquals( '☺️', WC_Example_Extension_Price_Emoji::price_to_emoji( '0.01' ) );
		$this->assertEquals( '☺️', WC_Example_Extension_Price_Emoji::price_to_emoji( 5.55 ) );
		$this->assertEquals( '☺️', WC_Example_Extension_Price_Emoji::price_to_emoji( '10.00' ) );

		// Probably expensive for most people?
		$this->assertEquals( '🤔', WC_Example_Extension_Price_Emoji::price_to_emoji( '10.01' ) );
		$this->assertEquals( '🤔', WC_Example_Extension_Price_Emoji::price_to_emoji( 100.00 ) );
	}
}
