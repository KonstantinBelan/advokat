<?php
/**
 * Extended Article Content, Media, ACF Visual Blocks, and Clean Gutenberg Environment
 *
 * @package BelanAgency
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 1. Convert video URLs from VK Video, Rutube, YouTube, Kinescope to responsive embed iframes
 */
function belan_get_video_embed_url($url) {
    $url = trim($url);

    // YouTube: https://www.youtube.com/watch?v=ID or https://youtu.be/ID
    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $url, $m)) {
        return 'https://www.youtube.com/embed/' . $m[1] . '?autoplay=1&rel=0';
    }

    // Rutube: https://rutube.ru/video/ID/ or https://rutube.ru/play/embed/ID
    if (preg_match('/rutube\.ru\/(?:video|play\/embed)\/([a-zA-Z0-9]+)/i', $url, $m)) {
        return 'https://rutube.ru/play/embed/' . $m[1] . '/?autoStart=true';
    }

    // Kinescope: https://kinescope.io/ID or https://kinescope.io/embed/ID
    if (preg_match('/kinescope\.io\/(?:embed\/)?([a-zA-Z0-9]+)/i', $url, $m)) {
        return 'https://kinescope.io/embed/' . $m[1] . '?autoplay=1';
    }

    // VK Video: https://vkvideo.ru/video-123_456 or https://vk.com/video-123_456
    if (preg_match('/vk(?:video)?\.(?:ru|com)\/video(-?\d+)_(\d+)/i', $url, $m)) {
        return 'https://vkvideo.ru/video_ext.php?oid=' . $m[1] . '&id=' . $m[2] . '&hd=2&autoplay=1';
    }

    // Direct embed link (e.g. video_ext.php)
    if (strpos($url, 'video_ext.php') !== false) {
        if (strpos($url, 'autoplay=') === false) {
            $url .= (strpos($url, '?') !== false ? '&' : '?') . 'autoplay=1';
        }
        return $url;
    }

    return $url;
}

/**
 * 2. Register Custom Block Category in Gutenberg
 */
function belan_register_block_categories($categories, $post) {
    return array_merge([
        [
            'slug'  => 'belan-articles',
            'title' => 'Статьи и медиа (Адвокат Ежов)',
            'icon'  => 'welcome-learn-more',
        ],
    ], $categories);
}
add_filter('block_categories_all', 'belan_register_block_categories', 10, 2);

/**
 * 3. Register ACF PRO Visual Gutenberg Blocks
 */
function belan_register_acf_blocks() {
    if (!function_exists('acf_register_block_type')) {
        return;
    }

    // Block 1: Callout (Информационный блок)
    acf_register_block_type([
        'name'            => 'article-callout',
        'title'           => 'Информационный блок',
        'description'     => 'Важная информация, предупреждение, ошибка или совет адвоката',
        'render_template' => get_template_directory() . '/template-parts/blocks/callout.php',
        'category'        => 'belan-articles',
        'icon'            => 'info-outline',
        'keywords'        => ['инфо', 'предупреждение', 'ошибка', 'важно', 'callout', 'alert', 'совет'],
        'mode'            => 'auto',
        'supports'        => [
            'align' => false,
            'mode'  => true,
            'jsx'   => true,
        ],
    ]);

    // Block 2: Video (Видео: VK, RuTube, YouTube, Кинескоп)
    acf_register_block_type([
        'name'            => 'article-video',
        'title'           => 'Видео (VK, RuTube, YouTube, Кинескоп)',
        'description'     => 'Адаптивное видео с обложкой и кнопкой запуска или прямой плеер',
        'render_template' => get_template_directory() . '/template-parts/blocks/video.php',
        'category'        => 'belan-articles',
        'icon'            => 'video-alt3',
        'keywords'        => ['видео', 'video', 'vk', 'rutube', 'youtube', 'kinescope', 'рутуб', 'ютуб'],
        'mode'            => 'auto',
        'supports'        => [
            'align' => false,
            'mode'  => true,
        ],
    ]);

    // Block 3: Slider (Слайдер Swiper)
    acf_register_block_type([
        'name'            => 'article-slider',
        'title'           => 'Слайдер фотографий (Swiper)',
        'description'     => 'Слайдер с перелистыванием изображений и точками навигации',
        'render_template' => get_template_directory() . '/template-parts/blocks/slider.php',
        'category'        => 'belan-articles',
        'icon'            => 'images-alt2',
        'keywords'        => ['слайдер', 'галерея', 'slider', 'swiper', 'фотографии'],
        'mode'            => 'auto',
        'supports'        => [
            'align' => false,
            'mode'  => true,
        ],
    ]);

    // Block 4: Image with Caption (Изображение с подписью)
    acf_register_block_type([
        'name'            => 'article-image-caption',
        'title'           => 'Изображение с подписью',
        'description'     => 'Фотография со стилизованным блоком пояснения под ней',
        'render_template' => get_template_directory() . '/template-parts/blocks/image-caption.php',
        'category'        => 'belan-articles',
        'icon'            => 'format-image',
        'keywords'        => ['изображение', 'картинка', 'подпись', 'фото', 'caption'],
        'mode'            => 'auto',
        'supports'        => [
            'align' => false,
            'mode'  => true,
        ],
    ]);

    // Block 5: Text with Image (Текст с изображением)
    acf_register_block_type([
        'name'            => 'article-text-image',
        'title'           => 'Текст с изображением',
        'description'     => 'Текст и изображение в одном блоке с настройкой расположения, обтекания и размера',
        'render_template' => get_template_directory() . '/template-parts/blocks/text-image.php',
        'category'        => 'belan-articles',
        'icon'            => 'align-pull-left',
        'keywords'        => ['текст', 'изображение', 'картинка', 'обтекание', 'колонки', 'фото'],
        'mode'            => 'auto',
        'supports'        => [
            'align' => false,
            'mode'  => true,
        ],
    ]);
}
add_action('acf/init', 'belan_register_acf_blocks');

