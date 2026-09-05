<?php
/**
 * Q&A Platform Core Module (Belan Agency)
 * Mini-platform for legal questions, verified lawyers, moderation, and notifications.
 * Reference: Pravoved.ru/questions/
 *
 * @package BelanAgency
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 1. Register Custom Post Type: consultation_answer (Ответы адвокатов)
 */
function belan_register_answer_cpt() {
    register_post_type('consultation_answer', [
        'labels' => [
            'name'               => 'Ответы адвокатов',
            'singular_name'      => 'Ответ адвоката',
            'add_new'            => 'Добавить ответ',
            'add_new_item'       => 'Добавить ответ адвоката',
            'edit_item'          => 'Редактировать ответ адвоката',
            'new_item'           => 'Новый ответ',
            'view_item'          => 'Просмотреть ответ',
            'search_items'       => 'Найти ответ',
            'not_found'          => 'Ответов не найдено',
            'not_found_in_trash' => 'В корзине ответов не найдено',
            'menu_name'          => 'Ответы адвокатов',
        ],
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => 'edit.php?post_type=consultation',
        'query_var'          => false,
        'rewrite'            => false,
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'supports'           => ['title', 'editor', 'author'],
        'show_in_rest'       => false,
    ]);
}
add_action('init', 'belan_register_answer_cpt');

/**
 * 2. Lawyer Profile Fields in WP Admin (Users -> Edit User)
 */
function belan_lawyer_profile_fields($user) {
    // Show for Administrator or Advokat
    if (!current_user_can('manage_options') && !in_array('advokat', (array) $user->roles, true)) {
        return;
    }
    ?>
    <h2>Профиль адвоката (для раздела «Вопросы и ответы»)</h2>
    <table class="form-table">
        <tr>
            <th><label for="advokat_reg_number">Регистрационный номер</label></th>
            <td>
                <input type="text" name="advokat_reg_number" id="advokat_reg_number"
                    value="<?php echo esc_attr(get_user_meta($user->ID, 'advokat_reg_number', true)); ?>"
                    class="regular-text" placeholder="№ 77/10522 в реестре адвокатов г. Москвы">
                <p class="description">Номер в региональном реестре адвокатов РФ</p>
            </td>
        </tr>
        <tr>
            <th><label for="advokat_chamber">Адвокатская палата</label></th>
            <td>
                <input type="text" name="advokat_chamber" id="advokat_chamber"
                    value="<?php echo esc_attr(get_user_meta($user->ID, 'advokat_chamber', true)); ?>"
                    class="regular-text" placeholder="Адвокатская палата г. Москвы">
            </td>
        </tr>
        <tr>
            <th><label for="advokat_specialization">Специализация</label></th>
            <td>
                <input type="text" name="advokat_specialization" id="advokat_specialization"
                    value="<?php echo esc_attr(get_user_meta($user->ID, 'advokat_specialization', true)); ?>"
                    class="regular-text" placeholder="Жилищное право, арбитражные и семейные споры">
            </td>
        </tr>
        <tr>
            <th><label for="advokat_experience">Стаж практики</label></th>
            <td>
                <input type="text" name="advokat_experience" id="advokat_experience"
                    value="<?php echo esc_attr(get_user_meta($user->ID, 'advokat_experience', true)); ?>"
                    class="regular-text" placeholder="Стаж 15 лет">
            </td>
        </tr>
        <tr>
            <th><label for="advokat_phone">Телефон для связи</label></th>
            <td>
                <input type="text" name="advokat_phone" id="advokat_phone"
                    value="<?php echo esc_attr(get_user_meta($user->ID, 'advokat_phone', true)); ?>"
                    class="regular-text" placeholder="8 (993) 909-90-50">
            </td>
        </tr>
        <tr>
            <th><label for="advokat_whatsapp">WhatsApp (ссылка или номер)</label></th>
            <td>
                <input type="text" name="advokat_whatsapp" id="advokat_whatsapp"
                    value="<?php echo esc_attr(get_user_meta($user->ID, 'advokat_whatsapp', true)); ?>"
                    class="regular-text" placeholder="https://wa.me/79939099050">
            </td>
        </tr>
        <tr>
            <th><label for="advokat_telegram">Telegram (ссылка или @username)</label></th>
            <td>
                <input type="text" name="advokat_telegram" id="advokat_telegram"
                    value="<?php echo esc_attr(get_user_meta($user->ID, 'advokat_telegram', true)); ?>"
                    class="regular-text" placeholder="https://t.me/advokatezhov">
            </td>
        </tr>
        <tr>
            <th><label for="advokat_avatar">Фото адвоката (URL)</label></th>
            <td>
                <input type="text" name="advokat_avatar" id="advokat_avatar"
                    value="<?php echo esc_attr(get_user_meta($user->ID, 'advokat_avatar', true)); ?>"
                    class="regular-text" placeholder="https://.../photo.jpg">
                <p class="description">URL фотографии. Если не указан, используется стандартное фото адвоката или Gravatar.</p>
            </td>
        </tr>
        <tr>
            <th><label for="advokat_verified">Верифицированный адвокат</label></th>
            <td>
                <label>
                    <input type="checkbox" name="advokat_verified" id="advokat_verified" value="1"
                        <?php checked(get_user_meta($user->ID, 'advokat_verified', true), '1'); ?>>
                    Подтвержденный статус адвоката (отображать бейдж верификации на сайте)
                </label>
            </td>
        </tr>
    </table>
    <?php
}
add_action('show_user_profile', 'belan_lawyer_profile_fields');
add_action('edit_user_profile', 'belan_lawyer_profile_fields');

