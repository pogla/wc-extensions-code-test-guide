<?php

class WC_Example_Extension_Test extends WP_UnitTestCase {

	public function test_basic_functions_are_available() {
		$this->assertTrue( function_exists( 'wc_ee_run' ) );
		$this->assertTrue( function_exists( 'wc_ee_instance' ) );
	}

	public function test_price_emoji_class_is_available() {
		$this->assertTrue( class_exists( 'WC_Example_Extension_Price_Emoji' ) );
	}

	public function test_run_can_only_be_run_once() {
		$this->assertFalse( wc_ee_run() );
	}

	public function test_hooks_are_attached() {
		$this->assertEquals( 10, has_filter( 'woocommerce_get_price_html', array( wc_ee_instance(), 'display_price_emoji' ) ) );
	}

	public function test_product_price_displayed_with_emoji() {
		$product = WC_Helper_Product::create_simple_product();

		// Free!
		update_post_meta( $product->id, '_price', 0 );
		$product = wc_get_product( $product->id );
		$this->assertEquals( '<span class="amount">Free!</span> &nbsp; 😍', $product->get_price_html() );

		// Good price.
		update_post_meta( $product->id, '_price', 10 );
		$product = wc_get_product( $product->id );
		$this->assertEquals( '<span class="amount">&pound;10.00</span> &nbsp; ☺️', $product->get_price_html() );

		update_post_meta( $product->id, '_price', '0.01' );
		$product = wc_get_product( $product->id );
		$this->assertEquals( '<span class="amount">&pound;0.01</span> &nbsp; ☺️', $product->get_price_html() );

		// Probably expensive for most people?
		update_post_meta( $product->id, '_price', '10.01' );
		$product = wc_get_product( $product->id );
		$this->assertEquals( '<span class="amount">&pound;10.01</span> &nbsp; 🤔', $product->get_price_html() );
	}
}
