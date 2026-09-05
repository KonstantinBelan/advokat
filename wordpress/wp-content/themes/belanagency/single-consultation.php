<?php
/**
 * Single Consultation Template
 * Comprehensive Legal Q&A Platform Page (Pravoved Reference)
 *
 * @package BelanAgency
 */

get_header();

$question_id = get_the_ID();
$author = belan_field('consultation_author', $question_id, 'Пользователь');
$date   = belan_field('consultation_date', $question_id, get_the_date('d.m.Y в H:i'));
$phone  = belan_option('site_phone', '8 (993) 909-90-50');
$phone_clean = belan_phone_clean($phone);

$terms = get_the_terms($question_id, 'consultation_category');
$cat_name = (!empty($terms) && !is_wp_error($terms)) ? $terms[0]->name : 'Вопросы и ответы';
$cat_link = (!empty($terms) && !is_wp_error($terms)) ? get_term_link($terms[0]) : home_url('/consultation/');

$q_text = belan_field('consultation_question', $question_id, get_the_content());
$attachments = get_post_meta($question_id, 'consultation_attachments', true);

// User and permission state
$current_user_id = get_current_user_id();
$is_logged_in    = is_user_logged_in();
$is_admin        = current_user_can('manage_options');
$current_user    = wp_get_current_user();
$is_advokat      = $is_logged_in && (in_array('advokat', (array) $current_user->roles, true) || $is_admin);

// Retrieve all answers (approved, and pending if admin or author)
$answers = belan_get_question_answers($question_id, $current_user_id, $is_admin);
$approved_count = belan_get_question_answers_count($question_id);
?>

<!-- Hero Section (Compact with seal) -->
<section class="section hero hero--page hero--article">
    <div class="container">
        <div class="hero__content">
            <div class="hero__reviews-row">
                <div class="hero__top">
                    <nav class="breadcrumbs" aria-label="Хлебные крошки">
                        <ul class="breadcrumbs__list">
                            <li><a href="<?php echo esc_url(home_url('/')); ?>" class="breadcrumbs__link">Главная</a></li>
                            <li class="breadcrumbs__separator">/</li>
                            <li><a href="<?php echo esc_url(home_url('/consultation/')); ?>" class="breadcrumbs__link">Вопросы адвокату</a></li>
                            <li class="breadcrumbs__separator">/</li>
                            <li><a href="<?php echo esc_url($cat_link); ?>" class="breadcrumbs__link"><?php echo esc_html($cat_name); ?></a></li>
                            <li class="breadcrumbs__separator">/</li>
                            <li class="breadcrumbs__current" aria-current="page">Вопрос №<?php echo esc_html($question_id); ?></li>
                        </ul>
                    </nav>
                </div>
                <div class="hero__seal">
                    <picture>
                        <source type="image/avif"
                            srcset="<?php echo esc_url(belan_asset('img/cases-seal-sm.avif')); ?> 320w, <?php echo esc_url(belan_asset('img/cases-seal.avif')); ?> 520w"
                            sizes="(max-width: 768px) 120px, (max-width: 1024px) 150px, 175px">
                        <source type="image/webp"
                            srcset="<?php echo esc_url(belan_asset('img/cases-seal-sm.webp')); ?> 320w, <?php echo esc_url(belan_asset('img/cases-seal.webp')); ?> 520w"
                            sizes="(max-width: 768px) 120px, (max-width: 1024px) 150px, 175px">
                        <img src="<?php echo esc_url(belan_asset('img/cases-seal.png')); ?>"
                            srcset="<?php echo esc_url(belan_asset('img/cases-seal-sm.png')); ?> 320w, <?php echo esc_url(belan_asset('img/cases-seal.png')); ?> 520w"
                            sizes="(max-width: 768px) 120px, (max-width: 1024px) 150px, 175px" width="175"
                            height="135" alt="Законъ" loading="lazy" decoding="async">
                    </picture>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Consultation Question & Answers -->
