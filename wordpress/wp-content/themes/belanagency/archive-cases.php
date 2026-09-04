<?php
/**
 * Archive Cases Template
 * Matches cases.html - Strictly from DB
 *
 * @package BelanAgency
 */

get_header();
?>

<!-- hero -->
<section class="section hero hero--page hero--cases">
    <div class="container">
        <div class="hero__content">
            <div class="hero__reviews-row">
                <div class="hero__top">
                    <nav class="breadcrumbs" aria-label="Хлебные крошки">
                        <ul class="breadcrumbs__list">
                            <li><a href="<?php echo esc_url(home_url('/')); ?>" class="breadcrumbs__link">Главная</a></li>
                            <li class="breadcrumbs__separator">/</li>
                            <li class="breadcrumbs__current" aria-current="page">Кейсы адвоката</li>
                        </ul>
                    </nav>
                    <h1 class="hero__title hero__title--page">
                        Практические кейсы<br>Ежова&nbsp;Антона Валентиновича
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
                            height="177" alt="Законъ — практические кейсы адвоката Ежова" loading="lazy"
                            decoding="async">
                    </picture>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- cases catalog grid -->
<section class="section cases-page bg-gray">
    <div class="container">
        <div class="cases-page__grid">
            <?php
            $cases_query = new WP_Query([
                'post_type'      => 'cases',
                'posts_per_page' => 12,
                'paged'          => 1,
            ]);

            if ($cases_query->have_posts()) :
                while ($cases_query->have_posts()) : $cases_query->the_post();
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
                <?php endwhile;
                wp_reset_postdata();
            else : ?>
                <p class="empty-message" style="grid-column: 1 / -1; padding: 40px 0; text-align: center; color: #777; font-size: 18px;">
                    Кейсов пока нет.
                </p>
            <?php endif; ?>
        </div>

        <?php if ($cases_query->max_num_pages > 1) : ?>
            <div class="cases-page__more-wrapper">
                <button type="button" class="btn btn--yellow btn--arrow btn--width js-load-more"
                    data-post-type="cases"
                    data-page="1"
                    data-max-pages="<?php echo esc_attr($cases_query->max_num_pages); ?>"
                    data-container=".cases-page__grid">
                    Показать еще
                </button>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
get_template_part('template-parts/section', 'cta');
get_template_part('template-parts/section', 'help');

get_footer();
