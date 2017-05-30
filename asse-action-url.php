<?php
defined('ABSPATH') || exit;

function asse_action_url( $buffer, $args ) {
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

// start buffer
ob_start('asse_action_url');