function belan_save_lawyer_profile_fields($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    $fields = [
        'advokat_reg_number',
        'advokat_chamber',
        'advokat_specialization',
        'advokat_experience',
        'advokat_phone',
        'advokat_whatsapp',
        'advokat_telegram',
        'advokat_avatar',
        'advokat_verified',
    ];

    foreach ($fields as $field) {
        if ($field === 'advokat_verified') {
            update_user_meta($user_id, $field, isset($_POST[$field]) ? '1' : '0');
        } elseif (isset($_POST[$field])) {
            update_user_meta($user_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('personal_options_update', 'belan_save_lawyer_profile_fields');
add_action('edit_user_profile_update', 'belan_save_lawyer_profile_fields');

/**
 * 3. Helper: Get Lawyer Profile by User ID
 */
function belan_get_lawyer_profile($user_id) {
    $user = get_userdata($user_id);
    if (!$user) {
        return [
            'id'             => 0,
            'name'           => 'Адвокат Ежов А.В.',
            'reg_number'     => 'Рег. № 77/10522',
            'chamber'        => 'Адвокатская палата г. Москвы',
            'specialization' => 'Комплексная правовая защита',
            'experience'     => 'Стаж более 20 лет',
            'phone'          => '8 (993) 909-90-50',
            'whatsapp'       => 'https://wa.me/79939099050',
            'telegram'       => 'https://t.me/advokatezhov',
            'avatar'         => 'https://secure.gravatar.com/avatar/dca0d4420b1286cd1d4f18418fd161b4?s=128&d=mm&r=g',
            'verified'       => true,
            'is_advokat'     => true,
            'answers_count'  => 0,
        ];
    }

    $full_name = trim($user->first_name . ' ' . $user->last_name);
    if (empty($full_name)) {
        $full_name = get_user_meta($user_id, 'author_full_name', true) ?: $user->display_name;
    }

    // Default avatar
    $avatar = get_user_meta($user_id, 'advokat_avatar', true);
    if (empty($avatar)) {
        if ($user_id === 1) {
            $avatar = belan_asset('img/about.webp');
        } else {
            $avatar = 'https://secure.gravatar.com/avatar/dca0d4420b1286cd1d4f18418fd161b4?s=128&d=mm&r=g';
        }
    }

    // Count approved answers by this lawyer
    $answers_count = count(get_posts([
        'post_type'      => 'consultation_answer',
        'post_status'    => 'publish',
        'author'         => $user_id,
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ]));

    $is_advokat = in_array('advokat', (array) $user->roles, true) || in_array('administrator', (array) $user->roles, true);

    return [
        'id'             => $user_id,
        'name'           => $full_name ?: 'Адвокат',
        'reg_number'     => get_user_meta($user_id, 'advokat_reg_number', true) ?: 'Рег. № 77/10522',
        'chamber'        => get_user_meta($user_id, 'advokat_chamber', true) ?: 'Адвокатская палата г. Москвы',
        'specialization' => get_user_meta($user_id, 'advokat_specialization', true) ?: (get_user_meta($user_id, 'author_credentials', true) ?: 'Юридическая помощь гражданам и бизнесу'),
        'experience'     => get_user_meta($user_id, 'advokat_experience', true) ?: 'Стаж 12 лет',
        'phone'          => get_user_meta($user_id, 'advokat_phone', true) ?: belan_option('site_phone', '8 (993) 909-90-50'),
        'whatsapp'       => get_user_meta($user_id, 'advokat_whatsapp', true) ?: belan_option('site_whatsapp', 'https://wa.me/79939099050'),
        'telegram'       => get_user_meta($user_id, 'advokat_telegram', true) ?: belan_option('site_telegram', 'https://t.me/advokatezhov'),
        'avatar'         => $avatar,
        'verified'       => get_user_meta($user_id, 'advokat_verified', true) === '1' || $user_id === 1,
        'is_advokat'     => $is_advokat,
        'answers_count'  => $answers_count,
        'is_disabled'    => (get_user_meta($user_id, 'belan_expert_disabled', true) === '1'),
    ];
}

/**
 * 4. Helper: Get Answers for a Question
 */
function belan_get_question_answers($question_id, $include_pending_for_user_id = 0, $is_admin = false) {
    $statuses = ['publish'];
    if ($is_admin) {
        $statuses[] = 'pending';
    }

    $args = [
        'post_type'      => 'consultation_answer',
        'post_parent'    => $question_id,
        'post_status'    => $statuses,
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'ASC',
    ];

    $answers = get_posts($args);

    // If lawyer has pending answer not yet included
    if (!$is_admin && $include_pending_for_user_id > 0) {
        $pending_answers = get_posts([
            'post_type'      => 'consultation_answer',
            'post_parent'    => $question_id,
            'post_status'    => 'pending',
            'author'         => $include_pending_for_user_id,
            'posts_per_page' => -1,
        ]);
        if (!empty($pending_answers)) {
            $answers = array_merge($answers, $pending_answers);
        }
    }

    return $answers;
}

/**
 * Helper: Count Approved Answers for a Question
 */
function belan_get_question_answers_count($question_id) {
    return count(get_posts([
        'post_type'      => 'consultation_answer',
        'post_parent'    => $question_id,
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ]));
}

/**
 * 5. Email Notification to Question Author when Answer is Approved
 */
function belan_on_answer_status_transition($new_status, $old_status, $post) {
    if ($post->post_type !== 'consultation_answer') {
        return;
    }

    // Trigger only when transitioning into 'publish'
    if ($new_status === 'publish' && $old_status !== 'publish') {
        $already_sent = get_post_meta($post->ID, '_notification_email_sent', true);
        if ($already_sent) {
            return;
        }

        $question_id = $post->post_parent ?: (int) get_post_meta($post->ID, 'question_id', true);
        if (!$question_id) {
            return;
        }

        $question = get_post($question_id);
        if (!$question) {
            return;
        }

        // Determine recipient email: prioritize the email specified in the question form
        $recipient_email = get_post_meta($question_id, 'consultation_user_email', true);
        if (empty($recipient_email) && $question->post_author) {
            $author_user = get_userdata($question->post_author);
            if ($author_user && !empty($author_user->user_email)) {
                $recipient_email = $author_user->user_email;
            }
        }

        if (empty($recipient_email) || !is_email($recipient_email)) {
            return;
        }

        $author_name = get_post_meta($question_id, 'consultation_author', true);
        if (empty($author_name) && !empty($author_user)) {
            $author_name = $author_user->display_name;
        }
        if (empty($author_name)) {
            $author_name = 'Пользователь';
        }

        $lawyer_profile = belan_get_lawyer_profile($post->post_author);
        $lawyer_name    = $lawyer_profile['name'] ?: 'Адвокат';

        $question_url   = get_permalink($question_id);
        $question_title = get_the_title($question_id);

        $subject = 'Адвокат ответил на ваш вопрос: ' . wp_strip_all_tags($question_title);

        $excerpt = wp_trim_words(wp_strip_all_tags($post->post_content), 35, '...');

        $body = "Здравствуйте, {$author_name}!\n\n";
        $body .= "На ваш вопрос на сайте «{$question_title}» получен ответ от адвоката ({$lawyer_name}).\n\n";
        $body .= "Выдержка из ответа адвоката:\n";
        $body .= "«{$excerpt}»\n\n";
        $body .= "Чтобы прочитать полный ответ, изучить ссылки на законы и при необходимости задать уточняющий вопрос, перейдите по ссылке:\n";
        $body .= "{$question_url}\n\n";
        $body .= "---\n";
        $body .= "С уважением,\nАдвокатский кабинет Ежова А.В.\n" . home_url('/');

        $headers = ['Content-Type: text/plain; charset=UTF-8'];
        $admin_email = get_option('admin_email');
        if ($admin_email) {
            $headers[] = 'From: Адвокатский кабинет Ежова А.В. <' . $admin_email . '>';
        }

        @wp_mail($recipient_email, $subject, $body, $headers);
        update_post_meta($post->ID, '_notification_email_sent', time());
    }
}
add_action('transition_post_status', 'belan_on_answer_status_transition', 10, 3);

/**
 * Send email notification to user when their question is approved and published
 */
function belan_on_question_status_transition($new_status, $old_status, $post) {
    if (!$post || $post->post_type !== 'consultation') {
        return;
    }

    if ($new_status === 'publish' && $old_status !== 'publish') {
        if (get_post_meta($post->ID, '_question_approved_email_sent', true)) {
            return;
        }

        $recipient_email = get_post_meta($post->ID, 'consultation_user_email', true);
        if (empty($recipient_email) || !is_email($recipient_email)) {
            return;
        }

        $author_name    = get_post_meta($post->ID, 'consultation_author', true) ?: 'Пользователь';
        $question_url   = get_permalink($post->ID);
        $question_title = get_the_title($post->ID);

        $subject = 'Ваш вопрос опубликован на сайте «Адвокат»: ' . wp_strip_all_tags($question_title);

        $body = "Здравствуйте, {$author_name}!\n\n";
        $body .= "Ваш вопрос «{$question_title}» успешно прошел предварительную модерацию и опубликован в открытой ленте консультаций.\n\n";
        $body .= "Практикующие адвокаты сайта уже могут ознакомиться с вашей ситуацией для подготовки развернутого ответа.\n";
        $body .= "Как только ответ адвоката будет готов и проверен, мы сразу уведомим вас отдельным письмом со ссылкой на ответ.\n\n";
        $body .= "Ссылка на страницу вашего вопроса:\n";
        $body .= "{$question_url}\n\n";
        $body .= "---\n";
        $body .= "С уважением,\nАдвокатский кабинет Ежова А.В.\n" . home_url('/');

        $headers = ['Content-Type: text/plain; charset=UTF-8'];
        $admin_email = get_option('admin_email');
        if ($admin_email) {
            $headers[] = 'From: Адвокатский кабинет Ежова А.В. <' . $admin_email . '>';
        }

        @wp_mail($recipient_email, $subject, $body, $headers);
        update_post_meta($post->ID, '_question_approved_email_sent', time());
    }
}
add_action('transition_post_status', 'belan_on_question_status_transition', 10, 3);


/**
 * 6. Admin Columns for consultation_answer
 */
add_filter('manage_consultation_answer_posts_columns', function($columns) {
    return [
        'cb'            => '<input type="checkbox" />',
        'title'         => 'Ответ',
        'question'      => 'Вопрос',
        'lawyer'        => 'Адвокат',
        'answer_status' => 'Статус модерации',
        'date'          => 'Дата',
        'quick_actions' => 'Действия',
    ];
});

add_action('manage_consultation_answer_posts_custom_column', function($column, $post_id) {
    $answer = get_post($post_id);
    if (!$answer) return;

    switch ($column) {
        case 'question':
            $qid = $answer->post_parent ?: get_post_meta($post_id, 'question_id', true);
            if ($qid && $q = get_post($qid)) {
                echo '<strong><a href="' . esc_url(get_permalink($qid)) . '" target="_blank">' . esc_html($q->post_title) . '</a></strong>';
                echo '<br><small><a href="' . esc_url(get_edit_post_link($qid)) . '">Редактировать вопрос</a></small>';
            } else {
                echo '<span style="color:#999;">—</span>';
            }
            break;

        case 'lawyer':
            $lawyer = belan_get_lawyer_profile($answer->post_author);
            echo '<strong>' . esc_html($lawyer['name']) . '</strong><br>';
            echo '<small>' . esc_html($lawyer['reg_number']) . '</small>';
            break;

        case 'answer_status':
            if ($answer->post_status === 'publish') {
                echo '<span style="color:#2e7d32; font-weight:600;">✓ Опубликован</span>';
            } else {
                echo '<span style="color:#e65100; font-weight:600;">⏳ На модерации</span>';
            }
            break;

        case 'quick_actions':
            if (current_user_can('manage_options')) {
                if ($answer->post_status !== 'publish') {
                    $approve_url = wp_nonce_url(admin_url('admin-ajax.php?action=belan_admin_approve_get&answer_id=' . $post_id), 'belan_approve_' . $post_id);
                    echo '<a href="' . esc_url($approve_url) . '" class="button button-primary button-small">Одобрить</a> ';
                } else {
                    $unpublish_url = wp_nonce_url(admin_url('admin-ajax.php?action=belan_admin_unpublish_get&answer_id=' . $post_id), 'belan_unpublish_' . $post_id);
                    echo '<a href="' . esc_url($unpublish_url) . '" class="button button-small">В черновик</a> ';
                }
            }
            break;
    }
}, 10, 2);

// Quick approve action from admin table link
add_action('wp_ajax_belan_admin_approve_get', function() {
    if (!current_user_can('manage_options')) {
        wp_die('Доступ запрещен', 403);
    }
    $answer_id = (int) ($_GET['answer_id'] ?? 0);
    check_admin_referer('belan_approve_' . $answer_id);
    if ($answer_id) {
        wp_update_post(['ID' => $answer_id, 'post_status' => 'publish']);
    }
    $target = wp_get_referer() ?: admin_url('edit.php?post_type=consultation_answer');
    wp_safe_redirect($target);
    exit;
});

add_action('wp_ajax_belan_admin_unpublish_get', function() {
    if (!current_user_can('manage_options')) {
        wp_die('Доступ запрещен', 403);
    }
    $answer_id = (int) ($_GET['answer_id'] ?? 0);
    check_admin_referer('belan_unpublish_' . $answer_id);
    if ($answer_id) {
        wp_update_post(['ID' => $answer_id, 'post_status' => 'pending']);
    }
    wp_safe_redirect(admin_url('edit.php?post_type=consultation_answer'));
    exit;
});

/**
 * 7. Admin Columns for consultation (Questions)
 */
add_filter('manage_consultation_posts_columns', function($columns) {
    return [
        'cb'                => '<input type="checkbox" />',
        'title'             => 'Заголовок вопроса',
        'question_author'   => 'Автор / E-mail',
        'question_category' => 'Рубрика',
        'question_status'   => 'Модерация',
        'answers_status'    => 'Ответы адвокатов',
        'question_docs'     => 'Документы',
        'date'              => 'Дата',
        'question_actions'  => 'Действия',
    ];
});

add_action('manage_consultation_posts_custom_column', function($column, $post_id) {
    switch ($column) {
        case 'question_author':
            $author = get_post_meta($post_id, 'consultation_author', true) ?: 'Пользователь';
            $email  = get_post_meta($post_id, 'consultation_user_email', true);
            echo '<strong>' . esc_html($author) . '</strong>';
            if ($email) {
                echo '<br><small><a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a></small>';
            }
            break;

        case 'question_category':
            $terms = get_the_terms($post_id, 'consultation_category');
            if (!empty($terms) && !is_wp_error($terms)) {
                $names = wp_list_pluck($terms, 'name');
                echo esc_html(implode(', ', $names));
            } else {
                echo '<span style="color:#999;">—</span>';
            }
            break;

        case 'question_status':
            $st = get_post_status($post_id);
            if ($st === 'publish') {
                echo '<span style="color:#2e7d32; font-weight:600;">✓ Опубликован</span>';
            } elseif ($st === 'pending') {
                echo '<span style="color:#e65100; font-weight:600;">⏳ На модерации</span>';
            } else {
                echo '<span style="color:#777;">' . esc_html($st) . '</span>';
            }
            break;

        case 'answers_status':
            $count = belan_get_question_answers_count($post_id);
            if ($count > 0) {
                $url = admin_url('edit.php?post_type=consultation_answer');
                echo '<a href="' . esc_url($url) . '" style="color:#2e7d32; font-weight:600;">Ответов: ' . $count . '</a>';
            } else {
                echo '<span style="color:#d32f2f; font-weight:600;">Ждет ответа</span>';
            }
            break;

        case 'question_docs':
            $attachments = get_post_meta($post_id, 'consultation_attachments', true);
            if (!empty($attachments) && is_array($attachments)) {
                $count = count($attachments);
                echo '<span style="display:inline-flex; align-items:center; gap:4px; font-weight:600; color:#1d2327;">';
                echo '<span class="dashicons dashicons-paperclip" style="color:#2271b1; font-size:16px; width:16px; height:16px; line-height:16px;"></span> ';
                echo esc_html($count) . ' ' . _n('документ', 'документа', $count, 'belanagency');
                echo '</span>';
            } else {
                echo '<span style="color:#bbb;">—</span>';
            }
            break;

        case 'question_actions':
            if (current_user_can('manage_options')) {
                $st = get_post_status($post_id);
                if ($st !== 'publish') {
                    $approve_url = wp_nonce_url(admin_url('admin-ajax.php?action=belan_admin_approve_question_get&question_id=' . $post_id), 'belan_approve_q_' . $post_id);
                    echo '<a href="' . esc_url($approve_url) . '" class="button button-primary button-small">Одобрить</a> ';
                } else {
                    $unpub_url = wp_nonce_url(admin_url('admin-ajax.php?action=belan_admin_unpublish_question_get&question_id=' . $post_id), 'belan_unpublish_q_' . $post_id);
                    echo '<a href="' . esc_url($unpub_url) . '" class="button button-small">В черновик</a> ';
                }
            }
            break;
    }
}, 10, 2);

// Quick approve question from admin table link
add_action('wp_ajax_belan_admin_approve_question_get', function() {
    if (!current_user_can('manage_options')) {
        wp_die('Доступ запрещен', 403);
    }
    $question_id = (int) ($_GET['question_id'] ?? 0);
    check_admin_referer('belan_approve_q_' . $question_id);
    if ($question_id) {
        wp_update_post(['ID' => $question_id, 'post_status' => 'publish']);
    }
    $target = wp_get_referer() ?: admin_url('edit.php?post_type=consultation');
    wp_safe_redirect($target);
    exit;
});

add_action('wp_ajax_belan_admin_unpublish_question_get', function() {
    if (!current_user_can('manage_options')) {
        wp_die('Доступ запрещен', 403);
    }
    $question_id = (int) ($_GET['question_id'] ?? 0);
    check_admin_referer('belan_unpublish_q_' . $question_id);
    if ($question_id) {
        wp_update_post(['ID' => $question_id, 'post_status' => 'pending']);
    }
    wp_safe_redirect(admin_url('edit.php?post_type=consultation'));
    exit;
});

/**
 * 8. Meta Boxes & Document Management in WP Admin
 */

// 8.1 Enqueue media scripts for consultation edit screens
add_action('admin_enqueue_scripts', function($hook) {
    global $post_type, $pagenow;
    if (in_array($pagenow, ['post.php', 'post-new.php'], true) && in_array($post_type, ['consultation', 'consultation_answer'], true)) {
        wp_enqueue_media();
    }
});

// 8.2 Register Meta Boxes for Questions (consultation) & Answers (consultation_answer)
function belan_register_consultation_meta_boxes() {
    // 1. Client Attached Documents
    add_meta_box(
        'belan_consultation_attachments_box',
        'Прикрепленные документы клиента',
        'belan_render_consultation_attachments_box',
        'consultation',
        'normal',
        'high'
    );

    // 2. Lawyer Answers to this Question
    add_meta_box(
        'belan_consultation_answers_box',
        'Ответы адвокатов к этому вопросу',
        'belan_render_consultation_answers_box',
        'consultation',
        'normal',
        'high'
    );

    // 3. Parent Question for Answer CPT
    add_meta_box(
        'belan_answer_parent_question_box',
        'Исходный вопрос клиента',
        'belan_render_answer_parent_question_box',
        'consultation_answer',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'belan_register_consultation_meta_boxes');

/**
 * Helper to get attachment icon / thumbnail HTML for admin
 */
function belan_get_attachment_admin_thumb($att_id) {
    if (wp_attachment_is_image($att_id)) {
        $img = wp_get_attachment_image($att_id, [48, 48], true, [
            'style' => 'width:48px; height:48px; object-fit:cover; border-radius:4px; border:1px solid #dcdcde; display:block;'
        ]);
        if ($img) return $img;
    }

    $mime = get_post_mime_type($att_id);
    $dashicon = 'dashicons-media-default';
    if (strpos($mime, 'pdf') !== false) {
        $dashicon = 'dashicons-pdf';
    } elseif (strpos($mime, 'word') !== false || strpos($mime, 'document') !== false || strpos($mime, 'text') !== false) {
        $dashicon = 'dashicons-media-document';
    } elseif (strpos($mime, 'sheet') !== false || strpos($mime, 'excel') !== false) {
        $dashicon = 'dashicons-media-spreadsheet';
    } elseif (strpos($mime, 'zip') !== false || strpos($mime, 'tar') !== false || strpos($mime, 'rar') !== false) {
        $dashicon = 'dashicons-media-archive';
    } elseif (strpos($mime, 'image') !== false) {
        $dashicon = 'dashicons-format-image';
    }

    return '<span class="dashicons ' . esc_attr($dashicon) . '" style="display:flex; align-items:center; justify-content:center; width:48px; height:48px; font-size:28px; background:#f0f0f1; border-radius:4px; color:#50575e; border:1px solid #dcdcde;"></span>';
}

/**
 * Render Attached Documents Meta Box in Question Edit Screen
 */
function belan_render_consultation_attachments_box($post) {
    $attachments = get_post_meta($post->ID, 'consultation_attachments', true);
    if (!is_array($attachments)) {
        $attachments = !empty($attachments) ? [(int) $attachments] : [];
    }
    wp_nonce_field('belan_save_consultation_attachments', 'belan_consultation_attachments_nonce');
    ?>
    <div class="belan-attachments-meta-wrapper" style="font-family: inherit;">
        <p class="description" style="margin:0 0 12px; font-size:13px; color:#50575e;">
            Документы и файлы, загруженные клиентом при создании вопроса. Доступны для скачивания только администраторам и адвокатам.
        </p>

        <div id="belan-attachments-list" style="display:flex; flex-direction:column; gap:8px;">
            <?php
            $has_items = false;
            foreach ($attachments as $att_id) :
                $att_id = (int) $att_id;
                $att_post = get_post($att_id);
                if (!$att_post) continue;

                $has_items = true;
                $att_url   = wp_get_attachment_url($att_id);
                $att_file  = get_attached_file($att_id);
                $att_size  = ($att_file && file_exists($att_file)) ? size_format(filesize($att_file)) : '';
                $att_mime  = get_post_mime_type($att_id);
                $att_title = get_the_title($att_id) ?: basename($att_url);
                $thumb_html = belan_get_attachment_admin_thumb($att_id);
                ?>
                <div class="belan-att-item" data-id="<?php echo esc_attr($att_id); ?>" style="display:flex; align-items:center; justify-content:space-between; gap:12px; padding:10px 14px; background:#fff; border:1px solid #c3c4c7; border-radius:6px; box-shadow:0 1px 2px rgba(0,0,0,0.03);">
                    <input type="hidden" name="consultation_attachments[]" value="<?php echo esc_attr($att_id); ?>">
                    <div style="display:flex; align-items:center; gap:12px; min-width:0; flex-grow:1;">
                        <div style="flex-shrink:0;">
                            <?php echo $thumb_html; ?>
                        </div>
                        <div style="min-width:0; flex-grow:1;">
                            <a href="<?php echo esc_url($att_url); ?>" target="_blank" style="font-weight:600; font-size:13px; color:#2271b1; text-decoration:none; display:inline-block; max-width:100%; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="<?php echo esc_attr($att_title); ?>">
                                <?php echo esc_html($att_title); ?>
                            </a>
                            <div style="font-size:12px; color:#646970; margin-top:2px;">
                                <?php if ($att_size) : ?><span><?php echo esc_html($att_size); ?></span> &bull; <?php endif; ?>
                                <span><?php echo esc_html($att_mime); ?></span> &bull;
                                <span>ID: <?php echo esc_html($att_id); ?></span>
                            </div>
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:8px; flex-shrink:0;">
                        <a href="<?php echo esc_url($att_url); ?>" target="_blank" download class="button button-secondary button-small" title="Скачать файл">
                            <span class="dashicons dashicons-download" style="vertical-align:text-bottom; font-size:15px; width:15px; height:15px; line-height:15px;"></span> Скачать ↗
                        </a>
                        <button type="button" class="button button-link-delete button-small belan-remove-att-btn" style="color:#d63638; text-decoration:none; padding:0 6px;">
                            <span class="dashicons dashicons-trash" style="vertical-align:text-bottom; font-size:15px; width:15px; height:15px; line-height:15px;"></span> Открепить
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div id="belan-attachments-empty" style="<?php echo $has_items ? 'display:none;' : ''; ?> padding:20px; background:#f6f7f7; border:1px dashed #c3c4c7; border-radius:6px; text-align:center; color:#646970;">
            <span class="dashicons dashicons-media-default" style="font-size:32px; width:32px; height:32px; color:#a7aaad; margin-bottom:6px; display:inline-block;"></span>
            <p style="margin:0; font-size:13px;">К данному вопросу клиент не прикрепил документы.</p>
        </div>

        <div style="margin-top:14px; padding-top:12px; border-top:1px solid #f0f0f1; display:flex; align-items:center; justify-content:space-between;">
            <button type="button" id="belan-add-attachments-btn" class="button button-secondary">
                <span class="dashicons dashicons-plus-alt2" style="vertical-align:text-bottom; font-size:16px; width:16px; height:16px; line-height:16px;"></span> Прикрепить документ из медиабиблиотеки
            </button>
            <span style="font-size:12px; color:#646970;">Не забудьте нажать «Обновить», чтобы сохранить изменения.</span>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        // Remove attachment item
        $(document).on('click', '.belan-remove-att-btn', function(e) {
            e.preventDefault();
            $(this).closest('.belan-att-item').remove();
            if ($('#belan-attachments-list .belan-att-item').length === 0) {
                $('#belan-attachments-empty').show();
            }
        });

        // Add attachment from media library
        var file_frame;
        $('#belan-add-attachments-btn').on('click', function(e) {
            e.preventDefault();

            if (file_frame) {
                file_frame.open();
                return;
            }

            file_frame = wp.media({
                title: 'Прикрепить документы к вопросу',
                button: { text: 'Прикрепить выбранные файлы' },
                multiple: true
            });

            file_frame.on('select', function() {
                var selection = file_frame.state().get('selection');
                selection.each(function(attachment) {
                    var att = attachment.toJSON();
                    // Check if already in list
                    if ($('#belan-attachments-list .belan-att-item[data-id="' + att.id + '"]').length > 0) {
                        return;
                    }

                    var thumbHtml = '';
                    if (att.type === 'image' && att.sizes && (att.sizes.thumbnail || att.sizes.full)) {
                        var src = att.sizes.thumbnail ? att.sizes.thumbnail.url : att.sizes.full.url;
                        thumbHtml = '<img src="' + src + '" style="width:48px; height:48px; object-fit:cover; border-radius:4px; border:1px solid #dcdcde; display:block;">';
                    } else {
                        thumbHtml = '<span class="dashicons dashicons-media-default" style="display:flex; align-items:center; justify-content:center; width:48px; height:48px; font-size:28px; background:#f0f0f1; border-radius:4px; color:#50575e; border:1px solid #dcdcde;"></span>';
                    }

                    var sizeText = att.filesizeHumanReadable || '';
                    var titleText = att.title || att.filename;

                    var itemHtml = '' +
                        '<div class="belan-att-item" data-id="' + att.id + '" style="display:flex; align-items:center; justify-content:space-between; gap:12px; padding:10px 14px; background:#fff; border:1px solid #c3c4c7; border-radius:6px; box-shadow:0 1px 2px rgba(0,0,0,0.03);">' +
                            '<input type="hidden" name="consultation_attachments[]" value="' + att.id + '">' +
                            '<div style="display:flex; align-items:center; gap:12px; min-width:0; flex-grow:1;">' +
                                '<div style="flex-shrink:0;">' + thumbHtml + '</div>' +
                                '<div style="min-width:0; flex-grow:1;">' +
                                    '<a href="' + att.url + '" target="_blank" style="font-weight:600; font-size:13px; color:#2271b1; text-decoration:none; display:inline-block; max-width:100%; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="' + titleText + '">' + titleText + '</a>' +
                                    '<div style="font-size:12px; color:#646970; margin-top:2px;">' +
                                        (sizeText ? '<span>' + sizeText + '</span> &bull; ' : '') +
                                        '<span>' + (att.mime || att.subtype) + '</span> &bull; ' +
                                        '<span>ID: ' + att.id + '</span>' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                            '<div style="display:flex; align-items:center; gap:8px; flex-shrink:0;">' +
                                '<a href="' + att.url + '" target="_blank" download class="button button-secondary button-small">' +
                                    '<span class="dashicons dashicons-download" style="vertical-align:text-bottom; font-size:15px; width:15px; height:15px; line-height:15px;"></span> Скачать ↗' +
                                '</a>' +
                                '<button type="button" class="button button-link-delete button-small belan-remove-att-btn" style="color:#d63638; text-decoration:none; padding:0 6px;">' +
                                    '<span class="dashicons dashicons-trash" style="vertical-align:text-bottom; font-size:15px; width:15px; height:15px; line-height:15px;"></span> Открепить' +
                                '</button>' +
                            '</div>' +
                        '</div>';

                    $('#belan-attachments-list').append(itemHtml);
                });

                if ($('#belan-attachments-list .belan-att-item').length > 0) {
                    $('#belan-attachments-empty').hide();
                }
            });

            file_frame.open();
        });
    });
    </script>
    <?php
}

/**
 * Save Consultation Attachments Meta on Post Save
 */
add_action('save_post_consultation', function($post_id, $post) {
    if (!isset($_POST['belan_consultation_attachments_nonce']) || !wp_verify_nonce($_POST['belan_consultation_attachments_nonce'], 'belan_save_consultation_attachments')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (!empty($_POST['consultation_attachments'])) {
        $attachment_ids = array_values(array_unique(array_filter(array_map('intval', (array) $_POST['consultation_attachments']))));
        update_post_meta($post_id, 'consultation_attachments', $attachment_ids);
    } else {
        delete_post_meta($post_id, 'consultation_attachments');
    }
}, 10, 2);

/**
 * Render Lawyer Answers Meta Box in Question Edit Screen
 */
function belan_render_consultation_answers_box($post) {
    $parent_id = $post->ID;
    $answers = get_posts([
        'post_type'      => 'consultation_answer',
        'post_parent'    => $parent_id,
        'post_status'    => ['publish', 'pending', 'draft'],
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'ASC',
    ]);

    $meta_answers = get_posts([
        'post_type'      => 'consultation_answer',
        'post_status'    => ['publish', 'pending', 'draft'],
        'meta_key'       => 'question_id',
        'meta_value'     => $parent_id,
        'posts_per_page' => -1,
    ]);

    $all_answers = [];
    foreach (array_merge($answers, $meta_answers) as $ans) {
        $all_answers[$ans->ID] = $ans;
    }

    $count = count($all_answers);
    ?>
    <div class="belan-answers-meta-wrapper" style="font-family: inherit;">
        <p class="description" style="margin:0 0 12px; font-size:13px; color:#50575e;">
            Всего ответов от адвокатов: <strong><?php echo esc_html($count); ?></strong>. Ответы публикуются на сайте после одобрения модератором.
        </p>

        <?php if (!empty($all_answers)) : ?>
            <div style="display:flex; flex-direction:column; gap:10px;">
                <?php foreach ($all_answers as $ans) :
                    $lawyer = belan_get_lawyer_profile($ans->post_author);
                    $is_published = ($ans->post_status === 'publish');
                    $approve_url = wp_nonce_url(admin_url('admin-ajax.php?action=belan_admin_approve_get&answer_id=' . $ans->ID), 'belan_approve_' . $ans->ID);
                    $unpub_url   = wp_nonce_url(admin_url('admin-ajax.php?action=belan_admin_unpublish_get&answer_id=' . $ans->ID), 'belan_unpublish_' . $ans->ID);
                    $edit_url    = get_edit_post_link($ans->ID);
                    $site_url    = get_permalink($parent_id) . '#answer-' . $ans->ID;
                    ?>
                    <div style="padding:12px 16px; background:#fff; border:1px solid #c3c4c7; border-left:4px solid <?php echo $is_published ? '#00a32a' : '#dba617'; ?>; border-radius:4px; box-shadow:0 1px 2px rgba(0,0,0,0.03);">
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <img src="<?php echo esc_url($lawyer['avatar']); ?>" alt="" style="width:32px; height:32px; border-radius:50%; object-fit:cover;">
                                <div>
                                    <strong style="font-size:13px; color:#1d2327;"><?php echo esc_html($lawyer['name']); ?></strong>
                                    <?php if (!empty($lawyer['reg_number'])) : ?>
                                        <span style="font-size:12px; color:#646970; margin-left:6px;">(<?php echo esc_html($lawyer['reg_number']); ?>)</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div>
                                <?php if ($is_published) : ?>
                                    <span style="background:#e8f5e9; color:#2e7d32; font-weight:600; padding:2px 8px; border-radius:3px; font-size:12px;">✓ Опубликован</span>
                                <?php else : ?>
                                    <span style="background:#fff3e0; color:#e65100; font-weight:600; padding:2px 8px; border-radius:3px; font-size:12px;">⏳ На модерации</span>
                                <?php endif; ?>
                                <span style="font-size:12px; color:#646970; margin-left:8px;"><?php echo esc_html(get_the_date('d.m.Y H:i', $ans->ID)); ?></span>
                            </div>
                        </div>

                        <div style="font-size:13px; line-height:1.5; color:#2c3338; background:#f9f9f9; padding:10px 12px; border-radius:4px; margin-bottom:10px;">
                            <?php echo nl2br(esc_html(wp_trim_words(wp_strip_all_tags($ans->post_content), 40, '...'))); ?>
                        </div>

                        <div style="display:flex; align-items:center; gap:8px;">
                            <?php if (!$is_published) : ?>
                                <a href="<?php echo esc_url($approve_url); ?>" class="button button-primary button-small">Одобрить ответ</a>
                            <?php else : ?>
                                <a href="<?php echo esc_url($unpub_url); ?>" class="button button-small">В черновик</a>
                            <?php endif; ?>
                            <a href="<?php echo esc_url($edit_url); ?>" class="button button-small">Редактировать</a>
                            <a href="<?php echo esc_url($site_url); ?>" target="_blank" class="button button-secondary button-small">Открыть на сайте ↗</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div style="padding:20px; background:#f6f7f7; border:1px dashed #c3c4c7; border-radius:6px; text-align:center; color:#646970;">
                <span class="dashicons dashicons-format-chat" style="font-size:32px; width:32px; height:32px; color:#a7aaad; margin-bottom:6px; display:inline-block;"></span>
                <p style="margin:0; font-size:13px;">Ответов экспертов на этот вопрос пока нет.</p>
            </div>
        <?php endif; ?>

        <div style="margin-top:14px; padding-top:12px; border-top:1px solid #f0f0f1; display:flex; align-items:center; justify-content:space-between;">
            <a href="<?php echo esc_url(admin_url('post-new.php?post_type=consultation_answer&parent_question=' . $parent_id)); ?>" class="button button-secondary">
                <span class="dashicons dashicons-plus-alt2" style="vertical-align:text-bottom; font-size:16px; width:16px; height:16px; line-height:16px;"></span> Написать ответ от адвоката
            </a>
            <a href="<?php echo esc_url(admin_url('edit.php?post_type=consultation_answer')); ?>" class="button button-link" style="text-decoration:none; font-size:12px;">
                Все ответы в системе &rarr;
            </a>
        </div>
    </div>
    <?php
}

/**
 * Render Parent Question Meta Box in Consultation Answer Edit Screen
 */
function belan_render_answer_parent_question_box($post) {
    $parent_id = $post->post_parent ?: (int) get_post_meta($post->ID, 'question_id', true);
    if (!$parent_id && isset($_GET['parent_question'])) {
        $parent_id = (int) $_GET['parent_question'];
    }

    $q = $parent_id ? get_post($parent_id) : null;
    ?>
    <div class="belan-answer-parent-q-wrapper" style="font-family: inherit;">
        <?php if ($q) :
            $author = get_post_meta($parent_id, 'consultation_author', true) ?: 'Пользователь';
            $q_text = get_post_meta($parent_id, 'consultation_question', true) ?: $q->post_content;
            ?>
            <input type="hidden" name="belan_parent_question_id" value="<?php echo esc_attr($parent_id); ?>">
            <div style="margin-bottom:8px;">
                <strong style="font-size:14px; color:#1d2327;">
                    <a href="<?php echo esc_url(get_edit_post_link($parent_id)); ?>" target="_blank" style="text-decoration:none; color:#2271b1;">
                        <?php echo esc_html($q->post_title); ?> ↗
                    </a>
                </strong>
                <div style="font-size:12px; color:#646970; margin-top:2px;">
                    Автор: <strong><?php echo esc_html($author); ?></strong> &bull;
                    Дата: <?php echo esc_html(get_the_date('d.m.Y H:i', $parent_id)); ?> &bull;
                    <a href="<?php echo esc_url(get_permalink($parent_id)); ?>" target="_blank">Открыть на сайте ↗</a>
                </div>
            </div>
            <div style="font-size:13px; line-height:1.5; color:#2c3338; background:#f9f9f9; padding:10px 12px; border:1px solid #dcdcde; border-radius:4px; max-height:160px; overflow-y:auto;">
                <?php echo nl2br(esc_html($q_text)); ?>
            </div>
        <?php else : ?>
            <p style="color:#646970; margin:0;">Этот ответ пока не привязан к конкретному вопросу. Укажите ID вопроса при создании.</p>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Save Parent Question ID on Consultation Answer Save
 */
add_action('save_post_consultation_answer', function($post_id, $post) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (!empty($_POST['belan_parent_question_id'])) {
        $parent_id = (int) $_POST['belan_parent_question_id'];
        update_post_meta($post_id, 'question_id', $parent_id);
        if ($post->post_parent != $parent_id) {
            remove_action('save_post_consultation_answer', __FUNCTION__);
            wp_update_post([
                'ID' => $post_id,
                'post_parent' => $parent_id,
            ]);
        }
    }
}, 10, 2);

/**
 * Anti-Spam Helper: Generate Math Captcha Challenge and Token
 */
function belan_qa_generate_captcha() {
    $num1 = wp_rand(2, 8);
    $num2 = wp_rand(1, 6);
    $sum  = $num1 + $num2;
    $time = time();
    $salt = wp_salt('nonce');

    $sig   = hash_hmac('sha256', $sum . '|' . $time, $salt);
    $token = base64_encode($sum . '|' . $time . '|' . $sig);

    return [
        'num1'  => $num1,
        'num2'  => $num2,
        'text'  => "{$num1} + {$num2}",
        'token' => $token,
    ];
}

/**
 * Anti-Spam Helper: Generate Signed Timestamp Form Token
 */
function belan_qa_generate_time_token() {
    $time = time();
    $salt = wp_salt('nonce');
    $sig  = hash_hmac('sha256', (string) $time, $salt);
    return base64_encode($time . '|' . $sig);
}

/**
 * Anti-Spam Validator
 * Returns true if valid, or WP_Error on failure.
 */
function belan_qa_validate_anti_spam($post_data) {
    // 1. Honeypot check
    $honeypot = trim($post_data['qa_hp_company'] ?? '');
    if (!empty($honeypot)) {
        return new WP_Error('spam_honeypot', 'Обнаружена автоматическая отправка (спам-фильтр).');
    }

    // 2. Time-trap check (token must be signed and not submitted in < 2 seconds or > 48 hours)
    $time_token = trim($post_data['qa_form_time'] ?? '');
    if (!empty($time_token)) {
        $decoded = base64_decode($time_token);
        $parts = explode('|', $decoded);
        if (count($parts) === 2) {
            $render_time  = (int) $parts[0];
            $sig          = $parts[1];
            $expected_sig = hash_hmac('sha256', (string) $render_time, wp_salt('nonce'));

            if (hash_equals($expected_sig, $sig)) {
                $elapsed = time() - $render_time;
                if ($elapsed < 2) {
                    return new WP_Error('spam_too_fast', 'Форма отправлена слишком быстро. Пожалуйста, заполняйте поля внимательно.');
                }
                if ($elapsed > (48 * HOUR_IN_SECONDS)) {
                    return new WP_Error('spam_expired', 'Сессия формы устарела. Пожалуйста, обновите страницу.');
                }
            }
        }
    }

    // 3. Math Captcha verification
    $user_answer   = trim($post_data['qa_antispam_answer'] ?? '');
    $captcha_token = trim($post_data['qa_antispam_token'] ?? '');

    if ($user_answer === '' || empty($captcha_token)) {
        return new WP_Error('spam_captcha_missing', 'Пожалуйста, решите проверочный пример (защита от спама).');
    }

    $c_decoded = base64_decode($captcha_token);
    $c_parts   = explode('|', $c_decoded);
    if (count($c_parts) !== 3) {
        return new WP_Error('spam_captcha_invalid', 'Ошибка проверки защиты от спама. Пожалуйста, обновите страницу.');
    }

    $expected_sum = (int) $c_parts[0];
    $c_time       = (int) $c_parts[1];
    $c_sig        = $c_parts[2];

    $check_sig = hash_hmac('sha256', $expected_sum . '|' . $c_time, wp_salt('nonce'));
    if (!hash_equals($check_sig, $c_sig)) {
        return new WP_Error('spam_captcha_tampered', 'Подпись защиты от спама недействительна.');
    }

    if ((int) $user_answer !== $expected_sum) {
        return new WP_Error('spam_captcha_wrong', 'Неверный ответ на проверочный пример защиты от спама. Попробуйте еще раз.');
    }

    // 4. Rate Limiting / Flood Control by IP (20 seconds between submissions)
    $ip = sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? '');
    if (!empty($ip)) {
        $ip_hash   = md5($ip);
        $flood_key = 'belan_qa_flood_' . $ip_hash;
        if (get_transient($flood_key)) {
            return new WP_Error('spam_rate_limit', 'Пожалуйста, подождите 20 секунд перед отправкой следующего вопроса.');
        }
        set_transient($flood_key, 1, 20);
    }

    // 5. Content Heuristics (link count and stop words)
    $text = ($post_data['question-title'] ?? '') . ' ' . ($post_data['question-text'] ?? '');
    $links_count = preg_match_all('/https?:\/\//i', $text, $matches);
    if ($links_count > 2) {
        return new WP_Error('spam_links', 'В вопросе содержится слишком много внешних ссылок.');
    }

    $bad_words = ['vulkan', 'vulcan', 'казино', 'онлайн-казино', 'игровые автоматы', 'ставки на спорт', 'порно', 'виагра', 'viagra', 'проститутки', 'интим'];
    $text_lower = mb_strtolower($text);
    foreach ($bad_words as $bw) {
        if (mb_strpos($text_lower, $bw) !== false) {
            return new WP_Error('spam_content', 'Вопрос содержит запрещенные слова рекламного характера.');
        }
    }

    return true;
}

/**
 * 8. AJAX Handlers: Asking Questions and Answering
 */

// D) Ask Question via AJAX (Open to ANY visitor without registration, saved as pending for admin moderation)
function belan_ajax_ask_question() {
    check_ajax_referer('belan_nonce', 'nonce');

    // Anti-Spam Shield Check
    $spam_check = belan_qa_validate_anti_spam($_POST);
    if (is_wp_error($spam_check)) {
        wp_send_json_error([
            'message'     => $spam_check->get_error_message(),
            'new_captcha' => belan_qa_generate_captcha(),
        ]);
    }

    $author_name = sanitize_text_field($_POST['user-name'] ?? '');
    $user_email  = sanitize_email($_POST['user-email'] ?? '');
    $title       = sanitize_text_field($_POST['question-title'] ?? '');
    $category    = sanitize_text_field($_POST['question-category'] ?? '');
    $text        = sanitize_textarea_field($_POST['question-text'] ?? '');

    if (empty($author_name)) {
        $author_name = 'Пользователь';
    }

    if (empty($title) || empty($text)) {
        wp_send_json_error([
            'message'     => 'Пожалуйста, укажите заголовок и подробный текст вопроса.',
            'new_captcha' => belan_qa_generate_captcha(),
        ]);
    }

    if (empty($user_email) || !is_email($user_email)) {
        wp_send_json_error([
            'message'     => 'Пожалуйста, укажите корректный e-mail для получения ответа адвоката.',
            'new_captcha' => belan_qa_generate_captcha(),
        ]);
    }

    $author_id = is_user_logged_in() ? get_current_user_id() : 1;

    // Insert consultation post as pending (awaiting admin moderation)
    $post_id = wp_insert_post([
        'post_title'   => $title,
        'post_content' => $text,
        'post_status'  => 'pending',
        'post_type'    => 'consultation',
        'post_author'  => $author_id,
    ]);

    if (!$post_id || is_wp_error($post_id)) {
        wp_send_json_error([
            'message'     => 'Не удалось сохранить вопрос. Пожалуйста, попробуйте еще раз.',
            'new_captcha' => belan_qa_generate_captcha(),
        ]);
    }

    // Assign Category
    if (!empty($category)) {
        $term = term_exists($category, 'consultation_category');
        if (!$term) {
            $term = wp_insert_term($category, 'consultation_category');
        }
        if (!is_wp_error($term) && isset($term['term_id'])) {
            wp_set_object_terms($post_id, (int) $term['term_id'], 'consultation_category');
        }
    }

    // Meta
    update_post_meta($post_id, 'consultation_author', $author_name);
    update_post_meta($post_id, 'consultation_user_email', $user_email);
    update_post_meta($post_id, 'consultation_date', date_i18n('d.m.Y'));
    update_post_meta($post_id, 'consultation_question', $text);

    // Handle Attachments
    $attachment_ids = [];
    if (!empty($_FILES['question-attachment'])) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        $files = $_FILES['question-attachment'];
        if (is_array($files['name'])) {
            foreach ($files['name'] as $key => $value) {
                if (!empty($files['name'][$key])) {
                    $file = [
                        'name'     => $files['name'][$key],
                        'type'     => $files['type'][$key],
                        'tmp_name' => $files['tmp_name'][$key],
                        'error'    => $files['error'][$key],
                        'size'     => $files['size'][$key],
                    ];
                    $att_id = media_handle_sideload($file, $post_id);
                    if (!is_wp_error($att_id)) {
                        $attachment_ids[] = $att_id;
                    }
                }
            }
        }
    }
    if (!empty($attachment_ids)) {
        update_post_meta($post_id, 'consultation_attachments', $attachment_ids);
    }

    // Notify Admin of new question waiting for moderation
    $admin_email = get_option('admin_email');
    if ($admin_email) {
        $subject     = '[Модерация] Новый вопрос на сайте: ' . $title;
        $edit_url    = admin_url('post.php?post=' . $post_id . '&action=edit');
        $approve_url = wp_nonce_url(admin_url('admin-ajax.php?action=belan_admin_approve_question_get&question_id=' . $post_id), 'belan_approve_q_' . $post_id);

        $body = "На сайте задан новый вопрос, ожидающий предварительной проверки администратором:\n\n";
        $body .= "Автор: {$author_name}\n";
        $body .= "E-mail: {$user_email}\n";
        if ($category) {
            $body .= "Рубрика: {$category}\n";
        }
        $body .= "Заголовок: {$title}\n\n";
        $body .= "Текст вопроса:\n{$text}\n\n";
        if (!empty($attachment_ids)) {
            $body .= "Прикреплено документов: " . count($attachment_ids) . "\n\n";
        }
        $body .= "--------------------------------------------------\n";
        $body .= "Одобрить и опубликовать в 1 клик:\n{$approve_url}\n\n";
        $body .= "Открыть в панели управления:\n{$edit_url}\n";

        $headers = ['Content-Type: text/plain; charset=UTF-8'];
        @wp_mail($admin_email, $subject, $body, $headers);
    }

    wp_send_json_success([
        'message'     => 'Спасибо! Ваш вопрос успешно отправлен на предварительную модерацию. После проверки администратором он будет опубликован на сайте, а ответ адвоката придет вам на указанный e-mail.',
        'question_id' => $post_id,
        'new_captcha' => belan_qa_generate_captcha(),
    ]);
}
add_action('wp_ajax_belan_ask_question', 'belan_ajax_ask_question');
add_action('wp_ajax_nopriv_belan_ask_question', 'belan_ajax_ask_question');

// E) Submit Lawyer Answer via AJAX (Logged in Lawyer/Admin only, saved as pending for moderation)
function belan_ajax_submit_answer() {
    check_ajax_referer('belan_nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Для ответа на вопрос необходимо войти в аккаунт адвоката.']);
    }

    $current_user = wp_get_current_user();
    $is_advokat   = in_array('advokat', (array) $current_user->roles, true);
    $is_admin     = in_array('administrator', (array) $current_user->roles, true);

    if (!$is_advokat && !$is_admin) {
        wp_send_json_error(['message' => 'Отвечать на вопросы могут только зарегистрированные адвокаты.']);
    }

    if (get_user_meta($current_user->ID, 'belan_expert_disabled', true) === '1') {
        wp_send_json_error(['message' => 'Ваш аккаунт эксперта временно отключен администратором сайта. Публикация ответов не разрешена.']);
    }

    $question_id = (int) ($_POST['question_id'] ?? 0);
    $answer_text = trim($_POST['answer_text'] ?? '');

    if (!$question_id || empty($answer_text)) {
        wp_send_json_error(['message' => 'Пожалуйста, напишите развернутый ответ на вопрос.']);
    }

    $question = get_post($question_id);
    if (!$question || $question->post_type !== 'consultation') {
        wp_send_json_error(['message' => 'Вопрос не найден.']);
    }

    // Insert Answer as PENDING (модерация администратором)
    $answer_id = wp_insert_post([
        'post_title'   => 'Ответ адвоката к вопросу #' . $question_id,
        'post_content' => wp_kses_post($answer_text),
        'post_status'  => 'pending', // НА МОДЕРАЦИИ!
        'post_type'    => 'consultation_answer',
        'post_parent'  => $question_id,
        'post_author'  => $current_user->ID,
    ]);

    if (!$answer_id || is_wp_error($answer_id)) {
        wp_send_json_error(['message' => 'Не удалось сохранить ответ. Попробуйте снова.']);
    }

    update_post_meta($answer_id, 'question_id', $question_id);
    update_post_meta($answer_id, 'lawyer_user_id', $current_user->ID);

    // Notify Administrator of new lawyer answer pending moderation
    $admin_email = get_option('admin_email');
    if ($admin_email) {
        $lawyer_name = $current_user->display_name;
        $q_title     = $question->post_title;
        $q_url       = get_permalink($question_id);
        $edit_url    = admin_url('post.php?post=' . $answer_id . '&action=edit');

        $subject = 'Новый ответ адвоката на модерацию: ' . wp_strip_all_tags($q_title);
        $msg  = "Адвокат {$lawyer_name} предоставил ответ на вопрос «{$q_title}».\n\n";
        $msg .= "Текст ответа:\n" . wp_strip_all_tags($answer_text) . "\n\n";
        $msg .= "Вопрос на сайте: {$q_url}\n";
        $msg .= "Проверить и одобрить ответ в админке: {$edit_url}\n";

        @wp_mail($admin_email, $subject, $msg);
    }

    wp_send_json_success([
        'message'   => 'Ваш ответ успешно отправлен и передан на модерацию главному администратору. После одобрения он будет опубликован, а автору вопроса поступит email-уведомление.',
        'answer_id' => $answer_id,
    ]);
}
add_action('wp_ajax_belan_submit_answer', 'belan_ajax_submit_answer');

