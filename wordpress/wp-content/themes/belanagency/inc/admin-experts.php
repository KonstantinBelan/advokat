<?php
/**
 * Expert / Lawyer Accounts Administration Module
 * Dedicated management page for legal experts in the "Консультации" section.
 *
 * @package BelanAgency
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 1. Register Submenu in Admin: Консультации -> Эксперты & Dedicated Expert Profile
 */
add_action('admin_menu', 'belan_register_experts_admin_menu');
function belan_register_experts_admin_menu() {
    // Admin management page under "Консультации"
    add_submenu_page(
        'edit.php?post_type=consultation',
        'Эксперты и адвокаты платформы',
        'Эксперты',
        'manage_options',
        'belan-experts',
        'belan_render_experts_admin_page'
    );

    // Dedicated self-profile page for lawyers/experts (accessible with 'read' capability)
    add_menu_page(
        'Профиль эксперта',
        'Профиль эксперта',
        'read',
        'belan-expert-profile',
        'belan_render_expert_self_profile_page',
        'dashicons-businessman',
        2
    );

    // Hide dedicated self-profile from the sidebar for administrators (they use Консультации -> Эксперты)
    if (current_user_can('manage_options')) {
        remove_menu_page('belan-expert-profile');
    }
}

/**
 * 2. Enqueue Media Uploader on the Experts Admin Pages
 */
add_action('admin_enqueue_scripts', function($hook) {
    if (isset($_GET['page']) && in_array($_GET['page'], ['belan-experts', 'belan-expert-profile'], true)) {
        wp_enqueue_media();
    }
});

/**
 * 3. Expert Restrictions & Redirections for advokat Role
 */

// A) Redirect advokat on login to custom expert profile page instead of profile.php
add_filter('login_redirect', 'belan_expert_login_redirect', 30, 3);
function belan_expert_login_redirect($redirect_to, $request, $user) {
    if ($user instanceof WP_User && function_exists('belan_is_advokat_user') && belan_is_advokat_user($user)) {
        return admin_url('admin.php?page=belan-expert-profile');
    }
    return $redirect_to;
}

// B) Filter profile URL so all core links (admin bar, user menu) point to expert profile
add_filter('edit_profile_url', 'belan_expert_profile_url', 30, 3);
function belan_expert_profile_url($url, $user_id, $scheme) {
    if (function_exists('belan_is_advokat_user') && belan_is_advokat_user($user_id)) {
        return admin_url('admin.php?page=belan-expert-profile');
    }
    return $url;
}

// Filter avatar URL in admin to use custom lawyer avatar or default gravatar
add_filter('get_avatar_url', function($url, $id_or_email, $args) {
    $user_id = 0;
    if (is_numeric($id_or_email)) {
        $user_id = (int) $id_or_email;
    } elseif (is_object($id_or_email) && !empty($id_or_email->user_id)) {
        $user_id = (int) $id_or_email->user_id;
    } elseif (is_string($id_or_email) && is_email($id_or_email)) {
        $user = get_user_by('email', $id_or_email);
        if ($user) $user_id = $user->ID;
    }
    if ($user_id) {
        $custom_avatar = get_user_meta($user_id, 'advokat_avatar', true);
        if (!empty($custom_avatar)) {
            return $custom_avatar;
        }
        if (function_exists('belan_is_advokat_user') && belan_is_advokat_user($user_id)) {
            return 'https://secure.gravatar.com/avatar/dca0d4420b1286cd1d4f18418fd161b4?s=128&d=mm&r=g';
        }
    }
    return $url;
}, 10, 3);

// C) Block manual access to index.php, profile.php, edit.php, etc. for advokat users
add_action('admin_init', 'belan_restrict_expert_admin_access');
function belan_restrict_expert_admin_access() {
    if (defined('DOING_AJAX') && DOING_AJAX) {
        return;
    }
    if (!is_user_logged_in()) {
        return;
    }
    if (!function_exists('belan_is_advokat_user') || !belan_is_advokat_user()) {
        return;
    }

    global $pagenow;
    $current_page = $_GET['page'] ?? '';

    // Allow media uploading async
    if ($pagenow === 'async-upload.php') {
        return;
    }

    // Allowed page is only the expert self-profile
    if ($current_page === 'belan-expert-profile') {
        return;
    }

    // Redirect any other page access (index.php, profile.php, edit.php, etc.)
    wp_safe_redirect(admin_url('admin.php?page=belan-expert-profile'));
    exit;
}

// C) Intercept any access denied checks (e.g. attempting to open admin edit page or other restricted pages)
add_action('admin_page_access_denied', function() {
    if (function_exists('belan_is_advokat_user') && belan_is_advokat_user()) {
        wp_safe_redirect(admin_url('admin.php?page=belan-expert-profile'));
        exit;
    }
});

// C) Hide sidebar #adminmenumain completely, expand content to full width, and hide all core/plugin warnings
add_action('admin_head', 'belan_expert_admin_custom_css');
function belan_expert_admin_custom_css() {
    if (!function_exists('belan_is_advokat_user') || !belan_is_advokat_user()) {
        return;
    }
    ?>
    <style id="belan-expert-admin-hide-menu">
        /* Completely hide WordPress admin sidebar */
        #adminmenumain,
        #adminmenuback,
        #adminmenuwrap {
            display: none !important;
            width: 0 !important;
        }

        /* Full width content area without left blank margin */
        #wpcontent,
        #wpfooter {
            margin-left: 0 !important;
            padding-left: 24px !important;
            padding-right: 24px !important;
        }

        /* Hide mobile sidebar toggle */
        #wp-admin-bar-menu-toggle,
        .auto-fold #adminmenumain {
            display: none !important;
        }

        /* Clean up top admin bar */
        #wp-admin-bar-wp-logo,
        #wp-admin-bar-comments,
        #wp-admin-bar-new-content {
            display: none !important;
        }

        /* Hide all WordPress update nags, core/plugin warnings, errors and notices for experts */
        .update-nag,
        #update-nag,
        .notice-warning,
        .notice.inline,
        .notice-warning.inline,
        .inline,
        #wpbody-content > .notice:not(.belan-notice),
        #wpbody-content > .updated:not(.belan-notice),
        #wpbody-content > .error:not(.belan-notice),
        #wpbody-content > div.notice-warning,
        #wpbody-content > div.notice-error:not(.belan-notice),
        #wpbody-content > div.notice-info,
        .wrap > .notice:not(.belan-notice) {
            display: none !important;
        }

        .belan-notice {
            display: block !important;
        }

        /* Wider container */
        .belan-form-container {
            max-width: 1040px !important;
        }
    </style>
    <?php
}

// D) Adjust Admin Bar profile links for advokat to point directly to expert profile
add_action('admin_bar_menu', function($wp_admin_bar) {
    if (!function_exists('belan_is_advokat_user') || !belan_is_advokat_user()) {
        return;
    }
    $my_account = $wp_admin_bar->get_node('my-account');
    if ($my_account) {
        $my_account->href = admin_url('admin.php?page=belan-expert-profile');
        $wp_admin_bar->add_node($my_account);
    }
    $edit_profile = $wp_admin_bar->get_node('edit-profile');
    if ($edit_profile) {
        $edit_profile->href = admin_url('admin.php?page=belan-expert-profile');
        $wp_admin_bar->add_node($edit_profile);
    }
    $user_info = $wp_admin_bar->get_node('user-info');
    if ($user_info) {
        $user_info->href = admin_url('admin.php?page=belan-expert-profile');
        $wp_admin_bar->add_node($user_info);
    }
}, 999);

// E) Suppress all core/plugin admin notices, warnings and update nags for experts
add_action('in_admin_header', function() {
    if (function_exists('belan_is_advokat_user') && belan_is_advokat_user()) {
        remove_action('admin_notices', 'update_nag', 3);
        remove_action('admin_notices', 'maintenance_nag', 10);
        remove_all_actions('admin_notices');
        remove_all_actions('all_admin_notices');
        remove_all_actions('user_admin_notices');
        remove_all_actions('network_admin_notices');
    }
}, 1);

// F) Restrict Media Library for experts: only see their own uploaded media files (avatars)
add_filter('ajax_query_attachments_args', function($query) {
    if (function_exists('belan_is_advokat_user') && belan_is_advokat_user()) {
        $query['author'] = get_current_user_id();
    }
    return $query;
});

