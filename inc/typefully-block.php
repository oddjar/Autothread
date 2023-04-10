<?php
/**
 * Typefully Block
 */

function typefully_block_register() {
  wp_register_script(
    'typefully-block',
    plugin_dir_url( __FILE__ ) . 'typefully-block.js',
    array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-api' )
  );

  register_block_type( 'typefully/insert-thread', array(
    'editor_script' => 'typefully-block',
  ) );
}
add_action( 'init', 'typefully_block_register' );

function typefully_block_enqueue_scripts() {
  wp_enqueue_script(
    'typefully-block',
    plugin_dir_url( __FILE__ ) . 'typefully-block.js',
    array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-api' ),
    filemtime( plugin_dir_path( __FILE__ ) . 'typefully-block.js' ),
    true
  );
}
add_action( 'enqueue_block_editor_assets', 'typefully_block_enqueue_scripts' );