// F) 1-Click Approve Answer via AJAX (Admin only)
function belan_ajax_approve_answer() {
    check_ajax_referer('belan_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Недостаточно прав для модерации ответов.']);
    }

    $answer_id = (int) ($_POST['answer_id'] ?? 0);
    if (!$answer_id) {
        wp_send_json_error(['message' => 'Неверный идентификатор ответа.']);
    }

    $result = wp_update_post([
        'ID'          => $answer_id,
        'post_status' => 'publish',
    ]);

    if (is_wp_error($result)) {
        wp_send_json_error(['message' => 'Ошибка при одобрении: ' . $result->get_error_message()]);
    }

    wp_send_json_success([
        'message' => 'Ответ адвоката успешно одобрен и опубликован! Автору вопроса отправлено уведомление на почту.',
    ]);
}
add_action('wp_ajax_belan_approve_answer', 'belan_ajax_approve_answer');

// G) 1-Click Approve Question via AJAX (Admin only)
function belan_ajax_approve_question() {
    check_ajax_referer('belan_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Недостаточно прав для модерации вопросов.']);
    }

    $question_id = (int) ($_POST['question_id'] ?? 0);
    if (!$question_id) {
        wp_send_json_error(['message' => 'Неверный идентификатор вопроса.']);
    }

    $result = wp_update_post([
        'ID'          => $question_id,
        'post_status' => 'publish',
    ]);

    if (is_wp_error($result)) {
        wp_send_json_error(['message' => 'Ошибка при одобрении: ' . $result->get_error_message()]);
    }

    wp_send_json_success([
        'message' => 'Вопрос успешно одобрен и опубликован! Автору направлено уведомление на почту.',
    ]);
}
add_action('wp_ajax_belan_approve_question', 'belan_ajax_approve_question');


