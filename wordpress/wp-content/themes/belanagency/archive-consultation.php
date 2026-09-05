<?php
/**
 * Archive Consultation Template
 * Comprehensive Legal Q&A Platform Feed
 *
 * @package BelanAgency
 */

get_header();

$search_query = sanitize_text_field($_GET['q'] ?? '');
$filter_cat   = sanitize_text_field($_GET['category'] ?? '');
$paged        = max(1, get_query_var('paged'));

// Base Query Args
$query_args = [
    'post_type'      => 'consultation',
    'post_status'    => 'publish',
    'posts_per_page' => 10,
    'paged'          => $paged,
];

// Search filter
if (!empty($search_query)) {
    $query_args['s'] = $search_query;
}

// Category filter
if (!empty($filter_cat)) {
    $query_args['tax_query'] = [
        [
            'taxonomy' => 'consultation_category',
            'field'    => is_numeric($filter_cat) ? 'term_id' : 'slug',
            'terms'    => $filter_cat,
        ],
    ];
}

$consult_query = new WP_Query($query_args);
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
                <h1 class="hero__title">БЕСПЛАТНАЯ ЮРИДИЧЕСКАЯ КОНСУЛЬТАЦИЯ ОНЛАЙН</h1>
                <p class="hero__description">
                    Задайте вопрос адвокатам бесплатно прямо на сайте. Действующие адвокаты изучат вашу ситуацию и дадут развернутый правовой ответ.
                </p>
                <div class="hero__actions">
                    <a href="#ask-question" class="btn btn--primary btn--red btn--arrow">Задать вопрос бесплатно</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Q&A Section -->
<section class="section consultation">
    <div class="container">
        <div class="consultation__layout">
            <!-- Left: Questions Feed -->
            <div class="consultation__main">
                <div class="consultation__main qa-questions-feed">
                    <?php
                    if ($consult_query->have_posts()) :
                        while ($consult_query->have_posts()) : $consult_query->the_post();
                            belan_render_consultation_card(get_the_ID());
                        endwhile;
                        wp_reset_postdata();
                    else : ?>
                        <div class="qa-empty-feed-card">
                            <p>Вопросов пока не найдено. Вы можете задать первый вопрос с помощью формы ниже.</p>
                            <a href="#ask-question" class="btn btn--small btn--yellow">
                                Задать вопрос
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($consult_query->max_num_pages > 1) : ?>
                    <!-- AJAX Load More Button -->
                    <div class="consultation__pagination">
                        <button type="button" class="btn btn--width btn--outline-more js-load-more"
                            data-post-type="consultation"
                            data-page="1"
                            data-max-pages="<?php echo esc_attr($consult_query->max_num_pages); ?>"
                            data-container=".consultation__main .qa-questions-feed">
                            Показать еще вопросы <span class="btn--outline-more__arrow">▼</span>
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right: Sidebar -->
            <aside class="consultation__sidebar">
                <a href="#ask-question" class="btn btn--primary btn--red btn--width btn--arrow">
                    Задать вопрос адвокату
                </a>

                <!-- Search Box -->
                <div class="consultation-search">
                    <form action="<?php echo esc_url(home_url('/consultation/')); ?>" method="GET" class="qa-sidebar-search-form">
                        <input type="text" name="q" class="consultation-search__input" placeholder="Поиск по вопросам" value="<?php echo esc_attr($search_query); ?>">
                        <button type="submit" class="consultation-search__btn" aria-label="Искать">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                        </button>
                    </form>
                </div>

                <!-- Categories Widget -->
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
                                    <a href="<?php echo esc_url(get_term_link($term)); ?>" class="consultation-categories__link">
                                        <span class="qa-cat-name"><?php echo esc_html($term->name); ?></span>
                                        <span class="qa-cat-count"><?php echo esc_html($term->count); ?></span>
                                    </a>
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

<!-- Section: Форма «Задать вопрос адвокату» -->
<?php
get_template_part('template-parts/section', 'consultation-form');
get_footer();
