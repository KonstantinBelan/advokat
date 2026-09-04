<?php
/**
 * About Section Template Part
 *
 * @package BelanAgency
 */

if (!defined('ABSPATH')) {
    exit;
}

$about_title = belan_option('about_title', '<span>ЕЖОВ АНТОН</span> ВАЛЕНТИНОВИЧ');
$about_subtitle = belan_option('about_subtitle', 'адвокат, которому доверяют суды и клиенты');
?>
<!-- about -->
<section class="section about bg-gray" id="about">
    <div class="container">
        <div class="about__wrapper">
            <div class="about__leftside">
                <div class="about__header">
                    <h2 class="about__title"><?php echo wp_kses_post($about_title); ?></h2>
                    <span class="about__subtitle"><?php echo esc_html($about_subtitle); ?></span>
                </div>
                <hr class="about__divider">
                <div class="about__content">
                    <ul class="about__list">
                        <li class="about__item">
                            <svg class="about__item-icon" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="9" cy="9" r="9" fill="#B93836" />
                                <path d="M5.5 9.2L7.8 11.5L12.5 6.8" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span>Член Адвокатской палаты, стаж с&nbsp;2002 года.</span>
                        </li>
                        <li class="about__item">
                            <svg class="about__item-icon" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="9" cy="9" r="9" fill="#B93836" />
                                <path d="M5.5 9.2L7.8 11.5L12.5 6.8" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span>Окончил Московскую государственную юридическую академию.</span>
                        </li>
                        <li class="about__item">
                            <svg class="about__item-icon" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="9" cy="9" r="9" fill="#B93836" />
                                <path d="M5.5 9.2L7.8 11.5L12.5 6.8" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span>Начинал в Гильдии Российских адвокатов, с&nbsp;2002 года – свой адвокатский кабинет.</span>
                        </li>
                        <li class="about__item">
                            <svg class="about__item-icon" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="9" cy="9" r="9" fill="#B93836" />
                                <path d="M5.5 9.2L7.8 11.5L12.5 6.8" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span>Участвовал в 4 делах в Конституционном Суде РФ по обжалованию федеральных законов.</span>
                        </li>
                        <li class="about__item">
                            <svg class="about__item-icon" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="9" cy="9" r="9" fill="#B93836" />
                                <path d="M5.5 9.2L7.8 11.5L12.5 6.8" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span>Эксперт на Первом канале, «Россия-1», ТВЦ, радио «Эхо Москвы», «Говорит Москва».</span>
                        </li>
                    </ul>
                </div>
                <hr class="about__divider">
                <div class="about__facts">
                    <div class="about__facts-title">
                        <p>Специализация:</p>
                    </div>
                    <ul class="about__facts-items">
                        <li class="about__facts-item"><p>Жилищные и земельные споры</p></li>
                        <li class="about__facts-item"><p>Уголовная защита</p></li>
                        <li class="about__facts-item"><p>Семейное и наследственное право</p></li>
                        <li class="about__facts-item"><p>Банкротство</p></li>
                        <li class="about__facts-item"><p>Корпоративные конфликты</p></li>
                    </ul>
                </div>
            </div>
            <div class="about__rightside">
                <div class="about__image">
                    <?php belan_picture('about', 'Ежов Антон Валентинович', '', '(max-width: 768px) 100vw, 476px', 476, 560); ?>
                </div>
            </div>
        </div>
    </div>
</section>
