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

    //bbp_remove_user_forum_subscription( $user_id, $forum_id);
    function unsubscribe_option(){
    	if(!bbp_admin_setting_callback_unsubscribe_from_all())
    		return;

    	echo '<a id="unsubscribe_from_add_topics_forums" class="button">'._x('Unsubscribe from all forums and topics','unsunscribe button label','bbpbu').'</a>';
    	?>
    	<script>

    	</script>
    	<?php
    	
    }
}

BBP_Bulk_Unsubscribe::init();

function bbp_admin_setting_callback_unsubscribe_from_all( $default = 0 ) {
	return apply_filters( 'bbp_admin_setting_callback_unsubscribe_from_all', (bool) get_option( '_bbp_admin_setting_callback_unsubscribe_from_all', $default ) );
}

