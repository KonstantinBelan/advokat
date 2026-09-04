<?php
/**
 * Single Service Template
 * Matches service-item.html - Strictly from DB
 *
 * @package BelanAgency
 */

get_header();

$current_id = get_the_ID();
$title      = get_the_title();
$excerpt    = has_excerpt() ? get_the_excerpt() : belan_field('service_short_desc', $current_id);
$content    = get_the_content();
$parent_id  = wp_get_post_parent_id($current_id);

// Category of the service
$terms = get_the_terms($current_id, 'service_category');
$cat   = (!empty($terms) && !is_wp_error($terms)) ? $terms[0] : null;

// Child subservices (if this is a parent service)
$subservices = get_posts([
    'post_type'   => 'service',
    'post_parent' => $current_id,
    'numberposts' => -1,
    'post_status' => 'publish',
    'orderby'     => 'menu_order ID',
    'order'       => 'ASC',
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
                        <?php if ($cat) :
                            $cat_url = ($cat->slug === 'dlya-fizicheskih-lic') ? home_url('/service-citizens/') : home_url('/service-legal/');
                            ?>
                            <li class="breadcrumbs__separator">/</li>
                            <li><a href="<?php echo esc_url($cat_url); ?>" class="breadcrumbs__link"><?php echo esc_html($cat->name); ?></a></li>
                        <?php endif; ?>
                        <?php if ($parent_id) :
                            $parent_post = get_post($parent_id);
                            if ($parent_post) : ?>
                                <li class="breadcrumbs__separator">/</li>
                                <li><a href="<?php echo esc_url(get_permalink($parent_post->ID)); ?>" class="breadcrumbs__link"><?php echo esc_html($parent_post->post_title); ?></a></li>
                            <?php endif;
                        endif; ?>
                        <li class="breadcrumbs__separator">/</li>
                        <li class="breadcrumbs__current" aria-current="page"><?php echo esc_html($title); ?></li>
                    </ul>
                </nav>
                <h1 class="hero__title hero__title--page">
                    <?php echo esc_html($title); ?>
                </h1>
                <?php if ($excerpt) : ?>
                    <div class="hero__description hero__description--page">
                        <p class="hero__description-text"><?php echo esc_html($excerpt); ?></p>
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

<?php if (!empty($subservices)) : ?>
    <!-- subservices catalog grid -->
    <section class="section services-catalog bg-gray">
        <div class="container">
            <div class="services-catalog__grid">
                <?php foreach ($subservices as $sub) :
                    $sub_desc = has_excerpt($sub->ID) ? get_the_excerpt($sub->ID) : get_post_meta($sub->ID, 'service_short_desc', true);
                    ?>
                    <div class="services-card">
                        <div class="services-card__header">
                            <div class="services-card__icon">
                                <?php echo belan_service_icon($sub->post_name); ?>
                            </div>
                            <h3 class="services-card__title"><?php echo esc_html($sub->post_title); ?></h3>
                        </div>
                        <?php if ($sub_desc) : ?>
                            <p class="services-card__description"><?php echo esc_html($sub_desc); ?></p>
                        <?php endif; ?>
                        <a href="<?php echo esc_url(get_permalink($sub->ID)); ?>" class="services-card__btn btn--yellow btn--arrow">Подробнее</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- service detail resolution block -->
<?php
$res_title  = belan_field('service_custom_title', $current_id);
if (!$res_title) {
    $res_title = 'Разрешаю любые конфликты, связанные с&nbsp;направлением «' . esc_html($title) . '»';
}

$res_list   = belan_field('service_custom_list', $current_id);
if (empty($res_list)) {
    $res_list = belan_option('global_service_detail_list');
}

$res_notice = belan_field('service_custom_notice', $current_id);
if (!$res_notice) {
    $res_notice = belan_option('global_service_detail_notice', 'Большинство дел закрываю на досудебной стадии — быстро и без лишних затрат. Если суд неизбежен, веду его «под ключ» до фактического исполнения, включая работу с приставами.');
}

$res_img    = belan_field('service_custom_image', $current_id);
if (!$res_img) {
    $res_img = belan_option('global_service_detail_image');
}
?>
<section class="section service-detail">
    <div class="container">
        <div class="service-detail__wrapper">
            <div class="service-detail__content">
                <h2 class="service-detail__title">
                    <?php echo wp_kses_post($res_title); ?>
                </h2>
                <div class="service-detail__lead">
                    <?php
                    if (!empty($content)) {
                        echo wp_kses_post(wpautop($content));
                    } else {
                        echo '<p>' . esc_html($excerpt ?: 'Защищаю права и законные интересы доверителей. Беру на себя весь процесс: от правового анализа документов до фактического получения положительного решения суда или досудебного урегулирования.') . '</p>';
                    }
                    ?>
                </div>
                <ul class="service-detail__list">
                    <?php if (!empty($res_list) && is_array($res_list)) :
                        foreach ($res_list as $item) :
                            $item_txt = is_array($item) ? ($item['item_text'] ?? '') : $item;
                            if ($item_txt) : ?>
                                <li class="service-detail__item"><?php echo esc_html($item_txt); ?></li>
                            <?php endif;
                        endforeach;
                    else : ?>
                        <li class="service-detail__item">Комплексный правовой анализ ситуации и выработка выигрышной стратегии</li>
                        <li class="service-detail__item">Досудебное урегулирование, подготовка претензий и процессуальных документов</li>
                        <li class="service-detail__item">Личное представительство и защита интересов во всех судебных инстанциях</li>
                        <li class="service-detail__item">Контроль реального исполнения судебного акта в службе судебных приставов</li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="service-detail__image">
                <?php
                if ($res_img) {
                    belan_picture($res_img, $title, '', '(max-width: 768px) 100vw, 576px', 576, 400);
                } else {
                    belan_picture('expertise-1', $title, '', '(max-width: 768px) 100vw, 576px', 576, 400);
                }
                ?>
            </div>
        </div>
        <?php if ($res_notice) : ?>
            <div class="service-detail__notice">
                <div class="service-detail__notice-icon">i</div>
                <p class="service-detail__notice-text">
                    <?php echo esc_html($res_notice); ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
get_template_part('template-parts/section', 'advantages');
if (!$parent_id) {
    get_template_part('template-parts/section', 'expertise');
    get_template_part('template-parts/section', 'about');
    get_template_part('template-parts/section', 'media');
}
get_template_part('template-parts/section', 'reviews');
get_template_part('template-parts/section', 'cases');
get_template_part('template-parts/section', 'cta');
get_template_part('template-parts/section', 'help');

get_footer();
