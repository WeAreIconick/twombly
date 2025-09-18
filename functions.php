<?php
/**
 * Twombly theme functions and definitions.
 *
 * @link    https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Iconick/Twombly
 */

namespace Twombly\Theme;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * =============================================================================
 * ACCESSIBILITY FEATURES
 * =============================================================================
 */

/**
 * Add skip-to-content link for better accessibility
 *
 * @return void
 */
function add_skip_to_content_link() {
	echo '<a class="skip-link screen-reader-text" href="#main">' . esc_html__( 'Skip to content', 'twombly' ) . '</a>';
}
add_action( 'wp_body_open', __NAMESPACE__ . '\add_skip_to_content_link' );

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
	$theme_dir = get_stylesheet_directory();
	$theme_uri = get_stylesheet_directory_uri();
	$style_path = $theme_dir . '/style.css';
	
	// Check if file exists and is readable
	if ( ! file_exists( $style_path ) || ! is_readable( $style_path ) ) {
		return;
	}
	
	wp_enqueue_style(
		'twombly',
		get_stylesheet_uri(),
		array(),
		filemtime( $style_path )
	);
}
add_action( 'enqueue_block_assets', __NAMESPACE__ . '\enqueue_block_assets' );

/**
 * Handle addition of any enqueues for the block editor only.
 *
 * @return void
 */
