<?php
/**
 * Consultation Question Form Section Template Part
 * Matches consultation.html lines 584-650
 *
 * @package BelanAgency
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- Ask Question Form Section -->
<section class="section consultation-form-section" id="ask-question">
    <div class="container">
        <div class="consultation-form-section__banner">
            <div class="consultation-form-section__content">
                <h2 class="consultation-form-section__title">ХОТИТЕ ЗАДАТЬ СВОЙ ВОПРОС?</h2>
                <p class="consultation-form-section__subtitle">
                    Заполните форму прямо сейчас и на него ответят опытные юристы и адвокаты бесплатно.
                </p>
                <div class="consultation-form-section__card">
                    <form action="#" method="POST" class="consultation-form-section__form belan-lead-form" data-form-id="ask_question_form" enctype="multipart/form-data">
                        <div class="consultation-form-section__field-group">
                            <input type="text" name="question-title" class="consultation-form-section__input"
                                placeholder="Заголовок вопроса" required>
                            <span class="consultation-form-section__helper">Например: «Как вернуть товар продавцу?», «Как обжаловать штраф за нарушение ПДД?»</span>
                        </div>
                        <div class="consultation-form-section__select-wrapper">
                            <select name="question-category" class="consultation-form-section__select" required>
                                <option value="" disabled selected>Выберите категорию</option>
                                <?php
                                $categories = get_terms([
                                    'taxonomy'   => 'consultation_category',
                                    'hide_empty' => false,
                                ]);
                                if (!empty($categories) && !is_wp_error($categories)) :
                                    foreach ($categories as $cat) : ?>
                                        <option value="<?php echo esc_attr($cat->name); ?>"><?php echo esc_html($cat->name); ?></option>
                                    <?php endforeach;
                                else : ?>
                                    <option value="Жилищные вопросы и ЖКХ">Жилищные вопросы и ЖКХ</option>
                                    <option value="Семейное право, разводы и алименты">Семейное право, разводы и алименты</option>
                                    <option value="Защита прав потребителей">Защита прав потребителей</option>
                                    <option value="Автоюристы и ДТП">Автоюристы и ДТП</option>
                                    <option value="Банкротство граждан и компаний">Банкротство граждан и компаний</option>
                                    <option value="Трудовое право">Трудовое право</option>
                                    <option value="Уголовное право и процесс">Уголовное право и процесс</option>
                                    <option value="Другая категория">Другая категория</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <textarea name="question-text" class="consultation-form-section__textarea" rows="4"
                            placeholder="Опишите ваш вопрос/ситуацию" required></textarea>

                        <label class="consultation-form-section__attach">
                            <input type="file" name="question-attachment[]" multiple hidden>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path
                                    d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48" />
                            </svg>
                            <span>Прикрепить файл</span>
                        </label>

                        <div class="consultation-form-section__row">
                            <input type="text" name="user-name" class="consultation-form-section__input"
                                placeholder="Ваше имя" required>
                            <input type="email" name="user-email" class="consultation-form-section__input"
                                placeholder="E-mail для уведомления" required>
                        </div>

                        <div class="consultation-form-section__submit">
                            <button type="submit" class="btn btn--primary btn--red btn--width btn--arrow">
                                Задать вопрос
                            </button>
                        </div>

                        <div class="form-feedback" style="display:none; margin-top:10px; font-size:14px; text-align:center;"></div>

                        <p class="consultation-form-section__agreement">
                            Задавая вопрос, Вы даете <a href="<?php echo esc_url(belan_option('site_privacy_url', '#')); ?>" target="_blank">Согласие на обработку персональных данных</a> и соглашаетесь с <a href="#" target="_blank">Пользовательским соглашением сайта</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
