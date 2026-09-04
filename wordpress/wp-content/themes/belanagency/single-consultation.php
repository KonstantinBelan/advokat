<?php
/**
 * Single Consultation Template
 * Matches consultation-item.html - Strictly from DB
 *
 * @package BelanAgency
 */

get_header();

$author = belan_field('consultation_author', get_the_ID(), 'Пользователь');
$date   = belan_field('consultation_date', get_the_ID(), get_the_date('d.m.Y'));
$phone  = belan_option('phone_number', '8 (993) 909-90-50');
$phone_clean = belan_phone_clean($phone);
$max_url = belan_option('site_max_url', '#');
$wa_url  = belan_option('site_whatsapp_url', '#');
$tg_url  = belan_option('site_telegram_url', '#');

$terms = get_the_terms(get_the_ID(), 'consultation_category');
$cat_name = !empty($terms) ? $terms[0]->name : 'Вопросы и ответы';
$cat_link = !empty($terms) ? get_term_link($terms[0]) : home_url('/consultation/');

$q_text = belan_field('consultation_question', get_the_ID(), get_the_content());
$answer = belan_field('consultation_answer', get_the_ID());
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
                            <li><a href="<?php echo esc_url(home_url('/consultation/')); ?>" class="breadcrumbs__link">Бесплатная консультация</a></li>
                            <li class="breadcrumbs__separator">/</li>
                            <li class="breadcrumbs__current" aria-current="page"><?php echo esc_html($cat_name); ?></li>
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