function enqueue_block_editor_assets() {
	$theme_dir = get_stylesheet_directory();
	$js_path = $theme_dir . '/js/block-editor.js';
	
	// Check if file exists and is readable
	if ( ! file_exists( $js_path ) || ! is_readable( $js_path ) ) {
		return;
	}
	
	wp_enqueue_script(
		'twombly-editor',
		get_theme_file_uri( 'js/block-editor.js' ),
		array(),
		filemtime( $js_path ),
		true
	);
}
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\enqueue_block_editor_assets' );

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
	 * @var array|null
	 */
	private $browser_support = null;
	
	/**
	 * Allowed transition names to prevent injection
	 * 
	 * @var array
	 */
	private $allowed_transition_names = array(
		'post-title', 'post-thumbnail', 'post-content', 'post-meta', 
		'post-excerpt', 'post-navigation', 'post-comments', 
		'content-image', 'content-gallery', 'content-video', 'content-audio'
	);
	
	/**
	 * Initialize view transitions functionality
	 */
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'setup_theme_support' ) );
		add_action( 'wp_head', array( $this, 'add_meta_tags' ), 1 );
		add_action( 'wp_head', array( $this, 'add_view_transition_css' ), 10 );
		add_filter( 'render_block', array( $this, 'add_transition_names' ), 10, 2 );
	}
	
	/**
	 * Setup theme support with comprehensive configuration
	 */
	public function setup_theme_support() {
		$default_animation = get_option( 'twombly_view_transitions_animation', 'fade' );
		
		// Validate animation option
		$allowed_animations = array( 'fade', 'slide', 'none' );
		if ( ! in_array( $default_animation, $allowed_animations, true ) ) {
			$default_animation = 'fade';
		}
		
		$this->config = apply_filters( 'twombly_view_transitions_config', array(
			'default-animation' => $default_animation,
			'respect-reduced-motion' => true,
			'accessibility' => array(
				'respect-reduced-motion' => true,
			),
		) );
		
		if ( $this->should_enable_view_transitions() ) {
			add_theme_support( 'view-transitions', $this->config );
		}
	}
	
	/**
	 * Determine if view transitions should be enabled
	 * 
	 * @return bool Whether to enable view transitions
	 */
	private function should_enable_view_transitions() {
		$should_enable = apply_filters( 'twombly_enable_view_transitions', true );
		
		if ( ! $should_enable ) {
			return false;
		}
		
		$user_agent = $this->get_sanitized_user_agent();
		$browser_info = $this->get_browser_support( $user_agent );
		
		return $browser_info['supports_view_transitions'];
	}
	
	/**
	 * Get sanitized user agent string
	 * 
	 * @return string Sanitized user agent
	 */
	private function get_sanitized_user_agent() {
		if ( ! isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
			return '';
		}
		
		$user_agent = sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) );
		
		// Additional validation - ensure it's not suspiciously long
		if ( strlen( $user_agent ) > 500 ) {
			return '';
		}
		
		return $user_agent;
	}
	
	/**
	 * Get browser support information with enhanced validation
	 * 
	 * @param string $user_agent User agent string
	 * @return array Browser support information
	 */
	private function get_browser_support( $user_agent ) {
		if ( null !== $this->browser_support ) {
			return $this->browser_support;
		}
		
		$this->browser_support = array(
			'supports_view_transitions' => false, // Default to false for security
			'browser' => 'unknown',
			'version' => 0
		);
		
		if ( empty( $user_agent ) ) {
			return $this->browser_support;
		}
		
		// Chrome 111+, Edge 111+ (more specific regex)
		if ( preg_match( '/Chrome\/(\d+)\.[\d.]+/', $user_agent, $matches ) ) {
			$version = absint( $matches[1] );
			$this->browser_support['browser'] = 'chrome';
			$this->browser_support['version'] = $version;
			$this->browser_support['supports_view_transitions'] = $version >= 111;
		} 
		// Safari (more specific detection, excluding Chrome)
		elseif ( preg_match( '/Safari\/(\d+)\.[\d.]*/', $user_agent, $matches ) && 
		         false === strpos( $user_agent, 'Chrome' ) && 
		         false === strpos( $user_agent, 'Chromium' ) ) {
			$version = absint( $matches[1] );
			$this->browser_support['browser'] = 'safari';
			$this->browser_support['version'] = $version;
			// Safari 18+ support (build number approximation)
			$this->browser_support['supports_view_transitions'] = $version >= 605;
		}
		
		return $this->browser_support;
	}
	
	/**
	 * Add view transition names to blocks with enhanced security
	 * 
	 * @param string $block_content Block content
	 * @param array  $block Block data
	 * @return string Modified block content
	 */
	public function add_transition_names( $block_content, $block ) {
		if ( ! current_theme_supports( 'view-transitions' ) || empty( $block_content ) ) {
			return $block_content;
		}
		
		global $post;
		if ( ! $post instanceof \WP_Post ) {
			return $block_content;
		}
		
		// Validate post ID
		$post_id = absint( $post->ID );
		if ( $post_id <= 0 ) {
			return $block_content;
		}
		
		$block_name = isset( $block['blockName'] ) ? sanitize_text_field( $block['blockName'] ) : '';
		$transition_name = $this->get_transition_name_for_block( $block_name, $post_id );
		
		if ( ! $transition_name ) {
			return $block_content;
		}
		
		return $this->add_transition_name_to_content( $block_content, $transition_name );
	}
	
	/**
	 * Add essential view transition CSS directly to head with security measures
	 */
	public function add_view_transition_css() {
		if ( ! current_theme_supports( 'view-transitions' ) ) {
			return;
		}
		
		// Use wp_add_inline_style for better security when possible
		if ( wp_style_is( 'twombly', 'enqueued' ) ) {
			$css = $this->get_view_transition_css();
			wp_add_inline_style( 'twombly', $css );
			return;
		}
		
		// Fallback to direct output with proper escaping
		echo '<style id="twombly-view-transitions-core">';
		echo wp_strip_all_tags( $this->get_view_transition_css() );
		echo '</style>';
	}
	
	/**
	 * Get view transition CSS
	 * 
	 * @return string CSS content
	 */
	private function get_view_transition_css() {
		return '
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
		}';
	}
	
	/**
	 * Add meta tags for view transitions with proper escaping
	 */
	public function add_meta_tags() {
		if ( ! current_theme_supports( 'view-transitions' ) ) {
			return;
		}
		
		echo '<meta name="view-transition" content="same-origin" />' . "\n";
		echo '<meta name="view-transition-optimization" content="enabled" />' . "\n";
	}
	
	/**
	 * Get transition name for a specific block with enhanced validation
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
		
		// Validate against allowed names
		if ( ! in_array( $base_name, $this->allowed_transition_names, true ) ) {
			return false;
		}
		
		// Create unique names using post ID for post-specific blocks
		$post_specific_blocks = array( 'post-title', 'post-thumbnail', 'post-content', 'post-meta', 'post-excerpt' );
		if ( in_array( $base_name, $post_specific_blocks, true ) ) {
			return sanitize_html_class( $base_name . '-' . absint( $post_id ) );
		}
		
		return sanitize_html_class( $base_name );
	}
	
	/**
	 * Add transition name to content with enhanced security
	 * 
	 * @param string $content HTML content
	 * @param string $transition_name Transition name
	 * @return string Modified content
	 */
	private function add_transition_name_to_content( $content, $transition_name ) {
		// Validate transition name
		$transition_name = sanitize_html_class( $transition_name );
		if ( empty( $transition_name ) ) {
			return $content;
		}
		
		// Use WP_HTML_Tag_Processor if available (WP 6.2+)
		if ( class_exists( '\WP_HTML_Tag_Processor' ) ) {
			return $this->add_transition_name_with_processor( $content, $transition_name );
		}
		
		return $this->add_transition_name_fallback( $content, $transition_name );
	}
	
	/**
	 * Add transition name using WP_HTML_Tag_Processor
	 * 
	 * @param string $content HTML content
	 * @param string $transition_name Validated transition name
	 * @return string Modified content
	 */
	private function add_transition_name_with_processor( $content, $transition_name ) {
		$processor = new \WP_HTML_Tag_Processor( $content );
		
		if ( $processor->next_tag() ) {
			$existing_style = $processor->get_attribute( 'style' );
			$transition_style = 'view-transition-name: ' . esc_attr( $transition_name ) . ';';
			
			$new_style = $existing_style ? 
				rtrim( $existing_style, '; ' ) . '; ' . $transition_style : 
				$transition_style;
			
			$processor->set_attribute( 'style', $new_style );
			return $processor->get_updated_html();
		}
		
		return $content;
	}
	
	/**
	 * Fallback method for adding transition names with enhanced security
	 * 
	 * @param string $content HTML content
	 * @param string $transition_name Validated transition name
	 * @return string Modified content
	 */
	private function add_transition_name_fallback( $content, $transition_name ) {
		$transition_style = 'view-transition-name: ' . esc_attr( $transition_name ) . ';';
		
		// More specific regex patterns for better security
		$pattern = '/(<[a-zA-Z][a-zA-Z0-9\-]*[^>]*?)(style\s*=\s*["\'])([^"\']*?)(["\'])/';
		$replacement = '$1$2$3; ' . $transition_style . '$4';
		
		if ( preg_match( $pattern, $content ) ) {
			return preg_replace( $pattern, $replacement, $content, 1 );
		}
		
		// Fallback for elements without style attribute
		$pattern = '/(<[a-zA-Z][a-zA-Z0-9\-]*[^>]*?)(\s*>)/';
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
add_action( 'after_setup_theme', __NAMESPACE__ . '\init_view_transitions' );

/**
 * Helper function to check if view transitions are active
 * 
 * @return bool Whether view transitions are active
 */
function has_view_transitions() {
	return current_theme_supports( 'view-transitions' );
}

/**
 * Helper function to add transition name to any element
 * 
 * @param string $content HTML content
 * @param string $transition_name Transition name
 * @return string Modified content
 */
function add_view_transition_name( $content, $transition_name ) {
	if ( ! has_view_transitions() || empty( $content ) || empty( $transition_name ) ) {
		return $content;
	}
	
	$instance = View_Transitions::init();
	return $instance->add_transition_name_to_content( $content, $transition_name );
}

/**
 * =============================================================================
 * THEME COLOR UTILITIES
 * =============================================================================
 */

/**
 * Validate hex color
 * 
 * @param string $color Color to validate
 * @return string|false Valid hex color or false
 */
function validate_hex_color( $color ) {
	$color = sanitize_text_field( $color );
	
	// Remove # if present
	$color = ltrim( $color, '#' );
	
	// Check if it's a valid hex color (3 or 6 characters)
	if ( preg_match( '/^([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $color ) ) {
		return '#' . $color;
	}
	
	return false;
}

/**
 * Get the primary theme color with enhanced security
 * 
 * @return string Primary color hex value with fallback
 */
function get_theme_primary_color() {
	// Try to get from Global Styles first (WordPress 5.9+)
	if ( class_exists( '\WP_Theme_JSON_Resolver' ) ) {
		$theme_json = \WP_Theme_JSON_Resolver::get_merged_data();
		$settings = $theme_json->get_settings();
		$colors = isset( $settings['color']['palette']['theme'] ) ? $settings['color']['palette']['theme'] : array();
		
		// Look for primary color in theme palette
		foreach ( $colors as $color ) {
			if ( isset( $color['slug'], $color['color'] ) && 
			     in_array( $color['slug'], array( 'primary', 'accent', 'main' ), true ) ) {
				$validated_color = validate_hex_color( $color['color'] );
				if ( $validated_color ) {
					return $validated_color;
				}
			}
		}
		
		// Fallback to first color if no primary found
		if ( ! empty( $colors ) && isset( $colors[0]['color'] ) ) {
			$validated_color = validate_hex_color( $colors[0]['color'] );
			if ( $validated_color ) {
				return $validated_color;
			}
		}
	}
	
	// Try customizer option (legacy themes)
	$primary_color = get_theme_mod( 'primary_color' );
	if ( $primary_color ) {
		$validated_color = validate_hex_color( $primary_color );
		if ( $validated_color ) {
			return $validated_color;
		}
	}
	
	// Try CSS custom property detection (modern themes) - with validation
	$custom_css = wp_get_custom_css();
	if ( $custom_css && preg_match( '/--wp--preset--color--primary:\s*([^;]+);/', $custom_css, $matches ) ) {
		$color = trim( $matches[1] );
		$validated_color = validate_hex_color( $color );
		if ( $validated_color ) {
			return $validated_color;
		}
	}
	
	// Ultimate fallback - validated safe color
	$fallback = apply_filters( 'twombly_primary_color_fallback', '#54e27e' );
	$validated_fallback = validate_hex_color( $fallback );
	
	return $validated_fallback ? $validated_fallback : '#000000';
}

/**
 * =============================================================================
 * CUSTOM CURSOR IMPLEMENTATION
 * =============================================================================
 */

/**
 * Add custom cursor with enhanced security and performance
 */
add_action( 'wp_footer', function() {
	// Skip on admin pages, mobile devices, and for logged-in users editing
	if ( is_admin() || wp_is_mobile() ) {
		return;
	}
	
	// Skip if user is in customizer or block editor
	if ( is_customize_preview() || ( function_exists( 'is_block_editor' ) && is_block_editor() ) ) {
		return;
	}
	
	// Get and validate theme primary color
	$primary_color = get_theme_primary_color();
	
	// Generate nonce for inline scripts (if CSP is implemented)
	$nonce = wp_create_nonce( 'twombly_cursor_script' );
	?>
	<style id="twombly-custom-cursor">
		/* Custom cursor styles */
		.cursor-follower {
			width: 10px;
			height: 10px;
			background-color: <?php echo esc_attr( $primary_color ); ?>;
			border-radius: 50%;
			position: fixed;
			pointer-events: none;
			z-index: 9999;
			transform: translate(-50%, -50%);
			transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), 
			           height 0.3s cubic-bezier(0.4, 0, 0.2, 1), 
			           opacity 0.3s ease;
			opacity: 0.8;
			will-change: transform, opacity;
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
		
		/* Hide on touch devices and when user prefers reduced motion */
		@media (hover: none), (prefers-reduced-motion: reduce) {
			.cursor-follower { 
				display: none !important; 
			}
		}
		
		/* Ensure body doesn't interfere */
		body {
			cursor: auto;
		}
	</style>
	
	<script id="twombly-cursor-script" data-nonce="<?php echo esc_attr( $nonce ); ?>">
	(function() {
		'use strict';
		
		// Verify nonce for additional security (if needed for CSP)
		var scriptElement = document.getElementById('twombly-cursor-script');
		var nonce = scriptElement ? scriptElement.getAttribute('data-nonce') : '';
		
		// Exit early for touch devices or reduced motion preference
		if ('ontouchstart' in window || 
		    !window.requestAnimationFrame ||
		    window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
			return;
		}
		
		// Debounced resize handler
		function debounce(func, wait) {
			var timeout;
			return function executedFunction() {
				var later = function() {
					clearTimeout(timeout);
					func.apply(this, arguments);
				};
				clearTimeout(timeout);
				timeout = setTimeout(later, wait);
			};
		}
		
		// Create cursor follower element with error handling
		var follower;
		try {
			follower = document.createElement('div');
			follower.className = 'cursor-follower';
			follower.setAttribute('aria-hidden', 'true');
			follower.setAttribute('role', 'presentation');
			document.body.appendChild(follower);
		} catch (e) {
			console.warn('Twombly: Could not create cursor follower');
			return;
		}
		
		// Initialize positions safely
		var windowWidth = window.innerWidth || document.documentElement.clientWidth;
		var windowHeight = window.innerHeight || document.documentElement.clientHeight;
		
		var mouse = { x: windowWidth / 2, y: windowHeight / 2 };
		var followerPos = { x: windowWidth / 2, y: windowHeight / 2 };
		var animationId;
		var isAnimating = false;
		
		// Configuration with validation
		var config = {
			speed: Math.max(0.05, Math.min(0.5, 0.15)),        // Constrain speed
			offsetDistance: Math.max(10, Math.min(100, 30)),   // Constrain offset
			offsetAngle: Math.max(0, Math.min(360, 50))        // Constrain angle
		};
		
		// Calculate offset
		var angleInRadians = config.offsetAngle * (Math.PI / 180);
		var offsetX = Math.cos(angleInRadians) * config.offsetDistance;
		var offsetY = Math.sin(angleInRadians) * config.offsetDistance;
		
		// Start animation with error handling
		function startAnimation() {
			if (!isAnimating) {
				isAnimating = true;
				animate();
			}
		}
		
		// Optimized animation loop with error handling
		function animate() {
			try {
				var targetX = mouse.x + offsetX;
				var targetY = mouse.y + offsetY;
				
				// Smooth interpolation with bounds checking
				followerPos.x += (targetX - followerPos.x) * config.speed;
				followerPos.y += (targetY - followerPos.y) * config.speed;
				
				// Bounds checking
				followerPos.x = Math.max(-100, Math.min(windowWidth + 100, followerPos.x));
				followerPos.y = Math.max(-100, Math.min(windowHeight + 100, followerPos.y));
				
				// Apply position with validation
				if (follower && follower.style) {
					follower.style.left = Math.round(followerPos.x) + 'px';
					follower.style.top = Math.round(followerPos.y) + 'px';
				}
				
				if (isAnimating) {
					animationId = requestAnimationFrame(animate);
				}
			} catch (e) {
				console.warn('Twombly: Animation error', e);
				isAnimating = false;
			}
		}
		
		// Update mouse position with bounds checking
		function updateMousePosition(e) {
			if (e && typeof e.clientX === 'number' && typeof e.clientY === 'number') {
				mouse.x = Math.max(0, Math.min(windowWidth, e.clientX));
				mouse.y = Math.max(0, Math.min(windowHeight, e.clientY));
			}
		}
		
		// Interactive elements selector (more secure)
		var hoverElements = [
			'a', 'button', 
			'input[type="submit"]', 'input[type="button"]', 
			'.clickable', '[role="button"]', '.wp-block-button__link'
		];
		var hoverSelector = hoverElements.join(', ');
		
		// Event handlers with error handling
		function handleMouseMove(e) {
			updateMousePosition(e);
		}
		
		function handleMouseOver(e) {
			try {
				if (e.target && (e.target.matches(hoverSelector) || e.target.closest(hoverSelector))) {
					if (follower) follower.classList.add('hover');
				}
			} catch (err) {
				// Silent fail for selector errors
			}
		}
		
		function handleMouseOut(e) {
			try {
				if (e.target && (e.target.matches(hoverSelector) || e.target.closest(hoverSelector))) {
					if (follower) follower.classList.remove('hover');
				}
			} catch (err) {
				// Silent fail for selector errors
			}
		}
		
		function handleMouseDown() {
			if (follower) follower.classList.add('click');
		}
		
		function handleMouseUp() {
			if (follower) follower.classList.remove('click');
		}
		
		function handleMouseLeave() {
			if (follower) follower.style.opacity = '0';
		}
		
		function handleMouseEnter() {
			if (follower) follower.style.opacity = '0.8';
		}
		
		// Debounced resize handler
		var handleResize = debounce(function() {
			windowWidth = window.innerWidth || document.documentElement.clientWidth;
			windowHeight = window.innerHeight || document.documentElement.clientHeight;
			
			// Reset position on resize
			mouse.x = windowWidth / 2;
			mouse.y = windowHeight / 2;
			followerPos.x = mouse.x;
			followerPos.y = mouse.y;
		}, 150);
		
		// Add event listeners with passive option where appropriate
		document.addEventListener('mousemove', handleMouseMove, { passive: true });
		document.addEventListener('mouseover', handleMouseOver, { passive: true });
		document.addEventListener('mouseout', handleMouseOut, { passive: true });
		document.addEventListener('mousedown', handleMouseDown, { passive: true });
		document.addEventListener('mouseup', handleMouseUp, { passive: true });
		document.addEventListener('mouseleave', handleMouseLeave, { passive: true });
		document.addEventListener('mouseenter', handleMouseEnter, { passive: true });
		window.addEventListener('resize', handleResize, { passive: true });
		
		// Start animation
		startAnimation();
		
		// Cleanup function (for potential future use)
		window.twomblyCleanupCursor = function() {
			isAnimating = false;
			if (animationId) {
				cancelAnimationFrame(animationId);
			}
			if (follower && follower.parentNode) {
				follower.parentNode.removeChild(follower);
			}
		};
		
	})();
	</script>
	<?php
}, 20);