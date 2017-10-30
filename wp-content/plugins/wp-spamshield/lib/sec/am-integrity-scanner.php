<?php
/*
Plugin Name:	WP-SpamShield Anti-Malware & Functional Integrity Scanner
Plugin URI:		https://www.redsandmarketing.com/plugins/wp-spamshield/
Description:	This checks for malicious plugin files and ensures plugin functional integrity.
Version:		1.9.21
Author:			Red Sand Media Group and Blackhawk Cybersecurity
Author URI:		https://www.redsandmarketing.com/plugins/wp-spamshield/
License:		GPL2+
License URI:	https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:	wp-spamshield
Domain Path:	/languages
*/

/**
 *	Packaged with WP-SpamShield Plugin
 */

/* Make sure file remains secure if called directly */
if( !defined( 'ABSPATH' ) ) {
	if( !headers_sent() ) { @header( $_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden', TRUE, 403 ); @header( 'X-Robots-Tag: noindex', TRUE ); }
	die( 'ERROR: Direct access to this file is not allowed.' );
}

/* Prevents unintentional error display if WP_DEBUG not enabled. */
if( TRUE !== WP_DEBUG ) { @ini_set( 'display_errors', 0 ); @error_reporting( 0 ); }

/**
 *	Directories
 */
$mu_files = wpss_mu_scandir( __DIR__ );
$pl_files = wpss_mu_scandir( WP_PLUGIN_DIR );

/**
 *	Malicious Signatures
 *	Scans for fake malware plugin "X-WP-SPAM-SHIELD-PRO" (which is in no way associate with the real WP-SpamShield).
 *	More Info: https://www.redsandmarketing.com/blog/malware-alert-x-wp-spam-shield-pro-fake-plugin/
 */
$mal_keys	= array( 'spam-shield-pro', 'x-wp-spam-shield' );
$mal_rgx	= "~((x\-*)?wp\-*)?spam\-*shield\-*pro~i";

foreach( $mu_files as $i => $file ) {
	if( $file === basename( __FILE__ ) ) { continue; }
	$path = __DIR__ . DIRECTORY_SEPARATOR . $file;
	foreach( $mal_keys as $i => $k ) {
		if( FALSE !== stripos( $path, $k ) ) {
			if( @is_file( $path ) ) { @chmod( $path, 0310 ); @unlink( $path ); continue 2; }
		}
	}
	if( preg_match( $mal_rgx, $path ) ) {
		if( @is_file( $path ) ) { @chmod( $path, 0310 ); @unlink( $path ); continue; }
	}
	$file_contents = trim( @file_get_contents( $path ) );
	if( !empty( $file_contents ) && FALSE !== strpos( $file_contents, 'wp-spamshield' ) ) { @chmod( $path, 0310 ); @unlink( $path ); continue; }
}

foreach( $pl_files as $i => $file ) {
	$path = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $file;
	foreach( $mal_keys as $i => $k ) {
		if( FALSE !== stripos( $path, $k ) || preg_match( $mal_rgx, $path ) ) {
			if( @is_file( $path ) ) { @chmod( $path, 0310 ); @unlink( $path ); continue 2; }
			if( @is_dir( $path ) ) { wpss_mu_rmdir_deep( $path ); continue 2; }
		}
	}
	if( preg_match( $mal_rgx, $path ) ) {
		if( @is_file( $path ) ) { @chmod( $path, 0310 ); @unlink( $path ); continue; }
		if( @is_dir( $path ) ) { wpss_mu_rmdir_deep( $path ); continue; }
	}
}

/**
 *	Drop-in replacement for PHP function scandir()
 *	Has sanitation and error correction built-in
 *	@dependencies	none
 *	@since			1.9.20
 */
function wpss_mu_scandir( $dir ) {
	if( empty( $dir ) || ! @is_string( $dir ) ) { return $dir; }; @clearstatcache();
	$dot_files	= array( '..', '.' );
	$dir_listr	= (array) @scandir( $dir );
	$dir_list	= array_values( array_diff( $dir_listr, $dot_files ) );
	return $dir_list;
}

/**
 *	Drop-in replacement for PHP function rmdir(), Recursive
 *	@dependencies	none
 *	@since			1.9.20
 */
function wpss_mu_rmdir_deep( $path ) {
	$i = new DirectoryIterator( $path );
	foreach( $i as $f ) {
		if( $f->isFile() ) {
			@unlink( $f->getRealPath() );
		} else if( ! $f->isDot() && $f->isDir() ) {
			wpss_mu_rmdir_deep( $f->getRealPath() );
		}
	}
	@rmdir( $path );
}