/**
 * 4. Register ACF Fields for Visual Blocks
 */
function belan_register_acf_block_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    // Fields for: Callout (Информационный блок)
    acf_add_local_field_group([
        'key'      => 'group_block_article_callout',
        'title'    => 'Настройки информационного блока',
        'fields'   => [
            [
                'key'           => 'field_callout_type',
                'label'         => 'Тип блока',
                'name'          => 'callout_type',
                'type'          => 'select',
                'choices'       => [
                    'info'    => 'ℹ️ Важная информация (Синий)',
                    'warning' => '⚠️ Предупреждение / Внимание (Оранжевый)',
                    'error'   => '🛑 Распространенная ошибка / Запрещено (Красный)',
                    'success' => '💡 Совет адвоката / Рекомендация (Зеленый)',
                ],
                'default_value' => 'info',
                'allow_null'    => 0,
                'multiple'      => 0,
                'ui'            => 1,
            ],
            [
                'key'         => 'field_callout_title',
                'label'       => 'Заголовок блока',
                'name'        => 'callout_title',
                'type'        => 'text',
                'placeholder' => 'Оставьте пустым для стандартного заголовка...',
            ],
            [
                'key'          => 'field_callout_text',
                'label'        => 'Текст блока',
                'name'         => 'callout_text',
                'type'         => 'wysiwyg',
                'tabs'         => 'visual',
                'toolbar'      => 'basic',
                'media_upload' => 0,
                'rows'         => 4,
            ],
        ],
        'location' => [
            [
                [
                    'param'    => 'block',
                    'operator' => '==',
                    'value'    => 'acf/article-callout',
                ],
            ],
        ],
    ]);

    // Fields for: Video (Видео: VK, RuTube, YouTube, Кинескоп)
    acf_add_local_field_group([
        'key'      => 'group_block_article_video',
        'title'    => 'Настройки видео',
        'fields'   => [
            [
                'key'          => 'field_video_url',
                'label'        => 'Ссылка на видео',
                'name'         => 'video_url',
                'type'         => 'text',
                'instructions' => 'Вставьте любую ссылку на видео с VK Видео, RuTube, YouTube или Кинескоп (Kinescope)',
                'placeholder'  => 'https://rutube.ru/video/... или https://vkvideo.ru/... или https://youtu.be/...',
                'required'     => 1,
            ],
            [
                'key'           => 'field_video_poster',
                'label'         => 'Обложка видео (превью)',
                'name'          => 'video_poster',
                'type'          => 'image',
                'instructions'  => 'Рекомендуется. При выборе обложки видео будет запускаться по клику на кнопку Play прямо в карточке.',
                'return_format' => 'array',
                'preview_size'  => 'medium',
                'library'       => 'all',
            ],
            [
                'key'           => 'field_video_title',
                'label'         => 'Заголовок / описание видео',
                'name'          => 'video_title',
                'type'          => 'text',
                'default_value' => 'Смотреть видео',
            ],
        ],
        'location' => [
            [
                [
                    'param'    => 'block',
                    'operator' => '==',
                    'value'    => 'acf/article-video',
                ],
            ],
        ],
    ]);

    // Fields for: Slider (Слайдер Swiper)
    acf_add_local_field_group([
        'key'      => 'group_block_article_slider',
        'title'    => 'Настройки слайдера Swiper',
        'fields'   => [
            [
                'key'          => 'field_slider_images',
                'label'        => 'Фотографии слайдера',
                'name'         => 'slider_images',
                'type'         => 'gallery',
                'instructions' => 'Нажмите «Добавить в галерею» и выберите нужные фотографии. Их можно легко менять местами перетаскиванием.',
                'required'     => 1,
                'min'          => 1,
                'preview_size' => 'thumbnail',
                'library'      => 'all',
            ],
        ],
        'location' => [
            [
                [
                    'param'    => 'block',
                    'operator' => '==',
                    'value'    => 'acf/article-slider',
                ],
            ],
        ],
    ]);

    // Fields for: Image with Caption
    acf_add_local_field_group([
        'key'      => 'group_block_article_image_caption',
        'title'    => 'Настройки изображения с подписью',
        'fields'   => [
            [
                'key'           => 'field_image_caption_img',
                'label'         => 'Изображение',
                'name'          => 'image',
                'type'          => 'image',
                'return_format' => 'array',
                'preview_size'  => 'medium',
                'required'      => 1,
            ],
            [
                'key'          => 'field_image_caption_text',
                'label'        => 'Поясняющая подпись',
                'name'         => 'caption',
                'type'         => 'textarea',
                'rows'         => 3,
                'placeholder'  => 'Введите поясняющий текст к изображению...',
            ],
        ],
        'location' => [
            [
                [
                    'param'    => 'block',
                    'operator' => '==',
                    'value'    => 'acf/article-image-caption',
                ],
            ],
        ],
    ]);

    // Fields for: Text with Image (Текст с изображением)
    acf_add_local_field_group([
        'key'      => 'group_block_article_text_image',
        'title'    => 'Настройки блока «Текст с изображением»',
        'fields'   => [
            [
                'key'           => 'field_text_image_layout',
                'label'         => 'Режим отображения',
                'name'          => 'layout_type',
                'type'          => 'button_group',
                'choices'       => [
                    'columns' => 'Колонки (без обтекания)',
                    'float'   => 'Обтекание текстом',
                ],
                'default_value' => 'columns',
                'layout'        => 'horizontal',
            ],
            [
                'key'           => 'field_text_image_pos',
                'label'         => 'Расположение изображения',
                'name'          => 'image_position',
                'type'          => 'button_group',
                'choices'       => [
                    'left'  => '◀ Слева',
                    'right' => 'Справа ▶',
                ],
                'default_value' => 'left',
                'layout'        => 'horizontal',
            ],
            [
                'key'           => 'field_text_image_width',
                'label'         => 'Размер изображения (ширина)',
                'name'          => 'image_width',
                'type'          => 'button_group',
                'choices'       => [
                    '30' => '30% (Компактное)',
                    '40' => '40% (Стандартное)',
                    '50' => '50% (Половина)',
                    '60' => '60% (Крупное)',
                ],
                'default_value' => '40',
                'layout'        => 'horizontal',
            ],
            [
                'key'               => 'field_text_image_valign',
                'label'             => 'Вертикальное выравнивание',
                'name'              => 'vertical_align',
                'type'              => 'button_group',
                'choices'           => [
                    'top'    => 'По верхнему краю',
                    'center' => 'По центру',
                ],
                'default_value'     => 'top',
                'layout'            => 'horizontal',
                'conditional_logic' => [
                    [
                        [
                            'field'    => 'field_text_image_layout',
                            'operator' => '==',
                            'value'    => 'columns',
                        ],
                    ],
                ],
            ],
            [
                'key'           => 'field_text_image_img',
                'label'         => 'Изображение',
                'name'          => 'image',
                'type'          => 'image',
                'return_format' => 'array',
                'preview_size'  => 'medium',
                'required'      => 1,
            ],
            [
                'key'         => 'field_text_image_caption',
                'label'       => 'Подпись к изображению',
                'name'        => 'image_caption',
                'type'        => 'text',
                'placeholder' => 'Необязательно. Пояснение под фотографией...',
            ],
            [
                'key'          => 'field_text_image_text',
                'label'        => 'Текст статьи',
                'name'         => 'text',
                'type'         => 'wysiwyg',
                'tabs'         => 'visual',
                'toolbar'      => 'basic',
                'media_upload' => 0,
                'rows'         => 6,
                'required'     => 1,
            ],
        ],
        'location' => [
            [
                [
                    'param'    => 'block',
                    'operator' => '==',
                    'value'    => 'acf/article-text-image',
                ],
            ],
        ],
    ]);
}
add_action('acf/init', 'belan_register_acf_block_fields');

