<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/*
* function for get pcloud folders.
*/
function pcb_list_folders()
{
   
    
    $appinfo = pcb_app_info();

    try {
    if( !empty(esc_attr( get_option('pcb_client_id') )) && !empty(esc_attr( get_option('pcb_client_secret') )) )
    {

        $pcloudFolder = new pCloud\Folder();



        $content = $pcloudFolder->getContent(0);



        if( empty($content) )

        {

            echo '<ul class="p-cloud-list-main-outer schedule-backup-folder" style="display:block">

                    <li class="open-next-li">

                        <div class="new-folder-outer">

                            <input type="text" class="backup-with-new-folder" data-folder_id="0">

                            <a class="backup-in-newfolder button">'.__('Create & Select', 'pcb').'</a>

                        </div>

                    </li>

                </ul>';



        } else {

           appendChildFolderPcbp(0, $pcloudFolder); 

        }
    } else {
        _e('Please configure api settings', 'pcb');
    }
    } catch(Exception $e){
        _e($e->getMessage(), 'pcb');
    }

}


/*
* function for get pcloud folders in loop.
*/
function appendChildFolderPcbp($folderid, $pcloudFolder, $level=0) 

{

    if($level > 0 )

    {

       echo '<ul class="p-cloud-inner-list-outer" style="display:none">'; 

   } else {

        

        echo '<ul class="p-cloud-list-main-outer schedule-backup-folder" style="display:block">'; 

   }

    



    $pcloudFolder = new pCloud\Folder();



    // $content = $pcloudFolder->getContent($folderid);
    $list_folder = $pcloudFolder->getMetadata($folderid);
    $content = $list_folder->metadata->contents;

    if(!empty($content))

    {



        foreach ($content as $item) 

        {

            if($item->isfolder == 1)

            {

                echo "<li class='open-next-li'>

                <a class='pcloud-folder-name pcb-folder-close toggle' href='javascript:void(0)' data-folder_id='".$item->folderid."'>

                <span class='pcb-icon'>+</span>

                {$item->name}

                </a>

                ";



                echo '<span class="pcloud-backup-button"><a class="backup_folder button" data-folder_id="'.$item->folderid.'">'.__("Select","pcb").'</a></span><a href="javascript:void(0);" class="new-folder-create" data-folder_id="'.$item->folderid.'" style="display:none">+ '.__("New Folder","pcb").'</a>';



                if ($item->parentfolderid > 0)
                {

                   // appendChildFolderPcbp($item->folderid, $pcloudFolder, $level+1);

                }

            }



        }



        echo "</li></ul>";

    } 

}


/*
* function for create full site backup on pcloud.
*/
function pcb_create_full_backup($data)

{

    if( !empty( $data['pcb_selected_folder'] ) ){



        $folderid = $data['pcb_selected_folder'];


        $path_info = wp_upload_dir();



        wp_mkdir_p($path_info['basedir'] . '/db-backup');



        $WPDBFileName = 'databse';



        $SQLfilename = $WPDBFileName . '.sql';



        $filename = $WPDBFileName . '.zip';


        $handle = fopen($path_info['basedir'] . '/db-backup/' . $SQLfilename, 'w');



        fwrite($handle, pcb_mysql_backup());



        fclose($handle);





        $dir = ABSPATH;



        $folder_name = 'Backup-'.date("Y-m-d H:i:s", current_time( 'timestamp', 0 ));



        $today = date("F j, Y, g:i a");



        $zip_file = ABSPATH. $folder_name.'.zip';



        $rootPath = realpath($dir);



        $zip = new ZipArchive();



        $zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);



        $files = new RecursiveIteratorIterator(



            new RecursiveDirectoryIterator($rootPath),



            RecursiveIteratorIterator::LEAVES_ONLY



        );

        

        

        foreach ($files as $name => $file)

        {

        

            if (!$file->isDir())

            {

                

                $filePath = $file->getRealPath();



                $relativePath = substr($filePath, strlen($rootPath) + 1);



                $zip->addFile($filePath, $relativePath);



            }



        }



        

        $zip->addFile($path_info['basedir'] .'/db-backup/' . $SQLfilename, $SQLfilename);



        $zip->close();



        $folder = 'Full-backup - '.$today;



        $pcloudFile = new pCloud\File();



        $fileMetadata = $pcloudFile->upload($zip_file, $folderid);



        if (file_exists($zip_file))

        {

            chmod($zip_file, 0644);

            unlink($zip_file);

        }



        if (file_exists($path_info['basedir']."/db-backup/".$SQLfilename))

        {



            chmod($path_info['basedir']."/db-backup/".$SQLfilename, 0644);



            unlink($path_info['basedir']."/db-backup/".$SQLfilename);



        }



       

        if($fileMetadata)

        {
            return true;

        } else {

            return false;

        }



    }



}


/*
* function for create site files backup on pcloud.
*/
function pcb_files_full_backup($data) 

