<?php
/**
 * Helper Functions for Belan Agency Theme
 *
 * @package BelanAgency
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Return asset URI
 */
function belan_asset($path = '') {
    return get_template_directory_uri() . '/assets/' . ltrim($path, '/');
}

/**
 * Clean phone string for tel: link
 */
function belan_phone_clean($phone) {
    return preg_replace('/[^0-9+]/', '', $phone);
}

/**
 * Get ACF field with fallback
 */
function belan_field($name, $post_id = false, $default = '') {
    if (function_exists('get_field')) {
        $val = get_field($name, $post_id);
        if ($val !== null && $val !== '' && $val !== false) {
            return $val;
        }
    }
    return $default;
}

/**
 * Get ACF option field with fallback
 */
function belan_option($name, $default = '') {
    if (function_exists('get_field')) {
        $val = get_field($name, 'option');
        if ($val !== null && $val !== '' && $val !== false) {
            return $val;
        }
    }
    return $default;
}

/**
 * Render responsive picture tag for theme assets or attachment IDs
 */
function belan_picture($image_name, $alt = '', $extra_classes = '', $sizes_attr = '', $width = '', $height = '', $loading = 'lazy') {
    // If $image_name is an attachment ID
    if (is_numeric($image_name)) {
        $img_url = wp_get_attachment_image_url($image_name, 'full');
        $img_alt = $alt ?: get_post_meta($image_name, '_wp_attachment_image_alt', true);
        echo '<img src="' . esc_url($img_url) . '" alt="' . esc_attr($img_alt) . '" class="' . esc_attr($extra_classes) . '" loading="' . esc_attr($loading) . '" decoding="async"';
        if ($width) echo ' width="' . esc_attr($width) . '"';
        if ($height) echo ' height="' . esc_attr($height) . '"';
        echo '>';
        return;
    }

    // Otherwise it's a theme asset name e.g. "hero", "about", "cases-seal", "expertise-1"
    $base = trim($image_name);
    $assets_url = get_template_directory_uri() . '/assets/img/';
    $assets_dir = get_template_directory() . '/assets/img/';

    // Detect format (png or jpg)
    $ext = 'jpg';
    if (file_exists($assets_dir . $base . '.png') || file_exists($assets_dir . $base . '-2x.png')) {
        $ext = 'png';
    }

    $has_sm = file_exists($assets_dir . $base . '-sm.avif') || file_exists($assets_dir . $base . '-mobile.avif');
    $sm_suffix = file_exists($assets_dir . $base . '-mobile.avif') ? '-mobile' : '-sm';
    $has_2x = file_exists($assets_dir . $base . '-2x.avif');

    echo '<picture' . ($extra_classes ? ' class="' . esc_attr($extra_classes) . '"' : '') . '>';
    
    // AVIF source
    if (file_exists($assets_dir . $base . '.avif') || $has_sm || $has_2x) {
        $srcset_avif = [];
        if ($has_sm) $srcset_avif[] = $assets_url . $base . $sm_suffix . '.avif 480w';
        if (file_exists($assets_dir . $base . '.avif')) $srcset_avif[] = $assets_url . $base . '.avif 1200w';
        if ($has_2x) $srcset_avif[] = $assets_url . $base . '-2x.avif 2400w';
        if (!empty($srcset_avif)) {
            echo '<source type="image/avif" srcset="' . esc_attr(implode(', ', $srcset_avif)) . '"' . ($sizes_attr ? ' sizes="' . esc_attr($sizes_attr) . '"' : '') . '>';
        }
    }

    // WebP source
    if (file_exists($assets_dir . $base . '.webp') || $has_sm || $has_2x) {
        $srcset_webp = [];
        if ($has_sm && file_exists($assets_dir . $base . $sm_suffix . '.webp')) $srcset_webp[] = $assets_url . $base . $sm_suffix . '.webp 480w';
        if (file_exists($assets_dir . $base . '.webp')) $srcset_webp[] = $assets_url . $base . '.webp 1200w';
        if ($has_2x && file_exists($assets_dir . $base . '-2x.webp')) $srcset_webp[] = $assets_url . $base . '-2x.webp 2400w';
        if (!empty($srcset_webp)) {
            echo '<source type="image/webp" srcset="' . esc_attr(implode(', ', $srcset_webp)) . '"' . ($sizes_attr ? ' sizes="' . esc_attr($sizes_attr) . '"' : '') . '>';
        }
    }

    // Fallback IMG
    $img_src = $assets_url . $base . '.' . $ext;
    if (!file_exists($assets_dir . $base . '.' . $ext)) {
        if (file_exists($assets_dir . $base . '.jpg')) $img_src = $assets_url . $base . '.jpg';
        elseif (file_exists($assets_dir . $base . '.png')) $img_src = $assets_url . $base . '.png';
    }

    echo '<img src="' . esc_url($img_src) . '" alt="' . esc_attr($alt) . '" loading="' . esc_attr($loading) . '" decoding="async"';
    if ($width) echo ' width="' . esc_attr($width) . '"';
    if ($height) echo ' height="' . esc_attr($height) . '"';
    echo '>';

    echo '</picture>';
}

