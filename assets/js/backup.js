jQuery(document).ready(function() {



    jQuery('#tabs li a:not(:first)').addClass('inactive');

    jQuery('.container').hide();

    jQuery('.container:first').show();



    jQuery('#tabs li a').click(function(){

          var t = jQuery(this).attr('id');

          if(jQuery(this).hasClass('inactive'))

          { //this is the start of our condition 

            jQuery('#tabs li a').addClass('inactive');           

            jQuery(this).removeClass('inactive');



            jQuery('.container').hide();

            jQuery('#'+ t + 'C').fadeIn('slow');

          }

    });



    jQuery("#pcbCreateBackup").submit(function(event) {

        /* stop form from submitting normally */

        event.preventDefault();

        jQuery(".folder-loader").css('display','block');

        jQuery.ajax({   

            url: ajax_object.ajaxurl,

            type: 'POST',

            data: jQuery(this).serialize(),

            dataType: 'json',

            success: function(response)

            {

                jQuery(".folder-loader").hide();

                if(response.status == true)

                {

                  alert(response.message);

                  // jQuery("#pcbpscheduleTable tbody").html(response.schdule_table);

                } else {

                  alert(response.message);

                }

            }

        });

    });



    jQuery(document).on('click','.backup_folder', function(){

        var folder_id = jQuery(this).data('folder_id');

        jQuery(this).text(jqtexts.selectedText);

        jQuery("#pcbp_selected_folder").val(folder_id);

        jQuery("#pcb_choosen_folder").val(folder_id);

    });



    jQuery(document).on('click','.toggle', function(e) {

        e.preventDefault();

        var $this = jQuery(this);


        if ($this.next().next().next().hasClass('show')) {

            $this.next().next().next().removeClass('show');

            $this.next().next().next().slideUp(350);

            $this.find('.pcb-icon').text('+');

            $this.removeClass('pcb-folder-open');
        } else {
            jQuery(".folder-loader").css('display','block');

            jQuery.ajax({   

                url: ajax_object.ajaxurl,

                type: 'GET',

                data: { "folder_id" : jQuery(this).data('folder_id'), 'action' : 'pcb_get_child_folder' },

                dataType: 'json',

                success: function(response)
                {
                   jQuery(".folder-loader").hide();
                   if(response.success == true)
                   {
                      $this.parent().find('.p-cloud-inner-list-outer').remove();
                      $this.parent().append(response.html);

                   }
                }
            });

            $this.parent().parent().find('li .inner').removeClass('show');

            $this.parent().parent().find('li .inner').slideUp(350);

            $this.next().next().next().toggleClass('show');

            $this.next().next().next().slideToggle(350);

            $this.find('.pcb-icon').text('-');

            $this.addClass('pcb-folder-open');

            $this.next().next().show();

        }

    });



    jQuery(document).on('click','.new-folder-create',function(){

        var backup_type = jQuery(this).val();

        var folderid = jQuery(this).data('folder_id');

        if ( jQuery('.new-folder-outer').length == 0 )

        {

            jQuery('<div class="new-folder-outer"><input type="text" class="backup-with-new-folder" data-folder_id="'+folderid+'" ><a href="javascript:void(0);" class="backup-in-newfolder" id="'+backup_type+'">'+jqtexts.createText+'</a><span class="remove-new-folder">X</span></div>').insertAfter(this);

        }

  

        jQuery('.new-folder-create').attr('disabled','disabled');

        jQuery('.create-backup').attr('disabled','disabled');

    });



    jQuery(document).on('click','.remove-new-folder',function(){

        jQuery(this).parent().parent().find('.new-folder-create').css('display','block');

        jQuery(this).parent().remove();

        jQuery('.new-folder-create').removeAttr('disabled');

        jQuery('.create-backup').removeAttr('disabled');

    });



    jQuery(document).on("click",'.backup-in-newfolder',function(){

        var folder_name = jQuery(this).parent().find('.backup-with-new-folder').val();

        var parent_id = parseInt(jQuery(this).parent().find('.backup-with-new-folder').data('folder_id'));

        $this = jQuery(this);

        jQuery(".folder-loader").css('display','block');

        jQuery.ajax({   

          url: ajax_object.ajaxurl,

          type: 'POST',

          data: {'action':'pcb_create_set_backup_folder', 'fname': folder_name, 'parent_folder_id': parent_id},

          dataType: 'json',

          success: function(response)

          {

            

            if(response.status == 'success')

            {

              jQuery('#pcbp-selected-fname').text(folder_name);

              if(parent_id > 0)

              {

                if($this.parent().parent().find('.p-cloud-inner-list-outer').length > 0)

                {

                  $this.parent().parent().find('.p-cloud-inner-list-outer').append('<li class="open-next-li">'+

                  '<a class="pcloud-folder-name pcb-folder-close toggle" href="javascript:void(0)">'+

                  '<span class="pcb-icon">+</span>'+folder_name+'</a>'+

                  '<span class="pcloud-backup-button">'+

                  '<a class="backup_folder button" data-folder_id="'+response.folder_id+'">'+jqtexts.selectText+'</a></span>'+

                  '<button class="new-folder-create" data-folder_id="'+response.folder_id+'" style="">'+

                  '+ '+jqtexts.newfolderText+'</button></li>'

                  );

                } else {

                  $this.parent().parent().append('<ul class="p-cloud-inner-list-outer"><li class="open-next-li">'+

                  '<a class="pcloud-folder-name pcb-folder-close toggle" href="javascript:void(0)">'+

                  '<span class="pcb-icon">+</span>'+folder_name+'</a>'+

                  '<span class="pcloud-backup-button">'+

                  '<a class="backup_folder button" data-folder_id="'+response.folder_id+'">'+jqtexts.selectText+'</a></span>'+

                  '<button class="new-folder-create" data-folder_id="'+response.folder_id+'" style="">'+

                  '+ '+jqtexts.newfolderText+'</button></li></ul>'

                  );

                }

                

              } else {

                $this.parent().parent().html(

                  '<a class="pcloud-folder-name pcb-folder-close toggle" href="javascript:void(0)">'+

                  '<span class="pcb-icon">+</span>'+folder_name+'</a>'+

                  '<span class="pcloud-backup-button">'+

                  '<a class="backup_folder button" data-folder_id="'+response.folder_id+'">'+jqtexts.selectText+'</a></span>'+

                  '<button class="new-folder-create" data-folder_id="'+response.folder_id+'" style="">'+

                  '+ '+jqtexts.newfolderText+'</button></li>'

                  );

              }

              $this.parent().remove();

              jQuery(".folder-loader").css('display','none');

            } else {

              jQuery(".folder-loader").css('display','none');

              alert(response.message);

            }

                    

          }

        });

    });



    jQuery("#pcb_api_settings").submit(function(event) {

        /* stop form from submitting normally */

        event.preventDefault();

        jQuery(".folder-loader").css('display','block');

        jQuery.ajax({   

            url: ajax_object.ajaxurl,

            type: 'POST',

            data: jQuery(this).serialize(),

            dataType: 'json',

            success: function(response)

            {

                jQuery(".folder-loader").hide();

                if(response.status == true)

                {

                  window.location.href = response.redirect_url;

                } else {

                  alert(response.message);

                }

            }

        });

    })

});