/**
 * 9. One-time Migration for Legacy ACF Answers into Real CPT Posts
 */
function belan_migrate_legacy_consultation_answers() {
    if (get_option('belan_legacy_answers_migrated')) {
        return;
    }

    $questions = get_posts([
        'post_type'      => 'consultation',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
    ]);

    foreach ($questions as $q) {
        // Check if question already has child answers
        $existing = get_posts([
            'post_type'   => 'consultation_answer',
            'post_parent' => $q->ID,
            'post_status' => 'any',
            'fields'      => 'ids',
        ]);

        if (empty($existing)) {
            $legacy_answer = get_post_meta($q->ID, 'consultation_answer', true);
            if (!empty($legacy_answer) && strlen(trim($legacy_answer)) > 10) {
                $ans_id = wp_insert_post([
                    'post_title'   => 'Ответ адвоката к вопросу #' . $q->ID,
                    'post_content' => wp_kses_post($legacy_answer),
                    'post_status'  => 'publish',
                    'post_type'    => 'consultation_answer',
                    'post_parent'  => $q->ID,
                    'post_author'  => 1, // Admin / Ежов Антон
                    'post_date'    => $q->post_date,
                ]);
                if ($ans_id) {
                    update_post_meta($ans_id, 'question_id', $q->ID);
                    update_post_meta($ans_id, '_notification_email_sent', 1);
                }
            }
        }
    }

    update_option('belan_legacy_answers_migrated', 1);
}
add_action('init', 'belan_migrate_legacy_consultation_answers', 20);

