<?php
/**
 * Front end functions for Unsubscribe
 *
 * @author      VibeThemes
 * @category    Admin
 * @package     BBPBU
 * @version     1.0
 */

 if ( ! defined( 'ABSPATH' ) ) exit;

class BBP_Bulk_Unsubscribe{

    public static $instance;
    
    var $schedule;

    public static function init(){

        if ( is_null( self::$instance ) )
            self::$instance = new BBP_Bulk_Unsubscribe();

        return self::$instance;
    }

    private function __construct(){

    	add_action('bbp_template_after_user_subscriptions',array($this,'unsubscribe_option'));

    	add_action('wp_ajax_remove_user_from_forum_topic_subscription',array($this,'remove_user_from_forum_topic_subscription'));
    }

    function unsubscribe_option(){

    	if(!bbp_admin_setting_callback_unsubscribe_from_all())
    		return;

    	echo '<a id="unsubscribe_from_add_topics_forums" class="button">'._x('Unsubscribe from all forums and topics','unsunscribe button label','bbpbu').'</a>';

        $this->unsubscribe_users_from_all_forums();
    	
    }

    function unsubscribe_users_from_all_forums(){
        ?>
        <script>
            jQuery(document).ready(function($){
                $('#unsubscribe_from_add_topics_forums').on('click',function(){

                    var forum_ids = [];
                    $('.bbp-forums .bbp-body').find('ul').each(function(){

                        var id = $(this).attr('id');
                        var forum_id = id.match('[0-9]+');
                        forum_ids.push(forum_id[0]);
                    });
                    

                    var x = 0;
                    var loopArray = function(arr) {
                        bbpbu_ajaxcall(arr[x],function(){
                            x++;
                            if(x < arr.length) {
                                loopArray(arr);   
                            }
                            else if (x == arr.length) {
                               
                            }
                        }); 
                    }
                    
                    // start 'loop'
                    loopArray(forum_ids);

                    function bbpbu_ajaxcall(obj,callback) {
                        
                        $.ajax({
                            type: "POST",
                            dataType: 'json',
                            url: ajaxurl,
                            data: {
                                action:'remove_user_from_forum_topic_subscription',
                                id:obj,
                            },
                            cache: false,
                            success: function (html) {
                              $('#bbp-forum-'+obj).hide(200);
                            }
                        });
                        // do callback when ready
                        callback();
                    }
                        
                });
            });
        </script>
        <?php
    }

    function remove_user_from_forum_topic_subscription(){

        if ( !isset($_POST['id']) || !is_user_logged_in()){
            _e('Security check Failed. Contact Administrator.','bbpbu');
            die();
        }

        $user_id = get_current_user_id();
        $forum_id = $_POST['id'];
        bbp_remove_user_forum_subscription( $user_id, $forum_id);

        die();
    }

}

BBP_Bulk_Unsubscribe::init();

function bbp_admin_setting_callback_unsubscribe_from_all( $default = 0 ) {
	return apply_filters( 'bbp_admin_setting_callback_unsubscribe_from_all', (bool) get_option( '_bbp_admin_setting_callback_unsubscribe_from_all', $default ) );
}

