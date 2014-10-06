<?php
/**
 * This file contains various functions
 *
 * @since 1.0.0
 *
 * @package    MP Stacks
 * @subpackage Functions
 *
 * @copyright  Copyright (c) 2014, Mint Plugins
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @author     Philip Johnston
 */

/**
 * Action hook which brick metaboxes can use so they only load on brick and brick related admin apges
 *
 * @since 1.0
 * @return void
*/
function mp_brick_metabox() {
	
	//Get current page
	$current_page = get_current_screen();
	
	//Only load if we are on an mp_brick page
	if ( $current_page->id == 'mp_brick' || $current_page->id == 'settings_page_mp_stacks_create_template_page' ){
		
		//Use this action hook to run the metabox creation MP Core class for brick related metaboxes
		do_action( 'mp_brick_metabox' );
		
	}
	
}
add_action('current_screen', 'mp_brick_metabox');
	
/**
 * Remove "hentry" from bricks post_class
 *
 * @since 1.0
 * @return void
*/
function mp_stacks_remove_hentry( $classes, $class = '', $post_id = '' ) {
	
	if ( !$post_id || get_post_type( $post_id ) !== 'mp_brick' ){
		return $classes;
	}

	if ( ( $key = array_search( 'hentry', $classes ) ) !== false ) {
		unset( $classes[ $key ] );
	}

	return $classes;
}
add_filter( 'post_class', 'mp_stacks_remove_hentry', 100, 3 );

/**
 * Remove "hentry" from page post_class if the page template is "Optimize for MP Stacks"
 *
 * @since 1.0
 * @return void
*/
function mp_stacks_remove_hentry_from_stack_page_templates( $classes ) {
	global $post;
	
	$class_name_counter = 0;
	
	//Loop through each class name
	foreach( $classes as $class_name ){
		//If one of the class names is hentry
		if ( $class_name == 'hentry' ){
			//If we are using the mp-stacks-page-template
			if ( get_page_template_slug( $post->ID ) == 'mp-stacks-page-template.php' ){
				//Remove hentry from the classes array
				$classes[$class_name_counter] = '';	
			}
			
			//If we are using the default page template but it has had its title converted to include the word 'stack'
			else if ( empty( get_page_template_slug( $post->ID ) ) ){
				
				//Check the title of the default page template - This filter: https://core.trac.wordpress.org/ticket/27178
				$default_page_template_title = apply_filters( 'default_page_template_title', __('Default Template') );
					
				//If the default page template's title includes the word "Stack"
				if ( strpos( $default_page_template_title, 'Stack' ) !== false ){
					//Remove hentry from the classes array
					$classes[$class_name_counter] = '';	
				}	
			}
			
		}
		
		$class_name_counter = $class_name_counter + 1;
	}
	
	return $classes;
}
add_filter( 'post_class', 'mp_stacks_remove_hentry_from_stack_page_templates' );

/**
 * If there's no js in admin, let them know that life is too short for that.
 *
 * @since 1.0
 * @return void
*/
function mp_stacks_brick_edit_page_no_js_message() {
	
	global $post;
	
	if ( isset( $post->post_type) && $post->post_type == 'mp_brick' ){
	
		
		echo '<noscript>
			<style type="text/css">
				.wrap {display:none;}
			</style>
			<div class="noscriptmsg error">
			You don\'t have javascript enabled. Life\'s too short for that! Turn it on and then let\'s get cookin\'!
			</div>
		</noscript>';
		
	}
}
add_action( 'all_admin_notices', 'mp_stacks_brick_edit_page_no_js_message');

/**
 * Admin Stacks Icon
 *
 * Echoes the CSS for the downloads post type icon.
 *
 * @since 1.0
 * @global $post_type
 * @global $wp_version
 * @return void
*/
function mp_stacks_admin_stacks_and_bricks_icon() {
	global $post_type, $wp_version;

    $menu_icon   = '\f214';
	?>
    <style type="text/css" media="screen">
			#adminmenu #toplevel_page_mp-stacks-about .wp-menu-image:before {
				background: url("<?php echo plugins_url('assets/images/mp_stack-icon-2x.png', dirname(dirname(__FILE__) ) ); ?>") no-repeat scroll;
				content: '';
				background-size: 20px;
				background-position-y: 6px;
			}
			#mp_stack-media-button {
				background: url("<?php echo plugins_url('assets/images/mp_stack-icon-2x.png', dirname(dirname(__FILE__) ) ); ?>") no-repeat scroll;
				content: '';
				background-size: 16px;
				background-position-y: -1px;
			}
	</style>
	<?php
}
add_action( 'admin_head','mp_stacks_admin_stacks_and_bricks_icon' );

