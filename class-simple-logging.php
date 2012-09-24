<?php 

Class RWI_Simple_Logging {
	
	/* 
	 * Static property to hold our singleton instance
	 * @var SimpleBadges
	 */
	static $instance = false;

	
	/*
	 * This is our constructor, which is private to force the use of
	 * getInstance() to make this a singleton
	 * 
	 * @return SimpleBadges
	*/
	public function __construct() {

		add_action( 'init', array( $this, 'create_content_types' ) );
		add_filter( 'manage_edit-sl_item_columns', array( $this, 'set_log_columns' ) );
		add_filter( 'manage_sl_item_posts_custom_column', array( $this, 'set_log_custom_columns' ), 10, 2 );
		add_filter( 'manage_edit-sl_item_sortable_columns', array( $this, 'log_item_sortable_columns' ) );
		add_action( 'admin_menu', array( $this, 'remove_add_new' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );

	}
	
	
	/**
	 * If an instance exists, this returns it. If not, it creates one and 
	 * returns it.
	 *
	 * @return SimpleBadges
	 */
	 public static function getInstance() {
	 	if ( !self::$instance )
	 		self::$instance = new self;
	 	return self::$instance;
	 }
	
	
	/**
	 * Create a log item.
	 */
	public function create_log_item( $user_id, $event, $desc, $type = 'sl' ) {
		
		$args = array(
			'post_content'	=> $desc,
			'post_status'	=> 'publish',
			'post_title'	=> 'temp',
			'post_type'		=> 'sl_item',
			'post_name'		=> time() . uniqid()
		);
		
		//wp_insert_post( $args );
		
		$post_id = wp_insert_post( $args );
			
		add_post_meta( $post_id, $type . '_user_id', $user_id );
		add_post_meta( $post_id, $type . '_event', $event );
		
		if ( $type != 'sl' )
			add_post_meta( $post_id, 'sl_type', $type );
		
	}
	
	
	/**
	 * Content creation we need.
	 */
	public function create_content_types() {
		
		// Logging post type
		register_post_type( 'sl_item',
			array(
	 			
				'labels' => array(
				
					'name' => __( 'Log' ),
					'singular_name' => __( 'Log Item' ),
					'add_new' => __( 'Add New Log Items' ),
					'all_items' => __( 'Log' ),
					'add_new_item' => __( 'Add New Log Item' ),
					'edit_item' => __( 'Edit Item' ),
					'new_item' => __( 'New Item' ),
					'view_item' => __( 'View Item' ),
					'search_items' => __( 'Search Item' ),
					'not_found' => __( 'Log items not found.' ),
					'not_found_in_trash' => __( 'Log items not found in Trash' ),
					'parent_item_colon' => __( 'Parent Item' ),
					'menu_name' => __( 'Log' )
				
				),
				
				'description' => 'Provided by the Simple Badges plugin.',
				'public' => false,
				'exclude_from_search' => true,	 			
				'publicly_queryable' => false,
				'show_ui' => true,
				'show_in_nav_menus' => true,
				'show_in_admin_bar' => false,
				'menu_position' => 300,
				'menu_icon' => plugins_url( 'icon.png', __FILE__ ),
				'capabilities' => array(
					'publish_posts' => 'manage_options',
					'edit_posts' => 'manage_options',
					'edit_others_posts' => 'manage_options',
					'delete_posts' => 'manage_options',
					'read_private_posts' => 'manage_options',
					'edit_post' => 'manage_options',
					'delete_post' => 'manage_options',
					'read_post' => 'read'
				),
				'hierarchical' => false,
				'supports'	=> array( 'title', 'editor', 'custom-fields' ),
				'has_archive' => false,
				'rewrite'	=> false,
				'can_export' => false,
	 		
	 		)
	 	);
		
	}
	
	
	/**
	 * Removes add new
	 */
	public function remove_add_new() {
		global $submenu;
		unset( $submenu['edit.php?post_type=sl_item'][10] );		
	}
	
	
	/**
	 * Add our admin scripts
	 */
	public function scripts() {
		global $typenow, $pagenow;
		
		if ( ( $pagenow == 'edit.php' || $pagenow == 'post.php' ) && $typenow == 'sl_item' ) {
			wp_register_style( 'simple-logging-css', plugins_url( '/css/simple-logging.css', __FILE__ ) );
			wp_enqueue_style( 'simple-logging-css' );
		}

	}
	
	
	/**
	 * Set new columns for the manage screen.
	 */ 
	public function set_log_columns( $columns ) {
		
		unset( $columns['date'] );
		unset( $columns['title'] );
		
		return array_merge( $columns, 
			array(
				'user'		=> __( 'User' ),
				'event'		=> __( 'Event' ),
				'content'	=> __( 'Description' ),
				'time'		=> __( 'Time' )
			)
		);

	    return $columns;
	
	}
	
	
	/**
	 * Prepare the data for custom columns.
	 */
	public function set_log_custom_columns( $column_name, $post_id ) {
		
		$our_post = get_post( $post_id );
		$type = get_post_meta( $post_id, 'sl_type', true );
		
		if ( ! $type )
			$type = 'sl';

	    switch ( $column_name ) {
			
			case 'user' :
				$user = get_user_by( 'id', get_post_meta( $post_id, $type . '_user_id', true ) );
				echo $user->display_name;
				break;
				
			case 'event' :
				echo get_post_meta( $post_id, $type . '_event', true );
				break;
				
	        case 'content' :
	            echo $our_post->post_content;
	            break;
				
			case 'time' :
				echo human_time_diff( get_the_time( 'U', $post_id ), current_time( 'timestamp' ) ) . ' ago';
				break;

	        default:
	    }
		
	}
	
	
	/**
	 * Sortable columns for our custom post type.
	 */
	public function log_item_sortable_columns( $columns ) {
		
		$columns[ 'time' ] = 'time';
		$columns[ 'user' ] = 'user';
		
		return $columns;
		
	}
	
	
}


// Instantiate our class
$SimpleLogs = new RWI_Simple_Logging;