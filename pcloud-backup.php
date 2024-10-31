<?php

/**

 * Plugin Name: pCloud Backup

 * Description: With pCloud Backup plugin for WordPress you can create backup to your pCloud account easily ! Get the <a href="https://www.josselynjayant.fr/pcloud-backup-wordpress-plugin/">PRO VERSION</a> to create scheduled backups !

 * Version: 1.0.1

 * Author: Josselyn Jayant

 * Author URI: https://www.josselynjayant.fr

 * Text Domain: pcb

 */

if ( ! defined( 'ABSPATH' ) ) exit;

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

/*
* Deactivate pcloud pro plugin when deactivate free version.
*/
register_deactivation_hook( __FILE__, 'deactivate_pcloud_pro_version' );
function deactivate_pcloud_pro_version()
{
	deactivate_plugins( '/pcloud-backup-pro/pcloud-backup-pro.php' );
}


/*LOAD LANAGUGE LOCAL*/
add_action( 'init', 'pcb_lanaguage_load_textdomain' );
function pcb_lanaguage_load_textdomain() {

  load_plugin_textdomain( 'pcb', false, basename( dirname( __FILE__ ) ) . '/languages' ); 

}

/*
* Pcloud admin menu settings.
*/
add_action('admin_menu', 'pcb_menu_pages');
function pcb_menu_pages(){

  	add_menu_page('pCloud Backup', 'pCloud Backup', 'manage_options', 'pcb-api-settings', 'pcb_api_settings','dashicons-backup' );

}  

/*
* Function is used to render settings template.
*/
function pcb_api_settings()
{
	require_once (plugin_dir_path(__FILE__). '/templates/settings.php');
}

/*
* Filter added for multiple settings tabs.
*/
add_filter( 'pcb_settings_tabs_array', 'pcb_settings_tab_callback',10 );
function pcb_settings_tab_callback( $settings_tabs ) {
    $settings_tabs['pcb_settings'] = array('label' => __( 'API Settings', 'pcb' ), 'callback' => 'pcb_tab_api_settings');
    $settings_tabs['pcb_backup_settings'] = array('label' => __( 'Create Backup', 'pcb' ), 'callback' => 'pcb_tab_backup_settings');
    return $settings_tabs;
}

/*
* Callback function for api settings tab. 
*/
function pcb_tab_api_settings()
{
	require_once (plugin_dir_path(__FILE__). '/templates/pcb_api_settings.php');
}

/*
* Callback function for create backup settings tab. 
*/
function pcb_tab_backup_settings()
{
	require_once (plugin_dir_path(__FILE__). '/templates/pcb_backup_settings.php');
}

/*
* Include require css and js files.
*/
add_action( 'admin_enqueue_scripts', 'pcb_js_css_init' );
function pcb_js_css_init()
{

  wp_enqueue_style( 'pcb-admin-css', plugins_url( '/assets/css/admin-styles.css', __FILE__ ), false, '1.0' );

  wp_enqueue_script( 'pcb-backup', plugins_url( '/assets/js/backup.js', __FILE__ ), array('jquery'), '1.0' );

  wp_localize_script( 'pcb-backup', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

}

function pcb_save_token() {
    global $pagenow, $wpdb;

    if ( 'admin.php' === $pagenow && isset($_GET['code']) &&  !empty($_GET['code']) && isset($_GET['page']) && $_GET['page'] == 'pcb-api-settings')
    { 
        
            if (isset($_GET["code"])) 
            { 
                  $table = $wpdb->prefix.'pcb_config';
                  $user_id = get_current_user_id();

                  $appInfo = pcb_app_info();
                  $appInfo->code = $_GET["code"];
                  
                  try {

                    $access_token = pCloud\App::getToken($appInfo, '');
                    update_option('pcb_access_token', $access_token);
                    wp_redirect(admin_url('admin.php?page=pcb-api-settings'));
                    exit;
 
                  } catch (Exception $e) {
                    wp_redirect(admin_url('admin.php?page=pcb-api-settings'));
                    exit;
                  }
                  
            } else {

                // throw new Exception("\"code\" not found");
                wp_redirect(admin_url('admin.php?page=pcb-api-settings'));
                exit;
            }

          
    }
}
add_action( 'admin_init', 'pcb_save_token' );


/*
* Require functions and ajax functions files.
*/
require_once (plugin_dir_path(__FILE__). '/functions.php');
require_once (plugin_dir_path(__FILE__). '/ajax-functions.php');