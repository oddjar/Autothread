<?php
add_action( 'wp_enqueue_scripts', 'typefully_block_enqueue_scripts' );
function typefully_block_enqueue_scripts() {
  wp_enqueue_script(
    'typefully-block-script',
    esc_url( plugin_dir_url( __FILE__ ) . 'assets/js/typefully-block-script.js' ),
    array('wp-blocks', 'wp-element', 'wp-editor', 'wp-api'),
    filemtime( plugin_dir_path( __FILE__ ) . 'assets/js/typefully-block-script.js' ),
    true
  );
}

add_action( 'rest_api_init', 'typefully_api_init' );

function typefully_api_init() {
  register_rest_route( 'typefully/v1', '/threads', array(
    'methods' => 'GET',
    'callback' => 'typefully_api_get_threads',
  ) );
}

function typefully_api_get_threads( $request ) {
  $api_key = get_option( 'typefully_api_key' );

  $query_params = array(
    'limit' => 25
  );

  $url = 'https://api.typefully.com/v1/threads/';
  $url .= '?' . http_build_query( $query_params );

  $headers = array(
    'Content-Type: application/json',
    'X-API-KEY: Bearer ' . $api_key
  );

  $ch = curl_init( $url );
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
  curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

  $response = curl_exec( $ch );
  curl_close( $ch );

  $threads = json_decode( $response, true );

  $response = array();

  foreach ( $threads['results'] as $thread ) {
    $response[] = array(
      'id' => $thread['id'],
      'name' => $thread['name'],
    );
  }

  return $response;
}

