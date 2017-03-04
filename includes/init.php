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

    	add_action('wp_ajax_unsubscribe_user_from_forums',array($this,'unsubscribe_user_from_forums'));
        add_action('wp_ajax_unsubscribe_user_from_topics',array($this,'unsubscribe_user_from_topics'));
    }

    function unsubscribe_option(){

    	if(!bbp_admin_setting_callback_unsubscribe_from_all())
    		return;

        echo '<div class="unsubscribe_users_from_forums_topics">';

        echo '<a id="unsubscribe_from_all_forums" class="button">'._x('Unsubscribe from all forums','unsunscribe button label','bbpbu').'</a>';

        echo '<a id="unsubscribe_from_all_topics" class="button">'._x('Unsubscribe from all topics','unsunscribe button label','bbpbu').'</a>';
        echo '</div>';

        $this->unsubscribe_users_from_all_forums_topics();
    	
    }

    function unsubscribe_users_from_all_forums_topics(){
        ?>
        <script>
            jQuery(document).ready(function($){

                $('#unsubscribe_from_all_forums').on('click',function(){
                    
                    var forum_ids = [];
                    $('.bbp-forums .bbp-body').find('ul').each(function(){

                        var id = $(this).attr('id');
                        var forum_id = id.match('[0-9]+');
                        forum_ids.push(forum_id[0]);
                    });

                    $('.unsubscribe_users_from_forums_topics').append('<div class="wplms_bbpbu_progress" style="width:100%;margin-bottom:20px;height:10px;background:#fafafa;border-radius:10px;overflow:hidden;"><div class="bar" style="padding:0 1px;background:#37cc0f;height:100%;width:0;"></div></div>');                    

                    var x = 0;
                    var width = 100*1/forum_ids.length;
                    var number = width;
                    var loopArray = function(arr) {
                        bbpbu_ajaxcall(arr[x],function(){
                            x++;
                            if(x < arr.length) {
                                loopArray(arr);   
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
                                action:'unsubscribe_user_from_forums',
                                id:obj,
                            },
                            cache: false,
                            success: function (html) {
                                $('#bbp-forum-'+obj).hide(200);
                                number = number + width;
                                $('.wplms_bbpbu_progress .bar').css('width',number+'%');
                                if(number >= 100){
                                    $('#unsubscribe_from_all_forums').html('<strong>'+x+' '+'<?php _e('Forums successfuly unsubscribed','bbpbu'); ?>'+'</strong>');
                                }
                            }
                        });
                        // do callback when ready
                        callback();
                    }
                });

                $('#unsubscribe_from_all_topics').on('click',function(){

                    var topic_ids = [];
                    $('.bbp-topics .bbp-body').find('ul').each(function(){

                        var id = $(this).attr('id');
                        var topic_id = id.match('[0-9]+');
                        topic_ids.push(topic_id[0]);
                    });
                    
                    $('.unsubscribe_users_from_forums_topics').append('<div class="wplms_bbpbu_progress" style="width:100%;margin-bottom:20px;height:10px;background:#fafafa;border-radius:10px;overflow:hidden;"><div class="bar" style="padding:0 1px;background:#37cc0f;height:100%;width:0;"></div></div>');

                    var x = 0;
                    var width = 100*1/topic_ids.length;
                    var number = width;
                    var loopArray = function(arr) {
                        bbpbu_ajaxcall(arr[x],function(){
                            x++;
                            if(x < arr.length) {
                                loopArray(arr);   
                            }
                        }); 
                    }
                    
                    // start 'loop'
                    loopArray(topic_ids);

                    function bbpbu_ajaxcall(obj,callback) {
                        
                        $.ajax({
                            type: "POST",
                            dataType: 'json',
                            url: ajaxurl,
                            data: {
                                action:'unsubscribe_user_from_topics',
                                id:obj,
                            },
                            cache: false,
                            success: function (html) {
                                $('#bbp-topic-'+obj).hide(200);
                                number = number + width;
                                $('.wplms_bbpbu_progress .bar').css('width',number+'%');
                                if(number >= 100){
                                    $('#unsubscribe_from_all_topics').html('<strong>'+x+' '+'<?php _e('Topics successfuly unsubscribed','bbpbu'); ?>'+'</strong>');
                                }
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

    function unsubscribe_user_from_forums(){

        if ( !isset($_POST['id']) || !is_user_logged_in()){
            _e('Security check Failed. Contact Administrator.','bbpbu');
            die();
        }

        $user_id = get_current_user_id();
        $forum_id = $_POST['id'];
        bbp_remove_user_forum_subscription( $user_id, $forum_id);

        die();
    }

    function unsubscribe_user_from_topics(){

        if ( !isset($_POST['id']) || !is_user_logged_in()){
            _e('Security check Failed. Contact Administrator.','bbpbu');
            die();
        }

        $user_id = get_current_user_id();
        $topic_id = $_POST['id'];
        bbp_remove_user_topic_subscription( $user_id, $topic_id);

        die();
    }

}

BBP_Bulk_Unsubscribe::init();

function bbp_admin_setting_callback_unsubscribe_from_all( $default = 0 ) {
	return apply_filters( 'bbp_admin_setting_callback_unsubscribe_from_all', (bool) get_option( '_bbp_admin_setting_callback_unsubscribe_from_all', $default ) );
}

