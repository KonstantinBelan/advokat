<?php
/**
 * Hero Section Template Part
 *
 * @package BelanAgency
 */

if (!defined('ABSPATH')) {
    exit;
}

$hero_title = belan_field('hero_title', false, 'Комплексная правовая&nbsp;защита <span>в любых жизненных и&nbsp;деловых ситуациях</span>');
$hero_desc = belan_field('hero_description', false, 'Представляю интересы граждан и&nbsp;бизнеса в&nbsp;судах общей юрисдикции, арбитраже, по&nbsp;уголовным и&nbsp;административным делам.');
$hero_btn_text = belan_field('hero_btn_text', false, 'Получить бесплатную консультацию');
$hero_btn_mobile = belan_field('hero_btn_mobile', false, 'Бесплатная консультация');
$hero_btn_url = belan_field('hero_btn_url', false, '#');
$hero_btn_info = belan_field('hero_btn_info', false, 'От вселения в квартиру без суда до защиты по мошенничеству и корпоративным спорам.');
$hero_name = belan_field('hero_name', false, 'Ежов Антон Валентинович');
$hero_bio = belan_field('hero_bio', false, 'Адвокат широкого профиля c&nbsp;23-летним стажем. Гражданские, жилищные, семейные, уголовные дела. Защита в любых судах до Верховного и&nbsp;Конституционного');
?>
<!-- hero -->
<section class="section hero">
    <div class="container">
        <div class="hero__content">
            <div class="hero__top">
                <h1 class="hero__title">
                    <?php echo wp_kses_post($hero_title); ?>
                </h1>
                <div class="hero__description">
                    <p class="hero__description-text"><?php echo wp_kses_post($hero_desc); ?></p>
                </div>
                <div class="hero__buttons">
                    <a href="<?php echo esc_url($hero_btn_url ?: '#'); ?>" class="btn btn--primary btn--red btn--arrow hero__button">
                        <span class="hero__button-text-desktop"><?php echo esc_html($hero_btn_text); ?></span>
                        <span class="hero__button-text-mobile"><?php echo esc_html($hero_btn_mobile); ?></span>
                    </a>
                    <div class="hero__buttons-info">
                        <p><?php echo esc_html($hero_btn_info); ?></p>
                    </div>
                </div>
            </div>
            <div class="hero__bottom">
                <h2><?php echo esc_html($hero_name); ?></h2>
                <p><?php echo wp_kses_post($hero_bio); ?></p>
            </div>
        </div>
    </div>
</section>
