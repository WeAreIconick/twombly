<?php
/**
 * Some setup scripts to run when initializing a Playground environment.
 *
 * @package Iconick/Twombly
 */

/**
 * Clear the database for the subsequent WXR import.
 */
$twombly_posts = \get_posts(
	array(
		'numberposts' => -1,
		'post_status' => 'any',
		'post_type'   => array(
			'post',
			'page',
		),
	)
);
foreach ( $twombly_posts as $twombly_post ) {
	\wp_delete_post( $twombly_post->ID, true );
}

/**
 * Load in a Site Icon.
 */
require_once \ABSPATH . 'wp-admin/includes/media.php';
require_once \ABSPATH . 'wp-admin/includes/file.php';
require_once \ABSPATH . 'wp-admin/includes/image.php';

$twombly_site_icon_id = \media_sideload_image(
	'https://raw.githubusercontent.com/IconickThemes/twombly/main/_playground/site_icon.png',
	null,
	'Site Icon',
	'id'
);

if ( ! \is_wp_error( $twombly_site_icon_id ) ) {
	\update_option( 'site_icon', $twombly_site_icon_id );
}
