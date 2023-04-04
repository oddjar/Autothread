<?php

/**
 * Plugin Name: Autothread
 * Plugin URI: https://glug.blog/projects/autothread/
 * Description: Automatically create Typefully threads from your published posts
 * Version: 1.0
 * Author: Johnathon Williams
 * Author URI: https://www.glug.blog
 * Text Domain: autothread
 * Email: john@oddjar.com
 * License: GPL2
 */

function autothread_function_on_post_publish( $post_id ) {
  // code to execute when a new post is published
  // you can access the post's information using $post_id

  // get the post content from the post_id
  $post_content = get_post_field( 'post_content', $post_id );

  // Remove all HTML tags from the post content
  $post_content = strip_tags( $post_content );

  // retrieve the typefully_api_key from the plugin options
  $typefully_api_key = get_option( 'typefully_api_key' );

  // call the autothread_create_typefully_thread function and pass the API key
  $response = autothread_create_typefully_thread( $typefully_api_key, $post_content );

}
add_action( 'publish_post', 'autothread_function_on_post_publish' );

function autothread_create_typefully_thread( $api_key, $content ) {
    $url = 'https://api.typefully.com/v1/drafts/';
    $headers = array(
        'Content-Type: application/json',
        'X-API-KEY: Bearer ' . $api_key
    );
    $payload = array(
        'content'      => $content,
        'threadify'    => true
    );

    $ch = curl_init( $url );
    curl_setopt( $ch, CURLOPT_POST, 1 );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $payload ) );

    $response = curl_exec( $ch );
    curl_close( $ch );

    return json_decode( $response, true );
}

function autothread_add_settings_menu() {
	add_options_page(
		'Autothread Settings',
		'Autothread Settings',
		'manage_options',
		'autothread-settings',
		'autothread_render_settings_page'
	);
}
add_action( 'admin_menu', 'autothread_add_settings_menu' );

function autothread_render_settings_page() {
	?>
	<div class="wrap">
		<h2>Autothread Settings</h2>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'autothread-settings' );
			do_settings_sections( 'autothread-settings' );
			?>
			<table class="form-table">
				<tr>
					<th scope="row"><label for="typefully_api_key">Typefully API Key</label></th>
					<td><input type="text" id="typefully_api_key" name="typefully_api_key" value="<?php echo esc_attr( get_option( 'typefully_api_key' ) ); ?>" /></td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

function autothread_register_settings() {
	register_setting( 'autothread-settings', 'typefully_api_key', 'autothread_validate_api_key' );
	add_settings_section( 'autothread_settings_section', '', '', 'autothread-settings' );
}
add_action( 'admin_init', 'autothread_register_settings' );

function autothread_validate_api_key( $value ) {
	if ( strlen( $value ) < 10 ) {
		add_settings_error( 'typefully_api_key', 'autothread_invalid_api_key', 'Invalid Typefully API Key. Must be at least 10 characters long.' );
		return get_option( 'typefully_api_key' );
	}
	return $value;
}

