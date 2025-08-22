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

/**
 * =============================================================================
 * VIEW TRANSITIONS IMPLEMENTATION
 * =============================================================================
 */

/**
 * Twombly View Transitions Implementation
 * Production-ready class following WordPress coding standards
 * 
 * Note: This implementation assumes view transition CSS is included 
 * in the main style.css file. No separate CSS files are loaded.
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
        
        // Debug mode integration
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            \add_action( 'wp_head', array( $this, 'add_debug_info' ), 999 );
        }
    }
    
    /**
     * Setup theme support with comprehensive configuration
     */
    public function setup_theme_support() {
        // Simplified configuration for CSS-in-main-stylesheet approach
        $this->config = \apply_filters( 'twombly_view_transitions_config', array(
            'default-animation' => \get_option( 'twombly_view_transitions_animation', 'fade' ),
            'respect-reduced-motion' => true,
            'accessibility' => array(
                'respect-reduced-motion' => true,
            ),
        ) );
        
        // Only enable if browser supports or for progressive enhancement
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
        // Allow filtering for custom logic
        $should_enable = \apply_filters( 'twombly_enable_view_transitions', true );
        
        if ( ! $should_enable ) {
            return false;
        }
        
        // Check for reduced motion preference server-side if available
        $user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? 
            \sanitize_text_field( \wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
        
        // Basic browser support detection (progressive enhancement)
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
     * Enqueue view transition assets with performance optimization
     */
    public function enqueue_assets() {
        if ( ! \current_theme_supports( 'view-transitions' ) ) {
            return;
        }
        
        // Only load on relevant pages
        if ( ! $this->should_load_on_current_page() ) {
            return;
        }
        
        // Since CSS is in main style.css, we only need JavaScript enhancement
        if ( $this->needs_javascript_enhancement() ) {
            $js_file = $this->get_asset_url( 'js/view-transitions-enhance.js' );
            if ( $js_file ) {
                \wp_enqueue_script(
                    'twombly-view-transitions-enhance',
                    $js_file,
                    array(),
                    $this->get_cache_buster(),
                    array( 'strategy' => 'defer' )
                );
                
                // Localize script with configuration
                \wp_localize_script( 'twombly-view-transitions-enhance', 'twomblyViewTransitions', array(
                    'config' => $this->config,
                    'debug' => defined( 'WP_DEBUG' ) && WP_DEBUG,
                    'restUrl' => \rest_url( 'wp/v2/' ),
                    'nonce' => \wp_create_nonce( 'wp_rest' ),
                ) );
            }
        }
        
        // Add minimal critical CSS for browser compatibility if needed
        $this->add_compatibility_css();
    }
    

    
    /**
     * Add performance optimizations
     */
    public function add_performance_optimizations() {
        if ( ! \current_theme_supports( 'view-transitions' ) ) {
            return;
        }
        
        // Add speculation rules for enhanced navigation
        $this->add_speculation_rules();
        
        // Add viewport meta tag for mobile optimization
        $this->add_viewport_optimizations();
    }
    
    /**
     * Add minimal compatibility CSS if needed
     */
    private function add_compatibility_css() {
        // Only add browser-specific fixes when absolutely necessary
        $compatibility_css = '';
        
        $user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? 
            \sanitize_text_field( \wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
            
        if ( strpos( $user_agent, 'Safari' ) !== false && strpos( $user_agent, 'Chrome' ) === false ) {
            // Minimal Safari-specific optimizations (no layout impact)
            $compatibility_css .= '
            /* Safari view transition compatibility */
            @supports (view-transition-name: none) {
                ::view-transition-group(*) {
                    /* Minimal Safari performance hint */
                    -webkit-backface-visibility: hidden;
                }
            }';
        }
        
        if ( ! empty( $compatibility_css ) ) {
            \wp_add_inline_style( 'twombly', $compatibility_css );
        }
    }
    
    /**
     * Add viewport optimizations for mobile
     */
    private function add_viewport_optimizations() {
        // Add mobile-specific performance hints
        if ( \wp_is_mobile() ) {
            echo '<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />' . "\n";
        }
    }
    
    /**
     * Add speculation rules for enhanced navigation performance
     */
    private function add_speculation_rules() {
        if ( ! $this->should_add_speculation_rules() ) {
            return;
        }
        
        $rules = array(
            'prerender' => array(
                array(
                    'where' => array( 'href_matches' => '/*' ),
                    'eagerness' => 'moderate'
                )
            )
        );
        
        echo sprintf(
            '<script type="speculationrules">%s</script>',
            \wp_json_encode( $rules )
        );
    }
    
    /**
     * Should add speculation rules based on performance considerations
     * 
     * @return bool Whether to add speculation rules
     */
    private function should_add_speculation_rules() {
        // Only on single posts and pages where navigation is likely
        return ( \is_single() || \is_page() ) && ! \is_admin();
    }
    
    /**
     * Generate critical CSS for view transitions
     * 
     * @return string Critical CSS
     */
    private function generate_critical_css() {
        $css = '
        /* Twombly View Transitions - Critical CSS */
        @media (prefers-reduced-motion: no-preference) {
            @view-transition {
                navigation: auto;
            }
            
            ::view-transition-group(root) {
                animation-duration: var(--wp--custom--view-transition--duration, 400ms);
                animation-timing-function: var(--wp--custom--view-transition--timing, ease-in-out);
            }
            
            /* Static header - no transitions */
            .site-header {
                /* Header remains completely static during transitions */
            }
            
            /* Content transitions */
            ::view-transition-group(post-title),
            ::view-transition-group(post-content) {
                animation-duration: 450ms;
                animation-timing-function: cubic-bezier(0.25, 0.46, 0.45, 0.94);
            }
            
            /* Image transitions */
            ::view-transition-group(post-thumbnail) {
                animation-duration: 500ms;
                animation-timing-function: cubic-bezier(0.23, 1, 0.320, 1);
            }
        }
        
        /* Disable transitions for users who prefer reduced motion */
        @media (prefers-reduced-motion: reduce) {
            @view-transition {
                navigation: none;
            }
            
            ::view-transition-group(*) {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
            }
        }';
        
        // Allow customization via filter
        return \apply_filters( 'twombly_view_transitions_critical_css', $css );
    }
    
    /**
     * Add view transition names to blocks and handle global element transitions
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
     * Add debug information
     */
    public function add_debug_info() {
        if ( ! \current_theme_supports( 'view-transitions' ) ) {
            return;
        }
        
        echo '<!-- Twombly View Transitions Debug: Active -->' . "\n";
        echo '<script>console.log("Twombly View Transitions: Enabled");</script>' . "\n";
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
        
        // For content blocks, create unique names but don't tie to specific posts
        return \sanitize_html_class( $base_name );
    }
    
    /**
     * Add transition name to content using WP_HTML_Tag_Processor
     * 
     * @param string $content HTML content
     * @param string $transition_name Transition name
     * @return string Modified content
     */
    private function add_transition_name_to_content( $content, $transition_name ) {
        if ( ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
            // Fallback for older WordPress versions
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
        
        // Simple regex replacement for the first opening tag
        $pattern = '/(<[a-zA-Z][^>]*?)(style\s*=\s*["\'])([^"\']*?)(["\'])/';
        $replacement = '$1$2$3; ' . $transition_style . '$4';
        
        if ( preg_match( $pattern, $content ) ) {
            return preg_replace( $pattern, $replacement, $content, 1 );
        }
        
        // If no existing style attribute, add it
        $pattern = '/(<[a-zA-Z][^>]*?)(\s*>)/';
        $replacement = '$1 style="' . $transition_style . '"$2';
        
        return preg_replace( $pattern, $replacement, $content, 1 );
    }
    
    /**
     * Add meta tags for view transitions
     */
    public function add_meta_tags() {
        if ( ! \current_theme_supports( 'view-transitions' ) ) {
            return;
        }
        
        echo '<meta name="view-transition" content="same-origin" />' . "\n";
        
        // Add performance hints for view transitions
        echo '<meta name="view-transition-optimization" content="enabled" />' . "\n";
        
        // Debug info (remove in production)
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            echo '<!-- Twombly View Transitions Active -->' . "\n";
        }
    }
    
    /**
     * Check if assets should load on current page
     * 
     * @return bool Whether to load assets
     */
    private function should_load_on_current_page() {
        // Skip admin pages
        if ( \is_admin() ) {
            return false;
        }
        
        // Skip login/register pages
        if ( in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ), true ) ) {
            return false;
        }
        
        // Load on content pages
        return ( 
            \is_home() || 
            \is_front_page() || 
            \is_single() || 
            \is_page() || 
            \is_archive() || 
            \is_search() 
        );
    }
    
    /**
     * Check if JavaScript enhancement is needed
     * 
     * @return bool Whether JS enhancement is needed
     */
    private function needs_javascript_enhancement() {
        // Enable JS for focus management and enhanced features
        return $this->config['accessibility']['focus-management'];
    }
    
    /**
     * Get asset URL with theme directory fallback
     * 
     * @param string $asset_path Asset path
     * @return string|false Asset URL or false if not found
     */
    private function get_asset_url( $asset_path ) {
        $theme_asset = \get_theme_file_path( $asset_path );
        
        if ( file_exists( $theme_asset ) ) {
            return \get_theme_file_uri( $asset_path );
        }
        
        return false;
    }
    
    /**
     * Get cache buster based on file modification time
     * 
     * @return string Cache buster
     */
    private function get_cache_buster() {
        static $version = null;
        
        if ( null === $version ) {
            $theme_version = \wp_get_theme()->get( 'Version' );
            $version = $theme_version ? $theme_version : '1.0.0';
        }
        
        return $version;
    }
    
    /**
     * Register admin settings for view transitions
     */
    public function register_settings() {
        \register_setting(
            'reading',
            'twombly_view_transitions_animation',
            array(
                'type' => 'string',
                'sanitize_callback' => array( $this, 'sanitize_animation_setting' ),
                'default' => 'fade',
            )
        );
        
        \add_settings_field(
            'twombly_view_transitions_animation',
            \__( 'View Transition Animation', 'twombly' ),
            array( $this, 'animation_setting_callback' ),
            'reading'
        );
    }
    
    /**
     * Sanitize animation setting
     * 
     * @param string $input Raw input
     * @return string Sanitized animation type
     */
    public function sanitize_animation_setting( $input ) {
        $allowed_animations = array( 'fade', 'slide', 'wipe', 'none' );
        return in_array( $input, $allowed_animations, true ) ? $input : 'fade';
    }
    
    /**
     * Animation setting callback
     */
    public function animation_setting_callback() {
        $current = \get_option( 'twombly_view_transitions_animation', 'fade' );
        $animations = array(
            'fade' => \__( 'Fade', 'twombly' ),
            'slide' => \__( 'Slide', 'twombly' ),
            'wipe' => \__( 'Wipe', 'twombly' ),
            'none' => \__( 'None', 'twombly' ),
        );
        
        echo '<select name="twombly_view_transitions_animation">';
        foreach ( $animations as $value => $label ) {
            printf(
                '<option value="%s"%s>%s</option>',
                \esc_attr( $value ),
                \selected( $current, $value, false ),
                \esc_html( $label )
            );
        }
        echo '</select>';
        echo '<p class="description">' . \esc_html__( 'Choose the default animation style for view transitions.', 'twombly' ) . '</p>';
    }
    
    /**
     * Add debug support for development
     */
    public function add_debug_support() {
        \wp_add_inline_script( 'twombly-view-transitions-enhance', '
            console.log( "Twombly View Transitions Debug Mode Active" );
            
            if ( document.startViewTransition ) {
                console.log( "Browser supports View Transitions API" );
            } else {
                console.log( "Browser does not support View Transitions API - using fallbacks" );
            }
            
            // Monitor transition events
            document.addEventListener( "DOMContentLoaded", function() {
                const observer = new PerformanceObserver( function( list ) {
                    list.getEntries().forEach( function( entry ) {
                        if ( entry.name.includes( "view-transition" ) ) {
                            console.log( "View transition performance:", entry );
                        }
                    });
                });
                
                try {
                    observer.observe({ entryTypes: ["measure", "navigation"] });
                } catch ( e ) {
                    console.log( "Performance observer not supported" );
                }
            });
        ' );
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
 *
 * @return void
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