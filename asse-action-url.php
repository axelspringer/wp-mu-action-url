<?php

defined( 'ABSPATH' ) || exit;

class Asse_Action_Url {

  public function __construct() {
    if ( ! defined( 'ASSE_URL_REPLACEMENT' ) || false === ASSE_URL_REPLACEMENT ) {
      return;
    }

    if ( ! defined( 'STATIC_URL' ) || empty( 'STATIC_URL' ) ) {
      return;
    }

    // start buffer
    add_action( 'wp_head', function() {
      ob_start( array( $this, 'replace_urls' ) );
    });

    add_action( 'wp_footer', function() {
        ob_end_flush();
    });
  }

  public function replace_urls( $buffer, $args ) {
    $static_host = STATIC_URL;
    $origin_host = ORIGIN_HOST;

    // this is for old style, a hook
    if ( defined( 'ASSE_URL_REPLACEMENT_FIX' ) && true === ASSE_URL_REPLACEMENT_FIX ) {
      $old_src = '/src\s*=\s*"(\/data\/uploads.+?)"/i';

      $buffer = preg_replace_callback( $old_src, function( $match ) use ( $static_host, $origin_host ) {
        $path = substr($match[1], 0, 1) === '/' ? $match[1] : '/' . $match[1];
        return sprintf('src="%s"', $static_host . $path . $match[2]);
      }, $buffer );

      return $buffer;
    }

    $buffer = preg_replace_callback( sprintf( '/%s/i', preg_quote( $static_host, '/' ) ), function( $match ) use ( $static_host, $origin_host ) {
      return str_replace( $static_host, $origin_host, $match[0] );
    }, $buffer );

    return $buffer;
  }

}

$asse_action_url = new Asse_Action_Url();
