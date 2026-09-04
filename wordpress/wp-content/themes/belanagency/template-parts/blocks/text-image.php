<?php
/**
 * Block Name: Text with Image (Текст с изображением)
 *
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool $is_preview True during AJAX preview.
 * @param (int|string) $post_id The post ID this block is saved to.
 */

$layout_type  = get_field('layout_type') ?: 'columns'; // 'columns' or 'float'
$image_pos    = get_field('image_position') ?: 'left'; // 'left' or 'right'
$image_width  = get_field('image_width') ?: '40';     // '30', '40', '50', '60'
$valign       = get_field('vertical_align') ?: 'top';  // 'top' or 'center'
$image        = get_field('image');
$caption      = get_field('image_caption');
$text         = get_field('text');

$img_url = '';
$img_alt = 'Изображение к тексту';

if (!empty($image)) {
    if (is_array($image)) {
        $img_url = $image['sizes']['large'] ?? $image['url'];
        $img_alt = $image['alt'] ?: ($caption ?: 'Изображение');
    } elseif (is_numeric($image)) {
        $img_url = wp_get_attachment_image_url($image, 'large');
        $img_alt = get_post_meta($image, '_wp_attachment_image_alt', true) ?: ($caption ?: 'Изображение');
    } else {
        $img_url = $image;
    }
}

if (empty($img_url) && empty($text)) {
    if ($is_preview) {
        ?>
        <div style="border: 2px dashed #D1D5DB; border-radius: 16px; padding: 40px 20px; text-align: center; background-color: #F9FAFB; color: #6B7280;">
            <div style="font-size: 32px; margin-bottom: 8px;">📑</div>
            <div style="font-weight: 600; font-size: 16px; color: #191726; margin-bottom: 4px;">Блок «Текст с изображением»</div>
            <p style="margin: 0; font-size: 14px;">Кликните на блок, чтобы выбрать изображение и ввести текст с настройками расположения.</p>
        </div>
        <?php
    }
    return;
}

$classes = [
    'article-text-image',
    'article-text-image--' . $layout_type,
    'article-text-image--pos-' . $image_pos,
    'article-text-image--w-' . $image_width,
    'article-text-image--valign-' . $valign,
];
?>
<div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
    <?php if ($layout_type === 'columns') : ?>
        <div class="article-text-image__image-col">
            <?php if (!empty($img_url)) : ?>
                <div class="article-text-image__image-wrap">
                    <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($img_alt); ?>" loading="lazy" decoding="async">
                </div>
                <?php if (!empty($caption)) : ?>
                    <div class="article-text-image__caption"><?php echo esc_html($caption); ?></div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <div class="article-text-image__content">
            <?php echo wpautop($text); ?>
        </div>
    <?php else : ?>
        <?php if (!empty($img_url)) : ?>
            <div class="article-text-image__image-wrap article-text-image--pos-<?php echo esc_attr($image_pos); ?> article-text-image--w-<?php echo esc_attr($image_width); ?>">
                <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($img_alt); ?>" loading="lazy" decoding="async">
                <?php if (!empty($caption)) : ?>
                    <div class="article-text-image__caption"><?php echo esc_html($caption); ?></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="article-text-image__content">
            <?php echo wpautop($text); ?>
        </div>
    <?php endif; ?>
</div>