add_action('pre_get_posts', function($wp_query) {
    if (is_admin() && function_exists('belan_is_advokat_user') && belan_is_advokat_user()) {
        if ($wp_query->get('post_type') === 'attachment') {
            $wp_query->set('author', get_current_user_id());
        }
    }
});

/**
 * 3. Handle Admin Actions: Add, Edit, Toggle Status, Delete
 */
add_action('admin_init', 'belan_handle_expert_actions');
function belan_handle_expert_actions() {
    if (!isset($_GET['page']) || $_GET['page'] !== 'belan-experts') {
        return;
    }

    if (!current_user_can('manage_options')) {
        return;
    }

    $action = $_POST['expert_action'] ?? $_GET['action'] ?? '';
    if (empty($action)) {
        return;
    }

    $redirect_base = admin_url('edit.php?post_type=consultation&page=belan-experts');

    // A) ADD NEW EXPERT
    if ($action === 'add_expert' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        check_admin_referer('belan_expert_action', 'belan_expert_nonce');

        $login       = sanitize_user(trim($_POST['user_login'] ?? ''));
        $email       = sanitize_email(trim($_POST['user_email'] ?? ''));
        $pass        = trim($_POST['user_pass'] ?? '');
        $full_name   = sanitize_text_field(trim($_POST['full_name'] ?? ''));

        if (empty($login) || empty($email) || empty($pass) || empty($full_name)) {
            wp_safe_redirect(add_query_arg(['msg' => 'error', 'err' => urlencode('Пожалуйста, заполните все обязательные поля (Логин, Email, Пароль, ФИО).')], $redirect_base));
            exit;
        }

        if (username_exists($login)) {
            wp_safe_redirect(add_query_arg(['msg' => 'error', 'err' => urlencode('Пользователь с таким логином уже существует.')], $redirect_base));
            exit;
        }

        if (email_exists($email)) {
            wp_safe_redirect(add_query_arg(['msg' => 'error', 'err' => urlencode('Пользователь с таким Email уже зарегистрирован.')], $redirect_base));
            exit;
        }

        // Split name into first and last name
        $name_parts = explode(' ', $full_name, 2);
        $first_name = $name_parts[0] ?? '';
        $last_name  = $name_parts[1] ?? '';

        $user_id = wp_insert_user([
            'user_login'   => $login,
            'user_email'   => $email,
            'user_pass'    => $pass,
            'display_name' => $full_name,
            'first_name'   => $first_name,
            'last_name'    => $last_name,
            'role'         => 'advokat',
        ]);

        if (is_wp_error($user_id)) {
            wp_safe_redirect(add_query_arg(['msg' => 'error', 'err' => urlencode($user_id->get_error_message())], $redirect_base));
            exit;
        }

        // Save Lawyer Profile Metas
        update_user_meta($user_id, 'author_full_name', $full_name);
        update_user_meta($user_id, 'advokat_reg_number', sanitize_text_field($_POST['advokat_reg_number'] ?? ''));
        update_user_meta($user_id, 'advokat_chamber', sanitize_text_field($_POST['advokat_chamber'] ?? ''));
        update_user_meta($user_id, 'advokat_specialization', sanitize_text_field($_POST['advokat_specialization'] ?? ''));
        update_user_meta($user_id, 'advokat_experience', sanitize_text_field($_POST['advokat_experience'] ?? ''));
        update_user_meta($user_id, 'advokat_phone', sanitize_text_field($_POST['advokat_phone'] ?? ''));
        update_user_meta($user_id, 'advokat_whatsapp', sanitize_text_field($_POST['advokat_whatsapp'] ?? ''));
        update_user_meta($user_id, 'advokat_telegram', sanitize_text_field($_POST['advokat_telegram'] ?? ''));
        update_user_meta($user_id, 'advokat_avatar', esc_url_raw($_POST['advokat_avatar'] ?? ''));
        update_user_meta($user_id, 'advokat_verified', isset($_POST['advokat_verified']) ? '1' : '0');
        update_user_meta($user_id, 'belan_expert_disabled', isset($_POST['expert_disabled']) ? '1' : '0');

        wp_safe_redirect(add_query_arg(['msg' => 'created'], $redirect_base));
        exit;
    }

    // B) EDIT EXPERT
    if ($action === 'edit_expert' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        check_admin_referer('belan_expert_action', 'belan_expert_nonce');

        $user_id = (int) ($_POST['user_id'] ?? 0);
        if (!$user_id) {
            wp_safe_redirect(add_query_arg(['msg' => 'error', 'err' => urlencode('ID эксперта не указан.')], $redirect_base));
            exit;
        }

        $email     = sanitize_email(trim($_POST['user_email'] ?? ''));
        $full_name = sanitize_text_field(trim($_POST['full_name'] ?? ''));
        $pass      = trim($_POST['user_pass'] ?? '');

        if (empty($email) || empty($full_name)) {
            wp_safe_redirect(add_query_arg(['msg' => 'error', 'err' => urlencode('Email и ФИО обязательны для заполнения.')], $redirect_base));
            exit;
        }

        // Check if email taken by another user
        $existing_user = get_user_by('email', $email);
        if ($existing_user && $existing_user->ID !== $user_id) {
            wp_safe_redirect(add_query_arg(['msg' => 'error', 'err' => urlencode('Этот Email уже занят другим пользователем.')], $redirect_base));
            exit;
        }

        $name_parts = explode(' ', $full_name, 2);
        $first_name = $name_parts[0] ?? '';
        $last_name  = $name_parts[1] ?? '';

        $update_data = [
            'ID'           => $user_id,
            'user_email'   => $email,
            'display_name' => $full_name,
            'first_name'   => $first_name,
            'last_name'    => $last_name,
        ];

        if (!empty($pass)) {
            $update_data['user_pass'] = $pass;
        }

        $res = wp_update_user($update_data);
        if (is_wp_error($res)) {
            wp_safe_redirect(add_query_arg(['msg' => 'error', 'err' => urlencode($res->get_error_message())], $redirect_base));
            exit;
        }

        // Save lawyer metas
        update_user_meta($user_id, 'author_full_name', $full_name);
        update_user_meta($user_id, 'advokat_reg_number', sanitize_text_field($_POST['advokat_reg_number'] ?? ''));
        update_user_meta($user_id, 'advokat_chamber', sanitize_text_field($_POST['advokat_chamber'] ?? ''));
        update_user_meta($user_id, 'advokat_specialization', sanitize_text_field($_POST['advokat_specialization'] ?? ''));
        update_user_meta($user_id, 'advokat_experience', sanitize_text_field($_POST['advokat_experience'] ?? ''));
        update_user_meta($user_id, 'advokat_phone', sanitize_text_field($_POST['advokat_phone'] ?? ''));
        update_user_meta($user_id, 'advokat_whatsapp', sanitize_text_field($_POST['advokat_whatsapp'] ?? ''));
        update_user_meta($user_id, 'advokat_telegram', sanitize_text_field($_POST['advokat_telegram'] ?? ''));
        update_user_meta($user_id, 'advokat_avatar', esc_url_raw($_POST['advokat_avatar'] ?? ''));
        update_user_meta($user_id, 'advokat_verified', isset($_POST['advokat_verified']) ? '1' : '0');

        // Cannot disable current logged in admin
        if ($user_id !== get_current_user_id()) {
            update_user_meta($user_id, 'belan_expert_disabled', isset($_POST['expert_disabled']) ? '1' : '0');
        }

        wp_safe_redirect(add_query_arg(['msg' => 'updated'], $redirect_base));
        exit;
    }

    // C) TOGGLE STATUS (ACTIVE / DISABLED)
    if ($action === 'toggle_status') {
        $user_id = (int) ($_GET['user_id'] ?? 0);
        check_admin_referer('toggle_expert_' . $user_id);

        if ($user_id === get_current_user_id()) {
            wp_safe_redirect(add_query_arg(['msg' => 'error', 'err' => urlencode('Нельзя отключить собственный аккаунт администратора.')], $redirect_base));
            exit;
        }

        $current_disabled = get_user_meta($user_id, 'belan_expert_disabled', true) === '1';
        if ($current_disabled) {
            delete_user_meta($user_id, 'belan_expert_disabled');
            $new_msg = 'activated';
        } else {
            update_user_meta($user_id, 'belan_expert_disabled', '1');
            $new_msg = 'deactivated';
        }

        wp_safe_redirect(add_query_arg(['msg' => $new_msg], $redirect_base));
        exit;
    }

    // D) DELETE EXPERT
    if ($action === 'delete') {
        $user_id = (int) ($_GET['user_id'] ?? 0);
        check_admin_referer('delete_expert_' . $user_id);

        if ($user_id === get_current_user_id()) {
            wp_safe_redirect(add_query_arg(['msg' => 'error', 'err' => urlencode('Нельзя удалить собственный аккаунт администратора.')], $redirect_base));
            exit;
        }

        // Reassign all answers to the current administrator so no answers are lost
        require_once ABSPATH . 'wp-admin/includes/user.php';
        $deleted = wp_delete_user($user_id, get_current_user_id());

        if ($deleted) {
            wp_safe_redirect(add_query_arg(['msg' => 'deleted'], $redirect_base));
        } else {
            wp_safe_redirect(add_query_arg(['msg' => 'error', 'err' => urlencode('Не удалось удалить пользователя.')], $redirect_base));
        }
        exit;
    }
}

