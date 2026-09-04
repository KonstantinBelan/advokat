<?php
/**
 * Block Name: Slider (Слайдер Swiper)
 *
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool $is_preview True during AJAX preview.
 * @param (int|string) $post_id The post ID this block is saved to.
 */

$gallery = get_field('slider_images');

if (empty($gallery) || !is_array($gallery)) {
    if ($is_preview) {
        ?>
        <div style="border: 2px dashed #D1D5DB; border-radius: 16px; padding: 40px 20px; text-align: center; background-color: #F9FAFB; color: #6B7280;">
            <div style="font-size: 32px; margin-bottom: 8px;">🖼️</div>
            <div style="font-weight: 600; font-size: 16px; color: #191726; margin-bottom: 4px;">Слайдер фотографий (Swiper)</div>
            <p style="margin: 0; font-size: 14px;">Нажмите на блок, чтобы выбрать фотографии для слайдера в медиабиблиотеке.</p>
        </div>
        <?php
    }
    return;
}

if ($is_preview && is_admin()) : ?>
    <div style="background: #F4F4F6; border-radius: 16px; padding: 16px; border: 1px solid #E5E7EB;">
        <div style="font-size: 13px; font-weight: 700; color: #191726; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between;">
            <span>Слайдер Swiper (<?php echo count($gallery); ?> фото)</span>
            <span style="background: #CE494C; color: #fff; padding: 2px 8px; border-radius: 12px; font-size: 11px;">Перелистывание на сайте</span>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 10px;">
            <?php foreach ($gallery as $img) :
                $url = is_array($img) ? ($img['sizes']['medium'] ?? $img['url']) : wp_get_attachment_image_url($img, 'medium');
                $alt = is_array($img) ? ($img['alt'] ?? '') : '';
            ?>
                <div style="aspect-ratio: 4/3; border-radius: 8px; overflow: hidden; background: #e5e7eb;">
                    <img src="<?php echo esc_url($url); ?>" alt="<?php echo esc_attr($alt); ?>" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php else : ?>
    <div class="article-detail__slider swiper">
        <div class="swiper-wrapper">
            <?php foreach ($gallery as $img) :
                $url = is_array($img) ? ($img['sizes']['large'] ?? $img['url']) : wp_get_attachment_image_url($img, 'large');
                $alt = is_array($img) ? ($img['alt'] ?? 'Слайд') : 'Слайд';
            ?>
                <div class="swiper-slide">
                    <img src="<?php echo esc_url($url); ?>" alt="<?php echo esc_attr($alt); ?>" loading="lazy" decoding="async">
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="article-slider-btn article-slider-btn--prev" aria-label="Предыдущий слайд">‹</button>
        <button type="button" class="article-slider-btn article-slider-btn--next" aria-label="Следующий слайд">›</button>
        <div class="article-slider-dots swiper-pagination"></div>
    </div>
<?php endif;