/**
 * 10. Reusable Consultation Card Renderer (Pravoved Style)
 */
function belan_render_consultation_card($post_id = 0) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    $author = belan_field('consultation_author', $post_id, 'Пользователь');
    $date   = belan_field('consultation_date', $post_id, get_the_date('d.m.Y', $post_id));
    $cats   = get_the_terms($post_id, 'consultation_category');
    $cat_name = (!empty($cats) && !is_wp_error($cats)) ? $cats[0]->name : 'Вопросы';
    $cat_link = (!empty($cats) && !is_wp_error($cats)) ? get_term_link($cats[0]) : '#';
    $question = belan_field('consultation_question', $post_id, get_the_excerpt($post_id));

    $answers_count = belan_get_question_answers_count($post_id);
    $has_answers = ($answers_count > 0);

    // Get unique lawyers who answered this question
    $lawyers = [];
    if ($has_answers) {
        $answers_posts = get_posts([
            'post_type'      => 'consultation_answer',
            'post_parent'    => $post_id,
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'ASC',
        ]);
        if (!empty($answers_posts)) {
            $seen_authors = [];
            foreach ($answers_posts as $ans_post) {
                $author_id = (int) $ans_post->post_author;
                if (!in_array($author_id, $seen_authors, true)) {
                    $seen_authors[] = $author_id;
                    $profile = belan_get_lawyer_profile($author_id);
                    $profile['answer_id'] = $ans_post->ID;
                    $profile['answer_url'] = get_permalink($post_id) . '#answer-' . $ans_post->ID;
                    $lawyers[] = $profile;
                }
            }
        }
    }
    $lawyer_count = count($lawyers);
    ?>
    <article class="consultation-card">
        <div class="consultation-card__header">
            <div class="consultation-card__meta">
                <span class="consultation-card__author"><?php echo esc_html($author); ?></span>
                <span class="consultation-card__sep">/</span>
                <span class="consultation-card__num">Вопрос № <?php echo esc_html($post_id); ?></span>
                <span class="consultation-card__sep">/</span>
                <span class="consultation-card__date"><?php echo esc_html($date); ?></span>
            </div>
            <a href="<?php echo esc_url($cat_link); ?>" class="consultation-card__badge"><?php echo esc_html($cat_name); ?></a>
        </div>
        <h3 class="consultation-card__title">
            <a href="<?php echo esc_url(get_permalink($post_id)); ?>"><?php echo esc_html(get_the_title($post_id)); ?></a>
        </h3>
        <p class="consultation-card__text">
            <?php echo esc_html(wp_trim_words($question, 30, '...')); ?>
        </p>
        <div class="consultation-card__footer">
            <div class="consultation-card__responder">
                <?php if ($lawyer_count === 1) : ?>
                    <span>Отвечает</span>
                    <a href="<?php echo esc_url($lawyers[0]['answer_url']); ?>" class="consultation-card__responder-link" title="<?php echo esc_attr($lawyers[0]['name']); ?>">
                        <div class="consultation-card__avatar">
                            <img src="<?php echo esc_url($lawyers[0]['avatar']); ?>" alt="<?php echo esc_attr($lawyers[0]['name']); ?>">
                        </div>
                        <strong><?php echo esc_html($lawyers[0]['name']); ?></strong>
                    </a>
                <?php elseif ($lawyer_count > 1) : ?>
                    <span>Отвечают</span>
                    <div class="consultation-card__avatars">
                        <?php foreach ($lawyers as $l) : ?>
                            <a href="<?php echo esc_url($l['answer_url']); ?>" class="consultation-card__avatar-wrap" title="<?php echo esc_attr($l['name']); ?>">
                                <div class="consultation-card__avatar">
                                    <img src="<?php echo esc_url($l['avatar']); ?>" alt="<?php echo esc_attr($l['name']); ?>">
                                </div>
                                <span class="consultation-card__tooltip"><?php echo esc_html($l['name']); ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <span>Нет ответа</span>
                <?php endif; ?>
            </div>
            <div class="consultation-card__views">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                    <circle cx="12" cy="12" r="3" />
                </svg>
                <span><?php echo (30 + ($post_id % 73) * 7); ?></span>
            </div>
        </div>
    </article>
    <?php
}

