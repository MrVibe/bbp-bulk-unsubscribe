<?php
/**
 * Back end functions for Unsubscribe
 *
 * @author      VibeThemes
 * @category    Admin
 * @package     BBPBU
 * @version     1.0
 */

 if ( ! defined( 'ABSPATH' ) ) exit;

class BBP_Bulk_Unsubscribe_Tools{

    public static $instance;
    
    var $schedule;

    public static function init(){

        if ( is_null( self::$instance ) )
            self::$instance = new BBP_Bulk_Unsubscribe_Tools();

        return self::$instance;
    }

    private function __construct(){

		add_action( 'bbp_admin_menu',array( $this, 'add_tools_page')); 

		add_filter('bbp_admin_get_settings_fields',array($this,'enable_user_to_unsubscribe_from_all'));
		add_filter('bbp_get_default_options',array($this,'add_unsubscribe_option'));

        add_action('wp_ajax_unsubscribe_all_users',array($this,'unsubscribe_all_users'));
    }

    function add_unsubscribe_option($ops){
    	$ops['_bbp_admin_setting_callback_unsubscribe_from_all'] = true;
    	return $ops;
    }
    function enable_user_to_unsubscribe_from_all($settings){
    	
    	$settings['bbp_settings_users']['_bbp_admin_setting_callback_unsubscribe_from_all'] = array(
			'title'    => __( 'Enable Forum Users to unsubscribe from all Forums & topics', 'bbpress' ),
			'callback' => array($this,'bbp_admin_setting_callback_unsubscribe_from_all'),
			'page'     => 'discussion'
		);

    	return $settings;
    }
	
	function bbp_admin_setting_callback_unsubscribe_from_all(){
		?>
		<input name="_bbp_admin_setting_callback_unsubscribe_from_all" id="_bbp_admin_setting_callback_unsubscribe_from_all" type="checkbox" value="1" <?php checked( bbp_admin_setting_callback_unsubscribe_from_all( false ) ); ?> />
		<label for="_bbp_admin_setting_callback_unsubscribe_from_all"><?php esc_html_e( 'Allow Users to unsubscribe from all forums and topics', 'bbpbu' ); ?></label>
	<?php

	}	

    function add_tools_page(){

    	if(!function_exists('bbpress'))
    		return;

    	// These are later removed in admin_head
		if ( current_user_can( 'bbp_tools_page' ) ) {
			add_management_page(
				__( 'Bulk Unsubscribe', 'bbpress' ),
				__( 'BBPress Unsubscribe',  'bbpress' ),
				bbpress()->admin->minimum_capability,
				'bbp-bulk-unsubscribe',
				array($this,'bbp_bulk_unsubscribe')
			);
		}

    }


    function bbp_bulk_unsubscribe(){
    	?>
    	<div class="wrap">
    		<h1><?php _ex('Unsubscribe users from Forums and Topics','','bbpbu'); ?></h1>
    		<div class="card">
    			<h2><?php _ex('Unsubscribe all users from all Forums','','bbpbu'); ?></h2>
    			<p><?php _ex('If you\'re starting afresh or you want to stop sending emails to all your users. Use this option.','','bbpbu'); ?></p>
    			<a id="unsubscribe_all_users" class="button-primary"><?php _ex('Unsubscribe all users from all Forums and Topics','','bbpbu'); ?></a>
    		</div>
    		<div class="card">
    			<h2><?php _ex('Unsubscribe Selected Forums & Topics','','bbpbu'); ?></h2>
    			<p><?php _ex('Unsubscribe all users from selected forums and topics','','bbpbu'); ?></p>
    			<input type="text" id="serch_forums_topics" value="" placeholder="<?php _ex('Enter to search Forums & Topics','','bbpbu'); ?>" style="width:100%;margin-bottom:20px;">
    			<a id="unsubscribe_forums_topics" class="button-primary"><?php _ex('Unsubscribe all users from selected Forums & Topics','','bbpbu'); ?></a>
    		</div>
    		<div class="card">
    			<h2><?php _ex('Unsubscribe Selected Users','','bbpbu'); ?></h2>
    			<p><?php _ex('Unsubscribe selected users from all forums and topics','','bbpbu'); ?></p>
    			<input type="text" id="serch_users" value="" placeholder="<?php _ex('Enter to search User','','bbpbu'); ?>"  style="width:100%;margin-bottom:20px;">
    			<a id="unsubscribe_user" class="button-primary"><?php _ex('Unsubscribe selected users from all Forums & Topics','','bbpbu'); ?></a>
    		</div>
    	</div>
    	<?php

        $this->unsubscribe_users_from_forums_topics();
    }

    function unsubscribe_users_from_forums_topics(){
        ?>
        <script>
            jQuery(document).ready(function($){

                $('#unsubscribe_all_users').on('click',function(){

                    $.ajax({
                        type: "POST",
                        dataType: 'json',
                        url: ajaxurl,
                        data: {
                            action:'unsubscribe_all_users',
                        },
                        cache: false,
                        success: function (html) {
                            window.location.reload();
                        }
                    });

                });

                $('#unsubscribe_forums_topics').on('click',function(){

                });

                $('#unsubscribe_user').on('click',function(){

                });

            });
        </script>
        <?php
    }

    function unsubscribe_all_users(){

        global $wpdb;
        $option_name = '_bbp_forum_subscriptions';
        $option_name = $wpdb->get_blog_prefix() . $option_name;
        $users = $wpdb->get_results("SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = $option_name");

        if(empty($users)){
            die();
        }

        foreach ($users as $user){
            delete_user_meta( $user->user_id, $option_name );
        }

        die();

    }
}

BBP_Bulk_Unsubscribe_Tools::init();