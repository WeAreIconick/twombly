<?php

/**
 * Clear the database for the subsequent WXR import.
 */
$posts = \get_posts(
    array(
        'numberposts' => -1,
        'post_status' => 'any',
        'post_type'   => array(
            'post',
            'page',
        ),
    )
);
foreach ( $posts as $post) {
    \wp_delete_post( $post->ID, true );
}

/**
 * Load in a Site Icon.
 */
require_once( \ABSPATH . 'wp-admin/includes/media.php' );
require_once( \ABSPATH . 'wp-admin/includes/file.php' );
require_once( \ABSPATH . 'wp-admin/includes/image.php' );

$site_icon_id = \media_sideload_image(
    get_theme_file_uri( '_playground/site_icon.png' ),
    null,
    'Site Icon',
    'id'
);

if ( ! \is_wp_error( $site_icon_id ) ) {
    \update_option( 'site_icon', $site_icon_id );
}