/**
 * 5. Register Gutenberg Block Styles for Lists
 */
function belan_register_block_styles() {
    // List styles for unordered lists
    register_block_style('core/list', [
        'name'  => 'list-checks',
        'label' => 'Галочки (Checkmarks)',
    ]);
    register_block_style('core/list', [
        'name'  => 'list-crosses',
        'label' => 'Крестики (Запреты)',
    ]);
    register_block_style('core/list', [
        'name'  => 'list-dashes',
        'label' => 'Тире (Юридический)',
    ]);
    register_block_style('core/list', [
        'name'  => 'list-arrows',
        'label' => 'Стрелки',
    ]);

    // List styles for ordered lists
    register_block_style('core/list', [
        'name'  => 'list-badges',
        'label' => 'Номера в кружках (1, 2...)',
    ]);
    register_block_style('core/list', [
        'name'  => 'list-steps',
        'label' => 'Пошаговые этапы (Шаг 1...)',
    ]);
    register_block_style('core/list', [
        'name'  => 'list-alpha',
        'label' => 'Алфавитный (а, б, в...)',
    ]);
    register_block_style('core/list', [
        'name'  => 'list-roman',
        'label' => 'Римские цифры (I, II...)',
    ]);
}
add_action('init', 'belan_register_block_styles');

