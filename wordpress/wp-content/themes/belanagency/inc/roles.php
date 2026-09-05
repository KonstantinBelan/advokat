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
 * - Адвокат (ответы на вопросы, управление своим профилем)
 * - Автор (публикация/изменение только своего контента)
 * - Пользователь / Subscriber (авторизованный клиент, может задавать вопросы)
 * Удаляем только editor и contributor.
 */
function belan_configure_user_roles() {
    // 1. Remove unnecessary legacy roles
    remove_role('editor');
    remove_role('contributor');

    global $wp_roles;
    if (isset($wp_roles->roles['administrator'])) {
        $wp_roles->roles['administrator']['name'] = 'Администратор';
    }

    // 2. Lawyer (Адвокат) role
    $advokat_caps = [
        'read'         => true,
        'upload_files' => true,
    ];
    $advokat_role = get_role('advokat');
    if (!$advokat_role) {
        add_role('advokat', 'Адвокат', $advokat_caps);
    } else {
        $advokat_role->add_cap('read');
        $advokat_role->add_cap('upload_files');
        if (isset($wp_roles->roles['advokat'])) {
            $wp_roles->roles['advokat']['name'] = 'Адвокат';
        }
    }

    // 3. Regular Client / Subscriber (Пользователь) role
    $subscriber_caps = [
        'read'         => true,
        'upload_files' => true,
    ];
    $subscriber_role = get_role('subscriber');
    if (!$subscriber_role) {
        add_role('subscriber', 'Пользователь', $subscriber_caps);
    } else {
        $subscriber_role->add_cap('read');
        $subscriber_role->add_cap('upload_files');
        if (isset($wp_roles->roles['subscriber'])) {
            $wp_roles->roles['subscriber']['name'] = 'Пользователь';
        }
    }

    // 4. Ensure Author role has ONLY own content capabilities
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
        if (isset($wp_roles->roles['author'])) {
            $wp_roles->roles['author']['name'] = 'Автор';
        }
    }
}
add_action('init', 'belan_configure_user_roles', 1);

/**
 * Filter editable roles in WordPress admin so Administrator can select:
 * - Администратор
 * - Адвокат
 * - Автор
 * - Пользователь
 */
function belan_filter_editable_roles($roles) {
    $allowed = ['administrator', 'advokat', 'author', 'subscriber'];
    foreach ($roles as $role_key => $role_data) {
        if (!in_array($role_key, $allowed, true)) {
            unset($roles[$role_key]);
        }
    }
    if (isset($roles['administrator'])) {
        $roles['administrator']['name'] = 'Администратор';
    }
    if (isset($roles['advokat'])) {
        $roles['advokat']['name'] = 'Адвокат';
    }
    if (isset($roles['subscriber'])) {
        $roles['subscriber']['name'] = 'Пользователь';
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
    if ($text === 'Subscriber') {
        return 'Пользователь';
    }
    if ($text === 'advokat') {
        return 'Адвокат';
    }
    return $translation;
}, 10, 3);

/**
 * Check if the user is an expert (advokat role) and not an administrator
 *
 * @param WP_User|int|null $user
 * @return bool
 */
function belan_is_advokat_user($user = null) {
    if (!$user) {
        $user = wp_get_current_user();
    } elseif (is_numeric($user)) {
        $user = get_userdata($user);
    }
    if (!$user || empty($user->ID)) {
        return false;
    }
    // Administrator retains full permissions
    if (user_can($user, 'manage_options')) {
        return false;
    }
    return in_array('advokat', (array) $user->roles, true);
}

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
        remove_menu_page('edit.php?post_type=consultation_answer');
        remove_menu_page('tools.php');
        remove_menu_page('edit-comments.php');

        // Remove standard dashboard & profile pages for experts
        if (belan_is_advokat_user()) {
            remove_menu_page('index.php');
            remove_menu_page('profile.php');
            remove_menu_page('separator1');
            remove_menu_page('separator2');
            remove_menu_page('separator-last');
        }
    }
}, 999);

/**
 * Redirect regular clients (subscribers) away from wp-admin to the website
 */
add_action('admin_init', function () {
    if (defined('DOING_AJAX') && DOING_AJAX) {
        return;
    }
    $user = wp_get_current_user();
    if ($user && in_array('subscriber', (array) $user->roles, true) && !current_user_can('edit_posts')) {
        wp_safe_redirect(home_url('/consultation/'));
        exit;
    }
});

/**
 * Block direct URL access to restricted post types and tools for non-admins
 * If the user is an expert (advokat), redirect them to their expert profile instead of showing an error.
 */
add_action('load-tools.php', function () {
    if (!current_user_can('manage_options')) {
        if (function_exists('belan_is_advokat_user') && belan_is_advokat_user()) {
            wp_safe_redirect(admin_url('admin.php?page=belan-expert-profile'));
            exit;
        }
        wp_die(__('Извините, вам не разрешено просматривать эту страницу.'), 403);
    }
});

add_action('load-edit.php', function () {
    if (!current_user_can('manage_options')) {
        if (function_exists('belan_is_advokat_user') && belan_is_advokat_user()) {
            wp_safe_redirect(admin_url('admin.php?page=belan-expert-profile'));
            exit;
        }
        $screen = get_current_screen();
        if ($screen && in_array($screen->post_type, ['service', 'cases', 'review', 'consultation', 'consultation_answer'], true)) {
            wp_die(__('Извините, вам не разрешено просматривать эту страницу.'), 403);
        }
    }
});

add_action('load-post-new.php', function () {
    if (!current_user_can('manage_options')) {
        if (function_exists('belan_is_advokat_user') && belan_is_advokat_user()) {
            wp_safe_redirect(admin_url('admin.php?page=belan-expert-profile'));
            exit;
        }
        $screen = get_current_screen();
        if ($screen && in_array($screen->post_type, ['service', 'cases', 'review', 'consultation', 'consultation_answer'], true)) {
            wp_die(__('Извините, вам не разрешено просматривать эту страницу.'), 403);
        }
    }
});
