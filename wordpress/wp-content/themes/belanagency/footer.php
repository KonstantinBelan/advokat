<?php
/**
 * Footer Template
 * Matches index.html lines 1341-1390
 *
 * @package BelanAgency
 */

if (!defined('ABSPATH')) {
    exit;
}

$phone       = belan_option('phone_number', '8 (993) 909-90-50');
$phone_clean = belan_phone_clean($phone);
$privacy_url = belan_option('site_privacy_url', '#');
$max_url     = belan_option('site_max_url', '#');
$dev_url     = belan_option('site_dev_url', 'https://belanagency.ru');
?>
    </main>

    <footer>
        <div class="footer__card">
            <div class="footer__logo">
                <p class="footer__logo__title">
                    <span class="footer__logo__line-text">Ежов Антон</span>
                    <span class="footer__logo__line-text">Валентинович</span>
                </p>
                <span class="footer__logo__line">/</span>
                <p class="footer__logo__subtitle">Адвокатский кабинет. Практика с&nbsp;2002 года.</p>
            </div>
            <nav class="footer__menu">
                <?php
                if (has_nav_menu('footer')) {
                    wp_nav_menu([
                        'theme_location' => 'footer',
                        'container'      => false,
                        'menu_class'     => 'footer__menu__list',
                        'walker'         => new Belan_Footer_Nav_Walker(),
                        'depth'          => 1,
                    ]);
                } else {
                    ?>
                    <ul class="footer__menu__list">
                        <li><a class="footer__menu__item" href="<?php echo esc_url(home_url('/services/')); ?>">Услуги</a></li>
                        <li><a class="footer__menu__item" href="<?php echo esc_url(home_url('/#about')); ?>">Об адвокате</a></li>
                        <li><a class="footer__menu__item" href="<?php echo esc_url(home_url('/consultation/')); ?>">Вопросы</a></li>
                        <li><a class="footer__menu__item" href="<?php echo esc_url(home_url('/cases/')); ?>">Кейсы</a></li>
                        <li><a class="footer__menu__item" href="<?php echo esc_url(home_url('/articles/')); ?>">Статьи</a></li>
                        <li><a class="footer__menu__item" href="<?php echo esc_url(home_url('/reviews/')); ?>">Отзывы</a></li>
                        <li><a class="footer__menu__item" href="<?php echo esc_url(home_url('/contacts/')); ?>">Контакты</a></li>
                    </ul>
                    <?php
                }
                ?>
            </nav>
            <div class="footer__contacts">
                <a class="footer__contacts-phone" href="tel:<?php echo esc_attr($phone_clean); ?>"><?php echo esc_html($phone); ?></a>
                <a class="footer__contacts-btn" href="<?php echo esc_url(home_url('/consultation/')); ?>">Задать вопрос</a>
            </div>
        </div>
        <div class="container">
            <div class="footer__bottom">
                <div class="footer__bottom-left">
                    <span>© 2026. Все права защищены.</span>
                    <a href="<?php echo esc_url($privacy_url); ?>" class="footer__link" target="_blank">Политика конфиденциальности</a>
                </div>
                <div class="footer__bottom-center">
                    <a href="<?php echo esc_url($max_url); ?>" class="footer__messenger" target="_blank">
                        <span class="footer__messenger__icon">
                            <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M10.1642 0C15.6768 0 20 4.47337 20 9.98999C20 15.5066 15.5406 19.8698 10.2163 19.8698C8.32799 19.8698 7.41089 19.6035 5.93712 18.5603C5.837 18.4882 5.69684 18.5082 5.61073 18.5983C4.47737 19.8078 1.57189 20.6568 1.43973 19.0048C1.43973 16.1254 0 14.2531 0 9.93392C0 4.26512 4.64958 0 10.1642 0ZM10.3184 4.9159C7.70124 4.77974 5.66079 6.59592 5.21025 9.43732C4.83781 11.7901 5.4986 14.6576 6.06328 14.8038C6.30356 14.8638 6.88026 14.4233 7.30076 14.0268C7.38086 13.9527 7.499 13.9387 7.59111 13.9968C8.2459 14.3973 8.98678 14.6976 9.80377 14.7397C12.491 14.8799 14.8698 12.7773 15.01 10.0921C15.1522 7.40489 13.0056 5.05807 10.3184 4.9159Z" />
                            </svg>
                        </span>
                        <span>Написать в МАКС</span>
                    </a>
                </div>
                <div class="footer__bottom-right">
                    <a href="<?php echo esc_url($dev_url); ?>" class="footer__link" target="_blank">Разработка сайта Belan Agency</a>
                </div>
            </div>
        </div>
    </footer>

    <?php wp_footer(); ?>
</body>

</html>