<section class="section consultation consultation--single">
    <div class="container">
        <div class="consultation__layout">
            <!-- Left: Question & Answers Column -->
            <div class="consultation__main">

                <?php if ($is_admin && get_post_status($question_id) === 'pending') : ?>
                    <div class="qa-admin-notice-bar">
                        <div class="qa-admin-notice-bar__info">
                            <strong>Внимание, администратор:</strong>
                            <span>Этот вопрос находится на предварительной модерации и пока скрыт от обычных посетителей сайта.</span>
                        </div>
                        <div class="qa-admin-notice-bar__action">
                            <button type="button" class="btn btn--small btn--primary btn--yellow belan-approve-question-btn" data-question-id="<?php echo esc_attr($question_id); ?>">
                                Одобрить и опубликовать вопрос
                            </button>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Question Card -->
                <article class="consultation-item__card qa-question-card">
                    <!-- Question Header Meta -->
                    <div class="consultation-item__header">
                        <div class="consultation-card__meta">
                            <span class="consultation-card__author"><?php echo esc_html($author); ?></span>
                            <span class="consultation-card__sep">/</span>
                            <span class="consultation-card__num">Вопрос № <?php echo esc_html($question_id); ?></span>
                            <span class="consultation-card__sep">/</span>
                            <span class="consultation-card__date"><?php echo esc_html($date); ?></span>
                        </div>
                        <div class="qa-header-badges">
                            <a href="<?php echo esc_url($cat_link); ?>" class="consultation-card__badge"><?php echo esc_html($cat_name); ?></a>
                            <?php if (get_post_status($question_id) === 'pending') : ?>
                                <span class="qa-badge" style="background:#fff3e0; color:#e65100; border:1px solid #ffe0b2;">⏳ На модерации</span>
                            <?php elseif ($approved_count > 0) : ?>
                                <span class="qa-badge qa-badge--answered">Ответов: <?php echo esc_html($approved_count); ?></span>
                            <?php else : ?>
                                <span class="qa-badge qa-badge--waiting">Ждет ответа</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Question Title -->
                    <h1 class="consultation-item__title"><?php the_title(); ?></h1>

                    <!-- Question Text -->
                    <div class="consultation-item__text qa-question-body">
                        <?php
                        if ($q_text) {
                            echo wpautop(esc_html($q_text));
                        } else {
                            the_content();
                        }
                        ?>
                    </div>

                    <!-- Question Attachments (if any) -->
                    <?php if (!empty($attachments) && is_array($attachments)) : ?>
                        <div class="qa-attachments">
                            <h4 class="qa-attachments__title">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;">
                                    <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48" />
                                </svg>
                                Прикрепленные документы (<?php echo count($attachments); ?>):
                            </h4>
                            <ul class="qa-attachments__list">
                                <?php foreach ($attachments as $att_id) :
                                    $att_url = wp_get_attachment_url($att_id);
                                    $att_name = get_the_title($att_id) ?: basename($att_url);
                                    if ($att_url) : ?>
                                        <li class="qa-attachments__item">
                                            <a href="<?php echo esc_url($att_url); ?>" target="_blank" class="qa-attachments__link" download>
                                                <span>📄 <?php echo esc_html($att_name); ?></span>
                                            </a>
                                        </li>
                                    <?php endif;
                                endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <!-- Lawyer Answers Section -->
                    <div class="qa-answers-section" id="lawyer-answers">
                        <div class="qa-answers-section__header">
                            <h2 class="qa-answers-section__title">
                                Ответы адвокатов
                                <span class="qa-answers-section__count">(<?php echo count($answers); ?>)</span>
                            </h2>
                        </div>

                        <?php if (!empty($answers)) : ?>
                            <div class="qa-answers-list">
                                <?php foreach ($answers as $ans) :
                                    $ans_id       = $ans->ID;
                                    $ans_status   = $ans->post_status;
                                    $ans_author_id = $ans->post_author;
                                    $lawyer       = belan_get_lawyer_profile($ans_author_id);
                                    $ans_date     = get_the_date('d.m.Y в H:i', $ans_id);
                                    $is_pending   = ($ans_status === 'pending');
                                    ?>
                                    <article class="consultation-answer qa-answer-card <?php echo $is_pending ? 'qa-answer-card--pending' : ''; ?>" id="answer-<?php echo $ans_id; ?>">

                                        <!-- Pending Moderation Banner -->
                                        <?php if ($is_pending) : ?>
                                            <div class="qa-pending-banner">
                                                <div class="qa-pending-banner__text">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-3px; margin-right:6px;">
                                                        <circle cx="12" cy="12" r="10" />
                                                        <line x1="12" y1="8" x2="12" y2="12" />
                                                        <line x1="12" y1="16" x2="12.01" y2="16" />
                                                    </svg>
                                                    <?php if ($is_admin) : ?>
                                                        <strong>Этот ответ ожидает вашей модерации.</strong> После одобрения он будет виден всем пользователям, а автору вопроса отправится email.
                                                    <?php else : ?>
                                                        <strong>Ваш ответ находится на проверке администратором.</strong> После одобрения он будет опубликован.
                                                    <?php endif; ?>
                                                </div>

                                                <?php if ($is_admin) : ?>
                                                    <div class="qa-pending-banner__actions">
                                                        <button type="button" class="btn btn--small btn--primary btn--yellow belan-approve-answer-btn" data-answer-id="<?php echo esc_attr($ans_id); ?>">
                                                            ✓ Одобрить и опубликовать
                                                        </button>
                                                        <a href="<?php echo esc_url(admin_url('post.php?post=' . $ans_id . '&action=edit')); ?>" class="btn btn--small btn--outline" target="_blank">
                                                            Редактировать
                                                        </a>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>

                                        <div class="consultation-answer__badge-row">
                                            <span class="consultation-answer__badge">Официальный ответ адвоката</span>
                                            <?php if ($lawyer['verified']) : ?>
                                                <span class="qa-verified-badge" title="Статус адвоката подтвержден">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                                    </svg>
                                                    Верифицированный специалист
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Lawyer Profile Header -->
                                        <div class="consultation-answer__author-row">
                                            <div class="consultation-answer__author">
                                                <div class="consultation-answer__avatar">
                                                    <img src="<?php echo esc_url($lawyer['avatar']); ?>" alt="<?php echo esc_attr($lawyer['name']); ?>">
                                                </div>
                                                <div class="consultation-answer__author-info">
                                                    <div class="consultation-answer__name-row">
                                                        <span class="consultation-answer__name"><?php echo esc_html($lawyer['name']); ?></span>
                                                        <span class="qa-role-badge">Эксперт</span>
                                                    </div>
                                                    <span class="consultation-answer__reg"><?php echo esc_html($lawyer['reg_number']); ?> • <?php echo esc_html($lawyer['chamber']); ?></span>
                                                    <span class="consultation-answer__date"><?php echo esc_html($ans_date); ?></span>
                                                </div>
                                            </div>
                                            <div class="consultation-answer__stats">
                                                <p><?php echo esc_html($lawyer['experience']); ?></p>
                                                <span class="consultation-card__sep">/</span>
                                                <p>Консультаций: <?php echo esc_html($lawyer['answers_count'] ?: 1); ?></p>
                                            </div>
                                        </div>

                                        <!-- Answer Content -->
                                        <div class="consultation-answer__content qa-answer-body">
                                            <?php echo wpautop($ans->post_content); ?>
                                        </div>

                                        <!-- Lawyer Contacts & Actions Bar -->
                                        <div class="consultation-answer__lawyer-bar">
                                            <div class="consultation-answer__lawyer-meta">
                                                <div class="consultation-card__avatar">
                                                    <img src="<?php echo esc_url($lawyer['avatar']); ?>" alt="<?php echo esc_attr($lawyer['name']); ?>">
                                                </div>
                                                <strong><?php echo esc_html($lawyer['name']); ?></strong>
                                                <span class="consultation-card__sep">/</span>
                                                <span><?php echo esc_html($lawyer['specialization']); ?></span>
                                            </div>
                                            <div class="consultation-answer__contacts">
                                                <?php if (!empty($lawyer['phone'])) : ?>
                                                    <a href="tel:<?php echo esc_attr(belan_phone_clean($lawyer['phone'])); ?>" class="consultation-answer__contact-btn consultation-answer__contact-btn--phone">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                                        </svg>
                                                        <?php echo esc_html($lawyer['phone']); ?>
                                                    </a>
                                                <?php endif; ?>

                                                <?php if (!empty($lawyer['whatsapp'])) : ?>
                                                    <a href="<?php echo esc_url($lawyer['whatsapp']); ?>" class="consultation-answer__contact-btn consultation-answer__contact-btn--messenger" target="_blank" rel="noopener">
                                                        <span>WhatsApp</span>
                                                    </a>
                                                <?php endif; ?>

                                                <?php if (!empty($lawyer['telegram'])) : ?>
                                                    <a href="<?php echo esc_url($lawyer['telegram']); ?>" class="consultation-answer__contact-btn consultation-answer__contact-btn--messenger" target="_blank" rel="noopener">
                                                        <span>Telegram</span>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        <?php else : ?>
                            <!-- Waiting for Answers Banner -->
                            <div class="qa-no-answers-box">
                                <h3 class="qa-no-answers-box__title">Вопрос ожидает ответа адвоката</h3>
                                <p class="qa-no-answers-box__text">
                                    Вопрос передан практикующим адвокатам сайта. Ответы проходят предварительную проверку и будут опубликованы здесь. Если ваш вопрос срочный — <a href="<?php echo esc_url(home_url('/contacts/')); ?>">свяжитесь с нами для получения консультации</a>.
                                </p>
                                <a href="<?php echo esc_url(home_url('/contacts/')); ?>" class="btn btn--primary btn--yellow btn--arrow">
                                    Контакты
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </article>

                <!-- Lawyer Answer Form / Call to Action Box -->
                <div class="qa-lawyer-response-box" id="lawyer-response-form">
                    <?php if ($is_advokat) :
                        $cur_lawyer  = belan_get_lawyer_profile($current_user_id);
                        $is_disabled = !empty($cur_lawyer['is_disabled']);
                        if ($is_disabled) : ?>
                            <div class="qa-pending-banner" style="background:#FFF3F3; border:1px solid #FFCDD2; color:#C62828; border-radius:12px; padding:18px 24px;">
                                <div class="qa-pending-banner__text" style="font-size:15px; line-height:1.5;">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-4px; margin-right:8px;">
                                        <circle cx="12" cy="12" r="10"/>
                                        <line x1="12" y1="8" x2="12" y2="12"/>
                                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                                    </svg>
                                    <strong>Ваш профиль эксперта отключен администратором сайта.</strong> Публикация ответов на консультации временно приостановлена.
                                </div>
                            </div>
                        <?php else : ?>
                            <!-- Logged-in Lawyer Answer Form -->
                            <div class="qa-lawyer-form-card">
                                <div class="qa-lawyer-form-card__header">
                                    <div class="qa-lawyer-form-card__lawyer-avatar">
                                        <img src="<?php echo esc_url($cur_lawyer['avatar']); ?>" alt="<?php echo esc_attr($cur_lawyer['name']); ?>">
                                    </div>
                                    <div>
                                        <h3 class="qa-lawyer-form-card__title">Предоставить ответ на вопрос</h3>
                                        <p class="qa-lawyer-form-card__subtitle">
                                            Вы отвечаете как: <strong><?php echo esc_html($cur_lawyer['name']); ?></strong> (<?php echo esc_html($cur_lawyer['reg_number']); ?>)
                                        </p>
                                    </div>
                                </div>

                            <form action="#" method="POST" class="qa-lawyer-answer-form belan-lawyer-answer-form" data-question-id="<?php echo esc_attr($question_id); ?>">
                                <input type="hidden" name="question_id" value="<?php echo esc_attr($question_id); ?>">
                                
                                <div class="consultation-form-section__field-group">
                                    <label class="qa-form-label" for="answer_text">Текст правового ответа:</label>
                                    <textarea name="answer_text" id="answer_text" class="consultation-form-section__textarea" rows="6"
                                        placeholder="Изложите правовую позицию со ссылками на законы (ГК РФ, ЖК РФ, судебную практику) и дайте пошаговый план решения ситуации доверителя..." required></textarea>
                                </div>

                                <div class="qa-lawyer-form-card__footer">
                                    <p class="qa-lawyer-form-card__notice">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;">
                                            <circle cx="12" cy="12" r="10"/>
                                            <line x1="12" y1="16" x2="12" y2="12"/>
                                            <line x1="12" y1="8" x2="12.01" y2="8"/>
                                        </svg>
                                        Ответ будет отправлен на модерацию главному администратору. После одобрения автору вопроса придет уведомление на почту.
                                    </p>
                                    <button type="submit" class="btn btn--primary btn--red btn--arrow qa-lawyer-submit-btn">
                                        Отправить ответ на модерацию
                                    </button>
                                </div>
                                <div class="form-feedback" style="display:none; margin-top:12px; font-size:15px;"></div>
                            </form>
                        </div>
                        <?php endif; ?>

                    <?php elseif (!$is_logged_in) : ?>
                        <!-- Guest Call to Action: Login as Lawyer -->
                        <div class="qa-guest-lawyer-card">
                            <div class="qa-guest-lawyer-card__content">
                                <span class="qa-guest-lawyer-card__badge">Для практикующих адвокатов</span>
                                <h3 class="qa-guest-lawyer-card__title">Вы адвокат и готовы дать правовой ответ?</h3>
                                <p class="qa-guest-lawyer-card__text">
                                    Оставьте свою заявку на регистрацию, чтобы отвечать на вопросы. Мы&nbsp;проверим данные и активируем ваш профиль.
                                </p>
                            </div>
                            <div class="qa-guest-lawyer-card__action">
                                <a href="<?php echo esc_url(home_url('/contacts/')); ?>" class="btn btn--primary btn--yellow" target="_blank">
                                    Хочу зарегистрироваться
                                </a>
                            </div>
                        </div>

                    <?php else : ?>
                        <!-- Regular Client Info Banner -->
                        <div class="qa-client-notice-card">
                            <h3>У вас похожий вопрос или спорная ситуация?</h3>
                            <p>Задайте свой вопрос адвокатам бесплатно — вам ответят квалифицированные юристы и адвокаты реестра РФ.</p>
                            <a href="#ask-question" class="btn btn--primary btn--red btn--arrow">Задать свой вопрос адвокату</a>
                        </div>
                    <?php endif; ?>
                </div>

            </div>

            <!-- Right: Sidebar (Pravoved Style) -->
            <aside class="consultation__sidebar">
                <!-- Search Box -->
                <div class="consultation-search">
                    <form action="<?php echo esc_url(home_url('/consultation/')); ?>" method="GET" class="qa-sidebar-search-form">
                        <input type="text" name="q" class="consultation-search__input" placeholder="Поиск по вопросам">
                        <button type="submit" class="consultation-search__btn" aria-label="Искать">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                        </button>
                    </form>
                </div>

                <!-- Dark CTA Card -->
                <div class="consultation-sidebar-cta">
                    <h3 class="consultation-sidebar-cta__title">Хотите задать свой вопрос?</h3>
                    <p class="consultation-sidebar-cta__text">
                        Заполните форму прямо сейчас и на него ответят опытные юристы и адвокаты бесплатно.
                    </p>
                    <a href="#ask-question" class="btn btn--primary btn--yellow btn--width btn--arrow">Задать свой вопрос</a>
                </div>

                <!-- Widget: Статьи из блога -->
                <div class="article-sidebar__widget">
                    <h3 class="article-sidebar__widget-title">Статьи из блога:</h3>
                    <ul class="article-sidebar__widget-list">
                        <?php
                        $side_articles = new WP_Query(['post_type' => 'post', 'posts_per_page' => 3]);
                        if ($side_articles->have_posts()) :
                            while ($side_articles->have_posts()) : $side_articles->the_post(); ?>
                                <li class="article-sidebar__widget-item">
                                    <a href="<?php the_permalink(); ?>" class="article-sidebar__widget-link">
                                        <span><?php the_title(); ?></span>
                                    </a>
                                </li>
                            <?php endwhile;
                            wp_reset_postdata();
                        else : ?>
                            <li class="article-sidebar__widget-item">Статей пока нет</li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Widget: Новости -->
                <div class="article-sidebar__widget">
                    <h3 class="article-sidebar__widget-title">Новости:</h3>
                    <ul class="article-sidebar__widget-list">
                        <?php
                        $side_news = new WP_Query(['post_type' => 'news', 'posts_per_page' => 3]);
                        if ($side_news->have_posts()) :
                            while ($side_news->have_posts()) : $side_news->the_post(); ?>
                                <li class="article-sidebar__widget-item">
                                    <a href="<?php the_permalink(); ?>" class="article-sidebar__widget-link">
                                        <span><?php the_title(); ?></span>
                                    </a>
                                </li>
                            <?php endwhile;
                            wp_reset_postdata();
                        else : ?>
                            <li class="article-sidebar__widget-item">Новостей пока нет</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</section>

