<?php
/**
 * Register ACF Options Page and Field Groups in Code
 *
 * @package BelanAgency
 */

if (!defined('ABSPATH')) {
    exit;
}

// Only administrators can manage ACF (Field Groups, Tools, ACF Menu)
add_filter('acf/settings/show_admin', function($show) {
    return current_user_can('manage_options');
});
add_filter('acf/settings/capability', function($cap) {
    return 'manage_options';
});

// Register Options Pages
add_action('acf/init', function() {
    if (!function_exists('acf_add_options_page')) {
        return;
    }

    acf_add_options_page([
        'page_title'    => 'Настройки сайта',
        'menu_title'    => 'Настройки сайта',
        'menu_slug'     => 'theme-general-settings',
        'capability'    => 'manage_options',
        'redirect'      => false,
        'icon_url'      => 'dashicons-admin-generic',
        'position'      => 2,
    ]);

    acf_add_options_sub_page([
        'page_title'  => 'Контакты и реквизиты',
        'menu_title'  => 'Контакты',
        'menu_slug'   => 'theme-contacts',
        'parent_slug' => 'theme-general-settings',
        'capability'  => 'manage_options',
    ]);

    acf_add_options_sub_page([
        'page_title'  => 'Глобальные секции (CTA, FAQ, Об адвокате)',
        'menu_title'  => 'Глобальные секции',
        'menu_slug'   => 'theme-global-sections',
        'parent_slug' => 'theme-general-settings',
        'capability'  => 'manage_options',
    ]);
});

