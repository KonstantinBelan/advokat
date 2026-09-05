<?php
/**
 * Belan Agency Theme Functions and Definitions
 *
 * @package BelanAgency
 */

if (!defined('ABSPATH')) {
    exit;
}

// Load Inc Modules
require_once get_template_directory() . '/inc/helpers.php';
require_once get_template_directory() . '/inc/post-types.php';
require_once get_template_directory() . '/inc/acf-fields.php';
require_once get_template_directory() . '/inc/ajax-forms.php';
require_once get_template_directory() . '/inc/walker-nav-menu.php';
require_once get_template_directory() . '/inc/roles.php';
require_once get_template_directory() . '/inc/ajax-load-more.php';
require_once get_template_directory() . '/inc/article-content.php';
require_once get_template_directory() . '/inc/qa-platform.php';

/**
 * Theme Setup
 */
function belan_setup() {
    // Title tag support
    add_theme_support('title-tag');

    // Featured image support
    add_theme_support('post-thumbnails');

    // Editor styles and responsive embeds
    add_theme_support('editor-styles');
    add_editor_style('assets/css/style.min.css');
    add_editor_style('assets/css/editor-style.css');
    add_theme_support('responsive-embeds');
    add_theme_support('align-wide');

    // HTML5 markup support
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ]);

    // Register Navigation Menus
    register_nav_menus([
        'primary' => 'Главное меню (Шапка)',
        'footer'  => 'Меню подвала',
    ]);
}
add_action('after_setup_theme', 'belan_setup');

/**
 * Enqueue Styles and Scripts
 */
function belan_scripts() {
    $theme_dir = get_template_directory();
    $theme_uri = get_template_directory_uri();

    // Google Fonts - Inter
    wp_enqueue_style('belan-google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap', [], null);

    // Swiper CSS
    wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css', [], '12.0.0');

    // Main Theme CSS
    $css_path = $theme_dir . '/assets/css/style.min.css';
    $css_ver = file_exists($css_path) ? filemtime($css_path) : '1.0.0';
    wp_enqueue_style('belan-style', $theme_uri . '/assets/css/style.min.css', ['swiper-css'], $css_ver);

    // Swiper JS
    wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js', [], '12.0.0', true);

    // Main Theme JS
    $js_path = $theme_dir . '/assets/js/main.js';
    $js_ver = file_exists($js_path) ? filemtime($js_path) : '1.0.0';
    wp_enqueue_script('belan-main', $theme_uri . '/assets/js/main.js', ['swiper-js'], $js_ver, true);

    // Localize script for AJAX & Nonces
    wp_localize_script('belan-main', 'belan_ajax', [
        'url'   => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('belan_nonce'),
    ]);
}
add_action('wp_enqueue_scripts', 'belan_scripts');

/**
 * Preload Anticva Custom Font
 */
function belan_preload_fonts() {
    $font_url = get_template_directory_uri() . '/assets/fonts/Anticva-Regular.woff2';
    echo '<link rel="preload" href="' . esc_url($font_url) . '" as="font" type="font/woff2" crossorigin>' . "\n";
}
add_action('wp_head', 'belan_preload_fonts', 1);
