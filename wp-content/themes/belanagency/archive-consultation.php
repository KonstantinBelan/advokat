<?php
/**
 * Archive Consultation Template
 * Matches consultation.html - Strictly from DB
 *
 * @package BelanAgency
 */

get_header();
?>

<!-- Hero Section -->
<section class="section hero hero--page hero--consultation">
    <div class="container">
        <div class="hero__content">
            <div class="hero__top">
                <nav class="breadcrumbs" aria-label="Хлебные крошки">
                    <ul class="breadcrumbs__list">
                        <li><a href="<?php echo esc_url(home_url('/')); ?>" class="breadcrumbs__link">Главная</a></li>
                        <li class="breadcrumbs__separator">/</li>
                        <li class="breadcrumbs__current" aria-current="page">Юридическая консультация</li>
                    </ul>
                </nav>
                <h1 class="hero__title">ЮРИДИЧЕСКАЯ КОНСУЛЬТАЦИЯ ОНЛАЙН</h1>
                <p class="hero__description">
                    Задайте вопрос адвокату бесплатно прямо на сайте – получите профессиональный ответ. Все вопросы и ответы анонимны. Читайте чужие кейсы и находите решение своей проблемы.
                </p>
                <div class="hero__actions">
                    <a href="#ask-question" class="btn btn--primary btn--red btn--arrow">Задать вопрос юристу</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Consultation Section (Questions List + Sidebar) -->
<section class="section consultation">
    <div class="container">
        <div class="consultation__layout">
            <!-- Left: Questions List -->
            <div class="consultation__main">
                <div class="consultation__main">
                    <?php
                    $consult_query = new WP_Query([
                        'post_type'      => 'consultation',
                        'posts_per_page' => 10,
                        'paged'          => 1,
                    ]);

                    if ($consult_query->have_posts()) :
                        $idx = 0;
                        while ($consult_query->have_posts()) : $consult_query->the_post();
                            $idx++;
                            $author = belan_field('consultation_author', get_the_ID(), 'Пользователь');
                            $date   = belan_field('consultation_date', get_the_ID(), get_the_date('d.m.Y'));
                            $terms  = get_the_terms(get_the_ID(), 'consultation_category');
                            $cat_name = !empty($terms) ? $terms[0]->name : 'Общие вопросы';
                            $cat_link = !empty($terms) ? get_term_link($terms[0]) : '#';
                            $question = belan_field('consultation_question', get_the_ID(), get_the_excerpt());
                            ?>
                            <article class="consultation-card">
                                <div class="consultation-card__header">
                                    <div class="consultation-card__meta">
                                        <span class="consultation-card__author"><?php echo esc_html($author); ?></span>
                                        <span class="consultation-card__sep">/</span>
                                        <span class="consultation-card__num">Вопрос № <?php echo get_the_ID(); ?></span>
                                        <span class="consultation-card__sep">/</span>
                                        <span class="consultation-card__date"><?php echo esc_html($date); ?></span>
                                    </div>
                                    <a href="<?php echo esc_url($cat_link); ?>" class="consultation-card__badge"><?php echo esc_html($cat_name); ?></a>
                                </div>
                                <h3 class="consultation-card__title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <p class="consultation-card__text">
                                    <?php echo esc_html($question); ?>
                                </p>
                                <div class="consultation-card__footer">
                                    <div class="consultation-card__responder">
                                        <span>Отвечает</span>
                                        <div class="consultation-card__avatar">
                                            <img src="<?php echo esc_url(belan_asset('img/about.webp')); ?>" alt="Ежов Антон">
                                        </div>
                                        <strong>Ежов Антон</strong>
                                    </div>
                                    <div class="consultation-card__views">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                        <span><?php echo (30 + $idx * 15); ?></span>
                                    </div>
                                </div>
                            </article>
                        <?php endwhile;
                        wp_reset_postdata();
                    else : ?>
                        <p class="empty-message" style="padding: 40px 0; text-align: center; color: #777; font-size: 18px;">
                            Вопросов пока нет. Вы можете задать первый вопрос с помощью формы ниже.
                        </p>
                    <?php endif; ?>
                </div>

                <?php if ($consult_query->max_num_pages > 1) : ?>
                    <!-- Pagination Button -->
                    <div class="consultation__pagination">
                        <button type="button" class="btn btn--width btn--outline-more js-load-more"
                            data-post-type="consultation"
                            data-page="1"
                            data-max-pages="<?php echo esc_attr($consult_query->max_num_pages); ?>"
                            data-container=".consultation__main">
                            Показать еще <span class="btn--outline-more__arrow">▼</span>
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right: Sidebar -->
            <aside class="consultation__sidebar">
                <a href="#ask-question" class="btn btn--primary btn--red btn--width btn--arrow">
                    Задать вопросу адвокату
                </a>

                <div class="consultation-search">
                    <input type="text" class="consultation-search__input" placeholder="Поиск по вопросам">
                    <button type="button" class="consultation-search__btn" aria-label="Искать">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                    </button>
                </div>

                <div class="consultation-categories">
                    <h3 class="consultation-categories__title">Категории вопросов:</h3>
                    <ul class="consultation-categories__list">
                        <?php
                        $sidebar_terms = get_terms([
                            'taxonomy'   => 'consultation_category',
                            'hide_empty' => false,
                        ]);
                        if (!empty($sidebar_terms) && !is_wp_error($sidebar_terms)) :
                            foreach ($sidebar_terms as $term) : ?>
                                <li class="consultation-categories__item">
                                    <a href="<?php echo esc_url(get_term_link($term)); ?>" class="consultation-categories__link"><?php echo esc_html($term->name); ?></a>
                                </li>
                            <?php endforeach;
                        else : ?>
                            <li class="consultation-categories__item">Категорий пока нет</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</section>

<?php
get_template_part('template-parts/section', 'consultation-form');
get_footer();
