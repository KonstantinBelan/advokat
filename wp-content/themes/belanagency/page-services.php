<?php
/**
 * Template Name: Каталог услуг (Services)
 * Matches services.html - Strictly from DB
 *
 * @package BelanAgency
 */

get_header();

$citz_services = get_posts([
    'post_type'      => 'service',
    'post_parent'    => 0,
    'posts_per_page' => -1,
    'tax_query'      => [
        [
            'taxonomy' => 'service_category',
            'field'    => 'slug',
            'terms'    => ['dlya-fizicheskih-lic', 'citizens'],
        ],
    ],
    'orderby'        => 'menu_order ID',
    'order'          => 'ASC',
]);

$legal_services = get_posts([
    'post_type'      => 'service',
    'post_parent'    => 0,
    'posts_per_page' => -1,
    'tax_query'      => [
        [
            'taxonomy' => 'service_category',
            'field'    => 'slug',
            'terms'    => ['dlya-yuridicheskih-lic', 'legal'],
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
                        <li class="breadcrumbs__current" aria-current="page">Услуги</li>
                    </ul>
                </nav>
                <h1 class="hero__title hero__title--page">
                    Оказание юридической помощи адвоката
                </h1>
                <div class="hero__description hero__description--page">
                    <p class="hero__description-text">От адвоката широкого профиля с&nbsp;23-летним стажем. Гражданские, жилищные, семейные, уголовные дела. Защита в&nbsp;любых судах до Верховного и&nbsp;Конституционного</p>
                </div>
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

<!-- services for individuals -->
<section class="section services-catalog bg-gray">
    <div class="container">
        <div class="section__header">
            <h2 class="section__title">Услуги для физических&nbsp;лиц:</h2>
        </div>
        <div class="services-catalog__grid">
            <?php if (!empty($citz_services)) :
                foreach ($citz_services as $srv) :
                    $srv_desc = has_excerpt($srv->ID) ? get_the_excerpt($srv->ID) : get_post_meta($srv->ID, 'service_short_desc', true);
                    ?>
                    <div class="services-card">
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
                    Услуг для физических лиц пока нет.
                </p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- services for businesses -->
<section class="section services-catalog">
    <div class="container">
        <div class="section__header">
            <h2 class="section__title">Услуги для юридических&nbsp;лиц:</h2>
        </div>
        <div class="services-catalog__grid">
            <?php if (!empty($legal_services)) :
                foreach ($legal_services as $srv) :
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
                    Услуг для юридических лиц пока нет.
                </p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php
// Reused sections from template-parts:
get_template_part('template-parts/section', 'advantages');
get_template_part('template-parts/section', 'expertise');
get_template_part('template-parts/section', 'about');
get_template_part('template-parts/section', 'media');
get_template_part('template-parts/section', 'reviews');
get_template_part('template-parts/section', 'cases');
get_template_part('template-parts/section', 'cta');
get_template_part('template-parts/section', 'help');

get_footer();
