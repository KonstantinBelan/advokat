<?php
/**
 * Generic Archive Template
 *
 * @package BelanAgency
 */

if (is_post_type_archive('cases')) {
    require get_template_directory() . '/archive-cases.php';
    exit;
}

if (is_post_type_archive('consultation') || is_tax('consultation_category')) {
    require get_template_directory() . '/archive-consultation.php';
    exit;
}

if (is_post_type_archive('news')) {
    require get_template_directory() . '/archive-news.php';
    exit;
}

if (is_tax('service_category')) {
    require get_template_directory() . '/taxonomy-service_category.php';
    exit;
}

if (is_post_type_archive('service')) {
    require get_template_directory() . '/page-services.php';
    exit;
}

require get_template_directory() . '/page-articles.php';
