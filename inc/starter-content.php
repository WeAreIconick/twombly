<?php
/**
 * Twombly Starter Content
 *
 * @link https://make.wordpress.org/core/2016/11/30/starter-content-for-themes-in-4-7/
 *
 * @package Iconick/Twombly
 */

namespace Twombly\StarterContent;

/**
 * Returns the array of starter content for the theme.
 *
 * Passes it through the `twombly_starter_content` filter before returning.
 *
 * @return array A filtered array of args for the starter_content.
 */
function get_starter_content() {

	$front_page_pattern = '';

	if ( file_exists( __DIR__ . '/../patterns/homepage.php' ) ) {
		ob_start();
		include __DIR__ . '/../patterns/homepage.php';
		$front_page_pattern = ob_get_clean();
	}

	// Define and register starter content to showcase the theme on new sites.
	$starter_content = array(

		// Specify the core-defined pages to create and add custom thumbnails to some of them.
		'posts'     => array(
			'front' => array(
				'post_type'    => 'page',
				'post_title'   => esc_html_x( 'Front Page', 'Theme starter content', 'twombly' ),
				'post_content' => $front_page_pattern,
			),
			'about' => array(
				'post_type'  => 'page',
				'post_title' => esc_html_x( 'About Us', 'Theme starter content', 'twombly' ),
			),
			'contact' => array(
				'post_type'  => 'page',
				'post_title' => esc_html_x( 'Reach Out!', 'Theme starter content', 'twombly' ),
			),
			'blog' => array(
				'post_type'  => 'page',
				'post_title' => esc_html_x( 'Blog', 'Theme starter content', 'twombly' ),
			),
		),

		// Default to a static front page and assign the front and posts pages.
		'options'   => array(
			'show_on_front'  => 'page',
			'page_on_front'  => '{{front}}',
			'page_for_posts' => '{{blog}}',
		),

		// Set up nav menus for each of the two areas registered in the theme.
		'nav_menus' => array(
			// Assign a menu to the "primary" location.
			'primary' => array(
				'name'  => esc_html__( 'Primary menu', 'twombly' ),
				'items' => array(
					'link_home', // Note that the core "home" page is actually a link in case a static front page is not used.
					'page_about',
					'page_blog',
					'page_contact',
				),
			),

			// Assign a menu to the "footer1" location.
			'footer1'  => array(
				'name'  => esc_html__( 'Footer menu 1', 'twombly' ),
				'items' => array(
					'link_facebook',
					'link_twitter',
					'link_instagram',
					'link_email',
				),
			),

			// Assign a menu to the "footer2" location.
			'footer2'  => array(
				'name'  => esc_html__( 'Footer menu 2', 'twombly' ),
				'items' => array(
					'link_facebook',
					'link_twitter',
					'link_instagram',
					'link_email',
				),
			),
		),
	);

	/**
	 * Filters the array of starter content.
	 *
	 * @param array $starter_content Array of starter content.
	 */
	return apply_filters( 'twombly_starter_content', $starter_content );
}
