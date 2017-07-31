<?php
/*
Plugin Name: Extended Permissions
Plugin URI: https://github.com/clas-web/extended-permissions
Description: 
Version: 0.1.1
Author: Crystal Barton
Author URI: https://www.linkedin.com/in/crystalbarton
GitHub Plugin URI: https://github.com/clas-web/extended-permissions
*/


if( !defined('EXTENDED_PERMISSIONS') ):

/**
 * The full title of the Connections Hub plugin.
 * @var  string
 */
define( 'EXTENDED_PERMISSIONS', 'Extended Permissions' );

/**
 * True if debug is active, otherwise False.
 * @var  bool
 */
define( 'EXTENDED_PERMISSIONS_DEBUG', false );

/**
 * The path to the plugin.
 * @var  string
 */
define( 'EXTENDED_PERMISSIONS_PLUGIN_PATH', __DIR__ );

/**
 * The url to the plugin.
 * @var  string
 */
define( 'EXTENDED_PERMISSIONS_PLUGIN_URL', plugins_url('', __FILE__) );

/**
 * The version of the plugin.
 * @var  string
 */
define( 'EXTENDED_PERMISSIONS_VERSION', '0.0.1' );

/**
 * The database version of the plugin.
 * @var  string
 */
define( 'EXTENDED_PERMISSIONS_DB_VERSION', '0.0.1' );

/**
 * The database options key for the Connections Hub version.
 * @var  string
 */
define( 'EXTENDED_PERMISSIONS_VERSION_OPTION', 'extended-permissions-version' );

/**
 * The database options key for the Connections Hub database version.
 * @var  string
 */
define( 'EXTENDED_PERMISSIONS_DB_VERSION_OPTION', 'extended-permissions-db-version' );

/**
 * The database options key for the Connections Hub options.
 * @var  string
 */
define( 'EXTENDED_PERMISSIONS_OPTIONS', 'extended-permissions-options' );

/**
 * The full path to the log file used to log a synch.
 * @var  string
 */
define( 'EXTENDED_PERMISSIONS_LOG_FILE', __DIR__.'/logs/'.date('Ymd-His').'.txt' );

endif;


require_once( EXTENDED_PERMISSIONS_PLUGIN_PATH.'/functions.php' );
require_once( EXTENDED_PERMISSIONS_PLUGIN_PATH.'/model.php' );

