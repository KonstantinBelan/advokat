<?php
/**
 * Header Template
 *
 * @package BelanAgency
 */

if (!defined('ABSPATH')) {
    exit;
}

$phone = belan_option('site_phone', '8 (993) 909-90-50');
$phone_tel = belan_option('site_phone_tel', belan_phone_clean($phone));
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

    <!-- header -->
    <header>
        <div class="header-container">
            <a class="header-logo" href="<?php echo esc_url(home_url('/')); ?>">
                <p class="header-logo__title">
                    <span class="header-logo__line-text"><?php echo esc_html(belan_option('header_name_line1', 'Ежов Антон')); ?></span>
                    <span class="header-logo__line-text"><?php echo esc_html(belan_option('header_name_line2', 'Валентинович')); ?></span>
                </p>
                <span class="header-logo__line">/</span>
                <p class="header-logo__subtitle"><?php echo esc_html(belan_option('header_subtitle', 'Адвокатский кабинет. Практика с 2002 года.')); ?></p>
            </a>
            <nav class="header-menu">
                <?php
                if (has_nav_menu('primary')) {
                    wp_nav_menu([
                        'theme_location' => 'primary',
                        'container'      => false,
                        'menu_class'     => 'header-menu__list',
                        'walker'         => new Belan_Desktop_Nav_Walker(),
                        'depth'          => 3,
                    ]);
                } else {
                    $hdr_fiz_services = get_posts([
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

                    $hdr_yur_services = get_posts([
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
                    <ul class="header-menu__list">
                        <li class="header-menu__item-has-dropdown">
                            <a class="header-menu__item" href="<?php echo esc_url(home_url('/services/')); ?>">
                                Услуги
                                <svg class="header-menu__arrow" width="10" height="6" viewBox="0 0 10 6" fill="none">
                                    <path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </a>
                            <div class="header-dropdown header-dropdown--services">
                                <ul class="header-dropdown__list">
                                    <li class="header-dropdown__item-has-submenu">
                                        <a href="<?php echo esc_url(home_url('/service-citizens/')); ?>" class="header-dropdown__link">
                                            <span>Услуги для физических лиц</span>
                                            <svg class="header-dropdown__chevron" width="6" height="10" viewBox="0 0 6 10"
                                                fill="none">
                                                <path d="M1 9L5 5L1 1" stroke="currentColor" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </a>
                                        <div class="header-submenu">
                                            <ul class="header-submenu__list">
                                                <?php foreach ($hdr_fiz_services as $srv) : ?>
                                                    <li><a href="<?php echo esc_url(get_permalink($srv->ID)); ?>" class="header-submenu__link"><?php echo esc_html($srv->post_title); ?></a></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </li>
                                    <li class="header-dropdown__item-has-submenu">
                                        <a href="<?php echo esc_url(home_url('/service-legal/')); ?>" class="header-dropdown__link">
                                            <span>Услуги для юридических лиц</span>
                                            <svg class="header-dropdown__chevron" width="6" height="10" viewBox="0 0 6 10"
                                                fill="none">
                                                <path d="M1 9L5 5L1 1" stroke="currentColor" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </a>
                                        <div class="header-submenu">
                                            <ul class="header-submenu__list">
                                                <?php foreach ($hdr_yur_services as $srv) : ?>
                                                    <li><a href="<?php echo esc_url(get_permalink($srv->ID)); ?>" class="header-submenu__link"><?php echo esc_html($srv->post_title); ?></a></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li><a class="header-menu__item" href="<?php echo esc_url(home_url('/#about')); ?>">Об адвокате</a></li>
                        <li><a class="header-menu__item" href="<?php echo esc_url(home_url('/consultation/')); ?>">Вопросы</a></li>
                        <li><a class="header-menu__item" href="<?php echo esc_url(home_url('/cases/')); ?>">Кейсы</a></li>
                        <li class="header-menu__item-has-dropdown">
                            <a class="header-menu__item" href="<?php echo esc_url(home_url('/articles/')); ?>">
                                Статьи
                                <svg class="header-menu__arrow" width="10" height="6" viewBox="0 0 10 6" fill="none">
                                    <path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </a>
                            <div class="header-dropdown header-dropdown--simple">
                                <ul class="header-dropdown__list">
                                    <li><a href="<?php echo esc_url(home_url('/articles/')); ?>" class="header-dropdown__link"><span>Юридические статьи</span></a></li>
                                    <li><a href="<?php echo esc_url(home_url('/news/')); ?>" class="header-dropdown__link"><span>Новости</span></a></li>
                                </ul>
                            </div>
                        </li>
                        <li><a class="header-menu__item" href="<?php echo esc_url(home_url('/reviews/')); ?>">Отзывы</a></li>
                        <li><a class="header-menu__item" href="<?php echo esc_url(home_url('/contacts/')); ?>">Контакты</a></li>
                    </ul>
                    <?php
                }
                ?>
            </nav>
            <div class="header-contacts">
                <a class="header-contacts__phone" href="tel:<?php echo esc_attr($phone_tel); ?>"><?php echo esc_html($phone); ?></a>
                <a class="btn btn--small btn--yellow header-contacts__callback" href="<?php echo esc_url(home_url('/consultation/')); ?>">Задать вопрос онлайн</a>
            </div>
            <div class="header-mobile-actions">
                <a class="header-mobile-phone" href="tel:<?php echo esc_attr($phone_tel); ?>" aria-label="Позвонить">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56a.977.977 0 00-1.01.24l-2.2 2.2a15.053 15.053 0 01-6.59-6.59l2.2-2.21a.96.96 0 00.25-1.01A11.36 11.36 0 018.57 3.9c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1 0 9.39 7.61 17 17 17 .55 0 1-.45 1-1v-3.52c0-.55-.45-1-.99-1z"
                            fill="currentColor" />
                    </svg>
                </a>
                <button class="header-burger" aria-label="Открыть меню" type="button">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
        <div class="header-mobile-dropdown">
            <?php
            if (has_nav_menu('primary')) {
                wp_nav_menu([
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => 'header-mobile-dropdown__list',
                    'walker'         => new Belan_Mobile_Nav_Walker(),
                    'depth'          => 3,
                ]);
            } else {
                $hdr_fiz_services = get_posts([
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

                $hdr_yur_services = get_posts([
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
                <ul class="header-mobile-dropdown__list">
                    <li class="header-mobile-dropdown__group">
                        <div class="header-mobile-dropdown__toggle">
                            <a class="header-mobile-dropdown__item" href="<?php echo esc_url(home_url('/services/')); ?>">Услуги</a>
                            <button type="button" class="header-mobile-dropdown__arrow-btn" aria-label="Развернуть услуги">
                                <svg width="10" height="6" viewBox="0 0 10 6" fill="none">
                                    <path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </button>
                        </div>
                        <div class="header-mobile-dropdown__submenu">
                            <div class="header-mobile-dropdown__category">
                                <a href="<?php echo esc_url(home_url('/service-citizens/')); ?>" class="header-mobile-dropdown__category-title">Услуги для физических лиц:</a>
                                <ul class="header-mobile-dropdown__sublist">
                                    <?php foreach ($hdr_fiz_services as $srv) : ?>
                                        <li><a href="<?php echo esc_url(get_permalink($srv->ID)); ?>" class="header-mobile-dropdown__subitem"><?php echo esc_html($srv->post_title); ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div class="header-mobile-dropdown__category">
                                <a href="<?php echo esc_url(home_url('/service-legal/')); ?>" class="header-mobile-dropdown__category-title">Услуги для юридических лиц:</a>
                                <ul class="header-mobile-dropdown__sublist">
                                    <?php foreach ($hdr_yur_services as $srv) : ?>
                                        <li><a href="<?php echo esc_url(get_permalink($srv->ID)); ?>" class="header-mobile-dropdown__subitem"><?php echo esc_html($srv->post_title); ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li><a class="header-mobile-dropdown__item" href="<?php echo esc_url(home_url('/#about')); ?>">Об адвокате</a></li>
                    <li><a class="header-mobile-dropdown__item" href="<?php echo esc_url(home_url('/consultation/')); ?>">Вопросы</a></li>
                    <li><a class="header-mobile-dropdown__item" href="<?php echo esc_url(home_url('/cases/')); ?>">Кейсы</a></li>
                    <li class="header-mobile-dropdown__group">
                        <div class="header-mobile-dropdown__toggle">
                            <a class="header-mobile-dropdown__item" href="<?php echo esc_url(home_url('/articles/')); ?>">Статьи</a>
                            <button type="button" class="header-mobile-dropdown__arrow-btn" aria-label="Развернуть статьи">
                                <svg width="10" height="6" viewBox="0 0 10 6" fill="none">
                                    <path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </button>
                        </div>
                        <div class="header-mobile-dropdown__submenu">
                            <ul class="header-mobile-dropdown__sublist">
                                <li><a href="<?php echo esc_url(home_url('/articles/')); ?>" class="header-mobile-dropdown__subitem">Юридические статьи</a></li>
                                <li><a href="<?php echo esc_url(home_url('/news/')); ?>" class="header-mobile-dropdown__subitem">Новости</a></li>
                            </ul>
                        </div>
                    </li>
                    <li><a class="header-mobile-dropdown__item" href="<?php echo esc_url(home_url('/reviews/')); ?>">Отзывы</a></li>
                    <li><a class="header-mobile-dropdown__item" href="<?php echo esc_url(home_url('/contacts/')); ?>">Контакты</a></li>
                </ul>
                <?php
            }
            ?>
            <div class="header-mobile-dropdown__contacts">
                <a class="header-mobile-dropdown__phone" href="tel:<?php echo esc_attr($phone_tel); ?>"><?php echo esc_html($phone); ?></a>
                <a class="btn btn--small btn--yellow header-mobile-dropdown__callback" href="<?php echo esc_url(home_url('/consultation/')); ?>">Задать вопрос онлайн</a>
            </div>
        </div>
    </header>

    <main>
