<?php
/**
 * Services Preview Section Template Part
 * Strictly from DB (service CPT posts)
 *
 * @package BelanAgency
 */

if (!defined('ABSPATH')) {
    exit;
}

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
<!-- services -->
<section class="section services">
    <div class="container">
        <div class="section__header">
            <h2 class="section__title section__title--center">Юридические услуги для&nbsp;защиты&nbsp;ваших прав</h2>
        </div>
        <div class="services__wrapper">
            <!-- physical -->
            <div class="services__left">
                <h3 class="services__title">Для физических лиц</h3>
                <ul class="services__pills">
                    <?php if (!empty($citz_services)) :
                        foreach ($citz_services as $srv) : ?>
                            <li>
                                <a href="<?php echo esc_url(get_permalink($srv->ID)); ?>" class="services__pill">
                                    <span class="services__pill-arrow services__pill-arrow--red">↗</span>
                                    <span><?php echo esc_html($srv->post_title); ?></span>
                                </a>
                            </li>
                        <?php endforeach;
                    else : ?>
                        <li style="color: #777; padding: 10px 0;">Услуг для физических лиц пока нет.</li>
                    <?php endif; ?>
                </ul>
            </div>
            <!-- legal -->
            <div class="services__right">
                <div class="services__right-content">
                    <h3 class="services__title services__title--white">Для юридических лиц</h3>
                    <ul class="services__pills services__pills--column">
                        <?php if (!empty($legal_services)) :
                            foreach ($legal_services as $srv) : ?>
                                <li>
                                    <a href="<?php echo esc_url(get_permalink($srv->ID)); ?>" class="services__pill services__pill--large">
                                        <span class="services__pill-arrow services__pill-arrow--yellow">↗</span>
                                        <span><?php echo esc_html($srv->post_title); ?></span>
                                    </a>
                                </li>
                            <?php endforeach;
                        else : ?>
                            <li style="color: #fff; opacity: 0.7; padding: 10px 0;">Услуг для юридических лиц пока нет.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