/**
 * 6. Disable All WordPress Core Patterns and Remote Patterns
 */
function belan_disable_core_patterns() {
    remove_theme_support('core-block-patterns');
}
add_action('after_setup_theme', 'belan_disable_core_patterns');
add_filter('should_load_remote_block_patterns', '__return_false');

function belan_unregister_core_patterns() {
    if (class_exists('WP_Block_Patterns_Registry')) {
        $patterns = WP_Block_Patterns_Registry::get_instance()->get_all_registered();
        foreach ($patterns as $pattern) {
            unregister_block_pattern($pattern['name']);
        }
    }
}
add_action('init', 'belan_unregister_core_patterns', 99);

/**
 * 7. Restrict Allowed Blocks in Gutenberg for Articles & News
 * Only keep clean, essential text blocks + our friendly visual ACF blocks!
 */
function belan_allowed_block_types($allowed_blocks, $editor_context) {
    if (!empty($editor_context->post) && in_array($editor_context->post->post_type, ['post', 'news'])) {
        return [
            // Standard Text Blocks
            'core/paragraph',
            'core/heading',
            'core/list',
            'core/list-item',
            'core/quote',
            'core/table',
            'core/image',

            // Custom Visual ACF Blocks
            'acf/article-callout',
            'acf/article-video',
            'acf/article-slider',
            'acf/article-image-caption',
            'acf/article-text-image',
        ];
    }
    return $allowed_blocks;
}
add_filter('allowed_block_types_all', 'belan_allowed_block_types', 10, 2);

/**
 * 8. Backward-compatible Shortcodes (in case someone wants to use them)
 */
function belan_shortcode_video_embed($atts) {
    $atts = shortcode_atts([
        'url'    => '',
        'src'    => '',
        'poster' => '',
        'img'    => '',
        'title'  => 'Смотреть видео',
    ], $atts, 'video_embed');

    $video_url = !empty($atts['url']) ? $atts['url'] : $atts['src'];
    $poster    = !empty($atts['poster']) ? $atts['poster'] : $atts['img'];
    $title     = $atts['title'];

    if (empty($video_url)) {
        return '';
    }

    $embed_url = belan_get_video_embed_url($video_url);

    if (!empty($poster)) {
        ob_start();
        ?>
        <div class="article-detail__video-thumb" data-video-src="<?php echo esc_url($embed_url); ?>">
            <img src="<?php echo esc_url($poster); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" decoding="async">
            <button type="button" class="article-detail__play-btn" aria-label="<?php echo esc_attr($title); ?>">
                <svg viewBox="0 0 24 24">
                    <polygon points="5 3 19 12 5 21 5 3"></polygon>
                </svg>
            </button>
        </div>
        <?php
        return ob_get_clean();
    }

    return '<div class="article-video-responsive"><iframe src="' . esc_url($embed_url) . '" width="100%" height="100%" frameborder="0" allow="autoplay; encrypted-media; fullscreen; picture-in-picture; screen-wake-lock;" allowfullscreen></iframe></div>';
}
add_shortcode('video_embed', 'belan_shortcode_video_embed');
add_shortcode('vk_video', 'belan_shortcode_video_embed');
add_shortcode('rutube', 'belan_shortcode_video_embed');
add_shortcode('youtube', 'belan_shortcode_video_embed');
add_shortcode('kinescope', 'belan_shortcode_video_embed');

