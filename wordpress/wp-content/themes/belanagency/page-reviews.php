<?php
/**
 * Template Name: Отзывы (Reviews Page)
 * Matches reviews.html - Strictly from DB
 *
 * @package BelanAgency
 */

get_header();
?>

<!-- hero -->
<section class="section hero hero--page hero--reviews">
    <div class="container">
        <div class="hero__content">
            <div class="hero__reviews-row">
                <div class="hero__top">
                    <nav class="breadcrumbs" aria-label="Хлебные крошки">
                        <ul class="breadcrumbs__list">
                            <li><a href="<?php echo esc_url(home_url('/')); ?>" class="breadcrumbs__link">Главная</a></li>
                            <li class="breadcrumbs__separator">/</li>
                            <li class="breadcrumbs__current" aria-current="page">Отзывы адвоката</li>
                        </ul>
                    </nav>
                    <h1 class="hero__title hero__title--page">
                        Отзывы об&nbsp;адвокате<br>Ежов&nbsp;Антон Валентинович
                    </h1>
                </div>
                <div class="hero__reviews-phone">
                    <picture>
                        <source type="image/avif"
                            srcset="<?php echo esc_url(belan_asset('img/reviews-phone-sm.avif')); ?> 336w, <?php echo esc_url(belan_asset('img/reviews-phone.avif')); ?> 560w"
                            sizes="(max-width: 768px) 0vw, (max-width: 1024px) 200px, 260px">
                        <source type="image/webp"
                            srcset="<?php echo esc_url(belan_asset('img/reviews-phone-sm.webp')); ?> 336w, <?php echo esc_url(belan_asset('img/reviews-phone.webp')); ?> 560w"
                            sizes="(max-width: 768px) 0vw, (max-width: 1024px) 200px, 260px">
                        <img src="<?php echo esc_url(belan_asset('img/reviews-phone.png')); ?>"
                            srcset="<?php echo esc_url(belan_asset('img/reviews-phone-sm.png')); ?> 336w, <?php echo esc_url(belan_asset('img/reviews-phone.png')); ?> 560w"
                            sizes="(max-width: 768px) 0vw, (max-width: 1024px) 200px, 260px" width="260"
                            height="214" alt="Отзывы об адвокате Ежове А.В." loading="lazy" decoding="async">
                    </picture>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- reviews grid -->
<section class="section reviews-page">
    <div class="container">
        <div class="reviews-page__grid">
            <?php
            $reviews_query = new WP_Query([
                'post_type'      => 'review',
                'posts_per_page' => 6,
                'paged'          => 1,
            ]);

            if ($reviews_query->have_posts()) :
                while ($reviews_query->have_posts()) : $reviews_query->the_post();
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
                <?php endwhile;
                wp_reset_postdata();
            else : ?>
                <p class="empty-message" style="grid-column: 1 / -1; padding: 40px 0; text-align: center; color: #777; font-size: 18px;">
                    Отзывов пока нет. Вы можете оставить первый отзыв ниже.
                </p>
            <?php endif; ?>
        </div>

        <?php if ($reviews_query->max_num_pages > 1) : ?>
            <div class="reviews-page__more-wrapper">
                <button type="button" class="btn btn--yellow btn--arrow btn--width js-load-more"
                    data-post-type="review"
                    data-page="1"
                    data-max-pages="<?php echo esc_attr($reviews_query->max_num_pages); ?>"
                    data-container=".reviews-page__grid">
                    Показать еще
                </button>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
get_template_part('template-parts/section', 'leave-review');
get_template_part('template-parts/section', 'cta');
get_template_part('template-parts/section', 'help');

get_footer();
