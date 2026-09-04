<?php
/**
 * Advantages Section Template Part
 *
 * @package BelanAgency
 */

if (!defined('ABSPATH')) {
    exit;
}

$adv_title = belan_field('advantages_title', false, 'Почему клиенты доверяют мне&nbsp;дела любой сложности?');
?>
<!-- advantages -->
<section class="section advantages bg-gray">
    <div class="container">
        <div class="section__header">
            <h2 class="section__title section__title--center"><?php echo wp_kses_post($adv_title); ?></h2>
        </div>
        <div class="section__grid section__grid--3">
            <!-- 01 -->
            <div class="section__card advantages__card">
                <div class="advantages__card-number">01</div>
                <h3 class="advantages__card-title">Работа до результата в&nbsp;высших&nbsp;инстанциях</h3>
                <p class="advantages__card-text">Успешная практика в Конституционном Суде РФ (4 дела по&nbsp;обжалованию законов), арбитраже и Верховном Суде России.</p>
            </div>
            <!-- 02 -->
            <div class="section__card advantages__card">
                <div class="advantages__card-number">02</div>
                <h3 class="advantages__card-title">Широкий спектр специализаций</h3>
                <p class="advantages__card-text">Жилищные и земельные споры, семейное право, наследство, уголовная защита (в&nbsp;т.ч.&nbsp;мошенничество с&nbsp;недвижимостью), корпоративные конфликты, банкротство и&nbsp;взыскание долгов.</p>
            </div>
            <!-- 03 -->
            <div class="section__card advantages__card">
                <div class="advantages__card-number">03</div>
                <h3 class="advantages__card-title">Нестандартные решения для «безнадёжных» ситуаций</h3>
                <p class="advantages__card-text">В ситуациях с&nbsp;долевой собственностью помогаю вселиться в квартиру без многомесячных судов, используя механизм самозащиты гражданских прав.</p>
            </div>
            <!-- 04 -->
            <div class="section__card advantages__card">
                <div class="advantages__card-number">04</div>
                <h3 class="advantages__card-title">Медийная экспертность</h3>
                <p class="advantages__card-text">Постоянный эксперт на «Первом канале», «Россия», ТВЦ, радио «Эхо Москвы» и «Свобода». Автор публикаций в профильных изданиях.</p>
            </div>
            <!-- 05 -->
            <div class="section__card advantages__card">
                <div class="advantages__card-number">05</div>
                <h3 class="advantages__card-title">Реальные решения вместо долгих&nbsp;обещаний</h3>
                <p class="advantages__card-text">Ценю ваше время и нацелен на конкретный, измеримый результат, будь то вселение в&nbsp;квартиру, оправдание по уголовному делу&nbsp;или взыскание долга.</p>
            </div>
            <a href="<?php echo esc_url(home_url('/consultation/')); ?>" class="section__card advantages__card advantages__card--button">
                <h3 class="advantages__card-title">Оценить шансы моего дела&nbsp;бесплатно</h3>
            </a>
        </div>
    </div>
</section>
