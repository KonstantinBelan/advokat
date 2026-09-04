<?php
/**
 * Block Name: Video (Видео: VK, Rutube, YouTube, Kinescope)
 *
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool $is_preview True during AJAX preview.
 * @param (int|string) $post_id The post ID this block is saved to.
 */

$video_url = get_field('video_url');
$poster    = get_field('video_poster');
$title     = get_field('video_title') ?: 'Смотреть видео';

$poster_url = '';
if (!empty($poster)) {
    $poster_url = is_array($poster) ? ($poster['sizes']['large'] ?? $poster['url']) : (is_numeric($poster) ? wp_get_attachment_image_url($poster, 'large') : $poster);
}

if (empty($video_url)) {
    if ($is_preview) {
        ?>
        <div style="border: 2px dashed #D1D5DB; border-radius: 16px; padding: 40px 20px; text-align: center; background-color: #F9FAFB; color: #6B7280;">
            <div style="font-size: 32px; margin-bottom: 8px;">🎬</div>
            <div style="font-weight: 600; font-size: 16px; color: #191726; margin-bottom: 4px;">Блок «Видео (VK, RuTube, YouTube, Кинескоп)»</div>
            <p style="margin: 0; font-size: 14px;">Укажите ссылку на видео в панели справа или нажав на блок.</p>
        </div>
        <?php
    }
    return;
}

$embed_url = belan_get_video_embed_url($video_url);

if (!empty($poster_url)) : ?>
    <div class="article-detail__video-thumb" data-video-src="<?php echo esc_url($embed_url); ?>">
        <img src="<?php echo esc_url($poster_url); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" decoding="async">
        <button type="button" class="article-detail__play-btn" aria-label="<?php echo esc_attr($title); ?>">
            <svg viewBox="0 0 24 24">
                <polygon points="5 3 19 12 5 21 5 3"></polygon>
            </svg>
        </button>
    </div>
<?php else : ?>
    <div class="article-video-responsive">
        <iframe src="<?php echo esc_url($embed_url); ?>" width="100%" height="100%" frameborder="0" allow="autoplay; encrypted-media; fullscreen; picture-in-picture; screen-wake-lock;" allowfullscreen></iframe>
    </div>
<?php endif;