/**
 * 4. Handle Expert Self Profile Update
 */
add_action('admin_init', 'belan_handle_expert_self_profile_update');
function belan_handle_expert_self_profile_update() {
    if (!isset($_POST['expert_self_action']) || $_POST['expert_self_action'] !== 'update_self_profile') {
        return;
    }

    if (!is_user_logged_in()) {
        return;
    }

    check_admin_referer('belan_expert_self_action', 'belan_expert_self_nonce');

    $user_id      = get_current_user_id();
    $redirect_url = admin_url('admin.php?page=belan-expert-profile');

    $email     = sanitize_email(trim($_POST['user_email'] ?? ''));
    $full_name = sanitize_text_field(trim($_POST['full_name'] ?? ''));
    $pass      = trim($_POST['user_pass'] ?? '');

    if (empty($email) || empty($full_name)) {
        wp_safe_redirect(add_query_arg(['msg' => 'error', 'err' => urlencode('Email и ФИО обязательны для заполнения.')], $redirect_url));
        exit;
    }

    // Check if email taken by another user
    $existing_user = get_user_by('email', $email);
    if ($existing_user && $existing_user->ID !== $user_id) {
        wp_safe_redirect(add_query_arg(['msg' => 'error', 'err' => urlencode('Этот Email уже занят другим пользователем.')], $redirect_url));
        exit;
    }

    $name_parts = explode(' ', $full_name, 2);
    $first_name = $name_parts[0] ?? '';
    $last_name  = $name_parts[1] ?? '';

    $update_data = [
        'ID'           => $user_id,
        'user_email'   => $email,
        'display_name' => $full_name,
        'first_name'   => $first_name,
        'last_name'    => $last_name,
    ];

    if (!empty($pass)) {
        $update_data['user_pass'] = $pass;
    }

    $res = wp_update_user($update_data);
    if (is_wp_error($res)) {
        wp_safe_redirect(add_query_arg(['msg' => 'error', 'err' => urlencode($res->get_error_message())], $redirect_url));
        exit;
    }

    // Save lawyer metas
    update_user_meta($user_id, 'author_full_name', $full_name);
    update_user_meta($user_id, 'advokat_reg_number', sanitize_text_field($_POST['advokat_reg_number'] ?? ''));
    update_user_meta($user_id, 'advokat_chamber', sanitize_text_field($_POST['advokat_chamber'] ?? ''));
    update_user_meta($user_id, 'advokat_specialization', sanitize_text_field($_POST['advokat_specialization'] ?? ''));
    update_user_meta($user_id, 'advokat_experience', sanitize_text_field($_POST['advokat_experience'] ?? ''));
    update_user_meta($user_id, 'advokat_phone', sanitize_text_field($_POST['advokat_phone'] ?? ''));
    update_user_meta($user_id, 'advokat_whatsapp', sanitize_text_field($_POST['advokat_whatsapp'] ?? ''));
    update_user_meta($user_id, 'advokat_telegram', sanitize_text_field($_POST['advokat_telegram'] ?? ''));
    update_user_meta($user_id, 'advokat_avatar', esc_url_raw($_POST['advokat_avatar'] ?? ''));

    wp_safe_redirect(add_query_arg(['msg' => 'updated'], $redirect_url));
    exit;
}

/**
 * 5. Render Main Experts Management Page
 */
