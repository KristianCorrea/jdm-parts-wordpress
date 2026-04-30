<?php
/**
 * JDM Miami functions and definitions
 *
 * @package JDM_Miami
 */

if ( ! defined( '_S_VERSION' ) ) {
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Theme setup.
 */
function jdm_miami_setup() {
	load_theme_textdomain( 'jdm_miami', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );

	register_nav_menus(
		array(
			'menu-1'      => esc_html__( 'Primary', 'jdm_miami' ),
			'footer-shop' => esc_html__( 'Footer - Shop', 'jdm_miami' ),
			'footer-info' => esc_html__( 'Footer - Company', 'jdm_miami' ),
		)
	);

	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	add_theme_support( 'customize-selective-refresh-widgets' );

	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 220,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'jdm_miami_setup' );

/**
 * Content width.
 */
function jdm_miami_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'jdm_miami_content_width', 1200 );
}
add_action( 'after_setup_theme', 'jdm_miami_content_width', 0 );

/**
 * Register a single sidebar area. Woo pages hide the sidebar via CSS.
 */
function jdm_miami_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'jdm_miami' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'jdm_miami' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'jdm_miami_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function jdm_miami_scripts() {
	$theme_uri = get_template_directory_uri();
	$theme_dir = get_template_directory();

	// Google Fonts: Inter (body/UI) + Bebas Neue (display).
	wp_enqueue_style(
		'jdm_miami-fonts',
		'https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600&display=swap',
		array(),
		null
	);

	// Keep WordPress theme metadata stylesheet loaded (required).
	wp_enqueue_style( 'jdm_miami-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'jdm_miami-style', 'rtl', 'replace' );

	// Compiled Tailwind + design system.
	$tailwind_path = $theme_dir . '/assets/css/tailwind.css';
	$tailwind_ver  = file_exists( $tailwind_path ) ? filemtime( $tailwind_path ) : _S_VERSION;
	wp_enqueue_style(
		'jdm_miami-tailwind',
		$theme_uri . '/assets/css/tailwind.css',
		array( 'jdm_miami-style' ),
		$tailwind_ver
	);

	wp_enqueue_script( 'jdm_miami-navigation', $theme_uri . '/js/navigation.js', array(), _S_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'jdm_miami_scripts' );

/**
 * Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Theme enhancements.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';


/**
 * Jetpack compatibility (optional).
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * WooCommerce compatibility.
 */
if ( class_exists( 'WooCommerce' ) ) {
	require get_template_directory() . '/inc/woocommerce.php';
}

/**
 * -------------------------------------------------------------
 * Helper: safe term URL with a fallback.
 * Returns the term archive URL for a given slug, or $fallback
 * if the term does not exist yet (avoids WP_Error fatals).
 * -------------------------------------------------------------
 */
function jdm_miami_term_url( $slug, $taxonomy, $fallback = '' ) {
	$link = get_term_link( $slug, $taxonomy );
	return is_wp_error( $link ) ? $fallback : $link;
}

/**
 * User ID for auto-created theme pages (first administrator, else 1).
 *
 * @return int
 */
function jdm_miami_default_page_author_id() {
	static $id = null;
	if ( null !== $id ) {
		return $id;
	}
	$admins = get_users(
		array(
			'role'   => 'administrator',
			'number' => 1,
			'fields' => 'ID',
		)
	);
	$id = ! empty( $admins[0] ) ? (int) $admins[0] : 1;
	return $id;
}

/**
 * Create the About page if missing so /about/ and header/footer links do not 404.
 */
function jdm_miami_ensure_about_page() {
	static $ran = false;
	if ( $ran ) {
		return;
	}
	$ran = true;

	if ( ! is_blog_installed() ) {
		return;
	}

	$cached_id = (int) get_option( 'jdm_miami_about_page_id', 0 );
	if ( $cached_id && 'publish' === get_post_status( $cached_id ) && 'page' === get_post_type( $cached_id ) ) {
		return;
	}

	$by_path = get_page_by_path( 'about', OBJECT, 'page' );
	if ( $by_path ) {
		if ( 'trash' === $by_path->post_status ) {
			wp_untrash_post( $by_path->ID );
		}
		if ( 'publish' !== get_post_status( $by_path->ID ) ) {
			wp_update_post(
				array(
					'ID'          => $by_path->ID,
					'post_status' => 'publish',
				)
			);
		}
		update_option( 'jdm_miami_about_page_id', $by_path->ID );
		return;
	}

	$new_id = wp_insert_post(
		array(
			'post_title'   => __( 'About', 'jdm_miami' ),
			'post_name'    => 'about',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => '',
			'post_author'  => jdm_miami_default_page_author_id(),
		),
		true
	);

	if ( is_wp_error( $new_id ) || ! $new_id ) {
		return;
	}

	update_option( 'jdm_miami_about_page_id', (int) $new_id );
	flush_rewrite_rules( false );
}
add_action( 'init', 'jdm_miami_ensure_about_page', 5 );

/**
 * Permalink for the About page (WordPress loads `page-about.php` for slug `about`).
 *
 * @return string Canonical About URL, or `/about/` if the Page has not been created yet.
 */
function jdm_miami_about_page_url() {
	$page = get_page_by_path( 'about', OBJECT, 'page' );
	if ( $page && 'publish' === $page->post_status ) {
		return get_permalink( $page );
	}
	return home_url( '/about/' );
}

/**
 * Render fallback primary navigation links when no WP menu is assigned.
 *
 * Keeps desktop and mobile fallback navigation in sync.
 */
function jdm_miami_fallback_primary_menu() {
	?>
	<ul>
		<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'jdm_miami' ); ?></a></li>
		<?php if ( class_exists( 'WooCommerce' ) ) : ?>
			<li><a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Shop', 'jdm_miami' ); ?></a></li>
		<?php endif; ?>
		<li><a href="<?php echo esc_url( jdm_miami_about_page_url() ); ?>"><?php esc_html_e( 'About', 'jdm_miami' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'jdm_miami' ); ?></a></li>
	</ul>
	<?php
}

