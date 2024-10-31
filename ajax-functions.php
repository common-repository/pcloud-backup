<?php
if ( ! defined( 'ABSPATH' ) ) exit;

require_once("lib/pCloud/autoload.php");

add_action('wp_ajax_pcb_create_backup', 'pcb_create_backup_action');
function pcb_create_backup_action()
{

    global $wpdb;

    $choosen_folder_id = (float) $_POST['pcb_choosen_folder'];

    if( empty( $choosen_folder_id ) )
    {
        wp_send_json(array('status' => false, 'message' => __('Please choose folder','pcb')));
        exit;
    }



    $backup_type = sanitize_text_field( $_POST['pcb_backup_type'] );



    $args = array(

        'pcb_backup_type' => $backup_type,

        'pcb_selected_folder' => $choosen_folder_id,

    );



    if($backup_type == 'full')
    {

        $backup_response = pcb_create_full_backup($args);

    }



    if($backup_type == 'database')
    {

        $backup_response = pcb_database_backup($args);

    }



    if($backup_type == 'folder-file')
    {

        $backup_response = pcb_files_full_backup($args);

    }



    if($backup_response == true)
    {

        wp_send_json(array('status' => true, 'message' => __('Backup created successfully.','pcb')));

        exit;

    } else {

        wp_send_json(array('status' => true, 'message' => __('There are some error.','pcb')));

        exit;

    }



    exit();

}


add_action('wp_ajax_pcb_create_set_backup_folder', 'pcb_create_set_backup_folder');
function pcb_create_set_backup_folder()
{

    $fname = $_POST['fname'];

    $parent_folder_id = (float) $_POST['parent_folder_id'];

    if( $parent_folder_id )
    {
        $parent_folder_id = (float) $_POST['parent_folder_id'];

    } else {

        $parent_folder_id = 0;
    }


    if($fname)
    {
        $pcloudFolder = new pCloud\Folder();

        $folderid = $pcloudFolder->create($fname,$parent_folder_id);

        if($folderid)
        {
            update_option('pbcp_backup_folder', $folderid);

            wp_send_json(array('status'=>'success', 'folder_id'=>$folderid, 'message' => __('Folder created and selected successfully.','pcb')));

        } else {
            wp_send_json(array('status'=>'error', 'message' => __('There are some error.','pcb')));

        }

    } else {
        wp_send_json(array('status'=>'error', 'message' => __('Folder name should not blank.','pcb')));
    }

    exit;
}



add_action('wp_ajax_pcb_save_settings', 'pcb_save_settings');
function pcb_save_settings()
{
    global $wpdb;

    $client_id = sanitize_text_field($_POST['pcb_app_key']);
    $app_secret = sanitize_text_field($_POST['pcb_app_secret']);
    $redirect_uri = esc_url($_POST['pcb_redirect_uri']);

    if($client_id == "")
    {
        wp_send_json(array('status'=>false, 'message' => __('Client ID should not blank.','pcb')));
        exit;
    }

    if($app_secret == "")
    {
        wp_send_json(array('status'=>false, 'message' => __('Client secret should not blank.','pcb')));
        exit;
    }

    if($redirect_uri == "")
    {
        wp_send_json(array('status'=>false, 'message' => __('Redirect uri should not blank.','pcb')));
        exit;
    }

    
    update_option('pcb_client_id', $client_id);
    update_option('pcb_client_secret', $app_secret);
    update_option('pcb_redirect_uri', $redirect_uri);

    try {

      $appInfo = new stdClass;

      $appInfo->appKey = $client_id;

      $appInfo->appSecret = $app_secret;

      $appInfo->redirect_uri = $redirect_uri;

      $codeUrl = pCloud\App::getAuthorizeCodeUrl($appInfo);

        wp_send_json(array('status'=>true, 'message' => __('Redirecting....', 'pcb'), 'redirect_url' => $codeUrl));
        exit;

    } catch (Exception $e) {

        wp_send_json(array('status'=>false, 'message' => $e->getMessage()));

        exit;

    }

    exit;

}

add_action('wp_ajax_pcb_get_child_folder', 'pcb_get_child_folder');

function pcb_get_child_folder() 
{
    $folder_id = (float) $_GET['folder_id'];

    $pcloudFolder = new pCloud\Folder();
    $list_folder = $pcloudFolder->getMetadata($folder_id);
    $content = $list_folder->metadata->contents;
    
    $html = '';
    if(!empty($content))
    {
        $html .= '<ul class="p-cloud-inner-list-outer show">';
        foreach ($content as $item) 
        {

            if($item->isfolder == 1)
            {
                $html .="<li class='open-next-li'>

                <a class='pcloud-folder-name pcb-folder-close toggle' href='javascript:void(0)' data-folder_id='".$item->folderid."'>

                <span class='pcb-icon'>+</span>

                {$item->name}

                </a>";



                $html .= '<span class="pcloud-backup-button"><a class="backup_folder button" data-folder_id="'.$item->folderid.'">'.__("Select","pcb").'</a></span><a href="javascript:void(0);" class="new-folder-create" data-folder_id="'.$item->folderid.'" style="display:none">+ '.__("New Folder","pcb").'</a>';
            }
        }
        $html .= '</ul>';
    }

    wp_send_json(array('success' => true, 'html' => $html));
    exit;
}