/**
 * 10. Admin Menu Notification Badges for Pending Moderation
 */
add_action('admin_menu', 'belan_add_qa_admin_menu_badges', 9999);
function belan_add_qa_admin_menu_badges() {
    global $menu, $submenu;

    if (!current_user_can('manage_options')) {
        return;
    }

    $count_q = wp_count_posts('consultation');
    $pending_questions = isset($count_q->pending) ? (int) $count_q->pending : 0;

    $count_a = wp_count_posts('consultation_answer');
    $pending_answers = isset($count_a->pending) ? (int) $count_a->pending : 0;

    $total_pending = $pending_questions + $pending_answers;

    // 1. Badge on main menu: "Консультации"
    if (!empty($menu)) {
        foreach ($menu as $key => $item) {
            if (isset($item[2]) && $item[2] === 'edit.php?post_type=consultation') {
                if ($total_pending > 0) {
                    $menu[$key][0] .= sprintf(
                        ' <span class="update-plugins count-%1$d"><span class="plugin-count">%1$d</span></span>',
                        $total_pending
                    );
                }
                break;
            }
        }
    }

    // 2. Badges in submenus
    if (!empty($submenu['edit.php?post_type=consultation'])) {
        foreach ($submenu['edit.php?post_type=consultation'] as $sub_key => $sub_item) {
            // First item: "Все вопросы" / "Консультации"
            if ($pending_questions > 0 && isset($sub_item[2]) && $sub_item[2] === 'edit.php?post_type=consultation') {
                $submenu['edit.php?post_type=consultation'][$sub_key][0] .= sprintf(
                    ' <span class="awaiting-mod count-%1$d"><span class="pending-count">%1$d</span></span>',
                    $pending_questions
                );
            }
            // "Ответы адвокатов"
            if ($pending_answers > 0 && isset($sub_item[2]) && strpos($sub_item[2], 'consultation_answer') !== false) {
                $submenu['edit.php?post_type=consultation'][$sub_key][0] .= sprintf(
                    ' <span class="awaiting-mod count-%1$d"><span class="pending-count">%1$d</span></span>',
                    $pending_answers
                );
            }
        }
    }
}

