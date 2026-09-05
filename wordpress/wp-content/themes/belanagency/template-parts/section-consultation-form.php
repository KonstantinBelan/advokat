<?php
/**
 * Consultation Question Form Section Template Part
 * Q&A Platform: Ask Question Form (Open to any user without registration)
 *
 * @package BelanAgency
 */

if (!defined('ABSPATH')) {
    exit;
}

$is_logged_in = is_user_logged_in();
$current_user = wp_get_current_user();

$spam_q     = belan_qa_generate_captcha();
$time_token = belan_qa_generate_time_token();

// Determine default preselected category if inside a category
$selected_category = '';

if (!empty($args['default_category'])) {
    $selected_category = $args['default_category'];
} elseif (!empty($_GET['category'])) {
    $selected_category = sanitize_text_field(wp_unslash($_GET['category']));
} elseif (is_tax('consultation_category')) {
    $current_term = get_queried_object();
    if ($current_term instanceof WP_Term) {
        $selected_category = $current_term->name;
    }
} elseif (is_singular('consultation')) {
    $post_terms = get_the_terms(get_the_ID(), 'consultation_category');
    if (!empty($post_terms) && !is_wp_error($post_terms)) {
        $selected_category = $post_terms[0]->name;
    }
}
?>
<!-- Ask Question Form Section -->
<section class="section consultation-form-section" id="ask-question">
    <div class="container">
        <div class="consultation-form-section__banner">
            <div class="consultation-form-section__content">
                <h2 class="consultation-form-section__title">ЗАДАЙТЕ СВОЙ ВОПРОС АДВОКАТУ</h2>
                <p class="consultation-form-section__subtitle">
                    Заполните форму ниже — после предварительной модерации ваш вопрос будет опубликован в открытой ленте, и на него ответят квалифицированные адвокаты. Уведомление об ответе придет вам на e-mail.
                </p>

                <div class="consultation-form-section__card">
                    <form action="#" method="POST" class="consultation-form-section__form belan-qa-ask-form" enctype="multipart/form-data">
                        <!-- Anti-Spam Hidden Honeypot & Time-Trap -->
                        <div style="position:absolute; left:-9999px; top:-9999px; height:0; width:0; overflow:hidden; opacity:0; pointer-events:none;" aria-hidden="true">
                            <label for="qa_hp_company">Оставьте это поле пустым</label>
                            <input type="text" id="qa_hp_company" name="qa_hp_company" tabindex="-1" autocomplete="off" value="">
                        </div>
                        <input type="hidden" name="qa_form_time" value="<?php echo esc_attr($time_token); ?>">
                        <input type="hidden" name="qa_antispam_token" value="<?php echo esc_attr($spam_q['token']); ?>">

                        <div class="consultation-form-section__field-group">
                            <input type="text" name="question-title" class="consultation-form-section__input"
                                placeholder="Краткая суть вопроса (Заголовок)" required>
                            <span class="consultation-form-section__helper">Например: «Как выписать бывшего родственника из квартиры?», «Раздел имущества после развода»</span>
                        </div>

                        <div class="consultation-form-section__select-wrapper">
                            <select name="question-category" class="consultation-form-section__select" required>
                                <option value="" disabled <?php echo empty($selected_category) ? 'selected' : ''; ?>>Выберите отрасль права</option>
                                <?php
                                $categories = get_terms([
                                    'taxonomy'   => 'consultation_category',
                                    'hide_empty' => false,
                                ]);
                                if (!empty($categories) && !is_wp_error($categories)) :
                                    foreach ($categories as $cat) :
                                        $is_selected = false;
                                        if (!empty($selected_category)) {
                                            if (
                                                strcasecmp($selected_category, $cat->name) === 0 ||
                                                strcasecmp($selected_category, $cat->slug) === 0 ||
                                                (int)$selected_category === $cat->term_id
                                            ) {
                                                $is_selected = true;
                                            }
                                        }
                                        ?>
                                        <option value="<?php echo esc_attr($cat->name); ?>" <?php echo $is_selected ? 'selected' : ''; ?>><?php echo esc_html($cat->name); ?></option>
                                    <?php endforeach;
                                else : ?>
                                    <option value="Жилищные вопросы и ЖКХ" <?php echo ($selected_category === 'Жилищные вопросы и ЖКХ') ? 'selected' : ''; ?>>Жилищные вопросы и ЖКХ</option>
                                    <option value="Семейное право, разводы и алименты" <?php echo ($selected_category === 'Семейное право, разводы и алименты') ? 'selected' : ''; ?>>Семейное право, разводы и алименты</option>
                                    <option value="Защита прав потребителей" <?php echo ($selected_category === 'Защита прав потребителей') ? 'selected' : ''; ?>>Защита прав потребителей</option>
                                    <option value="Автоюристы и ДТП" <?php echo ($selected_category === 'Автоюристы и ДТП') ? 'selected' : ''; ?>>Автоюристы и ДТП</option>
                                    <option value="Банкротство граждан и компаний" <?php echo ($selected_category === 'Банкротство граждан и компаний') ? 'selected' : ''; ?>>Банкротство граждан и компаний</option>
                                    <option value="Трудовое право" <?php echo ($selected_category === 'Трудовое право') ? 'selected' : ''; ?>>Трудовое право</option>
                                    <option value="Уголовное право и процесс" <?php echo ($selected_category === 'Уголовное право и процесс') ? 'selected' : ''; ?>>Уголовное право и процесс</option>
                                    <option value="Другая категория" <?php echo ($selected_category === 'Другая категория') ? 'selected' : ''; ?>>Другая категория</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <textarea name="question-text" class="consultation-form-section__textarea" rows="5"
                            placeholder="Подробно опишите вашу ситуацию: когда возникла проблема, какие документы имеются, чего вы хотите добиться..." required></textarea>

                        <label class="consultation-form-section__attach">
                            <input type="file" name="question-attachment[]" multiple hidden>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path
                                    d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48" />
                            </svg>
                            <span>Прикрепить документы / фото ситуации</span>
                        </label>

                        <div class="consultation-form-section__row">
                            <input type="text" name="user-name" class="consultation-form-section__input"
                                placeholder="Ваше имя" value="<?php echo $is_logged_in ? esc_attr($current_user->display_name) : ''; ?>" required>
                            <input type="email" name="user-email" class="consultation-form-section__input"
                                placeholder="E-mail (для уведомления об ответе адвоката)" value="<?php echo $is_logged_in ? esc_attr($current_user->user_email) : ''; ?>" required>
                        </div>

                        <!-- Anti-Spam Math Challenge Field -->
                        <div class="consultation-form-section__antispam">
                            <div class="qa-antispam-box">
                                <label for="qa-antispam-ans" class="qa-antispam-label">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="qa-antispam-icon">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                    </svg>
                                    <span class="qa-antispam-text">Защита от спама: сколько будет <strong><?php echo esc_html($spam_q['text']); ?></strong>?</span>
                                </label>
                                <input type="number" id="qa-antispam-ans" name="qa_antispam_answer" class="consultation-form-section__input qa-antispam-input" placeholder="Ответ" required min="0" max="99" autocomplete="off">
                            </div>
                        </div>

                        <div class="consultation-form-section__submit">
                            <button type="submit" class="btn btn--primary btn--red btn--width btn--arrow qa-submit-question-btn">
                                Отправить вопрос адвокату
                            </button>
                        </div>

                        <div class="form-feedback" style="display:none; margin-top:16px; padding:12px 16px; border-radius:8px; font-size:14.5px; line-height:1.5; text-align:center;"></div>

                        <p class="consultation-form-section__agreement">
                            Нажимая «Отправить вопрос адвокату», Вы даете <a href="<?php echo esc_url(belan_option('site_privacy_url', '#')); ?>" target="_blank">Согласие на обработку персональных данных</a> и соглашаетесь с правилами публикации вопросов на сайте.
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