<!-- Section: Похожие вопросы -->
<?php
$similar_query = new WP_Query([
    'post_type'      => 'consultation',
    'post_status'    => 'publish',
    'posts_per_page' => 5,
    'post__not_in'   => [$question_id],
]);

if ($similar_query->have_posts()) : ?>
    <section class="section similar-questions">
        <div class="container">
            <h2 class="similar-questions__title">Похожие вопросы</h2>
            <div class="similar-questions__list">
                <?php while ($similar_query->have_posts()) : $similar_query->the_post();
                    $sim_terms = get_the_terms(get_the_ID(), 'consultation_category');
                    $sim_cat   = !empty($sim_terms) ? $sim_terms[0]->name : 'Вопрос';
                    $sim_ans_count = belan_get_question_answers_count(get_the_ID());
                    ?>
                    <a href="<?php the_permalink(); ?>" class="similar-questions__item">
                        <h3 class="similar-questions__item-title"><?php the_title(); ?></h3>
                        <div class="similar-questions__badges">
                            <?php if ($sim_ans_count > 0) : ?>
                                <span class="qa-badge qa-badge--answered" style="margin-left: 8px;">Ответов: <?php echo esc_html($sim_ans_count); ?></span>
                            <?php else : ?>
                                <span class="qa-badge qa-badge--waiting" style="margin-left: 8px;">Ждет ответа</span>
                            <?php endif; ?>
                            <span class="consultation-card__badge"><?php echo esc_html($sim_cat); ?></span>
                        </div>
                    </a>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
            <div class="similar-questions__more-btn">
                <a href="<?php echo esc_url(home_url('/consultation/')); ?>" class="btn btn--yellow btn--arrow">Показать все вопросы</a>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Section: Форма «Задать вопрос адвокату» -->
<?php
get_template_part('template-parts/section', 'consultation-form');
?>

<?php
get_footer();
