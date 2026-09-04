<?php
/**
 * CTA Form Section Template Part
 *
 * @package BelanAgency
 */

if (!defined('ABSPATH')) {
    exit;
}

$cta_title = belan_option('cta_title', 'Сложная ситуация требует немедленного решения');
$cta_desc = belan_option('cta_subtitle', 'В делах, связанных с уголовным преследованием, корпоративными или жилищными спорами, промедление может обернуться лишением свободы или имущества.<br><br>Обратитесь к адвокату с 20-летним стажем прямо сейчас, опишите свою ситуацию, и я лично подберу для вас оптимальное решение на первой бесплатной консультации.');
?>
<!-- cta -->
<section class="section cta" id="cta">
    <div class="container">
        <div class="cta__wrapper">
            <div class="cta__grid">
                <div class="cta__content">
                    <h2 class="cta__title"><?php echo wp_kses_post($cta_title); ?></h2>
                    <div class="cta__description">
                        <p><?php echo wp_kses_post($cta_desc); ?></p>
                    </div>
                    <div class="cta__badge-wrapper">
                        <div class="cta__guarantee">
                            <div class="cta__guarantee-icon">
                                <picture>
                                    <source type="image/avif"
                                        srcset="<?php echo esc_url(belan_asset('img/hero-img-sm.avif')); ?> 56w, <?php echo esc_url(belan_asset('img/hero-img.avif')); ?> 82w, <?php echo esc_url(belan_asset('img/hero-img-2x.avif')); ?> 164w"
                                        sizes="(max-width: 768px) 56px, 82px">
                                    <source type="image/webp"
                                        srcset="<?php echo esc_url(belan_asset('img/hero-img-sm.webp')); ?> 56w, <?php echo esc_url(belan_asset('img/hero-img.webp')); ?> 82w, <?php echo esc_url(belan_asset('img/hero-img-2x.webp')); ?> 164w"
                                        sizes="(max-width: 768px) 56px, 82px">
                                    <img src="<?php echo esc_url(belan_asset('img/hero-img.png')); ?>"
                                        srcset="<?php echo esc_url(belan_asset('img/hero-img-sm.png')); ?> 56w, <?php echo esc_url(belan_asset('img/hero-img.png')); ?> 82w, <?php echo esc_url(belan_asset('img/hero-img-2x.png')); ?> 164w"
                                        sizes="(max-width: 768px) 56px, 82px" width="82" height="82"
                                        alt="Гарантия конфиденциальности" loading="lazy" decoding="async">
                                </picture>
                            </div>
                            <p class="cta__guarantee-text">Расскажу реальные шансы и&nbsp;план действий за&nbsp;15 минут.</p>
                        </div>
                        <svg class="cta__arrow-svg" viewBox="0 0 80 70" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 60 C 25 35, 45 45, 65 15" stroke="white" stroke-width="1.8" stroke-linecap="round" fill="none" />
                            <path d="M52 16 L65 15 L64 28" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" fill="none" />
                        </svg>
                    </div>
                </div>
                <div class="cta__form-card">
                    <form action="#" method="POST" class="belan-lead-form" data-form-id="cta_section">
                        <div class="cta__form-card-row">
                            <input type="text" name="name" placeholder="Ваше имя" required>
                            <input type="tel" name="phone" placeholder="Номер телефона" required>
                        </div>
                        <div class="cta__form-card-select">
                            <select name="service">
                                <option value="" <?php echo !is_singular('service') ? 'selected' : ''; ?> disabled>Услуга</option>
                                <?php
                                $current_service_title = is_singular('service') ? get_the_title() : '';
                                $services_cats = get_terms([
                                    'taxonomy'   => 'service_category',
                                    'hide_empty' => true,
                                    'parent'     => 0,
                                    'orderby'    => 'term_id',
                                    'order'      => 'ASC',
                                ]);

                                $used_ids = [];
                                if (!empty($services_cats) && !is_wp_error($services_cats)) {
                                    foreach ($services_cats as $cat) {
                                        $srv_items = get_posts([
                                            'post_type'        => 'service',
                                            'posts_per_page'   => -1,
                                            'tax_query'        => [
                                                [
                                                    'taxonomy'         => 'service_category',
                                                    'field'            => 'term_id',
                                                    'terms'            => $cat->term_id,
                                                    'include_children' => true,
                                                ],
                                            ],
                                            'orderby'          => 'menu_order title',
                                            'order'            => 'ASC',
                                        ]);
                                        if (!empty($srv_items)) {
                                            echo '<optgroup label="' . esc_attr($cat->name) . '">';
                                            foreach ($srv_items as $srv) {
                                                $used_ids[] = $srv->ID;
                                                $is_selected = ($current_service_title === $srv->post_title) ? ' selected' : '';
                                                echo '<option value="' . esc_attr($srv->post_title) . '"' . $is_selected . '>' . esc_html($srv->post_title) . '</option>';
                                            }
                                            echo '</optgroup>';
                                        }
                                    }
                                }

                                $other_services = get_posts([
                                    'post_type'      => 'service',
                                    'posts_per_page' => -1,
                                    'post__not_in'   => !empty($used_ids) ? $used_ids : [0],
                                    'orderby'        => 'menu_order title',
                                    'order'          => 'ASC',
                                ]);
                                if (!empty($other_services)) {
                                    if (!empty($used_ids)) {
                                        echo '<optgroup label="Другие услуги">';
                                    }
                                    foreach ($other_services as $srv) {
                                        $is_selected = ($current_service_title === $srv->post_title) ? ' selected' : '';
                                        echo '<option value="' . esc_attr($srv->post_title) . '"' . $is_selected . '>' . esc_html($srv->post_title) . '</option>';
                                    }
                                    if (!empty($used_ids)) {
                                        echo '</optgroup>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="cta__form-card-textarea">
                            <textarea name="message" placeholder="Кратко опишите проблему"></textarea>
                        </div>
                        <div class="cta__form-card-method">
                            <span class="cta__form-card-method__title">Способ связи:</span>
                            <div class="cta__form-card-method__group">
                                <label class="cta__form-card-method__label cta__form-card-method__label--active">
                                    <input type="radio" name="method" value="Почта" checked>
                                    Почта
                                </label>
                                <label class="cta__form-card-method__label">
                                    <input type="radio" name="method" value="Звонок">
                                    Звонок
                                </label>
                                <label class="cta__form-card-method__label">
                                    <input type="radio" name="method" value="МАКС">
                                    МАКС
                                </label>
                            </div>
                        </div>
                        <div class="cta__form-card-submit">
                            <button type="submit" class="btn btn--primary btn--width btn--red btn--arrow">Получить план действий</button>
                        </div>
                        <div class="form-feedback" style="display:none; margin-top:10px; font-size:14px; text-align:center;"></div>
                        <p class="cta__form-card-note">Гарантируем полную конфиденциальность.</p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
