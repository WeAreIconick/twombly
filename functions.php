<?php
/**
 * Twombly theme functions and definitions.
 *
 * @link    https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Iconick/Twombly
 */

namespace Twombly\Theme;

/**
 * Handle addition of any enqueues for the front-end.
 *
 * @return void
 */
function enqueue_block_assets() {
	// Handle adding the theme's style.css for generic non-block-specific styles.
	\wp_enqueue_style(
		'twombly',
		\get_stylesheet_uri(),
		array(),
		(string) filemtime( __DIR__ . '/style.css' )
	);
}
\add_action( 'enqueue_block_assets', __NAMESPACE__ . '\enqueue_block_assets' );

/**
 * Handle addition of any enqueues for the block editor only.
 *
 * @return void
 */
function enqueue_block_editor_assets() {
	\wp_enqueue_script(
		'twombly',
		\get_theme_file_uri( 'js/block-editor.js' ),
		array(),
		(string) filemtime( __DIR__ . '/js/block-editor.js' ),
		true
	);
}
\add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\enqueue_block_editor_assets' );

/**
 * Create Home and Posts pages automatically on theme activation
 *
 * @return void
 */
function create_theme_pages_on_activation() {
	// Check if pages already exist to avoid duplicates
	if ( \get_page_by_path( 'home' ) || \get_page_by_path( 'posts' ) ) {
		return;
	}
	
	// Get the current admin URL for template editing links
	$site_editor_url = \admin_url( 'site-editor.php?path=/wp_template' );
	
	// Create Home page (static homepage)
	$home_page = array(
		'post_title'   => 'Home',
		'post_content' => '
			<!-- wp:paragraph -->
			<p>This is a placeholder homepage. This page doesn\'t do anything by itself - it\'s just here to set up your site structure.</p>
			<!-- /wp:paragraph -->
			
			<!-- wp:paragraph -->
			<p>To customize your homepage design and content:</p>
			<!-- /wp:paragraph -->
			
			<!-- wp:list -->
			<ul>
				<li>Go to <strong>Appearance → Theme Editor</strong></li>
				<li>Look for the <strong>"Front Page"</strong> template</li>
				<li>Or <a href="' . $site_editor_url . '">click here to open the Site Editor</a></li>
			</ul>
			<!-- /wp:list -->
			
			<!-- wp:paragraph -->
			<p><em>Template file: front-page.html</em></p>
			<!-- /wp:paragraph -->
		',
		'post_status'  => 'publish',
		'post_type'    => 'page',
		'post_name'    => 'home'
	);
	
	$home_id = \wp_insert_post( $home_page );
	
	// Create Posts page (blog archive)
	$posts_page = array(
		'post_title'   => 'Posts',
		'post_content' => '
			<!-- wp:paragraph -->
			<p>This is a placeholder blog page. This page doesn\'t do anything by itself - it\'s just here to set up your site structure.</p>
			<!-- /wp:paragraph -->
			
			<!-- wp:paragraph -->
			<p>To customize your blog archive design and layout:</p>
			<!-- /wp:paragraph -->
			
			<!-- wp:list -->
			<ul>
				<li>Go to <strong>Appearance → Theme Editor</strong></li>
				<li>Look for the <strong>"Posts Page (Home)"</strong> template</li>
				<li>Or <a href="' . $site_editor_url . '">click here to open the Site Editor</a></li>
			</ul>
			<!-- /wp:list -->
			
			<!-- wp:paragraph -->
			<p><em>Template file: home.html</em></p>
			<!-- /wp:paragraph -->
		',
		'post_status'  => 'publish',
		'post_type'    => 'page',
		'post_name'    => 'posts'
	);
	
	$posts_id = \wp_insert_post( $posts_page );
	
	// Assign templates for block themes
	if ( $home_id ) {
		\update_post_meta( $home_id, '_wp_page_template', 'front-page.html' );
	}
	
	if ( $posts_id ) {
		\update_post_meta( $posts_id, '_wp_page_template', 'home.html' );
	}
	
	// Configure WordPress reading settings
	if ( $home_id && $posts_id ) {
		\update_option( 'show_on_front', 'page' );
		\update_option( 'page_on_front', $home_id );
		\update_option( 'page_for_posts', $posts_id );
	}
}

// Run on theme activation
\add_action( 'after_switch_theme', __NAMESPACE__ . '\create_theme_pages_on_activation' );