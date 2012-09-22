# Simple Logging

Log and display activity from the Simple Badges plugin.

## Adding support for Simple Logging

If you would like to include support for Simple Logging in your own plugin, it's easy to do. Just follow this format:

<pre>
add_action( 'your_function_to_hook_into', 'my_logging_function', 10, 2 );
	
function my_logging_function( $user_id ) {
	// Call the Simple Logging global.
	global $SimpleLogs;
		
	// The log item will need a user ID, event title and description.
	$event = 'A thing happened!';
	$desc = 'This is how/why that thing happened.';
		
	$SimpleLogs->create_log_item( $user_id, $event, $desc, 'your_plugin_identifier' );
		
}
</pre>