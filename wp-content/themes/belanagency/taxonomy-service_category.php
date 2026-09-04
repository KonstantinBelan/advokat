<?php
/**
 * Taxonomy Template for Service Categories (Для физических лиц / Для юридических лиц)
 * Displays services belonging to the category, linking directly to service pages.
 *
 * @package BelanAgency
 */

get_header();

$current_term = get_queried_object();
$term_id      = $current_term->term_id;
$term_name    = $current_term->name;
$term_desc    = belan_get_term_desc($current_term);

// Get root services belonging to this category
$services = get_posts([
    'post_type'      => 'service',
    'post_parent'    => 0,
    'posts_per_page' => -1,
    'tax_query'      => [
        [
            'taxonomy' => 'service_category',
            'field'    => 'term_id',
            'terms'    => $term_id,
        ],
    ],
    'orderby'        => 'menu_order ID',
    'order'          => 'ASC',
]);
?>

<!-- hero -->
<section class="section hero hero--page hero--services">
    <div class="container">
        <div class="hero__content">
            <div class="hero__top">
                <nav class="breadcrumbs" aria-label="Хлебные крошки">
                    <ul class="breadcrumbs__list">
                        <li><a href="<?php echo esc_url(home_url('/')); ?>" class="breadcrumbs__link">Главная</a></li>
                        <li class="breadcrumbs__separator">/</li>
                        <li><a href="<?php echo esc_url(home_url('/services/')); ?>" class="breadcrumbs__link">Услуги</a></li>
                        <li class="breadcrumbs__separator">/</li>
                        <li class="breadcrumbs__current" aria-current="page"><?php echo esc_html($term_name); ?></li>
                    </ul>
                </nav>
                <h1 class="hero__title hero__title--page">
                    <?php echo esc_html($term_name); ?>
                </h1>
                <?php if ($term_desc) : ?>
                    <div class="hero__description hero__description--page">
                        <p class="hero__description-text"><?php echo esc_html($term_desc); ?></p>
                    </div>
                <?php endif; ?>
                <div class="hero__buttons">
                    <a href="#cta" class="btn btn--primary btn--red btn--arrow hero__button">
                        <span class="hero__button-text-desktop">Получить бесплатную консультацию</span>
                        <span class="hero__button-text-mobile">Бесплатная консультация</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Grid -->
<section class="section services-catalog">
    <div class="container">
        <div class="section__header">
            <h2 class="section__title"><?php echo esc_html($term_name); ?>:</h2>
        </div>
        <div class="services-catalog__grid">
            <?php if (!empty($services)) :
                foreach ($services as $srv) :
                    $srv_desc = has_excerpt($srv->ID) ? get_the_excerpt($srv->ID) : get_post_meta($srv->ID, 'service_short_desc', true);
                    ?>
                    <div class="services-card services-card--grey">
                        <div class="services-card__header">
                            <div class="services-card__icon">
                                <?php echo belan_service_icon($srv->post_name); ?>
                            </div>
                            <h3 class="services-card__title"><?php echo esc_html($srv->post_title); ?></h3>
                        </div>
                        <?php if ($srv_desc) : ?>
                            <p class="services-card__description"><?php echo esc_html($srv_desc); ?></p>
                        <?php endif; ?>
                        <a href="<?php echo esc_url(get_permalink($srv->ID)); ?>" class="services-card__btn btn--yellow btn--arrow">Подробнее</a>
                    </div>
                <?php endforeach;
            else : ?>
                <p class="empty-message" style="grid-column: 1 / -1; padding: 40px 0; text-align: center; color: #777; font-size: 18px;">
                    Услуг в данной категории пока нет.
                </p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php
get_template_part('template-parts/section', 'advantages');
get_template_part('template-parts/section', 'expertise');
get_template_part('template-parts/section', 'about');
get_template_part('template-parts/section', 'media');
get_template_part('template-parts/section', 'reviews');
get_template_part('template-parts/section', 'cases');
get_template_part('template-parts/section', 'cta');
get_template_part('template-parts/section', 'help');

get_footer();
