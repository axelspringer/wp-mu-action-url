<?php

defined( 'ABSPATH' ) || exit;

class Asse_Action_Url {

  public $replace_urls;
  public $origin_host;

  public function __construct() {
    if ( ! defined( 'ASSE_URL_REPLACEMENT' ) || !(bool)ASSE_URL_REPLACEMENT ) {
      return;
    }

    if ( ! defined( 'ORIGIN_HOST' ) || '' === ORIGIN_HOST ) {
      return;
    }

    $this->replace_urls = ASSE_URL_REPLACEMENT;
    $this->origin_host = ORIGIN_HOST;

    add_action( 'template_redirect', array( $this, 'start_ob' ), 99 );
    add_action( 'shutdown', array( $this, 'end_ob' ), 99 );
  }

  public function start_ob() {
    ob_start( array( $this, 'replace_urls' ) );
  }

  public function end_ob() {
    ob_end_flush();
  }

  public function replace_urls( $buffer, $args ) {
    return str_replace( $this->replace_urls, $this->origin_host, $buffer );
  }

}

$asse_action_url = new Asse_Action_Url();