// Styling for badges in admin menu
add_action('admin_head', function() {
    echo '<style>
        #adminmenu .awaiting-mod,
        #adminmenu .update-plugins {
            background-color: #d63638;
            color: #ffffff;
            font-weight: 600;
        }
        #adminmenu .wp-submenu .awaiting-mod {
            margin-left: 6px;
        }
    </style>';
});

/**
 * 11. Admin Dashboard Widget on /wp-admin/index.php
 */
add_action('wp_dashboard_setup', 'belan_register_qa_dashboard_widget');
function belan_register_qa_dashboard_widget() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (!function_exists('wp_add_dashboard_widget')) {
        require_once ABSPATH . 'wp-admin/includes/dashboard.php';
    }

    if (function_exists('wp_add_dashboard_widget')) {
        wp_add_dashboard_widget(
            'belan_qa_moderation_dashboard',
            'Консультации: Модерация вопросов и ответов',
            'belan_render_qa_dashboard_widget',
            null,
            null,
            'normal',
            'high'
        );

        global $wp_meta_boxes;
        if (isset($wp_meta_boxes['dashboard']['normal']['high']['belan_qa_moderation_dashboard'])) {
            $qa_widget = ['belan_qa_moderation_dashboard' => $wp_meta_boxes['dashboard']['normal']['high']['belan_qa_moderation_dashboard']];
            $wp_meta_boxes['dashboard']['normal']['high'] = array_merge($qa_widget, $wp_meta_boxes['dashboard']['normal']['high']);
        }
    }
}

