<?php
/*
Plugin Name: Change Domain 
Plugin URI: https://github.com/MagicPressNet/magicpress-domain-change
Version: 1.1
Description: The Domain Change by MagicPress allows you to easily change your website domain or protocol.
Author: MagicPress
Author URI: https://magicpress.net/
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function domain_change_controller() {
	// Output the response
	$err=array();
	$currentDomain=(!isset($_POST['current_domain']))?get_site_url():$_POST['current_domain'];
	$newDomain=(!isset($_POST['new_domain']))?'':$_POST['new_domain'];

	if(is_array($_POST) && count($_POST)>0){
		if (!filter_var($_POST['current_domain'], FILTER_VALIDATE_URL)) {
 		    $err['current_domain']=1;
		}

		if (!filter_var($_POST['new_domain'], FILTER_VALIDATE_URL)) {
			$err['new_domain']=1;
		}
	}
	
	if(is_array($_POST) && count($_POST)>0 && count($err)==0 ){
		include_once('search-replace.php');		
	}else{
		include_once('form.php');	
	} 
}

function domain_change_menus() {
	if (function_exists('add_submenu_page')) {
		add_management_page('Change your domain','Change domain','manage_options',__FILE__,'domain_change_controller');
	}
}

function domain_change_plugin_action_links( $links, $file ) {
	if ( $file != plugin_basename( __FILE__ ))
		return $links;

	array_unshift( $links, '<a href="'.domainChangePage().'">Let\'s change your domain now!</a>' );

	return $links;
}

function domainChangePage(){
	return 'tools.php?page='.plugin_basename(__FILE__);
}

function domainChangePluginPath(){
	return plugin_dir_path( __FILE__ );
}

// Whitelist our options
//add_filter('whitelist_options', 'wp_mail_smtp_whitelist_options');

// Add the create pages options
add_action('admin_menu','domain_change_menus');

// Add an activation hook for this plugin
//register_activation_hook(__FILE__,'wp_mail_smtp_activate');

// Adds "Settings" link to the plugin action page
add_filter( 'plugin_action_links', 'domain_change_plugin_action_links',10,2);

?>