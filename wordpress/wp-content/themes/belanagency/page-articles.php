<?php
/**
 * Template Name: Каталог статей (Articles Archive)
 * Matches articles.html and blog.html
 *
 * @package BelanAgency
 */

get_header();
?>

<!-- hero -->
<section class="section hero hero--page hero--articles">
    <div class="container">
        <div class="hero__content">
            <div class="hero__reviews-row">
                <div class="hero__top">
                    <nav class="breadcrumbs" aria-label="Хлебные крошки">
                        <ul class="breadcrumbs__list">
                            <li><a href="<?php echo esc_url(home_url('/')); ?>" class="breadcrumbs__link">Главная</a></li>
                            <li class="breadcrumbs__separator">/</li>
                            <li class="breadcrumbs__current" aria-current="page">Блог</li>
                        </ul>
                    </nav>
                    <h1 class="hero__title hero__title--page">
                        Юридические статьи
                    </h1>
                </div>
                <div class="hero__seal">
                    <picture>
                        <source type="image/avif"
                            srcset="<?php echo esc_url(belan_asset('img/cases-seal-sm.avif')); ?> 320w, <?php echo esc_url(belan_asset('img/cases-seal.avif')); ?> 520w"
                            sizes="(max-width: 768px) 120px, (max-width: 1024px) 180px, 230px">
                        <source type="image/webp"
                            srcset="<?php echo esc_url(belan_asset('img/cases-seal-sm.webp')); ?> 320w, <?php echo esc_url(belan_asset('img/cases-seal.webp')); ?> 520w"
                            sizes="(max-width: 768px) 120px, (max-width: 1024px) 180px, 230px">
                        <img src="<?php echo esc_url(belan_asset('img/cases-seal.png')); ?>"
                            srcset="<?php echo esc_url(belan_asset('img/cases-seal-sm.png')); ?> 320w, <?php echo esc_url(belan_asset('img/cases-seal.png')); ?> 520w"
                            sizes="(max-width: 768px) 120px, (max-width: 1024px) 180px, 230px" width="230"
                            height="177" alt="Законъ — статьи адвоката Ежова" loading="lazy" decoding="async">
                    </picture>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- blog section -->
<section class="section blog-page">
    <div class="container">
        <!-- Categories Filter -->
        <?php
        $cats = get_categories(['hide_empty' => false]);
        if (!empty($cats)) : ?>
            <div class="blog-tags-container">
                <button type="button" class="blog-tags-toggle" aria-expanded="false">
                    <span class="blog-tags-toggle__text">Категории</span>
                    <span class="blog-tags-toggle__icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </span>
                </button>
                <div class="blog-tags-collapse">
                    <div class="blog-tags-collapse__inner">
                        <div class="blog-tags">
                            <a href="<?php echo esc_url(home_url('/articles/')); ?>" class="blog-tags__item blog-tags__item--active">Все статьи</a>
                            <?php foreach ($cats as $cat) :
                                if ($cat->slug === 'uncategorized') continue; ?>
                                <a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>" class="blog-tags__item"><?php echo esc_html($cat->name); ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Articles Grid strictly from DB -->
        <div class="blog-page__grid">
            <?php
            $articles_query = new WP_Query([
                'post_type'      => 'post',
                'posts_per_page' => 9,
                'paged'          => 1,
            ]);

            if ($articles_query->have_posts()) :
                while ($articles_query->have_posts()) : $articles_query->the_post();
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
                <?php endwhile;
                wp_reset_postdata();
            else : ?>
                <p class="empty-message" style="grid-column: 1 / -1; padding: 40px 0; text-align: center; color: #777; font-size: 18px;">
                    Статей пока нет.
                </p>
            <?php endif; ?>
        </div>

        <?php if ($articles_query->max_num_pages > 1) : ?>
            <div class="blog-page__more-wrapper">
                <button type="button" class="btn btn--outline-more btn--width js-load-more"
                    data-post-type="post"
                    data-page="1"
                    data-max-pages="<?php echo esc_attr($articles_query->max_num_pages); ?>"
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
