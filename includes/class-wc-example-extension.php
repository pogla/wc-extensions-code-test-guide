<?php

class WC_Example_Extension {

	/**
	 * The full path and filename of the file of plugin's main file.
	 *
	 * @var string
	 */
	public $file;

	/**
	 * The full path and filename of the file of plugin's main file.
	 *
	 * @var string
	 */
	public $version;

	/**
	 * Flag to indicate whether this extension is running already.
	 *
	 * @var bool
	 */
	protected $_is_running = false;

	/**
	 * Constructor.
	 *
	 * @param string $file    The full path and filename of the file of plugin's
	 *                        main file.
	 * @param string $version The full path and filename of the file of plugin's
	 *                        main file.
	 */
	public function __construct( $file, $version ) {
		$this->file    = $file;
		$this->version = $version;
	}

	/**
	 * Run the extension.
	 *
	 * @return bool Returns true when it's running
	 */
	public function run() {
		if ( $this->_is_running ) {
			return false;
		}

		$this->includes();
		$this->wc_hooks();

		$this->_is_running = true;
		return $this->_is_running;
	}

	/**
	 * Includes necessary files.
	 */
	public function includes() {
		require_once( trailingslashit( plugin_dir_path( $this->file ) ) . 'includes/class-wc-example-extension-price-emoji.php' );
	}

	/**
	 * Hooks into WC hooks.
	 *
	 * @return void
	 */
	public function wc_hooks() {
		add_filter( 'woocommerce_get_price_html', array( $this, 'display_price_emoji' ), 10, 2 );
	}

	/**
	 * Display price emoji.
	 *
	 * @param string     $html_price HTML price
	 * @param WC_Product $product    Product
	 *
	 * @return string $price
	 */
	public function display_price_emoji( $html_price, $product ) {
		$emoji = WC_Example_Extension_Price_Emoji::price_to_emoji( $product->get_price() );
		return sprintf( '%s &nbsp; %s', $html_price, $emoji );
	}
}
