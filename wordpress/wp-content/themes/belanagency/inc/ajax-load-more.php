<?php
/**
 * AJAX Load More Posts Handler
 *
 * @package BelanAgency
 */

if (!defined('ABSPATH')) {
    exit;
}

function belan_ajax_load_more_handler() {
    $post_type = sanitize_key($_POST['post_type'] ?? 'post');
    $paged     = max(1, (int) ($_POST['paged'] ?? 1));
    $category  = sanitize_text_field($_POST['category'] ?? '');

    $default_per_page = [
        'post'         => 9,
        'news'         => 9,
        'consultation' => 10,
        'cases'        => 12,
        'review'       => 6,
    ];

    $per_page = (int) ($_POST['posts_per_page'] ?? ($default_per_page[$post_type] ?? 9));

    $args = [
        'post_type'      => $post_type,
        'post_status'    => 'publish',
        'paged'          => $paged,
        'posts_per_page' => $per_page,
    ];

    if (!empty($category)) {
        if ($post_type === 'post') {
            $args['cat'] = (int) $category;
        } elseif ($post_type === 'consultation') {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'consultation_category',
                    'field'    => is_numeric($category) ? 'term_id' : 'slug',
                    'terms'    => $category,
                ],
            ];
        } elseif ($post_type === 'cases') {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'case_category',
                    'field'    => is_numeric($category) ? 'term_id' : 'slug',
                    'terms'    => $category,
                ],
            ];
        }
    }

    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        wp_send_json_success([
            'html'      => '',
            'has_more'  => false,
            'paged'     => $paged,
            'max_pages' => $query->max_num_pages,
        ]);
    }

    ob_start();

    if ($post_type === 'post') {
        while ($query->have_posts()) {
            $query->the_post();
            $post_cats = get_the_category();
            $post_cat_name = !empty($post_cats) ? $post_cats[0]->name : 'Статья';
            $post_cat_link = !empty($post_cats) ? get_category_link($post_cats[0]->term_id) : '#';
            ?>
            <article class="articles-card">
                <a href="<?php the_permalink(); ?>" class="articles-card__image">
                    <?php
                    if (has_post_thumbnail()) {
                        the_post_thumbnail('medium_large');
                    } else {
                        belan_picture('article-1', get_the_title(), '', '(max-width: 480px) 100vw, (max-width: 768px) 50vw, 364px', 364, 240);
                    }
                    ?>
                </a>
                <a href="<?php echo esc_url($post_cat_link); ?>" class="articles-card__category"><?php echo esc_html($post_cat_name); ?></a>
                <h3 class="articles-card__title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h3>
                <p class="articles-card__description"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                <a href="<?php the_permalink(); ?>" class="articles-card__read-more">Читать статью</a>
            </article>
            <?php
        }
    } elseif ($post_type === 'news') {
        while ($query->have_posts()) {
            $query->the_post();
            ?>
            <article class="news-card">
                <a href="<?php the_permalink(); ?>" class="news-card__image">
                    <?php
                    if (has_post_thumbnail()) {
                        the_post_thumbnail('medium_large');
                    } else {
                        belan_picture('article-1', get_the_title(), '', '(max-width: 480px) 100vw, (max-width: 768px) 50vw, 364px', 364, 240);
                    }
                    ?>
                </a>
                <span class="news-card__date"><?php echo get_the_date('j F Y'); ?></span>
                <h3 class="news-card__title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h3>
                <p class="news-card__description"><?php echo wp_trim_words(get_the_excerpt(), 18); ?></p>
                <a href="<?php the_permalink(); ?>" class="news-card__read-more">Читать новость</a>
            </article>
            <?php
        }
    } elseif ($post_type === 'consultation') {
        $idx = ($paged - 1) * $per_page;
        while ($query->have_posts()) {
            $query->the_post();
            $idx++;
            $author   = belan_field('consultation_author', get_the_ID(), 'Анонимно');
            $date     = get_the_date('d.m.Y');
            $question = belan_field('consultation_question', get_the_ID(), get_the_excerpt());
            $cats     = get_the_terms(get_the_ID(), 'consultation_category');
            $cat_name = (!empty($cats) && !is_wp_error($cats)) ? $cats[0]->name : 'Общие вопросы';
            $cat_link = (!empty($cats) && !is_wp_error($cats)) ? get_term_link($cats[0]) : '#';
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
            <?php
        }
    } elseif ($post_type === 'cases') {
        while ($query->have_posts()) {
            $query->the_post();
            $task     = belan_field('case_problem', get_the_ID(), get_the_excerpt());
            $decision = belan_field('case_actions', get_the_ID());
            $result   = belan_field('case_result', get_the_ID());
            ?>
            <div class="cases__slider-item">
                <div class="cases__slider-item-title">
                    <p><?php the_title(); ?></p>
                </div>
                <?php if ($task) : ?>
                    <div class="cases__slider-item-task">
                        <span>Задача:</span>
                        <p><?php echo esc_html($task); ?></p>
                    </div>
                <?php endif; ?>
                <?php if ($decision) : ?>
                    <div class="cases__slider-item-decision">
                        <span>Решение:</span>
                        <p><?php echo esc_html($decision); ?></p>
                    </div>
                <?php endif; ?>
                <?php if ($result) : ?>
                    <div class="cases__slider-item-result">
                        <span>Результат:</span>
                        <p><?php echo esc_html($result); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            <?php
        }
    } elseif ($post_type === 'review') {
        while ($query->have_posts()) {
            $query->the_post();
            $text   = belan_field('review_text', get_the_ID(), get_the_content());
            $author = belan_field('review_author', get_the_ID(), get_the_title());
            ?>
            <div class="reviews-page__card">
                <svg class="reviews-page__card-quote-top" viewBox="0 0 32 26" fill="currentColor">
                    <path d="M12.8 0H6.4L0 12.8V25.6H12.8V12.8H6.4L12.8 0ZM32 0H25.6L19.2 12.8V25.6H32V12.8H25.6L32 0Z" />
                </svg>
                <p class="reviews-page__card-text">«<?php echo esc_html($text); ?>»</p>
                <p class="reviews-page__card-author"><?php echo esc_html($author); ?></p>
                <svg class="reviews-page__card-quote-bottom" viewBox="0 0 32 26" fill="currentColor">
                    <path d="M19.2 25.6H25.6L32 12.8V0H19.2V12.8H25.6L19.2 25.6ZM0 25.6H6.4L12.8 12.8V0H0V12.8H6.4L0 25.6Z" />
                </svg>
            </div>
            <?php
        }
    }

    $html = ob_get_clean();
    wp_reset_postdata();

    $has_more = ($paged < $query->max_num_pages);

    wp_send_json_success([
        'html'      => $html,
        'has_more'  => $has_more,
        'paged'     => $paged,
        'max_pages' => $query->max_num_pages,
    ]);
}
add_action('wp_ajax_belan_load_more', 'belan_ajax_load_more_handler');
add_action('wp_ajax_nopriv_belan_load_more', 'belan_ajax_load_more_handler');
