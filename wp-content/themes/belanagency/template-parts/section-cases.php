<?php
/**
 * Cases Slider Section Template Part
 * Strictly from DB
 *
 * @package BelanAgency
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- cases -->
<section class="section cases bg-gray" id="cases">
    <div class="container">
        <div class="section__header">
            <h2 class="section__title section__title--center">Реальные результаты моей работы</h2>
        </div>
        <div class="cases__slider-container slider-container">
            <div class="cases__wrapper swiper">
                <div class="cases__slider swiper-wrapper">
                    <?php
                    $cases_query = new WP_Query([
                        'post_type'      => 'cases',
                        'posts_per_page' => 10,
                    ]);

                    if ($cases_query->have_posts()) :
                        while ($cases_query->have_posts()) : $cases_query->the_post();
                            $task     = belan_field('case_problem', get_the_ID(), get_the_excerpt());
                            $decision = belan_field('case_actions', get_the_ID());
                            $result   = belan_field('case_result', get_the_ID());
                            ?>
                            <div class="cases__slider-item swiper-slide">
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
                        <?php endwhile;
                        wp_reset_postdata();
                    else : ?>
                        <div class="cases__slider-item swiper-slide" style="width: 100%;">
                            <div class="cases__slider-item-title">
                                <p>Кейсов пока нет</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="cases__slider-dots"></div>
            <div class="cases__slider-nav slider-nav">
                <button class="slider-btn slider-btn--prev cases__slider-nav__item--prev"
                    aria-label="Предыдущий слайд"></button>
                <button class="slider-btn slider-btn--next cases__slider-nav__item--next"
                    aria-label="Следующий слайд"></button>
            </div>
        </div>
        <div class="cases__more-btn">
            <a href="<?php echo esc_url(home_url('/cases/')); ?>" class="btn btn--yellow btn--arrow">Смотреть все кейсы</a>
        </div>
    </div>
</section>