/**
 * Make the mp_stacks shortcode display the stack editor for TinyMCE
 *
 * @since   1.0.0
 * @link    http://mintplugins.com/doc/
 * @param   array $plugin_array See link for description.
 * @return  array $plugin_array
 */
function mp_stacks_add_stacks_tinymce_plugin($plugin_array) {
 	if ( get_user_option('rich_editing') == 'true') {
		$plugin_array['mpstacks'] =  plugins_url( '/js/', dirname(__FILE__) ) . 'mp-stacks-tinymce.js';
	}
    return $plugin_array;
}
add_filter("mce_external_plugins", "mp_stacks_add_stacks_tinymce_plugin");

/**
 * Add mp_stack stylesheet to the TinyMCE styles
 *
 * @since    1.0.0
 * @link     http://codex.wordpress.org/Function_Reference/add_editor_style
 * @see      get_bloginfo()
 * @param    array $wp See link for description.
 * @return   void
 */
function mp_stacks_addTinyMCELinkClasses( $wp ) {	
	add_editor_style( plugins_url( '/css/', dirname(__FILE__) ) . 'mp-stacks-tinyMCE-style.css' ); 
}
add_action( 'mp_core_editor_styles', 'mp_stacks_addTinyMCELinkClasses' );

/**
 * Get all the brick titles in this stack
 *
 * @since    1.0.0
 * @link     http://codex.wordpress.org/Function_Reference/add_editor_style
 * @see      get_bloginfo()
 * @param    array $wp See link for description.
 * @return   void
 */
function mp_stacks_get_brick_titles_in_stack( $stack_id ) {	
	
	//Set default for the brick titles in this stack
	$brick_titles_in_stack = array(); 
	
	//Get all Bricks in the current Stack - if a stack id has been passed to the URL
	if ( isset( $stack_id ) ){
		
		//Set the args for the new query
		$mp_stacks_args = array(
			'post_type' => "mp_brick",
			'posts_per_page' => -1,
			'meta_key' => 'mp_stack_order_' . $stack_id,
			'orderby' => 'meta_value_num menu_order',
			'order' => 'ASC',
			'tax_query' => array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'mp_stacks',
					'field'    => 'id',
					'terms'    => array( $stack_id ),
					'operator' => 'IN'
				)
			)
		);	
			
		//Create new query for stacks
		$mp_stack_query = new WP_Query( $mp_stacks_args );
		
		//Loop through the stack group		
		while( $mp_stack_query->have_posts() ) : $mp_stack_query->the_post(); 
			
			//Add the title of each brick in this stack to the array. This way, we can easily create links to each brick
			array_push( $brick_titles_in_stack, '#' . sanitize_title( get_the_title() ) );
			
		endwhile;
			
	}
	
	return $brick_titles_in_stack;	
}

/**
 * Function which create the admin notice for the mp_brick editor
 *
 * @since    1.0.0
 * @param    void
 * @return   void
 */
function mp_stacks_support_admin_notice(){
	 
	 global $pagenow;

	  //Only load message if mp_stack_id is set
	 if ( (isset( $_GET['mp_stack_id'] ) ) && ( $pagenow == 'post.php' || $pagenow == 'post-new.php' ) ){
		
	 }
	 else{
		 return; 
	 }
	 
	 $stack_info = get_term( $_GET['mp_stack_id'], 'mp_stacks' );
	 
	 if ( isset( $stack_info->name ) ){
	 
		 ?>
		 <div class="mp-stacks-editor-title-notice updated" style="display:none;">
			<p><?php echo __( 'You are editing a "Brick" in the "Stack" called "' . $stack_info->name . '".', 'mp_stacks'); ?>
			<?php echo __(' Having trouble? Feel free to email us: support@mintplugins.com and we\'ll be glad to help you out!', 'mp_stacks' ); ?></p>
		 </div>
		 <?php
	
	 }
	
}
add_action('admin_notices', 'mp_stacks_support_admin_notice');

