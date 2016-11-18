<?php
/**
    Plugin Name: Get Pingbacks
	Description: This plugin adds an endpoint for /get-pingbacks and /get-pingbacks/postid that returns a csv with pingback data
	Version: 0.1.0
	Author: Mat Gargano
	Author URI: http://matgargano.com
	License: GPL2
 */

spl_autoload_register( function ( $class ) {
	$base = explode( '\\', $class );
	if ( 'Morgan' === $base[0] ) {
		$file = __DIR__ . '/' . strtolower( str_replace( [ '\\', '_' ], [
					DIRECTORY_SEPARATOR,
					'-'
				], $class ) . '.php' );
		if ( file_exists( $file ) ) {
			require $file;
		} else {
			die( sprintf( 'File %s not found', $file ) );
		}
	}
} );

$export = new Morgan\Export\Pingbacks;
$export->init();


function pbe_flush_rewrites(){
	flush_rewrite_rules();
}

register_deactivation_hook( __FILE__, 'pbe_flush_rewrites' );
register_activation_hook( __FILE__,'pbe_flush_rewrites' );