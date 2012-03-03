<html>
    <head>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url().'assets/js/jfmfs/jquery.facebook.multifriend.select.js';?>"></script>
        <link rel="stylesheet" href="<?php echo base_url().'assets/js/jfmfs/jquery.facebook.multifriend.select-list.css';?>" />
        <style>
            body {
                background: #fff;
                color: #333;
                font: 11px verdana, arial, helvetica, sans-serif;
            }
            a:link, a:visited, a:hover {
                color: #666;
                font-weight: bold;
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <?php echo $fb_root;?>
        <script>
                function fbcallback(){
                        jfmfs_init();
                }
                 
                function jfmfs_init() {
                  FB.api('/me', function(response) {
                      $("#jfmfs-container").jfmfs({ max_selected: 5, max_selected_message: "{0} of {5} selected"});
                      $("#loading").hide();
                      $("#show-friends").show();
                  });
                }              

                $("body").on('click',"#jfmfs-container", function() {console.log($(this).data('jfmfs'));
                        $("#tagged").val($(this).data('jfmfs').getSelectedIds().join(','));
                });
        </script>
        <div>        
        <?php  

        $attributes = array('class' => '', 'id' => '');
        echo form_open('tag/execute/'.$app_install_id, $attributes); ?>

        <p>
                <label for="tagged">Facebook uids <span class="required">*</span></label>
                <?php echo form_error('tagged'); ?>
                <br /><input id="tagged" type="text" name="tagged" value="<?php echo set_value('tagged'); ?>"  />
        </p>

        <p>
                <?php echo form_submit( 'submit', 'Tag'); ?>
        </p>

        <?php echo form_close(); ?>
        </div>
        <div>
              <div id="loading" style="">
                  Loading Friend List
              </div>

              <div>
                  <div id="jfmfs-container"></div>
              </div>
        </div>
    </body>
</html>


