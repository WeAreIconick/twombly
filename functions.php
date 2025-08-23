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
 * =============================================================================
 * THEME SETUP & ENQUEUES
 * =============================================================================
 */

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
 * =============================================================================
 * THEME ACTIVATION
 * =============================================================================
 */

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
\add_action( 'after_switch_theme', __NAMESPACE__ . '\create_theme_pages_on_activation' );

/**
 * =============================================================================
 * VIEW TRANSITIONS IMPLEMENTATION
 * =============================================================================
 */

/**
 * Twombly View Transitions Implementation
 * Production-ready class following WordPress coding standards
 * 
 * @since 1.0.0
 */
class View_Transitions {
	
	/**
	 * Configuration array for view transitions
	 * 
	 * @var array
	 */
	private $config;
	
	/**
	 * Browser compatibility cache
	 * 
	 * @var array
	 */
	private $browser_support = null;
	
	/**
	 * Initialize view transitions functionality
	 */
	public function __construct() {
		\add_action( 'after_setup_theme', array( $this, 'setup_theme_support' ) );
		\add_action( 'wp_head', array( $this, 'add_meta_tags' ), 1 );
		\add_action( 'wp_head', array( $this, 'add_view_transition_css' ), 10 );
		\add_filter( 'render_block', array( $this, 'add_transition_names' ), 10, 2 );
	}
	
	/**
	 * Setup theme support with comprehensive configuration
	 */
	public function setup_theme_support() {
		$this->config = \apply_filters( 'twombly_view_transitions_config', array(
			'default-animation' => \get_option( 'twombly_view_transitions_animation', 'fade' ),
			'respect-reduced-motion' => true,
			'accessibility' => array(
				'respect-reduced-motion' => true,
			),
		) );
		
		if ( $this->should_enable_view_transitions() ) {
			\add_theme_support( 'view-transitions', $this->config );
		}
	}
	
