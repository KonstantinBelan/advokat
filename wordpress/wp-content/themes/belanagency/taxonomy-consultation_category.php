<?php
/**
 * Taxonomy Consultation Category Template
 * Comprehensive Legal Q&A Platform: Category Page
 *
 * @package BelanAgency
 */

get_header();
$current_term = get_queried_object();
?>

<!-- hero -->
<section class="section hero hero--page hero--consultation">
    <div class="container">
        <div class="hero__content">
            <div class="hero__top">
                <nav class="breadcrumbs" aria-label="Хлебные крошки">
                    <ul class="breadcrumbs__list">
                        <li><a href="<?php echo esc_url(home_url('/')); ?>" class="breadcrumbs__link">Главная</a></li>
                        <li class="breadcrumbs__separator">/</li>
                        <li><a href="<?php echo esc_url(home_url('/consultation/')); ?>" class="breadcrumbs__link">Вопросы адвокату</a></li>
                        <li class="breadcrumbs__separator">/</li>
                        <li class="breadcrumbs__current" aria-current="page"><?php echo esc_html($current_term->name); ?></li>
                    </ul>
                </nav>
                <h1 class="hero__title"><?php echo esc_html(mb_strtoupper($current_term->name)); ?> — ВОПРОСЫ И ОТВЕТЫ</h1>
                <p class="hero__description">
                    <?php echo esc_html($current_term->description ?: 'Ответы практикующих адвокатов по отрасли: ' . $current_term->name . '. Вы можете задать свой вопрос или изучить опубликованные решения правовых ситуаций.'); ?>
                </p>
                <div class="hero__actions">
                    <a href="#ask-question" class="btn btn--primary btn--red btn--arrow">Задать вопрос в эту рубрику</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Consultation Section -->
<section class="section consultation consultation--platform">
    <div class="container">
        <div class="consultation__layout">
            <div class="consultation__main">
                <div class="consultation__main qa-questions-feed">
                    <?php
                    if (have_posts()) :
                        while (have_posts()) : the_post();
                            belan_render_consultation_card(get_the_ID());
                        endwhile;
                    else : ?>
                        <div class="qa-empty-feed-card">
                            <h3>В этой рубрике пока нет опубликованных вопросов</h3>
                            <p>Вы можете задать первый вопрос по теме «<?php echo esc_html($current_term->name); ?>» с помощью формы ниже.</p>
                            <a href="#ask-question" class="btn btn--primary btn--yellow">
                                Задать вопрос адвокату
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <?php
                global $wp_query;
                if ($wp_query->max_num_pages > 1) : ?>
                    <div class="consultation__pagination">
                        <button type="button" class="btn btn--width btn--outline-more js-load-more"
                            data-post-type="consultation"
                            data-category="<?php echo esc_attr($current_term->term_id); ?>"
                            data-page="1"
                            data-max-pages="<?php echo esc_attr($wp_query->max_num_pages); ?>"
                            data-container=".consultation__main .qa-questions-feed">
                            Показать еще вопросы <span class="btn--outline-more__arrow">▼</span>
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right: Sidebar -->
            <aside class="consultation__sidebar">
                <div class="consultation-sidebar-cta">
                    <h3 class="consultation-sidebar-cta__title">Вопрос по теме «<?php echo esc_html($current_term->name); ?>»?</h3>
                    <p class="consultation-sidebar-cta__text">
                        Оставьте обращение прямо сейчас — адвокат проанализирует вашу ситуацию и подготовит правовое решение.
                    </p>
                    <a href="#ask-question" class="btn btn--primary btn--yellow btn--width btn--arrow">
                        Задать свой вопрос
                    </a>
                </div>

                <!-- Categories Widget -->
                <div class="article-sidebar__widget qa-categories-widget">
                    <h3 class="article-sidebar__widget-title">Все рубрики вопросов:</h3>
                    <ul class="article-sidebar__widget-list qa-cat-list">
                        <?php
                        $all_cats = get_terms([
                            'taxonomy'   => 'consultation_category',
                            'hide_empty' => false,
                        ]);
                        if (!empty($all_cats) && !is_wp_error($all_cats)) :
                            foreach ($all_cats as $cat) :
                                $is_active = ($cat->term_id === $current_term->term_id);
                                ?>
                                <li class="article-sidebar__widget-item <?php echo $is_active ? 'qa-cat-item--active' : ''; ?>">
                                    <a href="<?php echo esc_url(get_term_link($cat)); ?>" class="article-sidebar__widget-link qa-cat-link">
                                        <span><?php echo esc_html($cat->name); ?></span>
                                        <span class="qa-cat-count"><?php echo esc_html($cat->count); ?></span>
                                    </a>
                                </li>
                            <?php endforeach;
                        endif; ?>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</section>

<!-- Section: Форма «Задать вопрос адвокату» -->
<?php
get_template_part('template-parts/section', 'consultation-form');
?>

<?php
get_footer();
