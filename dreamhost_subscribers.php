<?php
/*
Plugin Name: WP-DreamLists
Plugin URI: http://www.piliavin.com/codes/
Description: THis plugin can manage subscribes throught Dreamhost API
Version: 0.2
Author: Ethan Piliavin
Author URI: http://piliavin.com
*/

/* DHS => Dreamhost Subscribers */
session_start();
require('frontend_interface.php');  // including widget declarations
require('backend_interface.php');

//  instalation plugin
function DHS_init(){
 // add link in admin console panel.
  	add_action('admin_menu', 'DHS_config_page');  
 
 // register function that take care about displaying frontend widget
	  register_sidebar_widget("Dreamhost Subscribes", "DHS_frontend_widget");   
 
 // register function that take care about displaying backend control of this widget	  
    register_widget_control("Dreamhost Subscribes", "DHS_backend_widget_control");

 // add database table for store mails
    DHS_table_for_mails();
            
}

function DHS_table_for_mails() {
  global $wpdb;

  $table_name = $wpdb->prefix . "dhs_mails";
  if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){
        $sql = "CREATE TABLE " . $table_name . " (
        	  id mediumint(9) NOT NULL AUTO_INCREMENT,
        	  html_source text DEFAULT NULL,
            mail_subject varchar(255) DEFAULT NULL,
            mail_content text DEFAULT NULL,
            status varchar(10) DEFAULT 'sent',
            created_at datetime,
        	  modified_at datetime,
        	  UNIQUE KEY id (id)
        	);";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
  }

}

function DHS_config_page(){
	//if ( function_exists('add_submenu_page') )
	//	add_submenu_page('options-general.php', 'Manage Dreamhost subscribers.', 'Manage Dreamhost subscribers.', 'manage_options', 'dreamhost_subscribers_list', 'DHS_manage');
        
    add_menu_page('Manage Lists', 'Subscribers', 'manage_options', 'dreamhost_subscribers_list', 'DHS_manage');
    add_submenu_page('dreamhost_subscribers_list', 'Send Message', 'Send Message', 'manage_options', 'dreamhost_sendmessage', 'DHS_sendmessage');
    add_submenu_page('dreamhost_subscribers_list', 'Mails Control', 'Mails Control', 'manage_options', 'dreamhost_mails_control', 'DHS_mails_control');
    add_submenu_page('dreamhost_subscribers_list', 'Configure', 'Configure', 'manage_options', 'dreamhost_configure', 'DHS_configure');
    
}

add_action('init', 'DHS_init');

if (isset($_GET['page']) && $_GET['page'] == 'dreamhost_subscribers') { 
    add_action( 'init', 'DHS_subscribers' );
}
if (isset($_GET['page']) && $_GET['page'] == 'dreamhost_sendmessage') { 
    add_action( 'admin_head', 'wp_tiny_mce' );
}

?>