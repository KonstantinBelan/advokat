<?php
/**
 * Single News Template
 * Matches news-single.html - Strictly from DB
 *
 * @package BelanAgency
 */

get_header();
?>

<!-- hero -->
<section class="section hero hero--page hero--article">
    <div class="container">
        <div class="hero__content">
            <div class="hero__reviews-row">
                <div class="hero__top">
                    <nav class="breadcrumbs" aria-label="Хлебные крошки">
                        <ul class="breadcrumbs__list">
                            <li><a href="<?php echo esc_url(home_url('/')); ?>" class="breadcrumbs__link">Главная</a></li>
                            <li class="breadcrumbs__separator">/</li>
                            <li><a href="<?php echo esc_url(home_url('/news/')); ?>" class="breadcrumbs__link">Новости</a></li>
                            <li class="breadcrumbs__separator">/</li>
                            <li class="breadcrumbs__current" aria-current="page"><?php the_title(); ?></li>
                        </ul>
                    </nav>
                </div>
                <div class="hero__seal">
                    <picture>
                        <source type="image/avif"
                            srcset="<?php echo esc_url(belan_asset('img/cases-seal-sm.avif')); ?> 320w, <?php echo esc_url(belan_asset('img/cases-seal.avif')); ?> 520w"
                            sizes="(max-width: 768px) 120px, (max-width: 1024px) 150px, 175px">
                        <source type="image/webp"
                            srcset="<?php echo esc_url(belan_asset('img/cases-seal-sm.webp')); ?> 320w, <?php echo esc_url(belan_asset('img/cases-seal.webp')); ?> 520w"
                            sizes="(max-width: 768px) 120px, (max-width: 1024px) 150px, 175px">
                        <img src="<?php echo esc_url(belan_asset('img/cases-seal.png')); ?>"
                            srcset="<?php echo esc_url(belan_asset('img/cases-seal-sm.png')); ?> 320w, <?php echo esc_url(belan_asset('img/cases-seal.png')); ?> 520w"
                            sizes="(max-width: 768px) 120px, (max-width: 1024px) 150px, 175px" width="175"
                            height="135" alt="Законъ" loading="lazy" decoding="async">
                    </picture>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Article Detail -->
<article class="section article-detail">
    <div class="container">
        <div class="article-detail__layout">
            <!-- Left: Main Article Content -->
            <div class="article-detail__main">
                <h1 class="article-detail__title"><?php the_title(); ?></h1>
                <div class="article-detail__date">
                    <time datetime="<?php echo get_the_date('Y-m-d'); ?>"><?php echo get_the_date('j F Y'); ?></time>
                </div>

                <div class="article-detail__image">
                    <?php
                    if (has_post_thumbnail()) {
                        the_post_thumbnail('large');
                    } else {
                        belan_picture('article-1', get_the_title(), '', '(max-width: 768px) 100vw, 770px', 770, 390);
                    }
                    ?>
                </div>

                <div class="article-detail__content-body">
                    <?php
                    if (have_posts()) :
                        while (have_posts()) : the_post();
                            the_content();
                        endwhile;
                    else : ?>
                        <p class="empty-message">Новость не найдена.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right: Sidebar -->
            <aside class="article-sidebar">
                <!-- Widget 1: Consultation -->
                <div class="article-sidebar__consultation">
                    <div class="article-sidebar__consultation-avatar">
                        <picture>
                            <source type="image/avif" srcset="<?php echo esc_url(belan_asset('img/article-author-img-2x.avif')); ?> 2x, <?php echo esc_url(belan_asset('img/article-author-img.avif')); ?> 1x">
                            <source type="image/webp" srcset="<?php echo esc_url(belan_asset('img/article-author-img-2x.webp')); ?> 2x, <?php echo esc_url(belan_asset('img/article-author-img.webp')); ?> 1x">
                            <img src="<?php echo esc_url(belan_asset('img/article-author-img-2x.webp')); ?>" alt="Адвокат Ежов А.В." loading="lazy" decoding="async">
                        </picture>
                    </div>
                    <h3 class="article-sidebar__consultation-title">Есть нестандартный вопрос? Спросите эксперта.</h3>
                    <p class="article-sidebar__consultation-text">
                        Если ваша ситуация не укладывается в стандартные рамки, задайте мне вопрос напрямую. Я лично проанализирую ситуацию и предложу стратегию защиты.
                    </p>
                    <a href="<?php echo esc_url(home_url('/consultation/')); ?>" class="btn btn--primary btn--width btn--red btn--arrow">
                        <span>Задать свой вопрос</span>
                    </a>
                </div>

                <div class="article-sidebar__widget">
                    <h3 class="article-sidebar__widget-title">Статьи из блога:</h3>
                    <ul class="article-sidebar__widget-list">
                        <?php
                        $side_articles = get_posts(['post_type' => 'post', 'numberposts' => 3]);
                        if (!empty($side_articles)) :
                            foreach ($side_articles as $sa) : ?>
                                <li class="article-sidebar__widget-item">
                                    <a href="<?php echo esc_url(get_permalink($sa->ID)); ?>" class="article-sidebar__widget-link">
                                        <span><?php echo esc_html($sa->post_title); ?></span>
                                    </a>
                                </li>
                            <?php endforeach; wp_reset_postdata();
                        else : ?>
                            <li class="article-sidebar__widget-item">Статей пока нет</li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="article-sidebar__widget">
                    <h3 class="article-sidebar__widget-title">Новости:</h3>
                    <ul class="article-sidebar__widget-list">
                        <?php
                        $other_news = get_posts(['post_type' => 'news', 'numberposts' => 3, 'post__not_in' => [get_the_ID()]]);
                        if (!empty($other_news)) :
                            foreach ($other_news as $on) : ?>
                                <li class="article-sidebar__widget-item">
                                    <a href="<?php echo esc_url(get_permalink($on->ID)); ?>" class="article-sidebar__widget-link">
                                        <span><?php echo esc_html($on->post_title); ?></span>
                                    </a>
                                </li>
                            <?php endforeach; wp_reset_postdata();
                        else : ?>
                            <li class="article-sidebar__widget-item">Новостей пока нет</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</article>

<?php
get_template_part('template-parts/section', 'help');
get_footer();
