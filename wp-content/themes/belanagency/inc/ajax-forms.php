<?php
/**
 * AJAX Form Handlers
 *
 * @package BelanAgency
 */

if (!defined('ABSPATH')) {
    exit;
}

function belan_handle_ajax_lead() {
    check_ajax_referer('belan_nonce', 'nonce');

    $name     = sanitize_text_field($_POST['name'] ?? $_POST['user-name'] ?? $_POST['name_sender'] ?? '');
    $email    = sanitize_email($_POST['email'] ?? $_POST['user-email'] ?? '');
    $phone    = sanitize_text_field($_POST['phone'] ?? '');
    $title    = sanitize_text_field($_POST['question-title'] ?? '');
    $service  = sanitize_text_field($_POST['service'] ?? $_POST['question-category'] ?? '');
    $message  = sanitize_textarea_field($_POST['message'] ?? $_POST['question-text'] ?? '');
    $method   = sanitize_text_field($_POST['method'] ?? 'Звонок');
    $form_id  = sanitize_text_field($_POST['form_id'] ?? 'general');

    if (empty($phone) && empty($name) && empty($email)) {
        wp_send_json_error(['message' => 'Пожалуйста, заполните необходимые поля формы.']);
    }

    $to = get_option('admin_email');
    $subject = 'Новая заявка с сайта: ' . ($title ?: ($name ?: $phone ?: $email));
    $body = "Получена новая заявка:\n\n";
    if ($title)   $body .= "Заголовок вопроса: $title\n";
    if ($name)    $body .= "Имя: $name\n";
    if ($email)   $body .= "E-mail: $email\n";
    if ($phone)   $body .= "Телефон: $phone\n";
    if ($service) $body .= "Категория / Услуга: $service\n";
    if ($method)  $body .= "Предпочтительный способ связи: $method\n";
    if ($message) $body .= "Сообщение:\n$message\n\n";
    $body .= "Форма: $form_id\n";
    $body .= "Дата: " . current_time('mysql') . "\n";

    $attachments = [];
    if (!empty($_FILES['question-attachment'])) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
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
                    $upload = wp_handle_upload($file, ['test_form' => false]);
                    if (!empty($upload['file'])) {
                        $attachments[] = $upload['file'];
                    }
                }
            }
        }
    }

    @wp_mail($to, $subject, $body, '', $attachments);

    wp_send_json_success([
        'message' => 'Спасибо! Ваша заявка успешно отправлена. Адвокат свяжется с вами в ближайшее время.'
    ]);
}
add_action('wp_ajax_belan_lead', 'belan_handle_ajax_lead');
add_action('wp_ajax_nopriv_belan_lead', 'belan_handle_ajax_lead');

function belan_handle_ajax_review() {
    check_ajax_referer('belan_nonce', 'nonce');

    $author  = sanitize_text_field($_POST['author'] ?? '');
    $role    = sanitize_text_field($_POST['role'] ?? '');
    $service = sanitize_text_field($_POST['service'] ?? '');
    $text    = sanitize_textarea_field($_POST['text'] ?? '');
    $rating  = intval($_POST['rating'] ?? 5);

    if (empty($author) || empty($text)) {
        wp_send_json_error(['message' => 'Пожалуйста, заполните имя и текст отзыва.']);
    }

    // Insert as a pending review post
    $post_id = wp_insert_post([
        'post_title'   => 'Отзыв от ' . $author,
        'post_content' => $text,
        'post_type'    => 'review',
        'post_status'  => 'pending',
    ]);

    if ($post_id && function_exists('update_field')) {
        update_field('review_author', $author, $post_id);
        update_field('review_author_role', $role, $post_id);
        update_field('review_service_name', $service, $post_id);
        update_field('review_text', $text, $post_id);
        update_field('review_rating', $rating, $post_id);
        update_field('review_date', date_i18n('j F Y'), $post_id);
    }

    wp_send_json_success([
        'message' => 'Спасибо за ваш отзыв! После модерации он появится на сайте.'
    ]);
}
add_action('wp_ajax_belan_review', 'belan_handle_ajax_review');
add_action('wp_ajax_nopriv_belan_review', 'belan_handle_ajax_review');