	/**
	 * Determine if view transitions should be enabled
	 * 
	 * @return bool Whether to enable view transitions
	 */
	private function should_enable_view_transitions() {
		$should_enable = \apply_filters( 'twombly_enable_view_transitions', true );
		
		if ( ! $should_enable ) {
			return false;
		}
		
		$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? 
			\sanitize_text_field( \wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
		
		$browser_info = $this->get_browser_support( $user_agent );
		
		return $browser_info['supports_view_transitions'];
	}
	
	/**
	 * Get browser support information
	 * 
	 * @param string $user_agent User agent string
	 * @return array Browser support information
	 */
	private function get_browser_support( $user_agent ) {
		if ( null !== $this->browser_support ) {
			return $this->browser_support;
		}
		
		$this->browser_support = array(
			'supports_view_transitions' => true, // Progressive enhancement approach
			'browser' => 'unknown',
			'version' => '0'
		);
		
		// Chrome 111+, Edge 111+, Safari 18+
		if ( preg_match( '/Chrome\/(\d+)/', $user_agent, $matches ) ) {
			$version = (int) $matches[1];
			$this->browser_support['browser'] = 'chrome';
			$this->browser_support['version'] = $version;
			$this->browser_support['supports_view_transitions'] = $version >= 111;
		} elseif ( preg_match( '/Safari\/(\d+)/', $user_agent, $matches ) && ! strpos( $user_agent, 'Chrome' ) ) {
			$version = (int) $matches[1];
			$this->browser_support['browser'] = 'safari';
			$this->browser_support['version'] = $version;
			// Safari 18+ support (approximation)
			$this->browser_support['supports_view_transitions'] = $version >= 605;
		}
		
		return $this->browser_support;
	}
	
	/**
	 * Add view transition names to blocks
	 * 
	 * @param string $block_content Block content
	 * @param array  $block Block data
	 * @return string Modified block content
	 */
	public function add_transition_names( $block_content, $block ) {
		if ( ! \current_theme_supports( 'view-transitions' ) || empty( $block_content ) ) {
			return $block_content;
		}
		
		global $post;
		if ( ! $post instanceof \WP_Post ) {
			return $block_content;
		}
		
		$block_name = $block['blockName'] ?? '';
		$transition_name = $this->get_transition_name_for_block( $block_name, $post->ID );
		
		if ( ! $transition_name ) {
			return $block_content;
		}
		
		return $this->add_transition_name_to_content( $block_content, $transition_name );
	}
	
	/**
	 * Add essential view transition CSS directly to head
	 */
	public function add_view_transition_css() {
		if ( ! \current_theme_supports( 'view-transitions' ) ) {
			return;
		}
		
		?>
		<style id="twombly-view-transitions-core">
		/* Twombly View Transitions - Essential CSS */
		@media (prefers-reduced-motion: no-preference) {
			@view-transition {
				navigation: auto;
			}
		}
		
		@media (prefers-reduced-motion: reduce) {
			@view-transition {
				navigation: none;
			}
		}
		
		/* FORCE HEADER STATIC - High specificity */
		header.header.wp-block-template-part,
		header.wp-block-template-part,
		.header.wp-block-template-part {
			view-transition-name: none !important;
		}
		
		/* FORCE NAVIGATION STATIC */
		nav.wp-block-navigation,
		.wp-block-navigation,
		.wp-block-navigation__responsive-container,
		.wp-block-navigation__container {
			view-transition-name: none !important;
		}
		
		/* Main content transitions */
		main.wp-block-group {
			view-transition-name: main-content;
		}
		
		/* Footer transitions */
		footer.wp-block-template-part,
		.footer.wp-block-template-part {
			view-transition-name: footer;
		}
		</style>
		<?php
	}
	
	/**
	 * Add meta tags for view transitions
	 */
	public function add_meta_tags() {
		if ( ! \current_theme_supports( 'view-transitions' ) ) {
			return;
		}
		
		echo '<meta name="view-transition" content="same-origin" />' . "\n";
		echo '<meta name="view-transition-optimization" content="enabled" />' . "\n";
	}
	
	/**
	 * Get transition name for a specific block
	 * 
	 * @param string $block_name Block name
	 * @param int    $post_id Post ID
	 * @return string|false Transition name or false if not applicable
	 */
	private function get_transition_name_for_block( $block_name, $post_id ) {
		$transition_map = array(
			'core/post-title' => 'post-title',
			'core/post-featured-image' => 'post-thumbnail',
			'core/post-content' => 'post-content',
			'core/post-date' => 'post-meta',
			'core/post-excerpt' => 'post-excerpt',
			'core/post-author' => 'post-meta',
			'core/post-terms' => 'post-meta',
			'core/post-navigation-link' => 'post-navigation',
			'core/comments' => 'post-comments',
			'core/image' => 'content-image',
			'core/gallery' => 'content-gallery',
			'core/video' => 'content-video',
			'core/audio' => 'content-audio',
		);
		
		if ( ! isset( $transition_map[ $block_name ] ) ) {
			return false;
		}
		
		$base_name = $transition_map[ $block_name ];
		
		// Create unique names using post ID for post-specific blocks
		if ( in_array( $base_name, array( 'post-title', 'post-thumbnail', 'post-content', 'post-meta', 'post-excerpt' ), true ) ) {
			return \sanitize_html_class( $base_name . '-' . $post_id );
		}
		
		return \sanitize_html_class( $base_name );
	}
	
	/**
	 * Add transition name to content
	 * 
	 * @param string $content HTML content
	 * @param string $transition_name Transition name
	 * @return string Modified content
	 */
	private function add_transition_name_to_content( $content, $transition_name ) {
		if ( ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
			return $this->add_transition_name_fallback( $content, $transition_name );
		}
		
		$processor = new \WP_HTML_Tag_Processor( $content );
		
		if ( $processor->next_tag() ) {
			$existing_style = $processor->get_attribute( 'style' );
			$transition_style = sprintf( 'view-transition-name: %s;', \esc_attr( $transition_name ) );
			
			$new_style = $existing_style ? 
				rtrim( $existing_style, '; ' ) . '; ' . $transition_style : 
				$transition_style;
			
			$processor->set_attribute( 'style', $new_style );
			return $processor->get_updated_html();
		}
		
		return $content;
	}
	
	/**
	 * Fallback method for adding transition names (pre-WP 6.2)
	 * 
	 * @param string $content HTML content
	 * @param string $transition_name Transition name
	 * @return string Modified content
	 */
	private function add_transition_name_fallback( $content, $transition_name ) {
		$transition_style = sprintf( 'view-transition-name: %s;', \esc_attr( $transition_name ) );
		
		$pattern = '/(<[a-zA-Z][^>]*?)(style\s*=\s*["\'])([^"\']*?)(["\'])/';
		$replacement = '$1$2$3; ' . $transition_style . '$4';
		
		if ( preg_match( $pattern, $content ) ) {
			return preg_replace( $pattern, $replacement, $content, 1 );
		}
		
		$pattern = '/(<[a-zA-Z][^>]*?)(\s*>)/';
		$replacement = '$1 style="' . $transition_style . '"$2';
		
		return preg_replace( $pattern, $replacement, $content, 1 );
	}
	
	/**
	 * Static method to initialize the class
	 */
	public static function init() {
		static $instance = null;
		
		if ( null === $instance ) {
			$instance = new self();
		}
		
		return $instance;
	}
}

/**
 * Initialize Twombly view transitions
 */
function init_view_transitions() {
	View_Transitions::init();
}
\add_action( 'after_setup_theme', __NAMESPACE__ . '\init_view_transitions' );

/**
 * Helper function to check if view transitions are active
 * 
 * @return bool Whether view transitions are active
 */
function has_view_transitions() {
	return \current_theme_supports( 'view-transitions' );
}

/**
 * Helper function to add transition name to any element
 * 
 * @param string $content HTML content
 * @param string $transition_name Transition name
 * @return string Modified content
 */
function add_view_transition_name( $content, $transition_name ) {
	if ( ! has_view_transitions() ) {
		return $content;
	}
	
	$instance = View_Transitions::init();
	return $instance->add_transition_name_to_content( $content, $transition_name );
}

/**
 * =============================================================================
 * CUSTOM CURSOR IMPLEMENTATION
 * =============================================================================
 */

/**
 * Add custom cursor with trailing green dot
 * Mirak-style cursor implementation
 */
\add_action( 'wp_footer', function() {
	// Skip on admin pages and mobile devices
	if ( \is_admin() || \wp_is_mobile() ) {
		return;
	}
	?>
	<style>
		/* Custom cursor styles */
		.cursor-follower {
			width: 10px;
			height: 10px;
			background-color: #54e27e;
			border-radius: 50%;
			position: fixed;
			pointer-events: none;
			z-index: 9999;
			transform: translate(-50%, -50%);
			transition: width 0.3s ease, height 0.3s ease, opacity 0.3s ease;
			opacity: 0.8;
		}
		
		.cursor-follower.hover {
			width: 15px;
			height: 15px;
			opacity: 0.6;
		}
		
		.cursor-follower.click {
			width: 8px;
			height: 8px;
			opacity: 1;
		}
		
		@media (hover: none) {
			.cursor-follower { 
				display: none !important; 
			}
		}
	</style>
	
	<script>
	(function() {
		// Exit if touch device
		if ('ontouchstart' in window) return;
		
		// Create cursor follower element
		var follower = document.createElement('div');
		follower.className = 'cursor-follower';
		document.body.appendChild(follower);
		
		// Initialize positions
		var mouse = { x: window.innerWidth / 2, y: window.innerHeight / 2 };
		var followerPos = { x: window.innerWidth / 2, y: window.innerHeight / 2 };
		
		// Configuration
		var speed = 0.15;          // Smooth following speed
		var offsetDistance = 30;   // Distance from cursor in pixels  
		var offsetAngle = 50;      // Angle in degrees
		
		// Calculate offset
		var angleInRadians = offsetAngle * (Math.PI / 180);
		var offsetX = Math.cos(angleInRadians) * offsetDistance;
		var offsetY = Math.sin(angleInRadians) * offsetDistance;
		
		// Update mouse position on move
		document.addEventListener('mousemove', function(e) {
			mouse.x = e.clientX;
			mouse.y = e.clientY;
		});
		
		// Animation loop
		function animate() {
			var targetX = mouse.x + offsetX;
			var targetY = mouse.y + offsetY;
			
			followerPos.x += (targetX - followerPos.x) * speed;
			followerPos.y += (targetY - followerPos.y) * speed;
			
			follower.style.left = followerPos.x + 'px';
			follower.style.top = followerPos.y + 'px';
			
			requestAnimationFrame(animate);
		}
		animate();
		
		// Interactive elements selector
		var hoverElements = 'a, button, input[type="submit"], input[type="button"], .clickable, [onclick]';
		
		// Hover effects
		document.addEventListener('mouseover', function(e) {
			if (e.target.matches(hoverElements)) {
				follower.classList.add('hover');
			}
		});
		
		document.addEventListener('mouseout', function(e) {
			if (e.target.matches(hoverElements)) {
				follower.classList.remove('hover');
			}
		});
		
		// Click effects
		document.addEventListener('mousedown', function() {
			follower.classList.add('click');
		});
		
		document.addEventListener('mouseup', function() {
			follower.classList.remove('click');
		});
		
		// Window leave/enter
		document.addEventListener('mouseleave', function() {
			follower.style.opacity = '0';
		});
		
		document.addEventListener('mouseenter', function() {
			follower.style.opacity = '0.8';
		});
	})();
	</script>
	<?php
});