<?php
/**
 * Plugin Name: WooCommerce example extension
 * Plugin URI: https://github.com/Automattic/wc-extensions-code-test-guide
 * Description: WooCommerce example extension as a guide to write tests.
 * Version: 1.0.0
 * Author: Akeda Bagus <admin@gedex.web.id>
 * Author URI: http://gedex.web.id
 */

/**
 * Run the plugin during `woocommerce_init`.
 *
 * @return bool
 */
function wc_ee_run() {
	return wc_ee_instance()->run();
}
add_action( 'woocommerce_init', 'wc_ee_run' );

/**
 * Get instance of WC_Example_Extension.
 *
 * @return WC_Example_Extension Instance of WC_Example_Extension
 */
function wc_ee_instance() {
	static $extension;

	if ( ! isset( $extension ) ) {
		require_once( 'includes/class-wc-example-extension.php' );
		$extension = new WC_Example_Extension( __FILE__, '1.0.0' );
	}

	return $extension;
}
