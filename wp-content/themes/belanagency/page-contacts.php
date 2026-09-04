<?php
/**
 * Template Name: Контакты (Contacts Page)
 * Matches contacts.html
 *
 * @package BelanAgency
 */

get_header();

$phone = belan_option('phone_number', '8 (993) 909-90-50');
$email = belan_option('email_address', 'email@email.ru');
$address = belan_option('address_text', 'Москва, Н.Арбат, д.21, стр.1, ком.909');
$schedule = belan_option('working_hours', 'Пн-Пт 9:00-18:00');
$map_iframe = belan_option('map_iframe_url', 'https://yandex.ru/map-widget/v1/?ll=37.587847%2C55.752395&z=16&pt=37.587847,55.752395,pm2rdm~37.587847,55.752395,pm2blm');
?>

<!-- hero -->
<section class="section hero hero--page hero--contacts">
    <div class="container">
        <div class="hero__content">
            <div class="hero__reviews-row">
                <div class="hero__top">
                    <nav class="breadcrumbs" aria-label="Хлебные крошки">
                        <ul class="breadcrumbs__list">
                            <li><a href="<?php echo esc_url(home_url('/')); ?>" class="breadcrumbs__link">Главная</a></li>
                            <li class="breadcrumbs__separator">/</li>
                            <li class="breadcrumbs__current" aria-current="page">Контакты</li>
                        </ul>
                    </nav>
                    <h1 class="hero__title hero__title--page">Контакты</h1>
                </div>
                <div class="hero__reviews-phone">
                    <?php belan_picture('reviews-phone', 'Контакты адвоката Ежова А.В.', '', '(max-width: 768px) 0vw, (max-width: 1024px) 200px, 260px', 260, 214); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- contacts section -->
<section class="section contacts-page">
    <div class="container">
        <div class="contacts-page__wrapper">
            <!-- Left card -->
            <div class="contacts-page__card">
                <!-- 01 Phone -->
                <div class="contacts-page__item">
                    <p class="contacts-page__label">Телефон для связи:</p>
                    <a href="tel:<?php echo esc_attr(belan_phone_clean($phone)); ?>" class="contacts-page__link contacts-page__link--lg"><?php echo esc_html($phone); ?></a>
                </div>
                <!-- 02 Email -->
                <div class="contacts-page__item">
                    <p class="contacts-page__label">E-mail:</p>
                    <a href="mailto:<?php echo esc_attr($email); ?>" class="contacts-page__link contacts-page__link--lg"><?php echo esc_html($email); ?></a>
                </div>
                <!-- 03 Address -->
                <div class="contacts-page__item">
                    <p class="contacts-page__label">Адрес:</p>
                    <p class="contacts-page__text"><?php echo esc_html($address); ?></p>
                </div>
                <!-- 04 Schedule -->
                <div class="contacts-page__item">
                    <p class="contacts-page__label">Время работы:</p>
                    <p class="contacts-page__text"><?php echo esc_html($schedule); ?></p>
                </div>
                <!-- 05 Form -->
                <div class="contacts-page__form-block" id="consultation-form">
                    <p class="contacts-page__label">Получить бесплатную консультацию:</p>
                    <form class="contacts-page__form belan-lead-form" action="#" method="POST" data-form-id="contacts_form">
                        <input type="tel" name="phone" placeholder="+ 7 000 000 00 00" required autocomplete="tel">
                        <button type="submit" class="btn btn--primary btn--red btn--arrow btn--width">
                            Оставить заявку
                        </button>
                        <div class="form-feedback" style="display:none; margin-top:10px; font-size:14px; text-align:center;"></div>
                    </form>
                </div>
            </div>

            <!-- Right Map -->
            <div class="contacts-page__map">
                <iframe src="<?php echo esc_url($map_iframe); ?>" width="100%" height="100%" allowfullscreen loading="lazy" title="Адвокатский кабинет Ежова А.В. на карте Москвы"></iframe>
            </div>
        </div>
    </div>
</section>

<?php
get_template_part('template-parts/section', 'help');
get_footer();