// Register Local Field Groups
add_action('acf/init', function() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    // 1. Theme Settings: Contacts & Header/Footer
    acf_add_local_field_group([
        'key' => 'group_theme_settings_contacts',
        'title' => 'Контакты и реквизиты сайта',
        'fields' => [
            [
                'key' => 'field_site_phone',
                'label' => 'Номер телефона (для отображения)',
                'name' => 'site_phone',
                'type' => 'text',
                'default_value' => '8 (993) 909-90-50',
            ],
            [
                'key' => 'field_site_phone_tel',
                'label' => 'Номер телефона (для ссылки tel:)',
                'name' => 'site_phone_tel',
                'type' => 'text',
                'default_value' => '+79939099050',
            ],
            [
                'key' => 'field_site_email',
                'label' => 'Email',
                'name' => 'site_email',
                'type' => 'email',
                'default_value' => 'ezhov-advokat@yandex.ru',
            ],
            [
                'key' => 'field_site_address',
                'label' => 'Адрес офиса',
                'name' => 'site_address',
                'type' => 'text',
                'default_value' => 'г. Москва, ул. Арбат, д. 20, офис 305',
            ],
            [
                'key' => 'field_site_hours',
                'label' => 'Режим работы',
                'name' => 'site_hours',
                'type' => 'text',
                'default_value' => 'Пн-Пт: 09:00 - 20:00, Сб: 10:00 - 17:00',
            ],
            [
                'key' => 'field_site_whatsapp',
                'label' => 'Ссылка WhatsApp',
                'name' => 'site_whatsapp',
                'type' => 'url',
                'default_value' => 'https://wa.me/79939099050',
            ],
            [
                'key' => 'field_site_telegram',
                'label' => 'Ссылка Telegram',
                'name' => 'site_telegram',
                'type' => 'url',
                'default_value' => 'https://t.me/advokatezhov',
            ],
            [
                'key' => 'field_site_max',
                'label' => 'Ссылка МАКС',
                'name' => 'site_max',
                'type' => 'url',
                'default_value' => '#',
            ],
            [
                'key' => 'field_site_copyright',
                'label' => 'Текст копирайта',
                'name' => 'site_copyright',
                'type' => 'text',
                'default_value' => '© 2026. Все права защищены.',
            ],
            [
                'key' => 'field_site_privacy_url',
                'label' => 'Ссылка на Политику конфиденциальности',
                'name' => 'site_privacy_url',
                'type' => 'text',
                'default_value' => '#',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'theme-contacts',
                ],
            ],
            [
                [
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'theme-general-settings',
                ],
            ],
        ],
    ]);

    // 2. Theme Settings: Global Sections (CTA, FAQ, About, Advantages)
    acf_add_local_field_group([
        'key' => 'group_theme_global_sections',
        'title' => 'Глобальные секции',
        'fields' => [
            // CTA
            [
                'key' => 'field_cta_tab',
                'label' => 'Секция CTA (Заявка)',
                'type' => 'tab',
            ],
            [
                'key' => 'field_cta_badge',
                'label' => 'Бейдж CTA',
                'name' => 'cta_badge',
                'type' => 'text',
                'default_value' => 'Срочная помощь',
            ],
            [
                'key' => 'field_cta_title',
                'label' => 'Заголовок CTA',
                'name' => 'cta_title',
                'type' => 'text',
                'default_value' => 'Запишитесь на первичную консультацию адвоката',
            ],
            [
                'key' => 'field_cta_subtitle',
                'label' => 'Подзаголовок CTA',
                'name' => 'cta_subtitle',
                'type' => 'textarea',
                'default_value' => 'Оставьте заявку прямо сейчас — проведу детальный правовой анализ вашей ситуации и предложу эффективный план решения.',
            ],
            // About
            [
                'key' => 'field_about_tab',
                'label' => 'Секция Об адвокате',
                'type' => 'tab',
            ],
            [
                'key' => 'field_about_badge',
                'label' => 'Бейдж',
                'name' => 'about_badge',
                'type' => 'text',
                'default_value' => 'Об адвокате',
            ],
            [
                'key' => 'field_about_title',
                'label' => 'Заголовок',
                'name' => 'about_title',
                'type' => 'text',
                'default_value' => 'Ежов Антон Валентинович',
            ],
            [
                'key' => 'field_about_subtitle',
                'label' => 'Подзаголовок',
                'name' => 'about_subtitle',
                'type' => 'text',
                'default_value' => 'Регистрационный номер 77/10522 в реестре адвокатов г. Москвы',
            ],
            [
                'key' => 'field_about_quote',
                'label' => 'Цитата адвоката',
                'name' => 'about_quote',
                'type' => 'textarea',
                'default_value' => '«Каждое дело требует индивидуальной стратегии. Моя цель — защитить ваши интересы и добиться справедливого результата законными методами.»',
            ],
            [
                'key' => 'field_about_text',
                'label' => 'Текст биографии/опыта',
                'name' => 'about_text',
                'type' => 'wysiwyg',
                'default_value' => '<p>Более 20 лет успешной юридической практики в судах общей юрисдикции и арбитраже. Специализируюсь на защите прав граждан и бизнеса по сложным жилищным, семейным, имущественным и арбитражным спорам.</p>',
            ],
            // FAQ
            [
                'key' => 'field_faq_tab',
                'label' => 'Секция FAQ',
                'type' => 'tab',
            ],
            [
                'key' => 'field_faq_items',
                'label' => 'Вопросы и ответы (FAQ)',
                'name' => 'faq_items',
                'type' => 'repeater',
                'layout' => 'row',
                'button_label' => 'Добавить вопрос-ответ',
                'sub_fields' => [
                    [
                        'key' => 'field_faq_q',
                        'label' => 'Вопрос',
                        'name' => 'question',
                        'type' => 'text',
                    ],
                    [
                        'key' => 'field_faq_a',
                        'label' => 'Ответ',
                        'name' => 'answer',
                        'type' => 'textarea',
                    ],
                ],
            ],
            // Service Detail Block (Разрешаю любые конфликты...)
            [
                'key' => 'field_service_detail_tab',
                'label' => 'Блок решения споров (Услуги)',
                'type' => 'tab',
            ],
            [
                'key' => 'field_global_service_detail_list',
                'label' => 'Пункты списка по умолчанию',
                'name' => 'global_service_detail_list',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => 'Добавить пункт',
                'sub_fields' => [
                    [
                        'key' => 'field_global_sdl_text',
                        'label' => 'Текст пункта',
                        'name' => 'item_text',
                        'type' => 'text',
                    ],
                ],
            ],
            [
                'key' => 'field_global_service_detail_notice',
                'label' => 'Текст желтой плашки с подсказкой (i)',
                'name' => 'global_service_detail_notice',
                'type' => 'textarea',
                'default_value' => 'Большинство дел закрываю на досудебной стадии — быстро и без лишних затрат. Если суд неизбежен, веду его «под ключ» до фактического исполнения, включая работу с приставами.',
            ],
            [
                'key' => 'field_global_service_detail_image',
                'label' => 'Изображение по умолчанию',
                'name' => 'global_service_detail_image',
                'type' => 'image',
                'return_format' => 'id',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'theme-global-sections',
                ],
            ],
            [
                [
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'theme-general-settings',
                ],
            ],
        ],
    ]);

    // 2b. Fields for Service Category (Категория услуг)
    acf_add_local_field_group([
        'key' => 'group_service_category_fields',
        'title' => 'Настройки категории услуги',
        'fields' => [
            [
                'key' => 'field_service_cat_description',
                'label' => 'Расширенное описание',
                'name' => 'service_cat_description',
                'type' => 'textarea',
                'instructions' => 'Описание категории / подкатегории для вывода на странице и в карточках каталога',
            ],
            [
                'key' => 'field_service_cat_hero_subtitle',
                'label' => 'Подзаголовок для первого экрана',
                'name' => 'service_cat_hero_subtitle',
                'type' => 'textarea',
                'instructions' => 'Дополнительный текст под заголовком в шапке страницы',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'taxonomy',
                    'operator' => '==',
                    'value' => 'service_category',
                ],
            ],
        ],
    ]);

    // 3. Fields for Service (Услуга)
    acf_add_local_field_group([
        'key' => 'group_service_fields',
        'title' => 'Параметры услуги',
        'fields' => [
            [
                'key' => 'field_service_price',
                'label' => 'Стоимость услуги',
                'name' => 'service_price',
                'type' => 'text',
                'placeholder' => 'от 30 000 ₽',
            ],
            [
                'key' => 'field_service_term',
                'label' => 'Срок выполнения',
                'name' => 'service_term',
                'type' => 'text',
                'placeholder' => 'от 5 дней',
            ],
            [
                'key' => 'field_service_short_desc',
                'label' => 'Краткое описание для карточки',
                'name' => 'service_short_desc',
                'type' => 'textarea',
            ],
            [
                'key' => 'field_service_features',
                'label' => 'Особенности / Преимущества услуги',
                'name' => 'service_features',
                'type' => 'repeater',
                'button_label' => 'Добавить преимущество',
                'sub_fields' => [
                    [
                        'key' => 'field_feat_title',
                        'label' => 'Заголовок',
                        'name' => 'title',
                        'type' => 'text',
                    ],
                    [
                        'key' => 'field_feat_text',
                        'label' => 'Описание',
                        'name' => 'text',
                        'type' => 'textarea',
                    ],
                ],
            ],
            [
                'key' => 'field_service_steps',
                'label' => 'Этапы работы',
                'name' => 'service_steps',
                'type' => 'repeater',
                'button_label' => 'Добавить этап',
                'sub_fields' => [
                    [
                        'key' => 'field_step_num',
                        'label' => 'Номер этапа',
                        'name' => 'number',
                        'type' => 'text',
                    ],
                    [
                        'key' => 'field_step_title',
                        'label' => 'Название этапа',
                        'name' => 'title',
                        'type' => 'text',
                    ],
                    [
                        'key' => 'field_step_desc',
                        'label' => 'Описание этапа',
                        'name' => 'description',
                        'type' => 'textarea',
                    ],
                ],
            ],
            [
                'key' => 'field_service_included',
                'label' => 'Что входит в стоимость',
                'name' => 'service_included',
                'type' => 'repeater',
                'button_label' => 'Добавить пункт',
                'sub_fields' => [
                    [
                        'key' => 'field_inc_text',
                        'label' => 'Пункт',
                        'name' => 'item',
                        'type' => 'text',
                    ],
                ],
            ],
            // Resolution section (Разрешаю любые конфликты...)
            [
                'key' => 'field_service_tab_resolution',
                'label' => 'Блок решения споров',
                'type' => 'tab',
            ],
            [
                'key' => 'field_service_custom_title',
                'label' => 'Индивидуальный заголовок блока',
                'name' => 'service_custom_title',
                'type' => 'text',
                'instructions' => 'Оставьте пустым для стандартного «Разрешаю любые конфликты, связанные с направлением «[Название]»»',
            ],
            [
                'key' => 'field_service_custom_list',
                'label' => 'Индивидуальный список пунктов',
                'name' => 'service_custom_list',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => 'Добавить пункт',
                'instructions' => 'Если список не заполнен, используются стандартные 4 пункта',
                'sub_fields' => [
                    [
                        'key' => 'field_scl_text',
                        'label' => 'Текст пункта',
                        'name' => 'item_text',
                        'type' => 'text',
                    ],
                ],
            ],
            [
                'key' => 'field_service_custom_notice',
                'label' => 'Индивидуальный текст плашки с подсказкой (i)',
                'name' => 'service_custom_notice',
                'type' => 'textarea',
                'instructions' => 'Оставьте пустым, чтобы использовать стандартный текст',
            ],
            [
                'key' => 'field_service_custom_image',
                'label' => 'Индивидуальное изображение',
                'name' => 'service_custom_image',
                'type' => 'image',
                'return_format' => 'id',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'service',
                ],
            ],
        ],
    ]);

    // 4. Fields for Cases (Кейс)
    acf_add_local_field_group([
        'key' => 'group_cases_fields',
        'title' => 'Параметры судебного дела (Кейса)',
        'fields' => [
            [
                'key' => 'field_case_number',
                'label' => 'Номер дела',
                'name' => 'case_number',
                'type' => 'text',
                'placeholder' => 'Дело № 2-1452/2023',
            ],
            [
                'key' => 'field_case_court',
                'label' => 'Суд / Инстанция',
                'name' => 'case_court',
                'type' => 'text',
                'placeholder' => 'Московский городской суд',
            ],
            [
                'key' => 'field_case_result',
                'label' => 'Результат / Сумма выигрыша',
                'name' => 'case_result',
                'type' => 'text',
                'placeholder' => 'Взыскано 4 850 000 ₽',
            ],
            [
                'key' => 'field_case_client_type',
                'label' => 'Кто обратился',
                'name' => 'case_client_type',
                'type' => 'text',
                'placeholder' => 'Собственник квартиры / Компания',
            ],
            [
                'key' => 'field_case_problem',
                'label' => 'Суть проблемы',
                'name' => 'case_problem',
                'type' => 'textarea',
            ],
            [
                'key' => 'field_case_actions',
                'label' => 'Что сделал адвокат',
                'name' => 'case_actions',
                'type' => 'textarea',
            ],
            [
                'key' => 'field_case_decision',
                'label' => 'Решение суда / Итог',
                'name' => 'case_decision',
                'type' => 'textarea',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'cases',
                ],
            ],
        ],
    ]);

    // 5. Fields for Reviews (Отзывы)
    acf_add_local_field_group([
        'key' => 'group_review_fields',
        'title' => 'Параметры отзыва',
        'fields' => [
            [
                'key' => 'field_review_author',
                'label' => 'Имя / ФИО клиента',
                'name' => 'review_author',
                'type' => 'text',
            ],
            [
                'key' => 'field_review_author_role',
                'label' => 'Статус / Должность / Город',
                'name' => 'review_author_role',
                'type' => 'text',
            ],
            [
                'key' => 'field_review_rating',
                'label' => 'Оценка (звезд)',
                'name' => 'review_rating',
                'type' => 'number',
                'default_value' => 5,
                'min' => 1,
                'max' => 5,
            ],
            [
                'key' => 'field_review_date',
                'label' => 'Дата отзыва',
                'name' => 'review_date',
                'type' => 'text',
                'placeholder' => '15 мая 2024',
            ],
            [
                'key' => 'field_review_service',
                'label' => 'Услуга, по которой оставлен отзыв',
                'name' => 'review_service_name',
                'type' => 'text',
            ],
            [
                'key' => 'field_review_text',
                'label' => 'Текст отзыва',
                'name' => 'review_text',
                'type' => 'textarea',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'review',
                ],
            ],
        ],
    ]);

    // 6. Fields for Consultations (Q&A)
    acf_add_local_field_group([
        'key' => 'group_consultation_fields',
        'title' => 'Информация о вопросе',
        'fields' => [
            [
                'key' => 'field_consult_author',
                'label' => 'Имя клиента',
                'name' => 'consultation_author',
                'type' => 'text',
                'default_value' => 'Михаил',
            ],
            [
                'key' => 'field_consult_date',
                'label' => 'Дата вопроса',
                'name' => 'consultation_date',
                'type' => 'text',
                'default_value' => '12 февраля 2024',
            ],
            [
                'key' => 'field_consult_question',
                'label' => 'Текст вопроса',
                'name' => 'consultation_question',
                'type' => 'textarea',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'consultation',
                ],
            ],
        ],
    ]);

    // 7. Fields for Articles (Статьи / Блог)
    acf_add_local_field_group([
        'key' => 'group_article_fields',
        'title' => 'Параметры статьи',
        'fields' => [
            [
                'key' => 'field_art_read_time',
                'label' => 'Время чтения',
                'name' => 'article_read_time',
                'type' => 'text',
                'default_value' => '5 мин',
            ],
            [
                'key' => 'field_art_video_url',
                'label' => 'VK Video URL (iframe embed)',
                'name' => 'article_video_url',
                'type' => 'url',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'post',
                ],
            ],
        ],
    ]);

    // 8. User Custom Settings (Author Box in Articles)
    acf_add_local_field_group([
        'key' => 'group_user_author_settings',
        'title' => 'Настройки автора (Блок автора в статьях)',
        'fields' => [
            [
                'key'           => 'field_author_photo',
                'label'         => 'Фотография автора',
                'name'          => 'author_photo',
                'type'          => 'image',
                'return_format' => 'url',
                'instructions'  => 'Аватар автора для блока внизу статей (рекомендуется квадратное фото).',
            ],
            [
                'key'           => 'field_author_full_name',
                'label'         => 'ФИО автора (для статей)',
                'name'          => 'author_full_name',
                'type'          => 'text',
                'placeholder'   => 'Ежов Антон Валентинович',
                'instructions'  => 'Отображаемое имя в блоке «Автор статьи: ...». Если не указано, берется имя из профиля.',
            ],
            [
                'key'           => 'field_author_credentials',
                'label'         => 'Регалии и описание автора',
                'name'          => 'author_credentials',
                'type'          => 'textarea',
                'rows'          => 3,
                'placeholder'   => 'Адвокат с 23-летним стажем, регистрационный № 77/10522 в реестре адвокатов г. Москвы.',
                'instructions'  => 'Описание опыта, стажа и специализации автора для блока статьи.',
            ],
        ],
        'location' => [
            [
                [
                    'param'    => 'user_form',
                    'operator' => '==',
                    'value'    => 'all',
                ],
            ],
        ],
    ]);
});
