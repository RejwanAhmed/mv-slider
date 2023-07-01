<?php

if (! class_exists( 'MV_Slider_Shortcode' ) ) {
	class MV_Slider_Shortcode {
		public function __construct() {
			// To register a shortcode
			add_shortcode('mv_slider', array($this, 'add_shortcode' ) );
		}

		// here the callback function must return something
		// Every shortcode callback is passed three parameters (not mandatory) by default, including an array of attributes ($atts), the shortcode content or null if not set ($content), and finally the shortcode tag itself ($shortcode_tag), in that order.

		// [mv_slider] Lorem Ipsum [/mv_slider]
		// Lorem Ipsum is the value of the $content if we put the shortcode in a post like above.

		public function add_shortcode( $atts = array(), $content = null, $tag = '' ) {
			$atts = array_change_key_case( (array) $atts, CASE_LOWER );

			extract( shortcode_atts(
					array(
						'id' => '',
						'orderby' => 'date'
					),
					$atts,
					$tag
				)
			);

			if ( !empty( $id ) ) {
				$id = array_map( 'absint', explode( ',', $id ) );
			}

			ob_start();
			require( MV_SLIDER_PATH . 'views/mv-slider_shortcode.php' );
			// this scripts and style will be loaded only when shortcode is called
			wp_enqueue_script( 'mv-slider-main-jq' );

			// wp_enqueue_script( 'mv-slider-options-js' );

			wp_enqueue_style( 'mv-slider-main-css' );
			wp_enqueue_style( 'mv-slider-style-css' );
			mv_slider_options();
			// Here we changed something like, we called a function mv_slider_options() which is in functions.php and in that file we took a value from database and inject it to flexslider.js file. Previously we enqueued a script named mv-slider-options-js and now we removed it.
			return ob_get_clean();
		}
	}
}