function belan_shortcode_callout($atts, $content = null, $tag = 'callout') {
    $default_type = 'info';
    if (in_array($tag, ['warning', 'error', 'success', 'info', 'tip'])) {
        $default_type = ($tag === 'tip') ? 'success' : $tag;
    }

    $atts = shortcode_atts([
        'type'  => $default_type,
        'title' => '',
    ], $atts, $tag);

    $type  = sanitize_key($atts['type']);
    $title = sanitize_text_field($atts['title']);

    if (empty($title)) {
        switch ($type) {
            case 'warning':
                $title = 'Важно!';
                break;
            case 'error':
            case 'danger':
                $title = 'Внимание! Ошибка';
                break;
            case 'success':
            case 'tip':
                $title = 'Совет адвоката';
                break;
            case 'info':
            default:
                $title = 'Полезная информация';
                break;
        }
    }

    $icons = [
        'info'    => '<svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>',
        'warning' => '<svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>',
        'error'   => '<svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>',
        'danger'  => '<svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>',
        'success' => '<svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>',
    ];

    $icon_svg = $icons[$type] ?? $icons['info'];

    ob_start();
    ?>
    <div class="article-callout article-callout--<?php echo esc_attr($type); ?>">
        <?php if (!empty($title)) : ?>
            <div class="article-callout__header">
                <span class="article-callout__icon"><?php echo $icon_svg; ?></span>
                <span class="article-callout__title"><?php echo esc_html($title); ?></span>
            </div>
        <?php endif; ?>
        <div class="article-callout__content">
            <?php echo do_shortcode(wpautop(trim($content))); ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('callout', 'belan_shortcode_callout');
add_shortcode('alert', 'belan_shortcode_callout');
add_shortcode('info', 'belan_shortcode_callout');
add_shortcode('warning', 'belan_shortcode_callout');
add_shortcode('error', 'belan_shortcode_callout');
add_shortcode('success', 'belan_shortcode_callout');
add_shortcode('tip', 'belan_shortcode_callout');

function belan_shortcode_slider($atts, $content = null) {
    $atts = shortcode_atts([
        'ids'    => '',
        'images' => '',
    ], $atts, 'slider');

    $image_urls = [];

    if (!empty($atts['ids'])) {
        $ids = array_map('intval', explode(',', $atts['ids']));
        foreach ($ids as $id) {
            $url = wp_get_attachment_image_url($id, 'large');
            $alt = get_post_meta($id, '_wp_attachment_image_alt', true) ?: 'Изображение слайдера';
            if ($url) {
                $image_urls[] = ['url' => $url, 'alt' => $alt];
            }
        }
    }

    if (empty($image_urls) && !empty($atts['images'])) {
        $urls = explode(',', $atts['images']);
        foreach ($urls as $url) {
            $u = trim($url);
            if ($u) {
                $image_urls[] = ['url' => $u, 'alt' => 'Изображение слайдера'];
            }
        }
    }

    if (empty($image_urls) && !empty($content)) {
        if (preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*alt=["\']?([^"\']*)["\']?[^>]*>/i', $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $image_urls[] = [
                    'url' => $m[1],
                    'alt' => !empty($m[2]) ? $m[2] : 'Слайд',
                ];
            }
        }
    }

    if (empty($image_urls)) {
        return '';
    }

    ob_start();
    ?>
    <div class="article-detail__slider swiper">
        <div class="swiper-wrapper">
            <?php foreach ($image_urls as $img) : ?>
                <div class="swiper-slide">
                    <img src="<?php echo esc_url($img['url']); ?>" alt="<?php echo esc_attr($img['alt']); ?>" loading="lazy" decoding="async">
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="article-slider-btn article-slider-btn--prev" aria-label="Предыдущий слайд">‹</button>
        <button type="button" class="article-slider-btn article-slider-btn--next" aria-label="Следующий слайд">›</button>
        <div class="article-slider-dots swiper-pagination"></div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('slider', 'belan_shortcode_slider');
add_shortcode('article_slider', 'belan_shortcode_slider');
