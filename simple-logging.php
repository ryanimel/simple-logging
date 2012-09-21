<?php
/* 
Plugin Name: Simple Logging
Plugin URI: TBD
Description: Log activity within the Simple Badges plugin.
Version: 0.1.alpha-201209
Author: Ryan Imel
Author URI: http://wpcandy.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/


// Require files
require plugin_dir_path( __FILE__ ) . 'class-simple-logging.php';


// Support for logging of new badges
add_action( 'simplebadges_after_adding', 'simple_logging_badges_added', 10, 2 );

function simple_logging_badges_added( $badge_id, $user_id ) {
	global $SimpleLogs;
	
	$event = 'Won ' . get_the_title( $badge_id ) . ' badge';
	$desc = 'An admin granted this badge.';
	
	$SimpleLogs->create_log_item( $user_id, $event, $desc, 'simple_badges' );
	
}


// Support for logging of badge removals
add_action( 'simplebadges_after_removing', 'simple_logging_badges_removed', 10, 2 );

function simple_logging_badges_removed( $badge_id, $user_id ) {
	global $SimpleLogs;
	
	$event = 'Lost ' . get_the_title( $badge_id ) . ' badge';
	$desc = 'An admin took away this badge.';
	
	$SimpleLogs->create_log_item( $user_id, $event, $desc, 'simple_badges' );
	
}
