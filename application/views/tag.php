<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap.css');?>" />
        <link rel="stylesheet" href="<?php echo base_url('assets/css/responsive.css');?>" />
        <link rel="stylesheet" href="<?php echo base_url('assets/css/main.css'); ?>" />
        <link rel="stylesheet" href="<?php echo base_url('assets/js/jfmfs/jquery.facebook.multifriend.select-list.css');?>" />
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url().'assets/js/jfmfs/jquery.facebook.multifriend.select.js';?>"></script>
        <style type="text/css">
            #img_1 {top:66px; left:131px}
            #img_2 {top:66px; left:276px}
            #img_3 {top:203px; left:58px}
            #img_4 {top:203px; left:204px}
            #img_5 {top:203px; left:351px}
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
                $("#jfmfs-container").jfmfs({ max_selected: 1, max_selected_message: "{0} of {1} selected"});
                $("#loading").hide();
                $("#show-friends").show();
            });
        }  

        $(function(){
            $("body").on('click',"#img_1, #img_2, #img_3, #img_4, #img_5", function(){
                $('.friends-selector-popup').show();
                var idStr = $(this).attr('id');
                var imgNo = idStr.charAt( idStr.length-1 );
                bind_jfmfs(imgNo);
            });
            $('#submit_form').click(upload);

            function bind_jfmfs(imgNo) {            
                $("body").off('click',"#jfmfs-container").on('click',"#jfmfs-container", function(event) {
                    // console.log($(this).data('jfmfs'));
                    $("#tagged").val($(this).data('jfmfs').getSelectedIds().join(','));
                    $(this).data('jfmfs').clearSelected();
                });
                $("body").off('click','#jfmfs-container .jfmfs-friend')
                    .on('click','#jfmfs-container .jfmfs-friend', function(event){
                    // console.log($(this).attr('id'), imgNo);
                    var facebookId = $(this).attr('id');
                    var duplicated = getSelectedIds().join(',').indexOf(facebookId) != -1;
                    if(!duplicated) {
                        $('#img_'+imgNo).data('facebookId',facebookId).html($('<img/>').attr('src', 'https://graph.facebook.com/'+facebookId+'/picture'));
                        $('.friends-selector-popup').hide();
                    } else {
                        alert('Duplicated');
                    }
                });

                $(".friends-selector-popup").hover(
                  function () {
                    $(this).addClass("hover");
                  },
                  function () {
                    $(this).removeClass("hover");
                  }
                );
                function hidePopupEvent() {
                    var fsPopup = $('.friends-selector-popup');
                    var popupVisible = fsPopup.is(':visible');
                    if(popupVisible && !fsPopup.hasClass('hover')){
                        fsPopup.hide();
                        $("body").unbind('mouseup');
                    } else if(popupVisible) {
                        $("body").one('mouseup', hidePopupEvent);
                    }
                }
                $("body").one('mouseup', hidePopupEvent);
            }

            function getSelectedIds() {
                var img = [];
                img[0] = $('#img_1').data('facebookId');
                img[1] = $('#img_2').data('facebookId');
                img[2] = $('#img_3').data('facebookId');
                img[3] = $('#img_4').data('facebookId');
                img[4] = $('#img_5').data('facebookId');
                return img;
            }
            
            function upload() {
                var img = getSelectedIds();
                $("#tagged").val(img.join(','));
                // console.log(img);
                var allTagged = true;
                for (var i = img.length - 1; i >= 0; i--) {
                    if(!img[i]){
                        allTagged = false;
                    }
                };
                if(allTagged) {
                    $('.overlay').show();
                    var formUrl = $('form#tag_form').attr('action');
                    $.post(formUrl,{
                        tagged: $('#tagged').val()
                    },
                    function(result){
                        $('.overlay').hide();
                        if(result.success == true) {
                            // console.log(result);
                            $('.popup-success').show();
                        } else {
                            //error
                            console.log(result);
                        }
                    },'json');
                } else {
                    alert('Please tag 5 people');
                }
                return false;
        }

        });
        </script>


        <div class="body-wrapper">
                <div class="like-us">
                        <img src="<?php echo base_url('assets/images/like-us.png');?>" alt="">
                </div>
                <div class="header-container">
                        <a class="button-detail" target="_top" href="http://www.fashionisland.co.th/activities/detail/250"></a>
                </div>
                <div class="content-container">


                    <div class="tagger-container">
                        <?php $attributes = array('class' => '', 'id' => 'tag_form');
                        echo form_open('tag/execute/'.$app_install_id, $attributes); ?>

                        <div class="tag-pic" id="img_1"></div>
                        <div class="tag-pic" id="img_2"></div>
                        <div class="tag-pic" id="img_3"></div>
                        <div class="tag-pic" id="img_4"></div>
                        <div class="tag-pic" id="img_5"></div>

                        <?php echo form_error('tagged'); ?>
                        <input id="tagged" type="hidden" name="tagged" value="<?php echo set_value('tagged'); ?>"  />

                        <p class="submit"><button id="submit_form" type="submit" class="button-ok"></button></p>

                        <?php echo form_close(); ?>
                        
                    </div>
                </div>

                <div class="footer-container">
                    <p>http://www.facebook.com/FashionIslandmall</p>
                </div>
        </div>

        <div class="friends-selector-popup">
              <div id="loading" style="">
                  <!-- Loading Friend List -->
              </div>

              <div>
                  <div id="jfmfs-container"></div>
              </div>
        </div>
        <div class="popup-success">
            <img src="<?php echo base_url('assets/images/popup-success.png');?>" alt="">
        </div>
        <div class="overlay"><img src="<?php echo base_url('assets/images/loading.gif');?>" alt=""></div>
    </body>
</html>
