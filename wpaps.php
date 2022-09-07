<?php
/**
 * Plugin Name: WPAS
 * Plugin URI: https://github.com/AutoPigeon/wpas
 * Description: Add a JSON-LD schema to all your pages and posts
 * Version 1.0.0
 * Author: AutoPigeon Team
 * Author URI: https://github.com/AutoPigeon
 * Text-Domain: wpas
 * Domain Path: /languages
 *
 * @package wpas
 */

/**
 * Function callback for plugin activation hook
 *
 * @function wpas_activate
 * @since 1.0.0
 */
function wpas_activate() {

}
/**
 * Function callback for plugin deactivation hook
 *
 * @function wpas_deactivate
 * @since 1.0.0
 */
function wpas_deactivate() {

}

register_activation_hook( __FILE__, 'wpas_activate' );
register_deactivation_hook( __FILE__, 'wpas_deactivate' );

/**
 * The main class for the plugin
 *
 * @class WPAS
 */
class WPAS {
	/**
	 * Constructor function for WPAS
	 *
	 * @function construct
	 * @since 1.0.0
	 */
	public function __construct() {

	}
	/**
	 * Starts up the plugin
	 *
	 * @function run
	 * @since 1.0.0
	 */
	public function run() {
		$this->setup_actions();
	}
	/**
	 * Sets up all the actions
	 *
	 * @function setup_actions
	 * @since 1.0.0
	 */
	private function setup_actions() {
		add_action( 'wp_footer', array( &$this, 'generate_json_ld' ) );
	}
	/**
	 * Get current url
	 *
	 * @function wp_get_current_url
	 * @since 1.0.0
	 */
	private function wp_get_current_url() {
		return home_url( $_SERVER['REQUEST_URI'] );
	}
	/**
	 * Generate json-ld scheme
	 *
	 * @function generate_json_ld
	 * @since 1.0.0
	 */
	public function generate_json_ld() {
		if ( ! is_single() && 'post' !== get_post_type() ) {
			return;
		}
		global $post;
		$user           = wp_get_current_user();
		$featured_image = wp_get_attachment_url( get_post_thumbnail_id( $post->ID ), 'thumbnail' );
		$author_name    = $user->user_login;
		$scheme         = array(
			'headline'        => esc_html( get_the_title() ),
			'description'     => esc_html( get_the_excerpt() ),
			'@context'        => 'https://schema.org',
			'@type'           => 'Article',
			'author'          => array(
				'@type' => 'Person',
				'name'  => esc_html( $author_name ),
			),
			'datePublish'      => esc_html( get_the_date() ),
			'mainEntityOfPage' => array(
				'@type' => 'WebPage',
				'@id'   => esc_url( $this->wp_get_current_url() ),
			),
			'publisher'      => array(
				'@type' => 'Organization',
				'name'  => esc_html( get_the_title() ),
				'logo'  => array(
					'@type' => 'ImageObject',
					'url'   => esc_url( get_site_icon_url() ),
				),
			),
		);
		if ( '' == $featured_image ) {
			$scheme['image'] = array(
				'@type' => 'ImageObject',
				'url'   => esc_html( $featured_image ),
			);
		}
		echo '<script type="application/ld+json">';
		echo wp_json_encode( $scheme, JSON_PRETTY_PRINT );
		echo '</script>';
	}
}



$wpas = new WPAS();

$wpas->run();
