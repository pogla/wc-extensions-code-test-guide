<?php

class WC_Example_Extension_Price_Emoji {

	/**
	 * Given a price returns its emoji.
	 *
	 * @param mixed $price Price
	 *
	 * @return string Emoji
	 */
	public static function price_to_emoji( $price ) {
		$emoji = '☺️';
		if ( $price > 10 ) {
			$emoji = '🤔';
		} else if ( 0 == $price ) {
			$emoji = '😍';
		}

		return $emoji;
	}
}
