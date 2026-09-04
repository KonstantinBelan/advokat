<?php
/**
 * Category Archive Template
 * Matches category.html - Strictly from DB
 *
 * @package BelanAgency
 */

get_header();
$current_category = get_queried_object();
?>

<!-- hero -->
<section class="section hero hero--page hero--articles">
    <div class="container">
        <div class="hero__content">
            <div class="hero__top">
                <nav class="breadcrumbs" aria-label="Хлебные крошки">
                    <ul class="breadcrumbs__list">
                        <li><a href="<?php echo esc_url(home_url('/')); ?>" class="breadcrumbs__link">Главная</a></li>
                        <li class="breadcrumbs__separator">/</li>
                        <li><a href="<?php echo esc_url(home_url('/articles/')); ?>" class="breadcrumbs__link">Статьи</a></li>
                        <li class="breadcrumbs__separator">/</li>
                        <li class="breadcrumbs__current" aria-current="page"><?php echo esc_html($current_category->name); ?></li>
                    </ul>
                </nav>
                <h1 class="hero__title hero__title--page">
                    Статьи на тему: <?php echo esc_html($current_category->name); ?>
                </h1>
                <div class="hero__description hero__description--page">
                    <p class="hero__description-text"><?php echo esc_html($current_category->description ?: 'Актуальные публикации и правовые разъяснения адвоката по направлению «' . $current_category->name . '».'); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- blog section -->
<section class="section blog-page">
    <div class="container">
        <!-- Tags -->
        <?php
        $cats = get_categories(['hide_empty' => false]);
        if (!empty($cats)) : ?>
            <div class="blog-tags-container">
                <button type="button" class="blog-tags-toggle" aria-expanded="false">
                    <span class="blog-tags-toggle__text"><?php echo esc_html($current_category->name); ?></span>
                    <span class="blog-tags-toggle__icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </span>
                </button>
                <div class="blog-tags-collapse">
                    <div class="blog-tags-collapse__inner">
                        <div class="blog-tags">
                            <a href="<?php echo esc_url(home_url('/articles/')); ?>" class="blog-tags__item">Все статьи</a>
                            <?php foreach ($cats as $cat) :
                                if ($cat->slug === 'uncategorized') continue;
                                $is_active = ($cat->term_id === $current_category->term_id) ? ' blog-tags__item--active' : '';
                                ?>
                                <a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>" class="blog-tags__item<?php echo $is_active; ?>"><?php echo esc_html($cat->name); ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Articles Grid -->
        <div class="blog-page__grid">
            <?php
            $cat_query = new WP_Query([
                'post_type'      => 'post',
                'cat'            => $current_category->term_id,
                'posts_per_page' => 9,
                'paged'          => 1,
            ]);

            if ($cat_query->have_posts()) :
                while ($cat_query->have_posts()) : $cat_query->the_post(); ?>
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
                        <a href="<?php echo esc_url(get_category_link($current_category->term_id)); ?>" class="articles-card__category"><?php echo esc_html($current_category->name); ?></a>
                        <h3 class="articles-card__title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        <p class="articles-card__description"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                        <a href="<?php the_permalink(); ?>" class="articles-card__read-more">Читать статью</a>
                    </article>
                <?php endwhile;
                wp_reset_postdata();
            else : ?>
                <p class="empty-message" style="grid-column: 1 / -1; padding: 40px 0; text-align: center; color: #777; font-size: 18px;">
                    В этой категории пока нет опубликованных статей.
                </p>
            <?php endif; ?>
        </div>

        <?php if ($cat_query->max_num_pages > 1) : ?>
            <div class="blog-page__more-wrapper">
                <button type="button" class="btn btn--outline-more btn--width js-load-more"
                    data-post-type="post"
                    data-category="<?php echo esc_attr($current_category->term_id); ?>"
                    data-page="1"
                    data-max-pages="<?php echo esc_attr($cat_query->max_num_pages); ?>"
                    data-container=".blog-page__grid">
                    Показать еще <span class="btn--outline-more__arrow">▼</span>
                </button>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
get_template_part('template-parts/section', 'help');
get_footer();