function belan_render_qa_dashboard_widget() {
    $count_q   = wp_count_posts('consultation');
    $pending_q = isset($count_q->pending) ? (int) $count_q->pending : 0;
    $publish_q = isset($count_q->publish) ? (int) $count_q->publish : 0;

    $count_a   = wp_count_posts('consultation_answer');
    $pending_a = isset($count_a->pending) ? (int) $count_a->pending : 0;
    $publish_a = isset($count_a->publish) ? (int) $count_a->publish : 0;

    $user_counts = count_users();
    $advokat_count = $user_counts['avail_roles']['advokat'] ?? 0;
    ?>
    <style>
        .qa-dash-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
            gap: 10px;
            margin-bottom: 18px;
        }
        .qa-dash-card {
            display: block;
            padding: 12px 10px;
            background: #f6f7f7;
            border: 1px solid #dcdcde;
            border-radius: 8px;
            text-decoration: none;
            color: #1d2327;
            text-align: center;
            transition: all 0.15s ease-in-out;
        }
        .qa-dash-card:hover {
            background: #f0f0f1;
            border-color: #2271b1;
            color: #2271b1;
        }
        .qa-dash-card--alert {
            background: #fcf0f0;
            border-color: #e0b4b4;
        }
        .qa-dash-card--alert .qa-dash-card__num {
            color: #d63638;
        }
        .qa-dash-card--ok .qa-dash-card__num {
            color: #1e7e34;
        }
        .qa-dash-card__num {
            font-size: 24px;
            font-weight: 700;
            line-height: 1.1;
            margin-bottom: 4px;
        }
        .qa-dash-card__label {
            font-size: 11.5px;
            color: #646970;
            line-height: 1.3;
        }
        .qa-dash-section {
            margin-top: 16px;
            padding-top: 14px;
            border-top: 1px solid #dcdcde;
        }
        .qa-dash-section__title {
            font-size: 13.5px;
            font-weight: 600;
            margin: 0 0 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .qa-dash-item {
            background: #ffffff;
            border: 1px solid #dcdcde;
            border-left: 4px solid #dba617;
            border-radius: 6px;
            padding: 10px 12px;
            margin-bottom: 8px;
        }
        .qa-dash-item--answer {
            border-left-color: #2271b1;
        }
        .qa-dash-item__header {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 10px;
            margin-bottom: 4px;
            flex-wrap: wrap;
        }
        .qa-dash-item__title {
            font-weight: 600;
            font-size: 13px;
        }
        .qa-dash-item__meta {
            font-size: 11.5px;
            color: #646970;
        }
        .qa-dash-item__text {
            font-size: 12.5px;
            color: #3c434a;
            margin: 6px 0 8px;
            line-height: 1.4;
        }
        .qa-dash-item__actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .qa-dash-empty {
            padding: 10px 14px;
            background: #f0f7f2;
            border-radius: 6px;
            color: #1e7e34;
            font-size: 13px;
            border: 1px solid #c8e6c9;
        }
        .qa-dash-footer {
            margin-top: 16px;
            padding-top: 12px;
            border-top: 1px dashed #dcdcde;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            font-size: 12px;
        }
    </style>

    <div class="qa-dash-stats">
        <a href="<?php echo esc_url(admin_url('edit.php?post_status=pending&post_type=consultation')); ?>" class="qa-dash-card <?php echo $pending_q > 0 ? 'qa-dash-card--alert' : 'qa-dash-card--ok'; ?>">
            <div class="qa-dash-card__num"><?php echo esc_html($pending_q); ?></div>
            <div class="qa-dash-card__label">Вопросы на модерации</div>
        </a>

        <a href="<?php echo esc_url(admin_url('edit.php?post_status=pending&post_type=consultation_answer')); ?>" class="qa-dash-card <?php echo $pending_a > 0 ? 'qa-dash-card--alert' : 'qa-dash-card--ok'; ?>">
            <div class="qa-dash-card__num"><?php echo esc_html($pending_a); ?></div>
            <div class="qa-dash-card__label">Ответы на модерации</div>
        </a>

        <a href="<?php echo esc_url(admin_url('edit.php?post_status=publish&post_type=consultation')); ?>" class="qa-dash-card">
            <div class="qa-dash-card__num"><?php echo esc_html($publish_q); ?></div>
            <div class="qa-dash-card__label">Опубликовано вопросов</div>
        </a>

        <a href="<?php echo esc_url(admin_url('edit.php?post_status=publish&post_type=consultation_answer')); ?>" class="qa-dash-card">
            <div class="qa-dash-card__num"><?php echo esc_html($publish_a); ?></div>
            <div class="qa-dash-card__label">Опубликовано ответов</div>
        </a>

        <a href="<?php echo esc_url(admin_url('users.php?role=advokat')); ?>" class="qa-dash-card">
            <div class="qa-dash-card__num"><?php echo esc_html($advokat_count); ?></div>
            <div class="qa-dash-card__label">Адвокатов в реестре</div>
        </a>
    </div>

    <!-- Section: Вопросы на модерации -->
    <div class="qa-dash-section">
        <div class="qa-dash-section__title">
            <span>Вопросы, ожидающие проверки (<?php echo esc_html($pending_q); ?>)</span>
            <?php if ($pending_q > 0) : ?>
                <a href="<?php echo esc_url(admin_url('edit.php?post_status=pending&post_type=consultation')); ?>">Все вопросы &rarr;</a>
            <?php endif; ?>
        </div>
        <?php
        $pending_q_query = new WP_Query([
            'post_type'      => 'consultation',
            'post_status'    => 'pending',
            'posts_per_page' => 5,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);

        if ($pending_q_query->have_posts()) :
            while ($pending_q_query->have_posts()) : $pending_q_query->the_post();
                $qid          = get_the_ID();
                $q_author     = get_post_meta($qid, 'consultation_author', true) ?: 'Пользователь';
                $q_email      = get_post_meta($qid, 'consultation_user_email', true);
                $q_text       = get_post_meta($qid, 'consultation_question', true) ?: get_the_content();
                $q_terms      = get_the_terms($qid, 'consultation_category');
                $q_cat        = !empty($q_terms) ? $q_terms[0]->name : '';
                $approve_q_url = wp_nonce_url(admin_url('admin-ajax.php?action=belan_admin_approve_question_get&question_id=' . $qid), 'belan_approve_q_' . $qid);
                ?>
                <div class="qa-dash-item">
                    <div class="qa-dash-item__header">
                        <span class="qa-dash-item__title">
                            <a href="<?php echo esc_url(get_edit_post_link($qid)); ?>"><?php the_title(); ?></a>
                        </span>
                        <span class="qa-dash-item__meta"><?php echo esc_html(get_the_date('d.m.Y H:i')); ?></span>
                    </div>
                    <div class="qa-dash-item__meta">
                        Автор: <strong><?php echo esc_html($q_author); ?></strong>
                        <?php if ($q_email) : ?>
                            (<a href="mailto:<?php echo esc_attr($q_email); ?>"><?php echo esc_html($q_email); ?></a>)
                        <?php endif; ?>
                        <?php if ($q_cat) : ?>
                            &bull; Рубрика: <em><?php echo esc_html($q_cat); ?></em>
                        <?php endif; ?>
                    </div>
                    <div class="qa-dash-item__text">
                        <?php echo esc_html(wp_trim_words(wp_strip_all_tags($q_text), 22, '...')); ?>
                    </div>
                    <div class="qa-dash-item__actions">
                        <a href="<?php echo esc_url($approve_q_url); ?>" class="button button-primary button-small">Одобрить вопрос</a>
                        <a href="<?php echo esc_url(get_edit_post_link($qid)); ?>" class="button button-small">Редактировать</a>
                    </div>
                </div>
            <?php endwhile;
            wp_reset_postdata();
        else : ?>
            <div class="qa-dash-empty">
                ✓ Все вопросы проверены — новых вопросов на модерации нет.
            </div>
        <?php endif; ?>
    </div>

    <!-- Section: Ответы на модерации -->
    <div class="qa-dash-section">
        <div class="qa-dash-section__title">
            <span>Ответы адвокатов, ожидающие проверки (<?php echo esc_html($pending_a); ?>)</span>
            <?php if ($pending_a > 0) : ?>
                <a href="<?php echo esc_url(admin_url('edit.php?post_status=pending&post_type=consultation_answer')); ?>">Все ответы &rarr;</a>
            <?php endif; ?>
        </div>
        <?php
        $pending_a_query = new WP_Query([
            'post_type'      => 'consultation_answer',
            'post_status'    => 'pending',
            'posts_per_page' => 5,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);

        if ($pending_a_query->have_posts()) :
            while ($pending_a_query->have_posts()) : $pending_a_query->the_post();
                $ans_id        = get_the_ID();
                $parent_qid    = wp_get_post_parent_id($ans_id) ?: get_post_meta($ans_id, 'question_id', true);
                $parent_q      = $parent_qid ? get_post($parent_qid) : null;
                $lawyer        = belan_get_lawyer_profile(get_the_author_meta('ID'));
                $approve_a_url = wp_nonce_url(admin_url('admin-ajax.php?action=belan_admin_approve_get&answer_id=' . $ans_id), 'belan_approve_' . $ans_id);
                ?>
                <div class="qa-dash-item qa-dash-item--answer">
                    <div class="qa-dash-item__header">
                        <span class="qa-dash-item__title">
                            <?php if ($parent_q) : ?>
                                К вопросу: <a href="<?php echo esc_url(get_permalink($parent_qid)); ?>" target="_blank">«<?php echo esc_html($parent_q->post_title); ?>»</a>
                            <?php else : ?>
                                Ответ #<?php echo esc_html($ans_id); ?>
                            <?php endif; ?>
                        </span>
                        <span class="qa-dash-item__meta"><?php echo esc_html(get_the_date('d.m.Y H:i')); ?></span>
                    </div>
                    <div class="qa-dash-item__meta">
                        Адвокат: <strong><?php echo esc_html($lawyer['name']); ?></strong>
                        <?php if (!empty($lawyer['reg_number'])) : ?>
                            (<?php echo esc_html($lawyer['reg_number']); ?>)
                        <?php endif; ?>
                    </div>
                    <div class="qa-dash-item__text">
                        <?php echo esc_html(wp_trim_words(wp_strip_all_tags(get_the_content()), 25, '...')); ?>
                    </div>
                    <div class="qa-dash-item__actions">
                        <a href="<?php echo esc_url($approve_a_url); ?>" class="button button-primary button-small">Одобрить ответ</a>
                        <a href="<?php echo esc_url(get_edit_post_link($ans_id)); ?>" class="button button-small">Редактировать</a>
                    </div>
                </div>
            <?php endwhile;
            wp_reset_postdata();
        else : ?>
            <div class="qa-dash-empty">
                ✓ Все ответы адвокатов проверены — новых ответов на модерации нет.
            </div>
        <?php endif; ?>
    </div>

    <!-- Quick Footer Links -->
    <div class="qa-dash-footer">
        <a href="<?php echo esc_url(admin_url('edit.php?post_type=consultation')); ?>">Все вопросы</a>
        <a href="<?php echo esc_url(admin_url('edit.php?post_type=consultation_answer')); ?>">Все ответы адвокатов</a>
        <a href="<?php echo esc_url(admin_url('edit.php?post_type=consultation&page=belan-experts')); ?>">Эксперты и адвокаты</a>
        <a href="<?php echo esc_url(admin_url('edit.php?post_type=consultation&page=belan-experts&view=new')); ?>">+ Добавить эксперта</a>
        <a href="<?php echo esc_url(home_url('/consultation/')); ?>" target="_blank">Лента на сайте &nearr;</a>
    </div>
    <?php
}

