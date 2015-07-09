<?php
/*
Plugin Name: Contact Form 7 Response Message Popup
Plugin URI:  http://prince.im/contact-form-7-response-message-popup/
Description: Contact Form 7 Response Message in Fancybox Popup
Version:     1.0
Author:      Sanowar Uddin Prince
Author URI:  http://prince.im/
License:     GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// URL to the fancybox directory
define( 'fancybox_dir', plugin_dir_url( __FILE__ ) . 'fancybox' );

// Add style and script
function cf7_enqueue_css_js() {
	wp_enqueue_style( 'jquery.fancybox.style', fancybox_dir . '/jquery.fancybox-1.3.4.css', '', '1.3.4' ); 
	wp_enqueue_style( 'cf7.rmp.style', fancybox_dir . '/cf7.rmp.css', '', '1.3.4' ); 

	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery.fancybox.script', fancybox_dir . '/jquery.fancybox-1.3.4.js', array( 'jquery' ), '1.3.4', true );
}
add_action( 'wp_enqueue_scripts', 'cf7_enqueue_css_js', 100 );

//Admin notice
function cf7_admin_notices() {
    if ( !is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
        echo '<div class="error"><p>Please install and active <a href="plugin-install.php?tab=search&s=contact+form+7"><strong>Contact Form 7</strong></a>.</p></div>';
    }
}
add_action( 'admin_notices', 'cf7_admin_notices' );

// Regsiter admin settings
function cf7_admin_settings() {
	register_setting( 'cf7_rmp_group', 'cf7_rmp_options' );
}
add_action( 'admin_init', 'cf7_admin_settings' );

// Admin option page settings
function cf7_rmp_settings_page() {
	if ( !current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
?>
    <div class="wrap">
        <h2>Contact Form 7 Response Message Popup</h2>
        <br />
        <form action="options.php" method="post">
<?php 
			settings_fields( 'cf7_rmp_group' );
			$options  = get_option( 'cf7_rmp_options' );
			$popup    = $options['popup'];
			echo "<label><input id='popup' name='cf7_rmp_options[popup]' type='checkbox' value='true'"  . checked( $popup, 'true', false ) . " /> Set popup for all forms</label>";
			submit_button(); 
?>
        </form>
    </div>
<?php
}

// Add admin menu
function cf7_rmp_menu() {
	add_submenu_page( 'wpcf7', 'Contact Form 7 Response Message Popup Settings', 'CF7 Response Message Popup', 'manage_options', 'cf7_rmp_settings', 'cf7_rmp_settings_page' );
}
add_action( 'admin_menu', 'cf7_rmp_menu' );

// JS code for popup
function cf7_rmp_js() {
	$options = get_option( 'cf7_rmp_options' );
	$popup   = $options['popup'];
?>
	<script type="text/javascript">
        jQuery(document).ready(function() { 
			jQuery('div.wpcf7-response-output').wrap("<div class='response-wrap'></div>");
			
            var options = { 
                success: showResponse
            }; 
        
            jQuery('.wpcf7 form').submit(function() { 
<?php if ( $popup !== 'true' ) { ?>
				if(jQuery(this).hasClass('cf7-rmp'))
<?php } ?>
				jQuery(this).ajaxSubmit(options); 
				return false; 
            }); 
        }); 
        
        function showResponse(responseText, statusText, xhr, $form) { 
            var responseOutput = $form.find('div.response-wrap').html();
                    
            jQuery.fancybox({
				'overlayColor'		: '#000',
				'padding'			: 15,
				'centerOnScroll'	: true,
				'content'			: responseOutput,
            });
        }
    </script>	
<?php
}
add_action( 'wp_footer', 'cf7_rmp_js', 100 );