/**
 * Render Breadcrumbs Component
 */
function belan_breadcrumbs($custom_current = '') {
    if (is_front_page()) {
        return;
    }
    ?>
    <nav class="breadcrumbs" aria-label="Хлебные крошки">
        <div class="container">
            <ol class="breadcrumbs__list">
                <li class="breadcrumbs__item">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="breadcrumbs__link">Главная</a>
                </li>
                <?php
                if (is_singular('service')) {
                    $terms = get_the_terms(get_the_ID(), 'service_category');
                    echo '<li class="breadcrumbs__item"><a href="' . esc_url(home_url('/services/')) . '" class="breadcrumbs__link">Услуги</a></li>';
                    if ($terms && !is_wp_error($terms)) {
                        $term = array_shift($terms);
                        echo '<li class="breadcrumbs__item"><a href="' . esc_url(get_term_link($term)) . '" class="breadcrumbs__link">' . esc_html($term->name) . '</a></li>';
                    }
                    echo '<li class="breadcrumbs__item"><span class="breadcrumbs__current">' . esc_html(get_the_title()) . '</span></li>';
                } elseif (is_singular('cases')) {
                    echo '<li class="breadcrumbs__item"><a href="' . esc_url(home_url('/cases/')) . '" class="breadcrumbs__link">Кейсы</a></li>';
                    echo '<li class="breadcrumbs__item"><span class="breadcrumbs__current">' . esc_html(get_the_title()) . '</span></li>';
                } elseif (is_singular('consultation')) {
                    echo '<li class="breadcrumbs__item"><a href="' . esc_url(home_url('/consultation/')) . '" class="breadcrumbs__link">Вопросы и ответы</a></li>';
                    echo '<li class="breadcrumbs__item"><span class="breadcrumbs__current">' . esc_html(get_the_title()) . '</span></li>';
                } elseif (is_singular('news')) {
                    echo '<li class="breadcrumbs__item"><a href="' . esc_url(home_url('/news/')) . '" class="breadcrumbs__link">Новости</a></li>';
                    echo '<li class="breadcrumbs__item"><span class="breadcrumbs__current">' . esc_html(get_the_title()) . '</span></li>';
                } elseif (is_single()) {
                    echo '<li class="breadcrumbs__item"><a href="' . esc_url(home_url('/articles/')) . '" class="breadcrumbs__link">Статьи</a></li>';
                    echo '<li class="breadcrumbs__item"><span class="breadcrumbs__current">' . esc_html(get_the_title()) . '</span></li>';
                } elseif (is_tax('consultation_category')) {
                    $term = get_queried_object();
                    echo '<li class="breadcrumbs__item"><a href="' . esc_url(home_url('/consultation/')) . '" class="breadcrumbs__link">Вопросы и ответы</a></li>';
                    echo '<li class="breadcrumbs__item"><span class="breadcrumbs__current">' . esc_html($term->name) . '</span></li>';
                } elseif (is_tax('service_category')) {
                    $term = get_queried_object();
                    echo '<li class="breadcrumbs__item"><a href="' . esc_url(home_url('/services/')) . '" class="breadcrumbs__link">Услуги</a></li>';
                    echo '<li class="breadcrumbs__item"><span class="breadcrumbs__current">' . esc_html($term->name) . '</span></li>';
                } elseif (is_page()) {
                    if ($custom_current) {
                        echo '<li class="breadcrumbs__item"><span class="breadcrumbs__current">' . esc_html($custom_current) . '</span></li>';
                    } else {
                        echo '<li class="breadcrumbs__item"><span class="breadcrumbs__current">' . esc_html(get_the_title()) . '</span></li>';
                    }
                } elseif (is_category()) {
                    echo '<li class="breadcrumbs__item"><a href="' . esc_url(home_url('/articles/')) . '" class="breadcrumbs__link">Статьи</a></li>';
                    echo '<li class="breadcrumbs__item"><span class="breadcrumbs__current">' . esc_html(single_cat_title('', false)) . '</span></li>';
                }
                ?>
            </ol>
        </div>
    </nav>
    <?php
}

