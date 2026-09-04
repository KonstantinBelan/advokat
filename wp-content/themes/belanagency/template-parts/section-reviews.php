<?php
/**
 * Reviews Slider Section Template Part
 * Strictly from DB
 *
 * @package BelanAgency
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- reviews -->
<section class="section reviews" id="reviews">
    <div class="container">
        <div class="section__header">
            <h2 class="section__title section__title--center">Отзывы клиентов</h2>
        </div>
        <div class="reviews__slider-container slider-container">
            <div class="reviews__wrapper swiper">
                <div class="reviews__slider swiper-wrapper">
                    <?php
                    $reviews_query = new WP_Query([
                        'post_type'      => 'review',
                        'posts_per_page' => 10,
                    ]);

                    if ($reviews_query->have_posts()) :
                        while ($reviews_query->have_posts()) : $reviews_query->the_post();
                            $text   = belan_field('review_text', get_the_ID(), get_the_content());
                            $author = belan_field('review_author', get_the_ID(), get_the_title());
                            ?>
                            <div class="reviews__slider-item swiper-slide">
                                <svg class="reviews__slider-item-quote-top" viewBox="0 0 32 26" fill="#191726" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.8 0H6.4L0 12.8V25.6H12.8V12.8H6.4L12.8 0ZM32 0H25.6L19.2 12.8V25.6H32V12.8H25.6L32 0Z" />
                                </svg>
                                <div class="reviews__slider-item-header">
                                    <p class="reviews__slider-item-header__text">«<?php echo esc_html($text); ?>»</p>
                                </div>
                                <div class="reviews__slider-item-footer">
                                    <p class="reviews__slider-item-footer__title"><?php echo esc_html($author); ?></p>
                                </div>
                                <svg class="reviews__slider-item-quote-bottom" viewBox="0 0 32 26" fill="#191726" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19.2 25.6H25.6L32 12.8V0H19.2V12.8H25.6L19.2 25.6ZM0 25.6H6.4L12.8 12.8V0H0V12.8H6.4L0 25.6Z" />
                                </svg>
                            </div>
                        <?php endwhile;
                        wp_reset_postdata();
                    else : ?>
                        <div class="reviews__slider-item swiper-slide" style="width: 100%;">
                            <div class="reviews__slider-item-header">
                                <p class="reviews__slider-item-header__text">Отзывов пока нет.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="reviews__slider-dots"></div>
            <div class="reviews__slider-nav slider-nav">
                <button class="slider-btn slider-btn--prev reviews__slider-nav__item--prev"
                    aria-label="Предыдущий отзыв"></button>
                <button class="slider-btn slider-btn--next reviews__slider-nav__item--next"
                    aria-label="Следующий отзыв"></button>
            </div>
        </div>
        <div class="reviews__more-btn">
            <a href="<?php echo esc_url(home_url('/reviews/')); ?>" class="btn btn--yellow btn--arrow">Смотреть все отзывы</a>
        </div>
    </div>
</section>