/**
 * -------------------------------------------------------------
 * Helper: render the brand / logo area.
 * Falls back to a stylized wordmark when no custom logo is set.
 * -------------------------------------------------------------
 */
function jdm_miami_brand() {
	if ( has_custom_logo() ) {
		the_custom_logo();
		return;
	}
	?>
	<a class="jdm-wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
		<span>JDM</span><em>Miami</em>
	</a>
	<?php
}

/**
 * -------------------------------------------------------------
 * Helper: cart count (safe when WC is inactive).
 * -------------------------------------------------------------
 */
function jdm_miami_cart_count() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return 0;
	}
	return intval( WC()->cart->get_cart_contents_count() );
}

/**
 * Refresh header cart count via AJAX fragment.
 */
function jdm_miami_cart_count_fragment( $fragments ) {
	ob_start();
	?>
	<span class="jdm-cart-count" data-jdm-cart-count><?php echo esc_html( jdm_miami_cart_count() ); ?></span>
	<?php
	$fragments['span[data-jdm-cart-count]'] = ob_get_clean();
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'jdm_miami_cart_count_fragment' );

/**
 * Inline SVG icon helper.
 */
function jdm_miami_icon( $name ) {
	$icons = array(
		'cart'    => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39A2 2 0 0 0 9.66 16H19a2 2 0 0 0 1.96-1.61L23 6H6"/></svg>',
		'user'    => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
		'search'  => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>',
		'menu'    => '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="7" x2="20" y2="7"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="17" x2="20" y2="17"/></svg>',
		'close'   => '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><line x1="6" y1="6" x2="18" y2="18"/><line x1="6" y1="18" x2="18" y2="6"/></svg>',
		'arrow'   => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>',
		'spark'   => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v6M12 16v6M4.93 4.93l4.24 4.24M14.83 14.83l4.24 4.24M2 12h6M16 12h6M4.93 19.07l4.24-4.24M14.83 9.17l4.24-4.24"/></svg>',
		'engine'  => '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M7 10h2V7h4v3h3l2 2v4h2v2h-2v2H7v-2H5v-2h2v-4l2-2"/><path d="M11 14h2"/></svg>',
		'shield'  => '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>',
		'truck'   => '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>',
		'tools'   => '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>',
	);

	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}

	return $icons[ $name ];
}
