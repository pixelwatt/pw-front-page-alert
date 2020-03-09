<?php
/*
Plugin Name: Front Page Alert
Description: This plugin allows you to display an alert on the front page of your site, at either the top of the page, or in a container of your choice. Alerts are loaded via the browser, so this plugin is immune to cache.
Author: Rob Clark
Version: 0.9.0
*/

//======================================================================
// PLUGIN STYLES AND SCRIPTS
//======================================================================

function pw_front_page_alert_admin_scripts() {
	
}

function pw_front_page_alert_admin_styles() {
	
}

add_action( 'admin_enqueue_scripts', 'pw_front_page_alert_admin_scripts' );
add_action( 'admin_enqueue_scripts', 'pw_front_page_alert_admin_styles' );

function pw_front_page_alert_scripts() {
	if ( is_front_page() ) {
		wp_enqueue_script( 'pw-front-page-alert-loader', plugins_url() . '/pw-front-page-alert/inc/alert-loader.js', array('jquery'), '', true );

		$data_array = array(
			'site_url' => get_site_url(),
		);
		wp_localize_script( 'pw-front-page-alert-loader', 'data_object', $data_array );
	}
}

add_action( 'wp_enqueue_scripts', 'pw_front_page_alert_scripts' );


//======================================================================
// REQUIRED PLUGIN CHECK
//======================================================================

require_once dirname( __FILE__ ) . '/inc/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'pw_front_page_alert_register_required_plugins' );

function pw_front_page_alert_register_required_plugins() {
	$plugins = array(
		array(
			'name'      => 'CMB2',
			'slug'      => 'cmb2',
			'required'  => true,
		),
	);

	$config = array(
		'id'           => 'pw-front-page-alert',                 // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                      // Default absolute path to bundled plugins.
		'menu'         => 'tgmpa-install-plugins', // Menu slug.
		'parent_slug'  => 'plugins.php',            // Parent menu slug.
		'capability'   => 'manage_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => false,                   // Automatically activate plugins after installation or not.
		'message'      => '',                      // Message to output right before the plugins table.	
	);

	tgmpa( $plugins, $config );
}


//======================================================================
// PLUGIN ADMIN OPTIONS
//======================================================================

function pw_front_page_alert_register_main_options_metabox() {
	/**
	 * Registers main options page menu item and form.
	 */
	$main_options = new_cmb2_box( array(
		'id'           => 'pw_front_page_alert_main_options_page',
		'title'        => __( '<strong>Front Page Alert Configuration</strong>', 'cmb2' ),
		'object_types' => array( 'options-page' ),
		'option_key'      => 'pw_front_page_alert_options', // The option key and admin menu page slug.
		'icon_url'        => 'dashicons-warning', // Menu icon. Only applicable if 'parent_slug' is left empty.
		'menu_title'      => esc_html__( 'Front Alert', 'cmb2' ), // Falls back to 'title' (above).
		'position'        => 4, // Menu position. Only applicable if 'parent_slug' is left empty.
	) );

	/**
	 * Options fields ids only need
	 * to be unique within this box.
	 * Prefix is not needed.
	 */

	$main_options->add_field( array(
		'name'     => __( '<div style="text-transform: none;"><span style="font-size: 1.25rem; font-weight: 800; line-height: 1;">Alert Content</span></div>', 'cmb2' ),
        'id'       => 'alert_content_info',
        'type'     => 'title',
	) );

	$main_options->add_field( array(
		'name'    => esc_html__( 'Headline', 'cmb2' ),
		//'desc'    => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'      => 'alert_content_headline',
		'type'    => 'text',
	) );

	$main_options->add_field( array(
		'name'    => esc_html__( 'Content', 'cmb2' ),
		//'desc'    => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'      => 'alert_content_content',
		'type'    => 'wysiwyg',
	) );

	$main_options->add_field( array(
		'name'    => esc_html__( 'Link Label', 'cmb2' ),
		//'desc'    => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'      => 'alert_content_link_label',
		'type'    => 'text',
	) );

	$main_options->add_field( array(
		'name'    => esc_html__( 'Link Destination URL', 'cmb2' ),
		//'desc'    => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'      => 'alert_content_link_url',
		'type'    => 'text_url',
	) );

	$main_options->add_field( array(
		'name'     => __( '<div style="text-transform: none;"><span style="font-size: 1.25rem; font-weight: 800; line-height: 1;">Options</span></div>', 'cmb2' ),
        'id'       => 'alert_options_info',
        'type'     => 'title',
	) );

	$main_options->add_field( array(
		'name'    => 'Status',
		'id'      => 'alert_options_status',
		'type'    => 'radio_inline',
		'default' => '',
		'options' => array(
			'' => __( 'Off (Will not appear)', 'cmb2' ),
			'enabled'   => __( 'On (Visible on the front page)', 'cmb2' ),
		),
	) );

	$main_options->add_field( array(
		'name'    => 'Custom Target',
		'id'      => 'alert_options_target',
		'type'    => 'text',
	) );

}

add_action( 'cmb2_admin_init', 'pw_front_page_alert_register_main_options_metabox' );


//======================================================================
// BUILD JSON
//======================================================================

add_action( 'add_option_pw_front_page_alert_options', 'pw_front_page_alert_build_json', 10 );
add_action( 'update_option_pw_front_page_alert_options', 'pw_front_page_alert_build_json', 10 );


function pw_front_page_alert_filter_content($content) {
    if (!empty($content)) {
        $content = apply_filters('the_content',$content);
    }
    return $content;
}

function pw_front_page_alert_build_json() {
	// Get alert data
	$data = get_option( 'pw_front_page_alert_options' );

	// Add a timestamp
	$dt = new DateTime("now", new DateTimeZone('CDT'));
	$data['generated_at'] = $dt->format('m/d/Y, h:i:s A');

	if ( ! array_key_exists('alert_options_status', $data ) ) {
		$data['alert_options_status'] = '';
	}

	if ( ! array_key_exists('alert_options_target', $data ) ) {
		$data['alert_options_target'] = '';
	}

	if ( ! array_key_exists('alert_content_link_label', $data ) ) {
		$data['alert_content_link_label'] = '';
	}

	if ( ! array_key_exists('alert_content_link_url', $data ) ) {
		$data['alert_content_link_url'] = '';
	}

	if ( ! array_key_exists('alert_content_content', $data ) ) {
		$data['alert_content_content'] = '';
	} else {
		$data['alert_content_content'] = pw_front_page_alert_filter_content( $data['alert_content_content'] );
	}

	// Encode alert data as json
	$data_json = json_encode($data);

	$upload_dir   = wp_upload_dir();
	if (!is_dir($upload_dir['basedir'] . '/pw-front-page-alert')) mkdir($upload_dir['basedir'] . '/pw-front-page-alert');

    $file = $upload_dir['basedir'] . '/pw-front-page-alert/data.json'; 
    file_put_contents( $file, $data_json );
}


