<?php

/**
 * bitspecter functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package bitspecter
 */

define("CONTACT_PAGE_ID", "-1");
define("REMOVE_COMMENTS", false);
define("ALLOW_SVG", true);
define("DISABLE_FILE_EDIT", true);

// remove unnecessary header information
function remove_header_info()
{
	remove_action('wp_head', 'feed_links_extra', 3);
	remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'wlwmanifest_link');
	remove_action('wp_head', 'wp_generator');
	remove_action('wp_head', 'start_post_rel_link');
	remove_action('wp_head', 'index_rel_link');
	remove_action('wp_head', 'parent_post_rel_link', 10, 0);
	remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0); // for WordPress >= 3.0
}
add_action('init', 'remove_header_info');

// remove wp version meta tag and from rss feed
add_filter('the_generator', '__return_false');

// remove wp version param from any enqueued scripts
function at_remove_wp_ver_css_js($src)
{
	if (strpos($src, 'ver='))
		$src = remove_query_arg('ver', $src);
	return $src;
}
add_filter('style_loader_src', 'at_remove_wp_ver_css_js', 9999);
add_filter('script_loader_src', 'at_remove_wp_ver_css_js', 9999);

/*Disable ping back scanner and complete xmlrpc class. */
add_filter('wp_xmlrpc_server_class', '__return_false');
add_filter('xmlrpc_enabled', '__return_false');

// remove various feeds
function fb_disable_feed()
{
	wp_die(__('No feed available,please visit our <a href="' . get_bloginfo('url') . '">homepage</a>!'));
}

// disable redirect to login page:
// http://wordpress.stackexchange.com/questions/85529/how-to-disable-multisite-sign-up-page
function rbz_prevent_multisite_signup()
{
	wp_redirect(site_url());
	die();
}
add_action('signup_header', 'rbz_prevent_multisite_signup');


//remove xpingback header
function remove_x_pingback($headers)
{
	unset($headers['X-Pingback']);
	return $headers;
}
add_filter('wp_headers', 'remove_x_pingback');


if (DISABLE_FILE_EDIT) {
	// disable_file_edit - disable file editor in admin panel for security reason
	function disable_file_edit()
	{
		define('DISALLOW_FILE_EDIT', TRUE);
	}

	add_action('init', 'disable_file_edit');
}

// Load assets
if (!is_admin()) {
	// Inlcuding CSS
	wp_enqueue_style('styles', get_template_directory_uri() . '/assets/css/main.css', false, '1', 'all');

	// Including JS
	wp_enqueue_script('app', get_template_directory_uri() . '/assets/js/app.js', 1, true);
}

if( current_user_can('edit_others_pages') ) {
	add_action('wp_head', 'remove_branding');
	function remove_branding()
	{
		echo '<style>.update-nag, .updated, .notice, .error, .is-dismissible, #wp-admin-bar-wp-logo { display: none; }</style>';
	}
}

if (is_admin()) {
	function remove_editor()
	{
		remove_post_type_support('page', 'editor');
	}
	add_action('admin_init', 'remove_editor');

	add_action('admin_enqueue_scripts', 'bs_admin_theme_style');
	add_action('login_enqueue_scripts', 'bs_admin_theme_style');
	function bs_admin_theme_style()
	{
		echo '
		<style>
			.update-nag, 
			.updated, 
			.notice, 
			.error, 
			.is-dismissible, 
			#wp-admin-bar-wp-logo, 
			.welcome-panel-content { 
				display: none !important; 
			}
		</style>';
	}
}

add_action('admin_bar_menu', 'admin_bar_item', 500);
function admin_bar_item(WP_Admin_Bar $admin_bar)
{
	$admin_bar->add_menu(array(
		'id' => 'bitspecter',
		'title' => 'Bitspecter',
		'href'  => 'https://bitspecter.com',
		'meta' => [
			'target' => '_blank',
		]
	));

	$admin_bar->add_menu(array(
		'parent' => 'bitspecter',
		'title' => 'Support',
		'href'  => 'https://bitspecter.com/kontakt',
		'meta' => [
			'target' => '_blank',
		]
	));
}

if (ALLOW_SVG) {
	// Allow to upload SVG files
	function cc_mime_types($mimes)
	{
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}
	add_filter('upload_mimes', 'cc_mime_types');
}

// Add custom classes to wp_nav_menu
function add_additional_class_on_li($classes, $item, $args)
{
	if (isset($args->add_li_class)) {
		$classes[] = $args->add_li_class;
	}
	return $classes;
}
add_filter('nav_menu_css_class', 'add_additional_class_on_li', 1, 3);

function add_menu_link_class($atts, $item, $args)
{
	if (property_exists($args, 'link_class')) {
		$atts['class'] = $args->link_class;
	}
	return $atts;
}
add_filter('nav_menu_link_attributes', 'add_menu_link_class', 1, 3);

add_filter('nav_menu_css_class', 'special_nav_class', 10, 2);

function special_nav_class($classes, $item)
{
	if (in_array('current-menu-item', $classes)) {
		$classes[] = 'active ';
	}
	return $classes;
}



/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function bitspecter_setup()
{
	// Translation file
	load_theme_textdomain('bitspecter', get_template_directory() . '/languages');

	// Add default posts and comments RSS feed links to head.
	add_theme_support('automatic-feed-links');

	// Add a title tag in the <head> of your pages for SEO and accessibility purposes
	add_theme_support('title-tag');

	// Add HTML5 to Wordpress output 
	add_theme_support('html5');

	/* post thumbnails */
	add_theme_support('post-thumbnails');

	// Registration navigation menus
	register_nav_menus(
		array(
			'primary' => esc_html__('Primary', 'bitspecter'),
			'footer' => esc_html__('Footer', 'bitspecter'),
			'sidebar' => esc_html__('Sidebar', 'bitspecter'),
		)
	);

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action('after_setup_theme', 'bitspecter_setup');


/**
 * Enqueue scripts and styles.
 */
function bitspecter_scripts()
{
	if (!REMOVE_COMMENTS) {
		if (is_singular() && comments_open() && get_option('thread_comments')) {
			wp_enqueue_script('comment-reply');
		}
	}
}

add_action('wp_enqueue_scripts', 'bitspecter_scripts');


add_action('wp_print_scripts', function () {
	global $post;
	if (!is_page(CONTACT_PAGE_ID)) {
		wp_dequeue_script('google-recaptcha');
		wp_dequeue_script('wpcf7-recaptcha');
	}
});


if (REMOVE_COMMENTS) {
	add_action('admin_init', function () {
		// Redirect any user trying to access comments page
		global $pagenow;

		if ($pagenow === 'edit-comments.php') {
			wp_redirect(admin_url());
			exit;
		}

		// Remove comments metabox from dashboard
		remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');

		// Disable support for comments and trackbacks in post types
		foreach (get_post_types() as $post_type) {
			if (post_type_supports($post_type, 'comments')) {
				remove_post_type_support($post_type, 'comments');
				remove_post_type_support($post_type, 'trackbacks');
			}
		}
	});

	// Close comments on the front-end
	add_filter('comments_open', '__return_false', 20, 2);
	add_filter('pings_open', '__return_false', 20, 2);

	// Hide existing comments
	add_filter('comments_array', '__return_empty_array', 10, 2);

	// Remove comments page in menu
	add_action('admin_menu', function () {
		remove_menu_page('edit-comments.php');
	});

	// Remove comments links from admin bar
	add_action('init', function () {
		if (is_admin_bar_showing()) {
			remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
		}
	});
}
