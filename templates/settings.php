<?php 
if ( ! defined( 'ABSPATH' ) ) exit;
$settings = apply_filters( 'pcb_settings_tabs_array',array()); 
?>

<div class="p-cloud-outer">



  <ul id="tabs">

      
      <?php $tabcount = 1; foreach ($settings as $key => $tab) { ?>
    
        <li><a id="tab<?php echo $tabcount; ?>"><?php echo $tab['label']; ?></a></li>

      <?php $tabcount++; } ?>

  </ul>

        
  <?php $tabc = 1; foreach ($settings as $key => $tab) { ?>

    <div class="container" id="tab<?php echo $tabc; ?>C">
      <?php if ( isset( $tab['callback'] ) ) { call_user_func( $tab['callback'], $key, $tab ); } ?>
    </div>
  <?php $tabc++; } ?>

  
    </div>

    <div class="all-folder-listing-outer" style="display: none;">

      <label class="folder-listing-heading"><?php _e('Select a folder to create backup', 'pcb'); ?></label>

      <div class="pcloud-all-folders"></div>



       <input type="hidden" name="single_file_name" class="single_file_name">

       <input type="hidden" name="next_folder_path" class="next_folder_path">

       <input type="hidden" name="file_path" class="file_path">

       <input type="hidden" name="shedule_type" class="shedule_type">

       <input type="hidden" name="shedule_time" class="shedule_time">

    </div>

    <div class="folder-loader" style="display:none;">

      <img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'assets/img/loading-bar.gif'; ?>">

    </div>

    <div class="form-group progressbar" style="display:none;">

        <img class="status-bar" src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'assets/img/prosess-bar.gif'; ?>">

    </div>

    <div class="uploaded-msg"></div>
    <script type="text/javascript">
      var jqtexts = {
        createText:"<?php _e('Create','pcb'); ?>", 
        selectText:"<?php _e('Select','pcb'); ?>", 
        selectedText:"<?php _e('Selected','pcb'); ?>",
        newfolderText:"<?php _e('New Folder','pcb'); ?>"
      };
    </script>
