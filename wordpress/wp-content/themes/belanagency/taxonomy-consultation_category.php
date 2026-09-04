<?php
/**
 * Taxonomy Consultation Category Template
 * Matches consultation-category.html - Strictly from DB
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
                        <li><a href="<?php echo esc_url(home_url('/consultation/')); ?>" class="breadcrumbs__link">Консультации</a></li>
                        <li class="breadcrumbs__separator">/</li>
                        <li class="breadcrumbs__current" aria-current="page"><?php echo esc_html($current_term->name); ?></li>
                    </ul>
                </nav>
                <h1 class="hero__title"><?php echo esc_html(mb_strtoupper($current_term->name)); ?> — ВОПРОСЫ И ОТВЕТЫ</h1>
                <p class="hero__description">
                    <?php echo esc_html($current_term->description ?: 'Ответы адвоката по теме: ' . $current_term->name . '. Вы можете задать свой вопрос или ознакомиться с опубликованными решениями.'); ?>
                </p>
                <div class="hero__actions">
                    <a href="#ask-question" class="btn btn--primary btn--red btn--arrow">Задать вопрос в эту рубрику</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Consultation Section -->
<section class="section consultation">
    <div class="container">
        <div class="consultation__layout">
            <div class="consultation__main">
                <?php
                if (have_posts()) :
                    $idx = 0;
                    while (have_posts()) : the_post();
                        $idx++;
                        $author = belan_field('consultation_author', get_the_ID(), 'Пользователь');
                        $date   = belan_field('consultation_date', get_the_ID(), get_the_date('d.m.Y'));
                        $question = belan_field('consultation_question', get_the_ID(), get_the_excerpt());
                        ?>
                        <article class="consultation-card">
                            <div class="consultation-card__header">
                                <div class="consultation-card__meta">
                                    <span class="consultation-card__author"><?php echo esc_html($author); ?></span>
                                    <span class="consultation-card__sep">/</span>
                                    <span class="consultation-card__num">Вопрос № <?php echo get_the_ID(); ?></span>
                                    <span class="consultation-card__sep">/</span>
                                    <span class="consultation-card__date"><?php echo esc_html($date); ?></span>
                                </div>
                                <span class="consultation-card__badge"><?php echo esc_html($current_term->name); ?></span>
                            </div>
                            <h3 class="consultation-card__title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            <p class="consultation-card__text">
                                <?php echo esc_html($question); ?>
                            </p>
                            <div class="consultation-card__footer">
                                <div class="consultation-card__responder">
                                    <span>Отвечает</span>
                                    <div class="consultation-card__avatar">
                                        <img src="<?php echo esc_url(belan_asset('img/about.webp')); ?>" alt="Ежов Антон">
                                    </div>
                                    <strong>Ежов Антон</strong>
                                </div>
                                <div class="consultation-card__views">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                    <span><?php echo (18 + $idx * 4); ?></span>
                                </div>
                            </div>
                        </article>
                    <?php endwhile;
                else : ?>
                    <p class="empty-message" style="padding: 40px 0; text-align: center; color: #777; font-size: 18px;">
                        В данной рубрике пока нет опубликованных вопросов. Вы можете задать первый вопрос!
                    </p>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <aside class="consultation__sidebar">
                <div class="consultation-categories">
                    <h3 class="consultation-categories__title">Категории вопросов:</h3>
                    <ul class="consultation-categories__list">
                        <?php
                        $sidebar_terms = get_terms([
                            'taxonomy'   => 'consultation_category',
                            'hide_empty' => false,
                        ]);
                        if (!empty($sidebar_terms) && !is_wp_error($sidebar_terms)) :
                            foreach ($sidebar_terms as $term) :
                                $active = ($term->term_id === $current_term->term_id) ? ' style="font-weight:700; color:#ce494c;"' : '';
                                ?>
                                <li class="consultation-categories__item">
                                    <a href="<?php echo esc_url(get_term_link($term)); ?>" class="consultation-categories__link"<?php echo $active; ?>><?php echo esc_html($term->name); ?></a>
                                </li>
                            <?php endforeach;
                        endif; ?>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</section>

<?php
get_template_part('template-parts/section', 'consultation-form');
get_footer();
