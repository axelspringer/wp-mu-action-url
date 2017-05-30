<?php

defined( 'ABSPATH' ) || exit;

class Asse_Action_Url {

  public function __construct() {
    // start buffer
    add_action( 'wp_head', function() {
      ob_start( array( $this, replace_urls ) );
    });

    add_action( 'wp_footer', function() {
        ob_end_flush();
    });
  }

  public function replace_urls( $buffer, $args ) {
    if ( ! defined( 'STATIC_URL' ) || empty( 'STATIC_URL' ) ) {
      return $buffer;
    }

    if ( ! defined( 'ASSE_URL_REPLACEMENT' ) || false === ASSE_URL_REPLACEMENT ) {
      return $buffer;
    }

    $static_host = STATIC_URL;
    $origin_host = ORIGIN_HOST;

    $buffer = preg_replace_callback( sprintf( '/%s/i', preg_quote( $static_host, '/' ) ), function( $match ) use ( $static_host, $origin_host ) {
      return str_replace( $static_host, $origin_host, $match[0] );
    }, $buffer );

    return $buffer;
  }

}

$asse_action_url = new Asse_Action_Url();

