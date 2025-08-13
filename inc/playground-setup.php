<?php

require_once( \ABSPATH . 'wp-admin/includes/media.php' );
require_once( \ABSPATH . 'wp-admin/includes/file.php' );
require_once( \ABSPATH . 'wp-admin/includes/image.php' );

$site_logo_id = \media_sideload_image(
    'https://raw.githubusercontent.com/georgestephanis/twombly/refs/heads/main/inc/original_wapuu.png',
    null,
    'Wapuu!',
    'id'
);

if ( ! \is_wp_error( $site_logo_id ) ) {
    \update_option( 'site_icon', $site_logo_id );
}
