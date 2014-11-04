<?php

/*
  Plugin Name: rtBiz Ideas
  Plugin URI: https://rtcamp.com
  Description: User submitted ideas/feature-request tracking for General Purpose. Also WooCommerce Support added.
  Version: 1.0.4
  Author: rtCamp
  Text Domain: rtbiz-ideas
  Author URI: https://rtcamp.com
 */

/**
 * Main file, contains the plugin metadata and activation processes
 *
 * @package rtbiz-ideas
 * @subpackage Main
 */
if ( ! defined( 'RTBIZ_IDEAS_PATH' ) ) {
    /**
     * The server file system path to the plugin directory
     */
    define( 'RTBIZ_IDEAS_PATH', plugin_dir_path( __FILE__ ) );
}


if ( ! defined( 'RTBIZ_IDEAS_URL' ) ) {

    /**
     * The url to the plugin directory
     *
     */
	define( 'RTBIZ_IDEAS_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'RTBIZ_IDEAS_BASE_NAME' ) ) {

    /**
     * The url to the plugin directory
     *
     */
	define( 'RTBIZ_IDEAS_BASE_NAME', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'RTBIZ_IDEAS_PATH_ADMIN' ) ) {

    /**
     * The url to the app/admin directory
     *
     */
	define( 'RTBIZ_IDEAS_PATH_ADMIN', plugin_dir_path( __FILE__ ) . 'app/admin/' );
}
if ( ! defined( 'RTBIZ_IDEAS_PATH_MAIN' ) ) {

    /**
     * The url to the app/main directory
     *
     */
	define( 'RTBIZ_IDEAS_PATH_MAIN', plugin_dir_path( __FILE__ ) . 'app/main/' );
}
if ( ! defined( 'RTBIZ_IDEAS_PATH_LIB' ) ) {

    /**
     * The url to the app/lib directory
     *
     */
    define( 'RTBIZ_IDEAS_PATH_LIB', plugin_dir_path( __FILE__ ) . 'app/lib/' );
}
if ( ! defined( 'RTBIZ_IDEAS_PATH_HELPER' ) ) {

    /**
     * The url to the app/helper directory
     *
     */
	define( 'RTBIZ_IDEAS_PATH_HELPER', plugin_dir_path( __FILE__ ) . 'app/helper/' );
}
if ( ! defined( 'RTBIZ_IDEAS_PATH_SETTINGS' ) ) {

	/**
	 * The url to the app/helper directory
	 *
	 */
	define( 'RTBIZ_IDEAS_PATH_SETTINGS', plugin_dir_path( __FILE__ ) . 'app/settings/' );
}

if ( ! defined( 'RTBIZ_IDEAS_SLUG' ) ) {

    /**
     * The post type / slug for the plugin - 'idea'
     *
     */
	define( 'RTBIZ_IDEAS_SLUG', 'idea' );
}

if ( ! defined( 'RTBIZ_IDEAS_PATH_TEMPLATES' ) ) {

    /**
     * The url to the templates directory
     *
     */
	define( 'RTBIZ_IDEAS_PATH_TEMPLATES', plugin_dir_path( __FILE__ ) . 'templates/' );
}

if ( ! defined( 'RTBIZ_IDEAS_PATH_VENDOR' ) ) {

    /**
     * The url to the app/helper directory
     *
     */
	define( 'RTBIZ_IDEAS_PATH_VENDOR', plugin_dir_path( __FILE__ ) . 'app/vendor/' );
}

function rtwpideas_enqueue_styles_and_scripts() {
	wp_register_script( 'rtwpideas-custom-script', plugins_url( '/app/assets/js/rtwpideas-custom-script.js', __FILE__ ), array( 'jquery' ) );
	wp_enqueue_script( 'rtwpideas-custom-script' );
	wp_register_style( 'rtwpideas-client-styles', plugins_url( '/app/assets/css/rtwpideas-client-styles.css', __FILE__ ) );
	wp_enqueue_style( 'rtwpideas-client-styles' );
	$ajax_url = admin_url( 'admin-ajax.php' );
	wp_localize_script( 'rtwpideas-custom-script', 'rt_wpideas_ajax_url', $ajax_url );
	wp_enqueue_script( 'jquery-form', array( 'jquery' ), false, true );
	//wp_register_script( 'validation', 'http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js', array( 'jquery' ) );
	//wp_enqueue_script( 'validation' );
}

add_action( 'wp_enqueue_scripts', 'rtwpideas_enqueue_styles_and_scripts' );

include_once RTBIZ_IDEAS_PATH_VENDOR . 'taxonomy-metadata.php';

/**
 * Loader function for all the classes
 * @param $class_name
 */
function rt_wordpress_idea_autoloader( $class_name ) {
	$rtlibpath = array(
		'app/admin/' . $class_name . '.php',
		'app/helper/' . $class_name . '.php',
		'app/settings/' . $class_name . '.php',
		'app/main/' . $class_name . '.php',
		'app/lib/rtdbmodel/' . $class_name . '.php',
	);
	foreach ( $rtlibpath as $path ) {
		$path = RTBIZ_IDEAS_PATH . $path;
		if ( file_exists( $path ) ) {
			include $path;
			break;
		}
	}
}

/**
 * Register the autoloader function into spl_autoload
 */
spl_autoload_register( 'rt_wordpress_idea_autoloader' );

include_once RTBIZ_IDEAS_PATH_LIB . 'rt-lib.php';


function rtbiz_idea_loader(){
	include_once 'app/helper/wpideas-votes.php';
	include_once 'app/helper/wpideas-common.php';
	include_once 'app/settings/class-redux-framework-idea-config.php';
	include_once 'app/helper/rt_idea_functions.php';
	require_once RTBIZ_IDEAS_PATH_VENDOR . 'redux/ReduxCore/framework.php';

//	global $rt_idea_autoload;
//
//	$rt_idea_autoload  = new RT_WP_Autoload( RTBIZ_IDEAS_PATH_LIB . 'rtdbmodel/' );
//	$rt_idea_autoload =	new RT_WP_Autoload( RTBIZ_IDEAS_PATH_ADMIN );
//	$rt_idea_autoload = new RT_WP_Autoload( RTBIZ_IDEAS_PATH_HELPER );
//	$rt_idea_autoload = new RT_WP_Autoload( RTBIZ_IDEAS_PATH_MAIN );
//	$rt_idea_autoload = new RT_WP_Autoload( RTBIZ_IDEAS_PATH_SETTINGS );

	/**
	 * Instantiate the RTWPIdeas class.
	 */
	global $rtWpIdeas, $rtWpIdeasAttributes, $taxonomy_metadata, $reduxFrameworkIdeaConfig;
	$reduxFrameworkIdeaConfig = new Redux_Framework_Idea_Config();
	$rtWpIdeas = new RTWPIdeas();
	$rtWpIdeasAttributes = new RTWPIdeasAttributes();

}

add_action( 'plugins_loaded', 'rtbiz_idea_loader', 10 );
/*
 * Look Ma! Very few includes! Next File: /app/main/RTWPIdeas.php
 */