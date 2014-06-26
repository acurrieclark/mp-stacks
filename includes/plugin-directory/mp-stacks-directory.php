<?php
/**
 * Plugin Directory Class for the mp_stacks Plugin by Mint Plugins
 * http://mintplugins.com/doc/plugin-directory-class/
 */
function mp_stacks_plugin_directory(){
	
	$args = array (
		'parent_slug' => 'mp-stacks-about',
		'page_title' => 'Add-On Shop',
		'slug' => 'mp_stacks_plugin_directory',
		'directory_list_url' => 'https://mintplugins.com/repo-group/mp-stacks/',
		'plugin_success_link' => add_query_arg( array('page' => 'mp_stacks_plugin_directory' ), admin_url('admin.php') )
	);
	
	new MP_CORE_Plugin_Directory( $args );
}
add_action( '_admin_menu', 'mp_stacks_plugin_directory' );