/**
 * Function which creates the TIP letting the user know they can double click on a brick
 *
 * @since    1.0.0
 * @param    void
 * @return   void
 */
function mp_stacks_double_click_tip(){
	
	global $pagenow;
	 
	 //Only load message if mp_stack_id is set
	 if ( isset( $_GET['mp_stack_id'] ) && $pagenow == 'post.php'){
		
	 }
	 else{
		 return; 
	 }
	 
	 //Check if this user has dismissed this tip
	 $user_dismissed_tip = get_user_meta( get_current_user_id(), 'mp_stacks_dis_doubleclick_tip', true);
	 
	 if ( $user_dismissed_tip ){
		 return;
	 }
	 
	 $stack_info = get_term( $_GET['mp_stack_id'], 'mp_stacks' );
	 
	 ?>
	 <div class="updated">
        <p><?php echo '<img style="width:10px;" class="mp-stacks-editor-notice-icon" src="' . plugins_url( 'assets/icon-256x256.png', dirname(dirname(__FILE__) ) ) . '" />' . __( 'MP Stacks TIP: You can open this Brick editor at any time by double clicking <em>anywhere</em> on a brick. ', 'mp_stacks' ) . '<a class="mp-stacks-dismiss-double-click button">' . __( 'Hide Tip', 'mp_stacks') . '</a>'; ?>
     </div>
     <?php
	
}
add_action('admin_notices', 'mp_stacks_double_click_tip');

/**
 * Function which adds extra "safe" styles to wp_kses
 *
 * @since    1.0.0
 * @param    array $safe_styles This is an array of the css style names that are 'safe'
 * @return   void
 */
function mp_stacks_wpkses_safe_styles( $safe_styles ){

	array_push( $safe_styles, 'white-space' );
	return $safe_styles;
		
}
add_filter( 'safe_style_css', 'mp_stacks_wpkses_safe_styles' );

/**
 * When we delete a Stack, we need to delete all bricks posts attached to that Stack as well
 *
 * @since    1.0.0
 * @param    int $deleted_stack_term_taxonomy_id The term_taxonomy_id (NOTE: not the term id) of the stack we are deleting
 * @return   void
 */
function mp_stacks_delete_stack( $deleted_stack_term_taxonomy_id ){
	
	$deleted_stack_id = get_term_by( 'term_taxonomy_id', $deleted_stack_term_taxonomy_id, 'mp_stacks' );
	$deleted_stack_id = $deleted_stack_id->term_id;
	
	//Loop through each brick that was in this stack using the meta_key mp_stack_order_' . $deleted_stack_id
	
	//Set the args for the new query
	$mp_stacks_args = array(
		'post_type' => "mp_brick",
		'posts_per_page' => -1,
		'meta_key' => 'mp_stack_order_' . $deleted_stack_id,
		'orderby' => 'meta_value_num menu_order',
		'order' => 'ASC',
	);	
		
	//Create new query for stacks
	$mp_stack_query = new WP_Query( $mp_stacks_args );
	
	//Loop through the stack group		
	if ( $mp_stack_query->have_posts() ) { 
		
		while( $mp_stack_query->have_posts() ) : $mp_stack_query->the_post();
			
			//Delete this brick
			wp_delete_post( get_the_ID(), true );
		
		endwhile;
		
	}
	
}
add_action( 'delete_term_taxonomy', 'mp_stacks_delete_stack' );

/**
 * If we are on an "Add New Brick" or "Edit Brick" page, temporarily set the title to be "Loading Brick..." - we update it later using JS
 *
 * @since    1.0.0
 * @param    void
 * @return   void
 */
function mp_stacks_edit_brick_loading_title(){
	
	global $title;
	
	if ( $title == __( 'Add New Brick', 'mp_stacks' ) || $title == __( 'Edit Brick', 'mp_stacks' ) ){
		$title = __( 'Loading Brick Editor...', 'mp_stacks' );
	}
	
}
add_action( 'admin_head', 'mp_stacks_edit_brick_loading_title' );