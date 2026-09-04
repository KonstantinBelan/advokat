<?php
/**
 * Single Post Template (Blog / Article)
 * Matches article.html
 *
 * @package BelanAgency
 */

get_header();

$cats = get_the_category();
$cat_name = !empty($cats) ? $cats[0]->name : 'Недвижимость';
$cat_link = !empty($cats) ? get_category_link($cats[0]->term_id) : '#';
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
                            <li><a href="<?php echo esc_url(home_url('/articles/')); ?>" class="breadcrumbs__link">Юридические статьи</a></li>
                            <li class="breadcrumbs__separator">/</li>
                            <li class="breadcrumbs__current" aria-current="page"><?php echo esc_html($cat_name); ?></li>
                        </ul>
                    </nav>
                </div>
                <div class="hero__seal">
                    <?php belan_picture('cases-seal', 'Законъ', '', '(max-width: 768px) 120px, (max-width: 1024px) 150px, 175px', 175, 135); ?>
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
                <a href="<?php echo esc_url($cat_link); ?>" class="article-detail__category"><?php echo esc_html($cat_name); ?></a>
                <h1 class="article-detail__title"><?php the_title(); ?></h1>

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
                    endif;
                    ?>
                </div>

                <!-- Author block -->
                <?php
                $author_id    = get_the_author_meta('ID');
                $author_photo = function_exists('get_field') ? get_field('author_photo', 'user_' . $author_id) : '';
                if (!$author_photo) {
                    $author_photo = belan_asset('img/about.webp');
                }

                $author_name = function_exists('get_field') ? get_field('author_full_name', 'user_' . $author_id) : '';
                if (!$author_name) {
                    $author_name = get_the_author_meta('display_name', $author_id) ?: 'Ежов Антон Валентинович';
                }

                $author_desc = function_exists('get_field') ? get_field('author_credentials', 'user_' . $author_id) : '';
                if (!$author_desc) {
                    $author_desc = get_the_author_meta('description', $author_id) ?: 'Адвокат с 23-летним стажем, регистрационный № 77/10522 в реестре адвокатов г. Москвы.';
                }
                ?>
                <div class="article-detail__author-box">
                    <div class="article-detail__author-avatar">
                        <img src="<?php echo esc_url($author_photo); ?>" alt="<?php echo esc_attr($author_name); ?>" loading="lazy" decoding="async">
                    </div>
                    <div class="article-detail__author-info">
                        <h4 class="article-detail__author-name">Автор статьи: <?php echo esc_html($author_name); ?></h4>
                        <p class="article-detail__author-desc"><?php echo esc_html($author_desc); ?></p>
                    </div>
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

                <!-- Widget 2: Categories -->
                <div class="article-sidebar__widget">
                    <h3 class="article-sidebar__widget-title">Категории блога:</h3>
                    <?php
                    $all_cats = get_categories(['hide_empty' => false]);
                    if (!empty($all_cats)) : ?>
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
                                    <div class="article-sidebar__widget-tags">
                                        <?php foreach ($all_cats as $c) :
                                            if ($c->slug === 'uncategorized') continue;
                                            $is_active = in_category($c->term_id) ? ' blog-tags__item--active' : '';
                                            ?>
                                            <a href="<?php echo esc_url(get_category_link($c->term_id)); ?>" class="blog-tags__item<?php echo $is_active; ?>"><?php echo esc_html($c->name); ?></a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Widget 3: Articles -->
                <div class="article-sidebar__widget">
                    <h3 class="article-sidebar__widget-title">Статьи из блога:</h3>
                    <ul class="article-sidebar__widget-list">
                        <?php
                        $recent_posts = get_posts(['numberposts' => 4, 'post__not_in' => [get_the_ID()]]);
                        foreach ($recent_posts as $rp) : ?>
                            <li class="article-sidebar__widget-item">
                                <a href="<?php echo esc_url(get_permalink($rp->ID)); ?>" class="article-sidebar__widget-link">
                                    <span><?php echo esc_html($rp->post_title); ?></span>
                                </a>
                            </li>
                        <?php endforeach; wp_reset_postdata(); ?>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</article>

<?php
get_template_part('template-parts/section', 'articles');
get_template_part('template-parts/section', 'cta');

get_footer();
