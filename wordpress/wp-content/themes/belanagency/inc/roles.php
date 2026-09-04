<?php
/**
 * Custom User Roles and Capabilities Management
 *
 * @package BelanAgency
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Configure roles:
 * - Administrator (полные права)
 * - Автор (публикация/изменение только своего контента)
 * - Адвокат (пока без прав)
 * Удаляем все остальные роли (editor, contributor, subscriber).
 */
function belan_configure_user_roles() {
    // 1. Remove unwanted roles
    remove_role('editor');
    remove_role('contributor');
    remove_role('subscriber');

    global $wp_roles;
    if (isset($wp_roles->roles['administrator'])) {
        $wp_roles->roles['administrator']['name'] = 'Администратор';
    }

    // 2. Ensure Author role has ONLY own content capabilities
    $author_caps = [
        'read'                   => true,
        'upload_files'           => true,
        'edit_posts'             => true,
        'edit_published_posts'   => true,
        'publish_posts'          => true,
        'delete_posts'           => true,
        'delete_published_posts' => true,
    ];

    $author_role = get_role('author');
    if (!$author_role) {
        add_role('author', 'Автор', $author_caps);
    } else {
        global $wp_roles;
        if (isset($wp_roles->roles['author'])) {
            $wp_roles->roles['author']['name'] = 'Автор';
        }
    }

    // 3. Ensure Lawyer (Адвокат) role exists with no rights for now
    $advokat_role = get_role('advokat');
    if (!$advokat_role) {
        add_role('advokat', 'Адвокат', []);
    } else {
        global $wp_roles;
        if (isset($wp_roles->roles['advokat'])) {
            $wp_roles->roles['advokat']['name'] = 'Адвокат';
        }
    }
}
add_action('init', 'belan_configure_user_roles', 1);

/**
 * Filter editable roles in WordPress admin so only allowed roles are selectable
 */
function belan_filter_editable_roles($roles) {
    $allowed = ['administrator', 'author', 'advokat'];
    foreach ($roles as $role_key => $role_data) {
        if (!in_array($role_key, $allowed, true)) {
            unset($roles[$role_key]);
        }
    }
    if (isset($roles['administrator'])) {
        $roles['administrator']['name'] = 'Администратор';
    }
    return $roles;
}
add_filter('editable_roles', 'belan_filter_editable_roles');

// Translate role names in user listings
add_filter('gettext', function ($translation, $text, $domain) {
    if ($text === 'Administrator') {
        return 'Администратор';
    }
    if ($text === 'Author') {
        return 'Автор';
    }
    return $translation;
}, 10, 3);

/**
 * Remove administrative and non-author menus for users without manage_options
 */
add_action('admin_menu', function () {
    if (!current_user_can('manage_options')) {
        remove_menu_page('edit.php?post_type=acf-field-group');
        remove_menu_page('theme-general-settings');
        remove_menu_page('edit.php?post_type=service');
        remove_menu_page('edit.php?post_type=cases');
        remove_menu_page('edit.php?post_type=review');
        remove_menu_page('edit.php?post_type=consultation');
        remove_menu_page('tools.php');
        remove_menu_page('edit-comments.php');
    }
}, 999);

/**
 * Block direct URL access to restricted post types and tools for authors
 */
add_action('load-tools.php', function () {
    if (!current_user_can('manage_options')) {
        wp_die(__('Извините, вам не разрешено просматривать эту страницу.'), 403);
    }
});

add_action('load-edit.php', function () {
    if (!current_user_can('manage_options')) {
        $screen = get_current_screen();
        if ($screen && in_array($screen->post_type, ['service', 'cases', 'review', 'consultation'], true)) {
            wp_die(__('Извините, вам не разрешено просматривать эту страницу.'), 403);
        }
    }
});

add_action('load-post-new.php', function () {
    if (!current_user_can('manage_options')) {
        $screen = get_current_screen();
        if ($screen && in_array($screen->post_type, ['service', 'cases', 'review', 'consultation'], true)) {
            wp_die(__('Извините, вам не разрешено просматривать эту страницу.'), 403);
        }
    }
});
