<?php
/**
 * Register Custom Post Types and Taxonomies
 *
 * @package BelanAgency
 */

if (!defined('ABSPATH')) {
    exit;
}

function belan_register_post_types() {
    // 1. Услуги (Services)
    register_post_type('service', [
        'labels' => [
            'name'               => 'Услуги',
            'singular_name'      => 'Услуга',
            'add_new'            => 'Добавить услугу',
            'add_new_item'       => 'Добавить новую услугу',
            'edit_item'          => 'Редактировать услугу',
            'new_item'           => 'Новая услуга',
            'view_item'          => 'Просмотреть услугу',
            'search_items'       => 'Найти услугу',
            'not_found'          => 'Услуг не найдено',
            'menu_name'          => 'Услуги',
        ],
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => ['slug' => 'service', 'with_front' => false],
        'capability_type'    => 'post',
        'has_archive'        => 'services',
        'hierarchical'       => true,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-portfolio',
        'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'],
        'show_in_rest'       => true,
    ]);

    // Таксономия для Услуг (Для физических лиц / Для юридических лиц и подкатегории)
    register_taxonomy('service_category', ['service'], [
        'labels' => [
            'name'              => 'Категории услуг',
            'singular_name'     => 'Категория услуг',
            'search_items'      => 'Поиск категорий',
            'all_items'         => 'Все категории',
            'parent_item'       => 'Родительская категория',
            'parent_item_colon' => 'Родительская категория:',
            'edit_item'         => 'Редактировать категорию',
            'update_item'       => 'Обновить категорию',
            'add_new_item'      => 'Добавить категорию',
            'new_item_name'     => 'Название новой категории',
            'menu_name'         => 'Категории услуг',
        ],
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => ['slug' => 'service-category', 'hierarchical' => true, 'with_front' => false],
        'show_in_rest'      => true,
    ]);

    // 2. Кейсы / Практика (Cases)
    register_post_type('cases', [
        'labels' => [
            'name'               => 'Кейсы',
            'singular_name'      => 'Кейс',
            'add_new'            => 'Добавить кейс',
            'add_new_item'       => 'Добавить новый кейс',
            'edit_item'          => 'Редактировать кейс',
            'new_item'           => 'Новый кейс',
            'view_item'          => 'Просмотреть кейс',
            'search_items'       => 'Найти кейс',
            'not_found'          => 'Кейсов не найдено',
            'menu_name'          => 'Кейсы (Практика)',
        ],
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => ['slug' => 'cases-item', 'with_front' => false],
        'capability_type'    => 'post',
        'has_archive'        => 'cases',
        'hierarchical'       => false,
        'menu_position'      => 6,
        'menu_icon'          => 'dashicons-hammer',
        'supports'           => ['title', 'editor', 'thumbnail', 'excerpt'],
        'show_in_rest'       => true,
    ]);

    // Таксономия для Кейсов (Недвижимость, Семейные, Арбитраж...)
    register_taxonomy('case_category', ['cases'], [
        'labels' => [
            'name'              => 'Категории дел',
            'singular_name'     => 'Категория дел',
            'search_items'      => 'Поиск категорий',
            'all_items'         => 'Все категории',
            'edit_item'         => 'Редактировать категорию',
            'update_item'       => 'Обновить категорию',
            'add_new_item'      => 'Добавить категорию',
            'new_item_name'     => 'Название новой категории',
            'menu_name'         => 'Категории дел',
        ],
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => ['slug' => 'case-category', 'with_front' => false],
        'show_in_rest'      => true,
    ]);

    // 3. Отзывы (Reviews)
    register_post_type('review', [
        'labels' => [
            'name'               => 'Отзывы',
            'singular_name'      => 'Отзыв',
            'add_new'            => 'Добавить отзыв',
            'add_new_item'       => 'Добавить новый отзыв',
            'edit_item'          => 'Редактировать отзыв',
            'new_item'           => 'Новый отзыв',
            'view_item'          => 'Просмотреть отзыв',
            'search_items'       => 'Найти отзыв',
            'not_found'          => 'Отзывов не найдено',
            'menu_name'          => 'Отзывы',
        ],
        'public'             => true,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 7,
        'menu_icon'          => 'dashicons-star-filled',
        'supports'           => ['title', 'editor', 'thumbnail'],
        'show_in_rest'       => true,
    ]);

    // 4. Вопросы и ответы / Консультации (Consultations)
    register_post_type('consultation', [
        'labels' => [
            'name'               => 'Консультации (Q&A)',
            'singular_name'      => 'Вопрос-ответ',
            'add_new'            => 'Добавить вопрос',
            'add_new_item'       => 'Добавить новый вопрос',
            'edit_item'          => 'Редактировать вопрос',
            'new_item'           => 'Новый вопрос',
            'view_item'          => 'Просмотреть вопрос',
            'search_items'       => 'Найти вопрос',
            'not_found'          => 'Вопросов не найдено',
            'menu_name'          => 'Консультации',
        ],
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => ['slug' => 'consultation-item', 'with_front' => false],
        'capability_type'    => 'post',
        'has_archive'        => 'consultation',
        'hierarchical'       => false,
        'menu_position'      => 8,
        'menu_icon'          => 'dashicons-format-chat',
        'supports'           => ['title'],
        'show_in_rest'       => false,
    ]);

    // Таксономия для Консультаций
    register_taxonomy('consultation_category', ['consultation'], [
        'labels' => [
            'name'              => 'Рубрики вопросов',
            'singular_name'     => 'Рубрика вопросов',
            'search_items'      => 'Поиск рубрик',
            'all_items'         => 'Все рубрики',
            'edit_item'         => 'Редактировать рубрику',
            'update_item'       => 'Обновить рубрику',
            'add_new_item'      => 'Добавить рубрику',
            'new_item_name'     => 'Название новой рубрики',
            'menu_name'         => 'Рубрики вопросов',
        ],
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => ['slug' => 'consultation-category', 'with_front' => false],
        'show_in_rest'      => true,
    ]);

    // 5. Новости (News)
    register_post_type('news', [
        'labels' => [
            'name'               => 'Новости',
            'singular_name'      => 'Новость',
            'add_new'            => 'Добавить новость',
            'add_new_item'       => 'Добавить новость',
            'edit_item'          => 'Редактировать новость',
            'new_item'           => 'Новая новость',
            'view_item'          => 'Просмотреть новость',
            'search_items'       => 'Найти новость',
            'not_found'          => 'Новостей не найдено',
            'menu_name'          => 'Новости',
        ],
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => ['slug' => 'news-single', 'with_front' => false],
        'capability_type'    => 'post',
        'has_archive'        => 'news',
        'hierarchical'       => false,
        'menu_position'      => 9,
        'menu_icon'          => 'dashicons-megaphone',
        'supports'           => ['title', 'editor', 'thumbnail', 'excerpt'],
        'show_in_rest'       => true,
    ]);
}
add_action('init', 'belan_register_post_types');
