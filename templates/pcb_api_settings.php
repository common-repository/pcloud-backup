<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
?>
<div class="wrap">

    <h2><?php _e('API Settings', 'pcb'); ?></h2>

    <form id="pcb_api_settings" method="post" autocomplete="off">

        <p><strong><?php _e('Client ID', 'pcb'); ?>:</strong><br />

            <input type="text" name="pcb_app_key" required="required" value="<?php echo esc_attr( get_option('pcb_client_id') ); ?>" />

        </p>

        <p><strong><?php _e('Client Secret', 'pcb'); ?>:</strong><br />

            <input type="password" name="pcb_app_secret" required="required" value="<?php echo esc_attr( get_option('pcb_client_secret') ); ?>" />

        </p>

        <p><strong><?php _e('Redirect Uri', 'pcb'); ?>:</strong><br />

            <input type="text" name="pcb_redirect_uri" required="required" value="<?php echo admin_url('admin.php?page=pcb-api-settings'); ?>" />

        </p>

        <input type="hidden" name="action" value="pcb_save_settings">

        <p><input type="submit" name="Submit" value="Save" class="p-cloud-sbmit-btn" /></p>

    </form>

</div>

<div class="api-informations">

  <label><?php _e('Find your API Credentials by following these steps', 'pcbp'); ?></label>

  <div class="step-first steps">

    <label><?php _e('Step 1', 'pcb'); ?>:</label><p><?php _e('Visit', 'pcb'); ?> https://docs.pcloud.com/my_apps/ </p>

  </div>

  <div class="step-two steps">

    <label><?php _e('Step 2', 'pcb'); ?>:</label><p><?php _e('Click On Details tab and get your Client ID and Client secret', 'pcb'); ?></p>

  </div>

  <div class="step-three steps">

    <label><?php _e('Step 3', 'pcb'); ?>:</label><p><?php _e('The Redirect Uri is :', 'pcb'); echo admin_url('admin.php?page=pcb-api-settings'); ?></p>

    <p><?php _e('Paste this url into Redirect uri on this page and also paste on', 'pcb'); ?> https://docs.pcloud.com/my_apps/ -> <?php _e('Settings tabs -> Redirect URIs text fields.', 'pcb'); ?>
    </p>

  </div>

</div>