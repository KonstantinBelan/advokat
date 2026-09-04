<?php
/**
 * Archive News Template
 * Matches news.html - Strictly from DB
 *
 * @package BelanAgency
 */

get_header();
?>

<!-- hero -->
<section class="section hero hero--page hero--news">
    <div class="container">
        <div class="hero__content">
            <div class="hero__reviews-row">
                <div class="hero__top">
                    <nav class="breadcrumbs" aria-label="Хлебные крошки">
                        <ul class="breadcrumbs__list">
                            <li><a href="<?php echo esc_url(home_url('/')); ?>" class="breadcrumbs__link">Главная</a></li>
                            <li class="breadcrumbs__separator">/</li>
                            <li class="breadcrumbs__current" aria-current="page">Новости</li>
                        </ul>
                    </nav>
                    <h1 class="hero__title hero__title--page">
                        Юридические новости
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
                            height="177" alt="Законъ — новости адвоката Ежова" loading="lazy" decoding="async">
                    </picture>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- news section -->
<section class="section news-page">
    <div class="container">
        <!-- News Grid -->
        <div class="news-page__grid">
            <?php
            $news_query = new WP_Query([
                'post_type'      => 'news',
                'posts_per_page' => 9,
                'paged'          => 1,
            ]);

            if ($news_query->have_posts()) :
                while ($news_query->have_posts()) : $news_query->the_post();
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
                <?php endwhile;
                wp_reset_postdata();
            else : ?>
                <p class="empty-message" style="grid-column: 1 / -1; padding: 40px 0; text-align: center; color: #777; font-size: 18px;">
                    Новостей пока нет.
                </p>
            <?php endif; ?>
        </div>

        <?php if ($news_query->max_num_pages > 1) : ?>
            <div class="news-page__more-wrapper">
                <button type="button" class="btn btn--outline-more btn--width js-load-more"
                    data-post-type="news"
                    data-page="1"
                    data-max-pages="<?php echo esc_attr($news_query->max_num_pages); ?>"
                    data-container=".news-page__grid">
                    Показать еще <span class="btn--outline-more__arrow">▼</span>
                </button>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
get_template_part('template-parts/section', 'help');
get_footer();