/**
 * Return inline SVG icon for service or category slug
 */
function belan_service_icon($slug = '') {
    $slug = trim($slug);

    switch ($slug) {
        case 'spory-s-nedvizhimostyu-i-zhilischnye-spory':
        case 'zhilischnye-spory':
        case 'spory-s-nedvizhimostyu':
            return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" /><polyline points="9 22 9 12 15 12 15 22" /></svg>';

        case 'semejnye-spory-razdel-imushhestva':
            return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M23 21v-2a4 4 0 0 0-3-3.87" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /></svg>';

        case 'nasledstvennye-spory':
            return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" /><polyline points="14 2 14 8 20 8" /><line x1="16" y1="13" x2="8" y2="13" /><line x1="16" y1="17" x2="8" y2="17" /><polyline points="10 9 9 9 8 9" /></svg>';

        case 'trudovye-spory':
            return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2" /><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" /></svg>';

        case 'soprovozhdenie-sdelok-s-nedvizhimostyu':
            return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" /><polyline points="17 21 17 13 7 13 7 21" /><polyline points="7 3 7 8 15 8" /></svg>';

        case 'administrativnye-dela':
            return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="3" x2="12" y2="21" /><path d="M5 6l7-3 7 3" /><path d="M2 13l3-7 3 7a3 3 0 0 1-6 0z" /><path d="M16 13l3-7 3 7a3 3 0 0 1-6 0z" /></svg>';

        case 'ugolovnye-dela':
            return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" /></svg>';

        case 'zaschita-prav-potrebitelej':
            return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" /><polyline points="9 12 11 14 15 10" /></svg>';

        case 'bankrotstvo':
            return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23" /><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" /></svg>';

        case 'ispolnitelnoe-proizvodstvo':
            return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" /><polyline points="22 4 12 14.01 9 11.01" /></svg>';

        case 'vzyskanie-dolgov':
            return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2" /><line x1="1" y1="10" x2="23" y2="10" /></svg>';

        case 'bankrotstvo-predpriyatij':
            return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2" /><line x1="9" y1="22" x2="9" y2="22.01" /><line x1="15" y1="22" x2="15" y2="22.01" /><line x1="9" y1="6" x2="9" y2="6.01" /><line x1="15" y1="6" x2="15" y2="6.01" /><line x1="9" y1="10" x2="9" y2="10.01" /><line x1="15" y1="10" x2="15" y2="10.01" /><line x1="9" y1="14" x2="9" y2="14.01" /><line x1="15" y1="14" x2="15" y2="14.01" /><line x1="9" y1="18" x2="9" y2="18.01" /><line x1="15" y1="18" x2="15" y2="18.01" /></svg>';

        case 'korporativnye-spory':
            return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M22 21v-2a4 4 0 0 0-3-3.87" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /></svg>';

        case 'yuridicheskoe-obsluzhivanie':
            return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7" /><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88" /></svg>';

        case 'zemelnye-spory':
            return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 8.2c0 7.3-8 11.8-8 11.8z" /><circle cx="12" cy="10" r="3" /></svg>';

        case 'zhilischnyj-sertifikat':
            return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7" /><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88" /></svg>';

        case 'ipoteka':
            return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2" /><line x1="2" y1="10" x2="22" y2="10" /></svg>';

        default:
            return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" /></svg>';
    }
}

/**
 * Get category / term description with ACF fallback
 */
function belan_get_term_desc($term) {
    if (empty($term)) {
        return '';
    }

    if (is_numeric($term)) {
        $term = get_term($term, 'service_category');
    }

    if (!$term || is_wp_error($term)) {
        return '';
    }

    // Check ACF field on term
    $acf_desc = belan_field('service_cat_description', 'service_category_' . $term->term_id);
    if (!empty($acf_desc)) {
        return $acf_desc;
    }

    // Check standard WP term description
    if (!empty($term->description)) {
        return $term->description;
    }

    return '';
}

