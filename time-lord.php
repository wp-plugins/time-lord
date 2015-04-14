<?php

/*
	Plugin Name: Time Lord
	Plugin URI: http://wordpress.org/plugins/time-lord/
	Description: Make modifications on your content based on time parameters. Show or hide part of a post at a given point in the future, calculate age and more. 
	Version: 1.0
	Author: Giorgos Sarigiannidis
	Author URI: http://www.gsarigiannidis.gr
*/

	load_plugin_textdomain('timelord', false, basename( dirname( __FILE__ ) ) . '/languages' ); // Localize it

	/*
		The [timelord] shortcode.
	*/
	function timelord_shortcode( $atts, $content = null ) {

		$a = shortcode_atts( array(
			'mode' 		=> 'show', 	// [timelord mode="hide"]
			'from' 		=> false, 	// [timelord from="YYYY-MM-DD hh:mm:ss"]
			'to' 		=> false,	// [timelord to="YYYY-MM-DD hh:mm:ss"]
			'message'	=> false,	// [timelord message="SOME MESSAGE"]
			'from_msg'	=> false, 	// [timelord from_msg="SOME MESSAGE"]
			'to_msg'	=> false, 	// [timelord to_msg="SOME MESSAGE"]
			'del' 		=> false, 	// [timelord del="yes"]
			'year' 		=> false,	// [timelord year="YYYY"]
			'ordinal' 	=> false, 	// [timelord ordinal="yes"]
		), $atts );

		if( !empty($a['year']) ) { // [timelord year="YEAR" ordinal="yes"]

			$year_set 		= filter_var( $a['year'], FILTER_SANITIZE_NUMBER_INT );
			$year_current 	= date('Y');
			$set_age 		= abs( $year_current - $year_set );
			$age 			= ( $a['ordinal'] === 'yes' ) ? ordinal($set_age) : $set_age;

			return $age;

		} else { // [timelord mode="hide" from="DATE" to="DATE" message="MESSAGE" from_msg="MESSAGE" to_msg="MESSAGE"]
			$set_from 		= strtotime($a['from']);
			$set_to 		= strtotime($a['to']);
			$today 			= time()-(60*60*24); // Get today.
			$from 			= !empty($set_from) ? $set_from : ($today-1);
			$to 			= !empty($set_to) ? $set_to : ($today+1);
			$empty_msg	 	= $a['del'] === 'yes' ? '<del>' . do_shortcode($content) . '</del>' : '';
			$message 		= !empty($a['message']) ? $a['message'] : $empty_msg;
			$condition 		= $a['mode'] === 'hide' ? ($today < $from || $today > $to) : ($today > $from && $today < $to);
			$from_msg 		= !empty($a['from_msg']) ? human_time_diff( $set_from ) : '';
			$to_msg			= !empty($a['to_msg']) ? human_time_diff( $set_to ) : '';
			$get_deadline 	= $today < $from ? $a['from_msg'] . $from_msg :  $a['to_msg'] . $to_msg;
			$get_class		= $today < $from ? 'message-from' : 'message-to'; 
			$deadline 		= '<span class="timelord-' . $get_class . '">' . $get_deadline . '</span>';
			$get_content 	= $condition ? do_shortcode($content) : $message;

			return $get_content . $deadline;
		}

	}
	add_shortcode( 'timelord', 'timelord_shortcode' );

		/* 
			Function ordinal($number) to get the ordinal suffix.
		*/
		function ordinal( $number ) {
			$ends = array( 
				__( 'th', 'timelord' ),
				__( 'st', 'timelord' ),
				__( 'nd', 'timelord' ),
				__( 'rd', 'timelord' ),
				__( 'th', 'timelord' ),
				__( 'th', 'timelord' ),
				__( 'th', 'timelord' ),
				__( 'th', 'timelord' ),
				__( 'th', 'timelord' ),
				__( 'th', 'timelord' ) 
			);
			$ordinal = (($number % 100) >= 11) && (($number % 100) <= 13) ? $number. 'th' : $number. $ends[$number % 10];

			return $ordinal;
		}

?>