<?php
/**
 * Block Name: Image with Caption (Изображение с поясняющей подписью)
 *
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool $is_preview True during AJAX preview.
 * @param (int|string) $post_id The post ID this block is saved to.
 */

$image   = get_field('image');
$caption = get_field('caption');

$img_url = '';
$img_alt = 'Изображение';

if (!empty($image)) {
    if (is_array($image)) {
        $img_url = $image['sizes']['large'] ?? $image['url'];
        $img_alt = $image['alt'] ?: 'Изображение';
    } elseif (is_numeric($image)) {
        $img_url = wp_get_attachment_image_url($image, 'large');
        $img_alt = get_post_meta($image, '_wp_attachment_image_alt', true) ?: 'Изображение';
    } else {
        $img_url = $image;
    }
}

if (empty($img_url)) {
    if ($is_preview) {
        ?>
        <div style="border: 2px dashed #D1D5DB; border-radius: 16px; padding: 40px 20px; text-align: center; background-color: #F9FAFB; color: #6B7280;">
            <div style="font-size: 32px; margin-bottom: 8px;">📷</div>
            <div style="font-weight: 600; font-size: 16px; color: #191726; margin-bottom: 4px;">Изображение с подписью</div>
            <p style="margin: 0; font-size: 14px;">Выберите изображение и введите поясняющий текст.</p>
        </div>
        <?php
    }
    return;
}
?>
<div class="article-detail__image article-detail__image--with-caption">
    <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($img_alt); ?>" loading="lazy" decoding="async">
</div>
<?php if (!empty($caption)) : ?>
    <div class="article-detail__caption">
        <?php echo esc_html($caption); ?>
    </div>
<?php endif;