{

    if( !empty( $data['pcb_selected_folder'] ) )

    {



        $folderid = $data['pcb_selected_folder'];



        $dir = ABSPATH;



        $folder_name = 'Full Backup-'.date('Y-m-d H:i:s');



        $zip_file = ABSPATH. $folder_name.'.zip';



        $rootPath = realpath($dir);



        $zip = new ZipArchive();



        $zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);



        $files = new RecursiveIteratorIterator(



        new RecursiveDirectoryIterator($rootPath),



        RecursiveIteratorIterator::LEAVES_ONLY



        );

            

            

        foreach ($files as $name => $file)

        {

            if (!$file->isDir())

            {

                

                $filePath = $file->getRealPath();



                $relativePath = substr($filePath, strlen($rootPath) + 1);



                $zip->addFile($filePath, $relativePath);



            }



        }

            

        $zip->close();



        $pcloudFile = new pCloud\File();



        $fileMetadata = $pcloudFile->upload($zip_file, $folderid);



        if (file_exists($zip_file))

        {

            chmod($zip_file, 0644);



            unlink($zip_file);

        }



        if($fileMetadata)

        {

            return true;

        } else {

            return false;

        }



    }



}


/*
* function for create database backup on pcloud.
*/
function pcb_database_backup($data)

{



    if( !empty( $data['pcb_selected_folder'] ) )

    {



        $folderid = $data['pcb_selected_folder'];


        $path_info = wp_upload_dir();



        wp_mkdir_p($path_info['basedir'] . '/db-backup');



        $WPDBFileName = 'Database-'.date("Y-m-d H:i:s", current_time( 'timestamp', 0 ));



        $SQLfilename = $WPDBFileName . '.sql';



        $filename = $WPDBFileName . '.zip';



        $mySqlDump = 1;



        if ($mySqlDump == 1) 

        {



            $handle = fopen($path_info['basedir'] . '/db-backup/' . $SQLfilename, 'w');



            fwrite($handle, pcb_mysql_backup());



            fclose($handle);



        }



        $arcname=$path_info['basedir'] . '/db-backup/' . $WPDBFileName . ".zip";



        if (class_exists('ZipArchive')) 

        {



            error_log("Class ZipArchive");



            $zip = new ZipArchive;



            $zip->open($arcname, ZipArchive::CREATE);



            $zip->addFile($path_info['basedir'] .'/db-backup/' . $SQLfilename, $SQLfilename);



            $zip->close();



            if (file_exists($path_info['basedir']."/db-backup/".$SQLfilename))

            {

                chmod($path_info['basedir']."/db-backup/".$SQLfilename, 0644);



                unlink($path_info['basedir']."/db-backup/".$SQLfilename);

            }



        }



        $today = date("F j, Y, g:i a");



        $folder = 'Database - '.$today;



        $pcloudFile = new pCloud\File();



        $fileMetadata = $pcloudFile->upload($arcname, $folderid);



        if (file_exists($arcname))

        {

            chmod($arcname, 0644);

            unlink($arcname);

        }



        if($fileMetadata)

        {

            return true;

        } else {

            return false;

        }

    }



}


/*
* function for get database tables.
*/
function pcb_mysql_backup()
{

    global $wpdb;



    $tables = $wpdb->get_col('SHOW TABLES');



    $output = '';



    foreach ($tables as $table) {



        $result = $wpdb->get_results("SELECT * FROM {$table}", ARRAY_N);



        $row2 = $wpdb->get_row('SHOW CREATE TABLE ' . $table, ARRAY_N);



        $output .= "\n\n" . $row2[1] . ";\n\n";



        for ($i = 0; $i < count($result); $i++) {



            $row = $result[$i];



            $output .= 'INSERT INTO ' . $table . ' VALUES(';



            for ($j = 0; $j < count($result[0]); $j++) {



                $row[$j] = $wpdb->_real_escape($row[$j]);



                $output .= (isset($row[$j])) ? '"' . $row[$j] . '"' : '""';



                if ($j < (count($result[0]) - 1)) {



                    $output .= ',';



                }



            }



            $output .= ");\n";



        }



        $output .= "\n";



    }



    $wpdb->flush();



    return $output;



}


/*
* function for return api info.
*/
function pcb_app_info()
{
    $appInfo = new stdClass();
    $appInfo->appKey = esc_attr( get_option('pcb_client_id') );
    $appInfo->appSecret = esc_attr( get_option('pcb_client_secret') );
    $appInfo->access_token = esc_attr( get_option('pcb_access_token') );
    $appInfo->redirect_uri = admin_url('admin.php?page=pcb-api-settings');
    return $appInfo;
}

function pcb_app_credentials()
{
    $appInfo['appKey'] = esc_attr( get_option('pcb_client_id') );
    $appInfo['appSecret'] = esc_attr( get_option('pcb_client_secret') );
    $appInfo['access_token'] = esc_attr( get_option('pcb_access_token') );
    $appInfo['redirect_uri'] = admin_url('admin.php?page=pcb-api-settings');
    return $appInfo;

}