<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<form id="pcbCreateBackup" method="post">

      <div class="pcb-choose-folder">

       <h1><?php _e('Choose Backup Folder', 'pcb'); ?></h1>

       <?php pcb_list_folders(); ?>

      </div>



      <div class="pcb-backup-type"> 

        <h1><?php _e('Choose Backup Type', 'pcb'); ?></h1>

        <select class="pcb-backup-type select-week" name="pcb_backup_type" required="required">

          <option value="full"><?php _e('All(Database &amp; Site)', 'pcb'); ?></option>

          <option value="database"><?php _e('Database', 'pcbp'); ?></option>

          <option value="folder-file"><?php _e('Folders &amp; File', 'pcb'); ?></option>

        </select>

      </div>



      <div class="pcb-btn-container">

          <button class="pcb-button"><?php _e('Backup', 'pcb'); ?></button>

      </div>

      <input type="hidden" name="pcb_choosen_folder" id="pcb_choosen_folder">

      <input type="hidden" name="action" id="pcb_create_backup" value="pcb_create_backup">

    </form>