<?php
/**
 * Articles Section Template Part
 * Strictly from DB
 *
 * @package BelanAgency
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- articles -->
<section class="section articles bg-gray" id="articles">
    <div class="container">
        <div class="articles__header">
            <h2 class="section__title">Экспертные статьи в&nbsp;блоге</h2>
            <a class="articles__btn-blog btn--arrow--2" href="<?php echo esc_url(home_url('/articles/')); ?>">Перейти в блог</a>
        </div>
        <div class="articles__slider-container slider-container">
            <div class="articles__wrapper swiper">
                <div class="articles__cards swiper-wrapper">
                    <?php
                    $articles_query = new WP_Query([
                        'post_type'      => 'post',
                        'posts_per_page' => 6,
                    ]);

                    if ($articles_query->have_posts()) :
                        while ($articles_query->have_posts()) : $articles_query->the_post();
                            $cats = get_the_category();
                            $cat_name = !empty($cats) ? $cats[0]->name : 'Право';
                            $cat_link = !empty($cats) ? get_category_link($cats[0]->term_id) : '#';
                            ?>
                            <article class="articles-card swiper-slide">
                                <a href="<?php the_permalink(); ?>" class="articles-card__image">
                                    <?php
                                    if (has_post_thumbnail()) {
                                        the_post_thumbnail('medium_large');
                                    } else {
                                        belan_picture('article-1', get_the_title(), '', '(max-width: 480px) 100vw, (max-width: 768px) 50vw, 364px', 364, 240);
                                    }
                                    ?>
                                </a>
                                <a href="<?php echo esc_url($cat_link); ?>" class="articles-card__category"><?php echo esc_html($cat_name); ?></a>
                                <h3 class="articles-card__title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <p class="articles-card__description"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                                <a href="<?php the_permalink(); ?>" class="articles-card__read-more">Читать статью</a>
                            </article>
                        <?php endwhile;
                        wp_reset_postdata();
                    else : ?>
                        <div class="articles-card swiper-slide" style="width: 100%;">
                            <p style="padding: 20px; text-align: center; color: #777;">Статей пока нет.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="articles__wrapper-dots"></div>
            <div class="articles__wrapper-nav slider-nav">
                <button class="slider-btn slider-btn--prev articles__wrapper-nav__item--prev" aria-label="Предыдущая статья"></button>
                <button class="slider-btn slider-btn--next articles__wrapper-nav__item--next" aria-label="Следующая статья"></button>
            </div>
        </div>
    </div>
</section>
