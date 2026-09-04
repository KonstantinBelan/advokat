<?php
/**
 * Block Name: Callout (Информационный блок)
 *
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool $is_preview True during AJAX preview.
 * @param (int|string) $post_id The post ID this block is saved to.
 */

$type  = get_field('callout_type') ?: 'info';
$title = get_field('callout_title');
$text  = get_field('callout_text');

if (empty($title)) {
    switch ($type) {
        case 'warning':
            $title = 'Внимание! Предупреждение';
            break;
        case 'error':
            $title = 'Распространенная ошибка';
            break;
        case 'success':
            $title = 'Совет адвоката';
            break;
        case 'info':
        default:
            $title = 'Важная информация';
            break;
    }
}

$icons = [
    'info'    => '<svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>',
    'warning' => '<svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>',
    'error'   => '<svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>',
    'success' => '<svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>',
];

$icon_svg = $icons[$type] ?? $icons['info'];
?>
<div class="article-callout article-callout--<?php echo esc_attr($type); ?>">
    <div class="article-callout__header">
        <span class="article-callout__icon"><?php echo $icon_svg; ?></span>
        <span class="article-callout__title"><?php echo esc_html($title); ?></span>
    </div>
    <div class="article-callout__content">
        <?php if (!empty($text)) : ?>
            <?php echo wpautop($text); ?>
        <?php elseif ($is_preview) : ?>
            <p style="opacity: 0.6; font-style: italic;">Нажмите на блок или откройте панель справа, чтобы ввести текст инфоблока...</p>
        <?php endif; ?>
    </div>
</div>