function belan_render_experts_admin_page() {
    if (!current_user_can('manage_options')) {
        wp_die('Доступ запрещен.');
    }

    $current_view  = $_GET['view'] ?? 'list';
    $msg           = $_GET['msg'] ?? '';
    $err_msg       = !empty($_GET['err']) ? sanitize_text_field(urldecode($_GET['err'])) : '';
    $redirect_base = admin_url('edit.php?post_type=consultation&page=belan-experts');

    // Query all experts (advokat role + administrator)
    $experts = get_users([
        'role__in' => ['advokat', 'administrator'],
        'orderby'  => 'registered',
        'order'    => 'ASC',
    ]);

    // Statistics counts
    $total_count    = count($experts);
    $active_count   = 0;
    $disabled_count = 0;
    $total_answers  = 0;
    $total_pending  = 0;

    foreach ($experts as $exp) {
        $is_dis = (get_user_meta($exp->ID, 'belan_expert_disabled', true) === '1');
        if ($is_dis) {
            $disabled_count++;
        } else {
            $active_count++;
        }

        $ans_pub = count(get_posts([
            'post_type'      => 'consultation_answer',
            'post_status'    => 'publish',
            'author'         => $exp->ID,
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ]));
        $ans_pend = count(get_posts([
            'post_type'      => 'consultation_answer',
            'post_status'    => 'pending',
            'author'         => $exp->ID,
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ]));

        $total_answers += $ans_pub;
        $total_pending += $ans_pend;
    }

    // Filter by status tab if in list view
    $status_filter = sanitize_text_field($_GET['status'] ?? 'all');
    ?>
    <div class="wrap belan-experts-wrap">
        <style>
            .belan-experts-wrap {
                margin-top: 18px;
            }
            .belan-experts-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 16px;
                margin-bottom: 20px;
            }
            .belan-experts-header h1 {
                margin: 0;
                font-size: 24px;
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .belan-stats-bar {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 14px;
                margin-bottom: 24px;
            }
            .belan-stat-card {
                background: #fff;
                border: 1px solid #ccd0d4;
                border-radius: 8px;
                padding: 14px 18px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            }
            .belan-stat-card__num {
                font-size: 26px;
                font-weight: 700;
                line-height: 1.1;
                margin-bottom: 4px;
            }
            .belan-stat-card__num--primary { color: #2271b1; }
            .belan-stat-card__num--success { color: #46b450; }
            .belan-stat-card__num--danger  { color: #d63638; }
            .belan-stat-card__num--warning { color: #dba617; }
            .belan-stat-card__label {
                font-size: 13px;
                color: #646970;
            }
            .belan-expert-avatar {
                width: 44px;
                height: 44px;
                border-radius: 50%;
                object-fit: cover;
                background: #f0f0f1;
                border: 2px solid #fff;
                box-shadow: 0 1px 3px rgba(0,0,0,0.15);
                vertical-align: middle;
            }
            .belan-badge-pill {
                display: inline-block;
                padding: 3px 8px;
                border-radius: 12px;
                font-size: 11px;
                font-weight: 600;
                line-height: 1.2;
                text-decoration: none;
            }
            .belan-badge-pill--active {
                background: #e7f6e9;
                color: #2e7d32;
                border: 1px solid #c8e6c9;
            }
            .belan-badge-pill--disabled {
                background: #fde8e8;
                color: #c62828;
                border: 1px solid #ffcdd2;
            }
            .belan-badge-pill--verified {
                background: #eef3fc;
                color: #1a56db;
                border: 1px solid #d0e1fd;
            }
            .belan-badge-pill--role {
                background: #f0f0f1;
                color: #50575e;
                font-size: 11px;
            }
            .belan-actions-group {
                display: flex;
                align-items: center;
                gap: 6px;
                flex-wrap: wrap;
            }
            .belan-form-container {
                max-width: 960px;
                background: #fff;
                border: 1px solid #c3c4c7;
                border-radius: 8px;
                padding: 24px 28px;
                margin-top: 15px;
                box-shadow: 0 1px 4px rgba(0,0,0,0.05);
            }
            .belan-form-grid {
                display: grid;
                grid-template-columns: 2fr 1fr;
                gap: 28px;
            }
            @media (max-width: 782px) {
                .belan-form-grid { grid-template-columns: 1fr; }
            }
            .belan-form-field {
                margin-bottom: 16px;
            }
            .belan-form-field label {
                display: block;
                font-weight: 600;
                margin-bottom: 5px;
            }
            .belan-form-field input[type="text"],
            .belan-form-field input[type="email"],
            .belan-form-field input[type="password"] {
                width: 100%;
            }
            .belan-avatar-preview-box {
                margin-top: 10px;
                display: flex;
                align-items: center;
                gap: 14px;
            }
            .belan-avatar-preview-img {
                width: 80px;
                height: 80px;
                border-radius: 50%;
                object-fit: cover;
                border: 2px solid #ccd0d4;
            }
        </style>

        <div class="belan-experts-header">
            <h1>
                <span>⚖️ Эксперты и адвокаты</span>
                <span class="count">(<?php echo esc_html($total_count); ?>)</span>
            </h1>
            <div>
                <?php if ($current_view === 'list') : ?>
                    <a href="<?php echo esc_url(add_query_arg(['view' => 'new'], $redirect_base)); ?>" class="button button-primary button-hero">
                        + Добавить эксперта
                    </a>
                <?php else : ?>
                    <a href="<?php echo esc_url($redirect_base); ?>" class="button button-secondary">
                        &larr; Назад к списку экспертов
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Success & Error Notices -->
        <?php if ($msg === 'created') : ?>
            <div class="notice notice-success is-dismissible">
                <p><strong>✓ Новый эксперт успешно зарегистрирован!</strong> Аккаунт создан с ролью «Адвокат» и может отвечать на вопросы.</p>
            </div>
        <?php elseif ($msg === 'updated') : ?>
            <div class="notice notice-success is-dismissible">
                <p><strong>✓ Данные эксперта успешно сохранены.</strong></p>
            </div>
        <?php elseif ($msg === 'activated') : ?>
            <div class="notice notice-success is-dismissible">
                <p><strong>✓ Профиль эксперта активирован.</strong> Теперь адвокат снова может входить и отвечать на вопросы.</p>
            </div>
        <?php elseif ($msg === 'deactivated') : ?>
            <div class="notice notice-warning is-dismissible">
                <p><strong>⚠️ Аккаунт эксперта отключен.</strong> Доступ к публикации ответов приостановлен.</p>
            </div>
        <?php elseif ($msg === 'deleted') : ?>
            <div class="notice notice-info is-dismissible">
                <p><strong>✓ Аккаунт эксперта удален.</strong> Все его опубликованные ответы сохранены на сайте и переназначены администратору.</p>
            </div>
        <?php elseif ($msg === 'error') : ?>
            <div class="notice notice-error is-dismissible">
                <p><strong>Ошибка:</strong> <?php echo esc_html($err_msg ?: 'Не удалось выполнить операцию.'); ?></p>
            </div>
        <?php endif; ?>

        <?php if ($current_view === 'list') : ?>
            <!-- Metric KPI Summary Bar -->
            <div class="belan-stats-bar">
                <div class="belan-stat-card">
                    <div class="belan-stat-card__num belan-stat-card__num--primary"><?php echo esc_html($total_count); ?></div>
                    <div class="belan-stat-card__label">Всего экспертов</div>
                </div>
                <div class="belan-stat-card">
                    <div class="belan-stat-card__num belan-stat-card__num--success"><?php echo esc_html($active_count); ?></div>
                    <div class="belan-stat-card__label">Активных экспертов</div>
                </div>
                <div class="belan-stat-card">
                    <div class="belan-stat-card__num belan-stat-card__num--danger"><?php echo esc_html($disabled_count); ?></div>
                    <div class="belan-stat-card__label">Отключенных</div>
                </div>
                <div class="belan-stat-card">
                    <div class="belan-stat-card__num belan-stat-card__num--primary"><?php echo esc_html($total_answers); ?></div>
                    <div class="belan-stat-card__label">Опубликованных ответов</div>
                </div>
                <div class="belan-stat-card">
                    <div class="belan-stat-card__num belan-stat-card__num--warning"><?php echo esc_html($total_pending); ?></div>
                    <div class="belan-stat-card__label">Ответов на проверке</div>
                </div>
            </div>

            <!-- Filter Tabs -->
            <ul class="subsubsub" style="margin-bottom: 15px;">
                <li>
                    <a href="<?php echo esc_url($redirect_base); ?>" class="<?php echo $status_filter === 'all' ? 'current' : ''; ?>">
                        Все <span class="count">(<?php echo esc_html($total_count); ?>)</span>
                    </a> |
                </li>
                <li>
                    <a href="<?php echo esc_url(add_query_arg(['status' => 'active'], $redirect_base)); ?>" class="<?php echo $status_filter === 'active' ? 'current' : ''; ?>">
                        Активные <span class="count">(<?php echo esc_html($active_count); ?>)</span>
                    </a> |
                </li>
                <li>
                    <a href="<?php echo esc_url(add_query_arg(['status' => 'disabled'], $redirect_base)); ?>" class="<?php echo $status_filter === 'disabled' ? 'current' : ''; ?>">
                        Отключенные <span class="count">(<?php echo esc_html($disabled_count); ?>)</span>
                    </a>
                </li>
            </ul>

            <!-- Experts Table -->
            <table class="wp-list-table widefat fixed striped table-view-list users">
                <thead>
                    <tr>
                        <th scope="col" style="width: 50px;">Фото</th>
                        <th scope="col" style="width: 220px;">ФИО и логин</th>
                        <th scope="col" style="width: 110px;">Статус</th>
                        <th scope="col">Реестр и Палата</th>
                        <th scope="col">Контакты</th>
                        <th scope="col" style="width: 140px;">Ответы</th>
                        <th scope="col" style="width: 240px;">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rendered_count = 0;
                    foreach ($experts as $exp) :
                        $exp_id      = $exp->ID;
                        $profile     = belan_get_lawyer_profile($exp_id);
                        $is_disabled = (get_user_meta($exp_id, 'belan_expert_disabled', true) === '1');
                        $is_verified = (get_user_meta($exp_id, 'advokat_verified', true) === '1' || empty(get_user_meta($exp_id, 'advokat_verified', true)));
                        $is_admin    = in_array('administrator', (array) $exp->roles, true);
                        $is_self     = ($exp_id === get_current_user_id());

                        if ($status_filter === 'active' && $is_disabled) continue;
                        if ($status_filter === 'disabled' && !$is_disabled) continue;

                        $rendered_count++;

                        // Count answers
                        $pub_answers = count(get_posts([
                            'post_type'      => 'consultation_answer',
                            'post_status'    => 'publish',
                            'author'         => $exp_id,
                            'posts_per_page' => -1,
                            'fields'         => 'ids',
                        ]));

                        $pend_answers = count(get_posts([
                            'post_type'      => 'consultation_answer',
                            'post_status'    => 'pending',
                            'author'         => $exp_id,
                            'posts_per_page' => -1,
                            'fields'         => 'ids',
                        ]));

                        $edit_url = add_query_arg(['view' => 'edit', 'user_id' => $exp_id], $redirect_base);
                        $toggle_url = wp_nonce_url(add_query_arg(['action' => 'toggle_status', 'user_id' => $exp_id], $redirect_base), 'toggle_expert_' . $exp_id);
                        $delete_url = wp_nonce_url(add_query_arg(['action' => 'delete', 'user_id' => $exp_id], $redirect_base), 'delete_expert_' . $exp_id);
                        $answers_url = admin_url('edit.php?post_type=consultation_answer&author=' . $exp_id);
                        ?>
                        <tr>
                            <!-- Avatar -->
                            <td>
                                <img src="<?php echo esc_url($profile['avatar']); ?>" alt="<?php echo esc_attr($profile['name']); ?>" class="belan-expert-avatar">
                            </td>

                            <!-- Name & Login -->
                            <td>
                                <strong>
                                    <a href="<?php echo esc_url($edit_url); ?>" style="font-size: 14px;">
                                        <?php echo esc_html($profile['name']); ?>
                                    </a>
                                </strong>
                                <?php if ($is_verified) : ?>
                                    <span class="belan-badge-pill belan-badge-pill--verified" title="Верифицированный специалист">✓</span>
                                <?php endif; ?>
                                <div style="color: #646970; font-size: 12px; margin-top: 3px;">
                                    Логин: <code><?php echo esc_html($exp->user_login); ?></code>
                                    <?php if ($is_admin) : ?>
                                        <span class="belan-badge-pill belan-badge-pill--role">Администратор</span>
                                    <?php else : ?>
                                        <span class="belan-badge-pill belan-badge-pill--role">Адвокат</span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Status -->
                            <td>
                                <?php if ($is_disabled) : ?>
                                    <span class="belan-badge-pill belan-badge-pill--disabled">● Отключен</span>
                                <?php else : ?>
                                    <span class="belan-badge-pill belan-badge-pill--active">● Активен</span>
                                <?php endif; ?>
                            </td>

                            <!-- Reg Number & Chamber -->
                            <td>
                                <div style="font-weight: 500;"><?php echo esc_html($profile['reg_number']); ?></div>
                                <div style="font-size: 12px; color: #50575e;"><?php echo esc_html($profile['chamber']); ?></div>
                                <div style="font-size: 12px; color: #646970; margin-top: 2px;">
                                    <?php echo esc_html($profile['specialization']); ?> &bull; <?php echo esc_html($profile['experience']); ?>
                                </div>
                            </td>

                            <!-- Contacts -->
                            <td>
                                <div><a href="mailto:<?php echo esc_attr($exp->user_email); ?>"><?php echo esc_html($exp->user_email); ?></a></div>
                                <?php if (!empty($profile['phone'])) : ?>
                                    <div style="font-size: 12px; color: #50575e; margin-top: 2px;"><?php echo esc_html($profile['phone']); ?></div>
                                <?php endif; ?>
                                <div style="margin-top: 3px; font-size: 12px;">
                                    <?php if (!empty($profile['whatsapp'])) :
                                        $wa_link = (strpos($profile['whatsapp'], 'http') === 0) ? $profile['whatsapp'] : ('https://wa.me/' . belan_phone_clean($profile['whatsapp']));
                                        ?>
                                        <a href="<?php echo esc_url($wa_link); ?>" target="_blank" style="color:#25d366; text-decoration:none; margin-right:6px;">WhatsApp</a>
                                    <?php endif; ?>
                                    <?php if (!empty($profile['telegram'])) :
                                        $tg_link = (strpos($profile['telegram'], 'http') === 0) ? $profile['telegram'] : ('https://t.me/' . ltrim($profile['telegram'], '@'));
                                        ?>
                                        <a href="<?php echo esc_url($tg_link); ?>" target="_blank" style="color:#0088cc; text-decoration:none;">Telegram</a>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Answers count -->
                            <td>
                                <div>
                                    <a href="<?php echo esc_url($answers_url . '&post_status=publish'); ?>" class="button button-small" style="font-weight: 600;">
                                        Ответов: <?php echo esc_html($pub_answers); ?>
                                    </a>
                                </div>
                                <?php if ($pend_answers > 0) : ?>
                                    <div style="margin-top: 4px;">
                                        <a href="<?php echo esc_url($answers_url . '&post_status=pending'); ?>" style="color: #dba617; font-size: 12px; font-weight: 600; text-decoration: none;">
                                            ⏳ <?php echo esc_html($pend_answers); ?> на проверке
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <!-- Actions -->
                            <td>
                                <div class="belan-actions-group">
                                    <a href="<?php echo esc_url($edit_url); ?>" class="button button-small" title="Редактировать профиль">
                                        ✏️ Изменить
                                    </a>

                                    <?php if (!$is_self) : ?>
                                        <?php if ($is_disabled) : ?>
                                            <a href="<?php echo esc_url($toggle_url); ?>" class="button button-small" style="color:#2e7d32; border-color:#c8e6c9;" title="Включить эксперта">
                                                ✓ Включить
                                            </a>
                                        <?php else : ?>
                                            <a href="<?php echo esc_url($toggle_url); ?>" class="button button-small" style="color:#d63638;" title="Отключить эксперта" onclick="return confirm('Отключить аккаунт этого эксперта? Он не сможет отвечать на вопросы.');">
                                                ✕ Отключить
                                            </a>
                                        <?php endif; ?>

                                        <a href="<?php echo esc_url($delete_url); ?>" class="button button-small button-link-delete" title="Удалить аккаунт" onclick="return confirm('Вы уверены, что хотите удалить этого эксперта? Его опубликованные ответы на сайте сохранятся и будут переназначены администратору.');">
                                            Удалить
                                        </a>
                                    <?php else : ?>
                                        <span style="font-size: 11px; color:#8c8f94;">(Ваш аккаунт)</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if ($rendered_count === 0) : ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 25px; color: #646970;">
                                Эксперты в этой категории не найдены.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        <?php elseif ($current_view === 'new') : ?>
            <!-- Add New Expert Form -->
            <div class="belan-form-container">
                <h2>+ Регистрация нового адвоката/эксперта</h2>
                <p style="color: #646970; margin-bottom: 20px;">
                    После регистрации эксперт получит аккаунт с ролью «Адвокат» и сможет авторизоваться на сайте для публикации профессиональных ответов.
                </p>

                <form action="<?php echo esc_url($redirect_base); ?>" method="POST">
                    <?php wp_nonce_field('belan_expert_action', 'belan_expert_nonce'); ?>
                    <input type="hidden" name="expert_action" value="add_expert">

                    <div class="belan-form-grid">
                        <!-- Left: Main Account & Credentials -->
                        <div>
                            <div class="belan-form-field">
                                <label for="user_login">Имя пользователя (Логин для входа) <span style="color:red;">*</span></label>
                                <input type="text" name="user_login" id="user_login" class="regular-text" required placeholder="например: advokat_ivanov">
                                <p class="description">Только латинские буквы, цифры и подчеркивание</p>
                            </div>

                            <div class="belan-form-field">
                                <label for="user_email">E-mail <span style="color:red;">*</span></label>
                                <input type="email" name="user_email" id="user_email" class="regular-text" required placeholder="ivanov@example.ru">
                            </div>

                            <div class="belan-form-field">
                                <label for="user_pass">Пароль <span style="color:red;">*</span></label>
                                <input type="text" name="user_pass" id="user_pass" class="regular-text" required value="<?php echo esc_attr(wp_generate_password(12, true)); ?>">
                                <p class="description">Сгенерирован надежный пароль (можно изменить)</p>
                            </div>

                            <div class="belan-form-field">
                                <label for="full_name">ФИО адвоката (Отображается на сайте) <span style="color:red;">*</span></label>
                                <input type="text" name="full_name" id="full_name" class="regular-text" required placeholder="Иванов Иван Иванович">
                            </div>

                            <hr style="margin: 20px 0; border: 0; border-top: 1px solid #e5e5e5;">

                            <div class="belan-form-field">
                                <label for="advokat_reg_number">Регистрационный номер в реестре адвокатов</label>
                                <input type="text" name="advokat_reg_number" id="advokat_reg_number" class="regular-text" placeholder="№ 77/10522 в реестре адвокатов г. Москвы">
                            </div>

                            <div class="belan-form-field">
                                <label for="advokat_chamber">Адвокатская палата</label>
                                <input type="text" name="advokat_chamber" id="advokat_chamber" class="regular-text" placeholder="Адвокатская палата г. Москвы">
                            </div>

                            <div class="belan-form-field">
                                <label for="advokat_specialization">Специализация адвоката</label>
                                <input type="text" name="advokat_specialization" id="advokat_specialization" class="regular-text" placeholder="Жилищное право, арбитражные и семейные споры">
                            </div>

                            <div class="belan-form-field">
                                <label for="advokat_experience">Стаж практики</label>
                                <input type="text" name="advokat_experience" id="advokat_experience" class="regular-text" placeholder="Стаж более 15 лет">
                            </div>
                        </div>

                        <!-- Right: Contacts, Photo & Status -->
                        <div>
                            <div class="belan-form-field">
                                <label for="advokat_phone">Телефон для связи</label>
                                <input type="text" name="advokat_phone" id="advokat_phone" class="regular-text" placeholder="8 (993) 909-90-50">
                            </div>

                            <div class="belan-form-field">
                                <label for="advokat_whatsapp">WhatsApp (номер или ссылка)</label>
                                <input type="text" name="advokat_whatsapp" id="advokat_whatsapp" class="regular-text" placeholder="https://wa.me/79939099050">
                            </div>

                            <div class="belan-form-field">
                                <label for="advokat_telegram">Telegram (никнейм или ссылка)</label>
                                <input type="text" name="advokat_telegram" id="advokat_telegram" class="regular-text" placeholder="https://t.me/advokat">
                            </div>

                            <div class="belan-form-field">
                                <label for="advokat_avatar">Фото адвоката (URL)</label>
                                <input type="text" name="advokat_avatar" id="advokat_avatar" class="regular-text" placeholder="https://.../photo.jpg">
                                <div style="margin-top: 6px;">
                                    <button type="button" class="button" id="upload_avatar_btn">📁 Выбрать из медиабиблиотеки</button>
                                </div>
                                <div class="belan-avatar-preview-box">
                                    <img src="https://secure.gravatar.com/avatar/dca0d4420b1286cd1d4f18418fd161b4?s=128&d=mm&r=g" id="avatar_preview" class="belan-avatar-preview-img" alt="Предпросмотр">
                                    <span style="font-size: 12px; color: #646970;">Предпросмотр аватара</span>
                                </div>
                            </div>

                            <hr style="margin: 20px 0; border: 0; border-top: 1px solid #e5e5e5;">

                            <div class="belan-form-field">
                                <label>
                                    <input type="checkbox" name="advokat_verified" value="1" checked>
                                    <strong>Верифицированный адвокат</strong> (отображать бейдж подтвержденного специалиста)
                                </label>
                            </div>

                            <div class="belan-form-field">
                                <label>
                                    <input type="checkbox" name="expert_disabled" value="1">
                                    <span style="color: #d63638;">Отключить аккаунт сразу после создания</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 25px; padding-top: 15px; border-top: 1px solid #ccd0d4; display: flex; gap: 10px;">
                        <button type="submit" class="button button-primary button-large">
                            ✓ Создать аккаунт эксперта
                        </button>
                        <a href="<?php echo esc_url($redirect_base); ?>" class="button button-secondary button-large">
                            Отмена
                        </a>
                    </div>
                </form>
            </div>

        <?php elseif ($current_view === 'edit') :
            $edit_id = (int) ($_GET['user_id'] ?? 0);
            $user = get_userdata($edit_id);
            if (!$user) : ?>
                <div class="notice notice-error"><p>Эксперт не найден.</p></div>
            <?php else :
                $profile     = belan_get_lawyer_profile($edit_id);
                $is_disabled = (get_user_meta($edit_id, 'belan_expert_disabled', true) === '1');
                $is_verified = (get_user_meta($edit_id, 'advokat_verified', true) === '1' || empty(get_user_meta($edit_id, 'advokat_verified', true)));
                $is_self     = ($edit_id === get_current_user_id());

                $pub_answers = count(get_posts([
                    'post_type'      => 'consultation_answer',
                    'post_status'    => 'publish',
                    'author'         => $edit_id,
                    'posts_per_page' => -1,
                    'fields'         => 'ids',
                ]));
                ?>
                <!-- Edit Expert Form -->
                <div class="belan-form-container">
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e5e5e5; padding-bottom: 15px; margin-bottom: 20px;">
                        <h2 style="margin: 0;">Редактирование профиля: <?php echo esc_html($profile['name']); ?></h2>
                        <a href="<?php echo esc_url(admin_url('edit.php?post_type=consultation_answer&author=' . $edit_id)); ?>" class="button button-secondary" target="_blank">
                            💬 Посмотреть все ответы эксперта (<?php echo esc_html($pub_answers); ?>) &nearr;
                        </a>
                    </div>

                    <form action="<?php echo esc_url($redirect_base); ?>" method="POST">
                        <?php wp_nonce_field('belan_expert_action', 'belan_expert_nonce'); ?>
                        <input type="hidden" name="expert_action" value="edit_expert">
                        <input type="hidden" name="user_id" value="<?php echo esc_attr($edit_id); ?>">

                        <div class="belan-form-grid">
                            <!-- Left: Credentials -->
                            <div>
                                <div class="belan-form-field">
                                    <label>Логин (Имя пользователя)</label>
                                    <input type="text" value="<?php echo esc_attr($user->user_login); ?>" class="regular-text" disabled style="background:#f0f0f1; cursor:not-allowed;">
                                    <p class="description">Логин пользователя не может быть изменен</p>
                                </div>

                                <div class="belan-form-field">
                                    <label for="user_email">E-mail <span style="color:red;">*</span></label>
                                    <input type="email" name="user_email" id="user_email" class="regular-text" required value="<?php echo esc_attr($user->user_email); ?>">
                                </div>

                                <div class="belan-form-field">
                                    <label for="full_name">ФИО адвоката (Отображается на сайте) <span style="color:red;">*</span></label>
                                    <input type="text" name="full_name" id="full_name" class="regular-text" required value="<?php echo esc_attr($profile['name']); ?>">
                                </div>

                                <div class="belan-form-field">
                                    <label for="user_pass">Новый пароль</label>
                                    <input type="password" name="user_pass" id="user_pass" class="regular-text" placeholder="Оставьте пустым, чтобы не менять пароль">
                                </div>

                                <hr style="margin: 20px 0; border: 0; border-top: 1px solid #e5e5e5;">

                                <div class="belan-form-field">
                                    <label for="advokat_reg_number">Регистрационный номер в реестре адвокатов</label>
                                    <input type="text" name="advokat_reg_number" id="advokat_reg_number" class="regular-text" value="<?php echo esc_attr(get_user_meta($edit_id, 'advokat_reg_number', true)); ?>" placeholder="№ 77/10522 в реестре адвокатов г. Москвы">
                                </div>

                                <div class="belan-form-field">
                                    <label for="advokat_chamber">Адвокатская палата</label>
                                    <input type="text" name="advokat_chamber" id="advokat_chamber" class="regular-text" value="<?php echo esc_attr(get_user_meta($edit_id, 'advokat_chamber', true)); ?>" placeholder="Адвокатская палата г. Москвы">
                                </div>

                                <div class="belan-form-field">
                                    <label for="advokat_specialization">Специализация</label>
                                    <input type="text" name="advokat_specialization" id="advokat_specialization" class="regular-text" value="<?php echo esc_attr(get_user_meta($edit_id, 'advokat_specialization', true)); ?>" placeholder="Жилищное право, арбитражные и семейные споры">
                                </div>

                                <div class="belan-form-field">
                                    <label for="advokat_experience">Стаж практики</label>
                                    <input type="text" name="advokat_experience" id="advokat_experience" class="regular-text" value="<?php echo esc_attr(get_user_meta($edit_id, 'advokat_experience', true)); ?>" placeholder="Стаж более 15 лет">
                                </div>
                            </div>

                            <!-- Right: Contacts, Photo & Status -->
                            <div>
                                <div class="belan-form-field">
                                    <label for="advokat_phone">Телефон для связи</label>
                                    <input type="text" name="advokat_phone" id="advokat_phone" class="regular-text" value="<?php echo esc_attr(get_user_meta($edit_id, 'advokat_phone', true)); ?>" placeholder="8 (993) 909-90-50">
                                </div>

                                <div class="belan-form-field">
                                    <label for="advokat_whatsapp">WhatsApp (номер или ссылка)</label>
                                    <input type="text" name="advokat_whatsapp" id="advokat_whatsapp" class="regular-text" value="<?php echo esc_attr(get_user_meta($edit_id, 'advokat_whatsapp', true)); ?>" placeholder="https://wa.me/79939099050">
                                </div>

                                <div class="belan-form-field">
                                    <label for="advokat_telegram">Telegram (никнейм или ссылка)</label>
                                    <input type="text" name="advokat_telegram" id="advokat_telegram" class="regular-text" value="<?php echo esc_attr(get_user_meta($edit_id, 'advokat_telegram', true)); ?>" placeholder="https://t.me/advokat">
                                </div>

                                <div class="belan-form-field">
                                    <label for="advokat_avatar">Фото адвоката (URL)</label>
                                    <input type="text" name="advokat_avatar" id="advokat_avatar" class="regular-text" value="<?php echo esc_attr(get_user_meta($edit_id, 'advokat_avatar', true)); ?>" placeholder="https://.../photo.jpg">
                                    <div style="margin-top: 6px;">
                                        <button type="button" class="button" id="upload_avatar_btn">📁 Загрузить / Выбрать из медиатеки</button>
                                    </div>
                                    <div class="belan-avatar-preview-box">
                                        <img src="<?php echo esc_url($profile['avatar']); ?>" id="avatar_preview" class="belan-avatar-preview-img" alt="Предпросмотр">
                                        <span style="font-size: 12px; color: #646970;">Текущий аватар</span>
                                    </div>
                                </div>

                                <hr style="margin: 20px 0; border: 0; border-top: 1px solid #e5e5e5;">

                                <div class="belan-form-field">
                                    <label>
                                        <input type="checkbox" name="advokat_verified" value="1" <?php checked($is_verified, true); ?>>
                                        <strong>Верифицированный адвокат</strong> (отображать бейдж подтвержденного специалиста)
                                    </label>
                                </div>

                                <?php if (!$is_self) : ?>
                                    <div class="belan-form-field">
                                        <label>
                                            <input type="checkbox" name="expert_disabled" value="1" <?php checked($is_disabled, true); ?>>
                                            <span style="color: #d63638; font-weight: 600;">Отключить аккаунт эксперта</span>
                                            <p class="description" style="margin-left: 24px;">Если отключен — адвокат не сможет публиковать новые ответы на сайте</p>
                                        </label>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div style="margin-top: 25px; padding-top: 15px; border-top: 1px solid #ccd0d4; display: flex; gap: 10px;">
                            <button type="submit" class="button button-primary button-large">
                                ✓ Сохранить изменения
                            </button>
                            <a href="<?php echo esc_url($redirect_base); ?>" class="button button-secondary button-large">
                                Отмена
                            </a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Inline Media Uploader Script -->
        <script>
        jQuery(document).ready(function($){
            var mediaUploader;
            $('#upload_avatar_btn').on('click', function(e) {
                e.preventDefault();
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }
                mediaUploader = wp.media({
                    title: 'Выберите фото адвоката',
                    button: { text: 'Использовать это фото' },
                    multiple: false
                });
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#advokat_avatar').val(attachment.url);
                    $('#avatar_preview').attr('src', attachment.url).show();
                });
                mediaUploader.open();
            });

            // Live preview if URL typed manually
            $('#advokat_avatar').on('input change', function(){
                var val = $(this).val();
                if (val) {
                    $('#avatar_preview').attr('src', val);
                }
            });
        });
        </script>
    </div>
    <?php
}

/**
 * 6. Render Expert Self-Profile Page
 * Dedicated page for experts with the exact same layout as admin edit screen
 */
function belan_render_expert_self_profile_page() {
    if (!is_user_logged_in()) {
        wp_die('Доступ запрещен.');
    }

    // If an administrator lands here, redirect to the full admin edit screen
    if (current_user_can('manage_options')) {
        wp_safe_redirect(admin_url('edit.php?post_type=consultation&page=belan-experts&view=edit&user_id=' . get_current_user_id()));
        exit;
    }

    $user_id     = get_current_user_id();
    $user        = wp_get_current_user();
    $profile     = belan_get_lawyer_profile($user_id);
    $is_verified = (get_user_meta($user_id, 'advokat_verified', true) === '1' || empty(get_user_meta($user_id, 'advokat_verified', true)));
    $is_disabled = (get_user_meta($user_id, 'belan_expert_disabled', true) === '1');

    $msg     = $_GET['msg'] ?? '';
    $err_msg = !empty($_GET['err']) ? sanitize_text_field(urldecode($_GET['err'])) : '';

    $pub_answers = count(get_posts([
        'post_type'      => 'consultation_answer',
        'post_status'    => 'publish',
        'author'         => $user_id,
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ]));

    $pend_answers = count(get_posts([
        'post_type'      => 'consultation_answer',
        'post_status'    => 'pending',
        'author'         => $user_id,
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ]));
    ?>
    <div class="wrap belan-experts-wrap">
        <style>
            .belan-experts-wrap {
                margin-top: 18px;
            }
            .belan-experts-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 16px;
                margin-bottom: 20px;
            }
            .belan-experts-header h1 {
                margin: 0;
                font-size: 24px;
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .belan-badge-pill {
                display: inline-block;
                padding: 3px 8px;
                border-radius: 12px;
                font-size: 11px;
                font-weight: 600;
                line-height: 1.2;
                text-decoration: none;
            }
            .belan-badge-pill--active {
                background: #e7f6e9;
                color: #2e7d32;
                border: 1px solid #c8e6c9;
            }
            .belan-badge-pill--disabled {
                background: #fde8e8;
                color: #c62828;
                border: 1px solid #ffcdd2;
            }
            .belan-badge-pill--verified {
                background: #eef3fc;
                color: #1a56db;
                border: 1px solid #d0e1fd;
            }
            .belan-badge-pill--role {
                background: #f0f0f1;
                color: #50575e;
                font-size: 11px;
            }
            .belan-form-container {
                max-width: 960px;
                background: #fff;
                border: 1px solid #c3c4c7;
                border-radius: 8px;
                padding: 24px 28px;
                margin-top: 15px;
                box-shadow: 0 1px 4px rgba(0,0,0,0.05);
            }
            .belan-form-grid {
                display: grid;
                grid-template-columns: 2fr 1fr;
                gap: 28px;
            }
            @media (max-width: 782px) {
                .belan-form-grid { grid-template-columns: 1fr; }
            }
            .belan-form-field {
                margin-bottom: 16px;
            }
            .belan-form-field label {
                display: block;
                font-weight: 600;
                margin-bottom: 5px;
            }
            .belan-form-field input[type="text"],
            .belan-form-field input[type="email"],
            .belan-form-field input[type="password"] {
                width: 100%;
            }
            .belan-avatar-preview-box {
                margin-top: 10px;
                display: flex;
                align-items: center;
                gap: 14px;
            }
            .belan-avatar-preview-img {
                width: 80px;
                height: 80px;
                border-radius: 50%;
                object-fit: cover;
                border: 2px solid #ccd0d4;
            }
        </style>

        <div class="belan-experts-header">
            <h1>
                <span>⚖️ Профиль эксперта</span>
            </h1>
            <div>
                <a href="<?php echo esc_url(home_url('/consultation/')); ?>" class="button button-secondary" target="_blank">
                    💬 Перейти к консультациям на сайте &nearr;
                </a>
            </div>
        </div>

        <!-- Success & Error Notices -->
        <?php if ($msg === 'updated') : ?>
            <div class="notice notice-success is-dismissible belan-notice">
                <p><strong>✓ Данные профиля успешно сохранены.</strong> Изменения сразу отображаются на сайте в ваших ответах.</p>
            </div>
        <?php elseif ($msg === 'error') : ?>
            <div class="notice notice-error is-dismissible belan-notice">
                <p><strong>Ошибка:</strong> <?php echo esc_html($err_msg ?: 'Не удалось сохранить данные.'); ?></p>
            </div>
        <?php endif; ?>

        <!-- Edit Profile Form Container -->
        <div class="belan-form-container">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e5e5e5; padding-bottom: 15px; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
                <h2 style="margin: 0;">Редактирование профиля: <?php echo esc_html($profile['name']); ?></h2>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <?php if ($is_verified) : ?>
                        <span class="belan-badge-pill belan-badge-pill--verified" title="Верифицированный специалист">✓ Подтвержденный эксперт</span>
                    <?php endif; ?>
                    <?php if ($is_disabled) : ?>
                        <span class="belan-badge-pill belan-badge-pill--disabled">● Отключен</span>
                    <?php else : ?>
                        <span class="belan-badge-pill belan-badge-pill--active">● Активен</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Activity summary -->
            <div style="background: #f6f7f7; border: 1px solid #dcdcde; border-radius: 6px; padding: 12px 16px; margin-bottom: 22px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                <div style="display: flex; gap: 20px; align-items: center; flex-wrap: wrap;">
                    <div>
                        <span style="color: #646970; font-size: 13px;">Опубликовано ответов:</span>
                        <strong style="font-size: 14px; color: #1d2327; margin-left: 4px;"><?php echo esc_html($pub_answers); ?></strong>
                    </div>
                    <?php if ($pend_answers > 0) : ?>
                        <div>
                            <span style="color: #646970; font-size: 13px;">Ответов на проверке:</span>
                            <strong style="font-size: 14px; color: #dba617; margin-left: 4px;">⏳ <?php echo esc_html($pend_answers); ?></strong>
                        </div>
                    <?php endif; ?>
                </div>
                <div>
                    <a href="<?php echo esc_url(home_url('/consultation/')); ?>" target="_blank" style="text-decoration: none; font-size: 13px; font-weight: 500; color: #2271b1;">
                        Смотреть вопросы клиентов на сайте &rarr;
                    </a>
                </div>
            </div>

            <form action="<?php echo esc_url(admin_url('admin.php?page=belan-expert-profile')); ?>" method="POST">
                <?php wp_nonce_field('belan_expert_self_action', 'belan_expert_self_nonce'); ?>
                <input type="hidden" name="expert_self_action" value="update_self_profile">

                <div class="belan-form-grid">
                    <!-- Left: Credentials & Profile Details -->
                    <div>
                        <div class="belan-form-field">
                            <label>Логин (Имя пользователя)</label>
                            <input type="text" value="<?php echo esc_attr($user->user_login); ?>" class="regular-text" disabled style="background:#f0f0f1; cursor:not-allowed;">
                            <p class="description">Логин пользователя не может быть изменен</p>
                        </div>

                        <div class="belan-form-field">
                            <label for="user_email">E-mail <span style="color:red;">*</span></label>
                            <input type="email" name="user_email" id="user_email" class="regular-text" required value="<?php echo esc_attr($user->user_email); ?>">
                        </div>

                        <div class="belan-form-field">
                            <label for="full_name">ФИО адвоката (Отображается на сайте) <span style="color:red;">*</span></label>
                            <input type="text" name="full_name" id="full_name" class="regular-text" required value="<?php echo esc_attr($profile['name']); ?>">
                        </div>

                        <div class="belan-form-field">
                            <label for="user_pass">Новый пароль</label>
                            <input type="password" name="user_pass" id="user_pass" class="regular-text" autocomplete="new-password" placeholder="Оставьте пустым, чтобы не менять пароль">
                        </div>

                        <hr style="margin: 20px 0; border: 0; border-top: 1px solid #e5e5e5;">

                        <div class="belan-form-field">
                            <label for="advokat_reg_number">Регистрационный номер в реестре адвокатов</label>
                            <input type="text" name="advokat_reg_number" id="advokat_reg_number" class="regular-text" value="<?php echo esc_attr(get_user_meta($user_id, 'advokat_reg_number', true)); ?>" placeholder="№ 77/10522 в реестре адвокатов г. Москвы">
                        </div>

                        <div class="belan-form-field">
                            <label for="advokat_chamber">Адвокатская палата</label>
                            <input type="text" name="advokat_chamber" id="advokat_chamber" class="regular-text" value="<?php echo esc_attr(get_user_meta($user_id, 'advokat_chamber', true)); ?>" placeholder="Адвокатская палата г. Москвы">
                        </div>

                        <div class="belan-form-field">
                            <label for="advokat_specialization">Специализация</label>
                            <input type="text" name="advokat_specialization" id="advokat_specialization" class="regular-text" value="<?php echo esc_attr(get_user_meta($user_id, 'advokat_specialization', true)); ?>" placeholder="Жилищное право, арбитражные и семейные споры">
                        </div>

                        <div class="belan-form-field">
                            <label for="advokat_experience">Стаж практики</label>
                            <input type="text" name="advokat_experience" id="advokat_experience" class="regular-text" value="<?php echo esc_attr(get_user_meta($user_id, 'advokat_experience', true)); ?>" placeholder="Стаж более 15 лет">
                        </div>
                    </div>

                    <!-- Right: Contacts & Photo -->
                    <div>
                        <div class="belan-form-field">
                            <label for="advokat_phone">Телефон для связи</label>
                            <input type="text" name="advokat_phone" id="advokat_phone" class="regular-text" value="<?php echo esc_attr(get_user_meta($user_id, 'advokat_phone', true)); ?>" placeholder="8 (993) 909-90-50">
                        </div>

                        <div class="belan-form-field">
                            <label for="advokat_whatsapp">WhatsApp (номер или ссылка)</label>
                            <input type="text" name="advokat_whatsapp" id="advokat_whatsapp" class="regular-text" value="<?php echo esc_attr(get_user_meta($user_id, 'advokat_whatsapp', true)); ?>" placeholder="https://wa.me/79939099050">
                        </div>

                        <div class="belan-form-field">
                            <label for="advokat_telegram">Telegram (никнейм или ссылка)</label>
                            <input type="text" name="advokat_telegram" id="advokat_telegram" class="regular-text" value="<?php echo esc_attr(get_user_meta($user_id, 'advokat_telegram', true)); ?>" placeholder="https://t.me/advokat">
                        </div>

                        <div class="belan-form-field">
                            <label for="advokat_avatar">Фото адвоката (URL)</label>
                            <input type="text" name="advokat_avatar" id="advokat_avatar" class="regular-text" value="<?php echo esc_attr(get_user_meta($user_id, 'advokat_avatar', true)); ?>" placeholder="https://.../photo.jpg">
                            <div style="margin-top: 6px;">
                                <button type="button" class="button" id="upload_avatar_btn">📁 Загрузить / Выбрать из медиатеки</button>
                            </div>
                            <div class="belan-avatar-preview-box">
                                <img src="<?php echo esc_url($profile['avatar']); ?>" id="avatar_preview" class="belan-avatar-preview-img" alt="Предпросмотр">
                                <span style="font-size: 12px; color: #646970;">Текущий аватар</span>
                            </div>
                        </div>

                        <hr style="margin: 20px 0; border: 0; border-top: 1px solid #e5e5e5;">

                        <div class="belan-form-field">
                            <label>
                                <input type="checkbox" disabled <?php checked($is_verified, true); ?>>
                                <strong>Верифицированный адвокат</strong>
                            </label>
                            <p class="description" style="margin-left: 24px;">Статус подтвержденного специалиста присваивается администратором сайта</p>
                        </div>

                        <div class="belan-form-field">
                            <label>
                                <input type="checkbox" disabled checked>
                                <span style="color: #2e7d32; font-weight: 600;">● Аккаунт активен</span>
                            </label>
                            <p class="description" style="margin-left: 24px;">Вы можете отвечать на консультации клиентов платформы</p>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 25px; padding-top: 15px; border-top: 1px solid #ccd0d4; display: flex; gap: 10px;">
                    <button type="submit" class="button button-primary button-large">
                        ✓ Сохранить изменения
                    </button>
                </div>
            </form>
        </div>

        <!-- Inline Media Uploader Script -->
        <script>
        jQuery(document).ready(function($){
            var mediaUploader;
            $('#upload_avatar_btn').on('click', function(e) {
                e.preventDefault();
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }
                mediaUploader = wp.media({
                    title: 'Загрузите фото профиля',
                    button: { text: 'Использовать это фото' },
                    multiple: false,
                    library: {
                        type: 'image'
                    }
                });
                mediaUploader.on('open', function() {
                    // Switch to upload tab by default
                    $('.media-frame-router .media-menu-item:first').trigger('click');
                });
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#advokat_avatar').val(attachment.url);
                    $('#avatar_preview').attr('src', attachment.url).show();
                });
                mediaUploader.open();
            });

            // Live preview if URL typed manually or fallback to default gravatar
            $('#advokat_avatar').on('input change', function(){
                var val = $(this).val();
                if (val) {
                    $('#avatar_preview').attr('src', val);
                } else {
                    $('#avatar_preview').attr('src', 'https://secure.gravatar.com/avatar/dca0d4420b1286cd1d4f18418fd161b4?s=128&d=mm&r=g');
                }
            });
        });
        </script>
    </div>
    <?php
}

/**
 * 7. Block Login for Disabled Experts
 */
add_filter('authenticate', 'belan_check_expert_login_status', 35, 3);
function belan_check_expert_login_status($user, $username, $password) {
    if ($user instanceof WP_User) {
        // Do not block main administrators
        if (in_array('administrator', (array) $user->roles, true)) {
            return $user;
        }

        if (get_user_meta($user->ID, 'belan_expert_disabled', true) === '1') {
            return new WP_Error('expert_disabled', '<strong>Доступ заблокирован:</strong> Ваш профиль эксперта временно отключен администратором сайта.');
        }
    }
    return $user;
}