<!-- Main Consultation Question & Answer -->
<section class="section consultation">
    <div class="container">
        <div class="consultation__layout">
            <!-- Left: Question & Answer Card -->
            <div class="consultation__main">
                <article class="consultation-item__card">
                    <!-- Question Header Meta -->
                    <div class="consultation-item__header">
                        <div class="consultation-card__meta">
                            <span class="consultation-card__author"><?php echo esc_html($author); ?></span>
                            <span class="consultation-card__sep">/</span>
                            <span class="consultation-card__num">Вопрос № <?php echo get_the_ID(); ?></span>
                            <span class="consultation-card__sep">/</span>
                            <span class="consultation-card__date"><?php echo esc_html($date); ?></span>
                        </div>
                        <a href="<?php echo esc_url($cat_link); ?>" class="consultation-card__badge"><?php echo esc_html($cat_name); ?></a>
                    </div>

                    <!-- Question Title -->
                    <h1 class="consultation-item__title"><?php the_title(); ?></h1>

                    <!-- Question Text -->
                    <div class="consultation-item__text">
                        <?php
                        if ($q_text) {
                            echo wpautop(esc_html($q_text));
                        } else {
                            the_content();
                        }
                        ?>
                    </div>

                    <!-- Lawyer Answer Box -->
                    <div class="consultation-answer">
                        <span class="consultation-answer__badge">Ответ от адвоката</span>

                        <div class="consultation-answer__author-row">
                            <div class="consultation-answer__author">
                                <div class="consultation-answer__avatar">
                                    <img src="<?php echo esc_url(belan_asset('img/about.webp')); ?>" alt="Ежов Антон">
                                </div>
                                <div class="consultation-answer__author-info">
                                    <span class="consultation-answer__name">Ежов Антон</span>
                                    <span class="consultation-answer__date"><?php echo esc_html($date); ?></span>
                                </div>
                            </div>
                            <div class="consultation-answer__stats">
                                <p>Консультаций на сайте: 87</p>
                                <span class="consultation-card__sep">/</span>
                                <p>Стаж 12 лет</p>
                            </div>
                        </div>

                        <div class="consultation-answer__content">
                            <?php
                            if ($answer) {
                                echo wp_kses_post($answer);
                            } else { ?>
                                <p>Ответ адвоката по данному вопросу формируется. Вы можете также позвонить или задать уточняющий вопрос через форму ниже.</p>
                            <?php } ?>
                        </div>

                        <div class="consultation-answer__lawyer-bar">
                            <div class="consultation-answer__lawyer-meta">
                                <div class="consultation-card__avatar">
                                    <img src="<?php echo esc_url(belan_asset('img/about.webp')); ?>" alt="Ежов Антон">
                                </div>
                                <strong>Ежов Антон</strong>
                                <span class="consultation-card__sep">/</span>
                                <span>Административное право</span>
                                <span class="consultation-card__sep">/</span>
                                <span>г. Москва</span>
                                <span class="consultation-card__sep">/</span>
                                <span>Стаж 12 лет</span>
                            </div>
                            <div class="consultation-answer__contacts">
                                <a href="tel:<?php echo esc_attr($phone_clean); ?>" class="consultation-answer__contact-btn consultation-answer__contact-btn--phone">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10.361 11.1059L11.5687 9.8999C11.7314 9.73948 11.9372 9.62968 12.1612 9.5838C12.3852 9.53792 12.6177 9.55796 12.8304 9.64147L14.3024 10.2283C14.5174 10.3155 14.7019 10.4642 14.8322 10.6559C14.9628 10.8475 15.0336 11.0734 15.0357 11.3052V13.9972C15.0344 14.1548 15.0013 14.3105 14.9382 14.455C14.8751 14.5994 14.7832 14.7297 14.6683 14.8378C14.5535 14.9461 14.418 15.0299 14.2698 15.0843C14.1217 15.1387 13.964 15.1627 13.8064 15.1548C3.49179 14.5141 1.41054 5.7919 1.01695 2.45381C0.998673 2.2899 1.01537 2.12396 1.06592 1.96695C1.11649 1.80994 1.19978 1.66538 1.31029 1.54281C1.42081 1.42024 1.55609 1.32243 1.70718 1.2558C1.85828 1.18918 2.0218 1.15526 2.18697 1.15627H4.79122C5.02361 1.15696 5.25048 1.22706 5.44266 1.35755C5.63483 1.48803 5.7835 1.67294 5.86958 1.8885L6.45729 3.35834C6.54369 3.56996 6.56574 3.80233 6.52067 4.02639C6.4756 4.25046 6.36542 4.45629 6.20387 4.61819L4.99611 5.8242C4.99611 5.8242 5.69164 10.5245 10.361 11.1059Z" fill="currentColor" />
                                    </svg>
                                    Телефон
                                </a>
                                <a href="<?php echo esc_url($max_url); ?>" class="consultation-answer__contact-btn consultation-answer__contact-btn--messenger" target="_blank">
                                    <span>Max</span>
                                </a>
                                <a href="<?php echo esc_url($wa_url); ?>" class="consultation-answer__contact-btn consultation-answer__contact-btn--messenger" target="_blank">
                                    <span>Whatsapp</span>
                                </a>
                                <a href="<?php echo esc_url($tg_url); ?>" class="consultation-answer__contact-btn consultation-answer__contact-btn--messenger" target="_blank">
                                    <span>Telegram</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
            </div>

            <!-- Right: Sidebar -->
            <aside class="consultation__sidebar">
                <!-- Search Box -->
                <div class="consultation-search">
                    <input type="text" class="consultation-search__input" placeholder="Поиск по вопросам">
                    <button type="button" class="consultation-search__btn" aria-label="Искать">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                    </button>
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
    'posts_per_page' => 5,
    'post__not_in'   => [get_the_ID()],
]);

if ($similar_query->have_posts()) : ?>
    <section class="section similar-questions">
        <div class="container">
            <h2 class="similar-questions__title">Похожие вопросы</h2>
            <div class="similar-questions__list">
                <?php while ($similar_query->have_posts()) : $similar_query->the_post();
                    $sim_terms = get_the_terms(get_the_ID(), 'consultation_category');
                    $sim_cat   = !empty($sim_terms) ? $sim_terms[0]->name : 'Вопрос';
                    ?>
                    <a href="<?php the_permalink(); ?>" class="similar-questions__item">
                        <h3 class="similar-questions__item-title"><?php the_title(); ?></h3>
                        <span class="consultation-card__badge"><?php echo esc_html($sim_cat); ?></span>
                    </a>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
            <div class="similar-questions__more-btn">
                <a href="<?php echo esc_url(home_url('/consultation/')); ?>" class="btn btn--yellow btn--arrow">Показать все вопросы</a>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php
get_template_part('template-parts/section', 'consultation-form');
?>

<?php
get_footer();
