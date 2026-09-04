<?php
/**
 * Expertise Section Template Part
 *
 * @package BelanAgency
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- expertise -->
<section class="section expertise">
    <div class="container">
        <div class="section__header">
            <h2 class="section__title section__title--center">Моя экспертиза будет полезна,&nbsp;если&nbsp;вы:</h2>
        </div>
        <div class="section__grid section__grid--2">
            <!-- 01 -->
            <div class="section__card expertise__card">
                <div class="expertise__image">
                    <?php belan_picture('expertise-1', 'Столкнулись со сложным спором о недвижимости или долей в квартире', '', '(max-width: 768px) 100vw, 575px', 575, 260); ?>
                </div>
                <div class="expertise__content">
                    <h3 class="expertise__title">Столкнулись со сложным спором о&nbsp;недвижимости или долей в&nbsp;квартире</h3>
                    <p class="expertise__description">Помогу вселиться без затяжных судов, взыскать ущерб после залива и&nbsp;оспорить незаконную сделку. Я&nbsp;–&nbsp;один из известных в&nbsp;России специалистов по долевой собственности.</p>
                </div>
            </div>
            <!-- 02 -->
            <div class="section__card expertise__card">
                <div class="expertise__image">
                    <?php belan_picture('expertise-2', 'Нуждаетесь в защите по уголовному или административному делу', '', '(max-width: 768px) 100vw, 575px', 575, 260); ?>
                </div>
                <div class="expertise__content">
                    <h3 class="expertise__title">Нуждаетесь в защите по&nbsp;уголовному или административному делу</h3>
                    <p class="expertise__description">Огромный опыт по делам о&nbsp;мошенничестве, экономических преступлениях и делам частного обвинения, где есть фальсификации. Защищаю как подсудимых, так и&nbsp;потерпевших.</p>
                </div>
            </div>
            <!-- 03 -->
            <div class="section__card expertise__card">
                <div class="expertise__image">
                    <?php belan_picture('expertise-3', 'Предприниматель или компания в споре с контрагентами или государством', '', '(max-width: 768px) 100vw, 575px', 575, 260); ?>
                </div>
                <div class="expertise__content">
                    <h3 class="expertise__title">Предприниматель или&nbsp;компания в споре с&nbsp;контрагентами или государством</h3>
                    <p class="expertise__description">Взыскание долгов, корпоративные конфликты, банкротство, налоговые и&nbsp;таможенные споры. Я&nbsp;представляю интересы бизнеса в арбитражных судах.</p>
                </div>
            </div>
            <!-- 04 -->
            <div class="section__card expertise__card">
                <div class="expertise__image">
                    <?php belan_picture('expertise-4', 'Участник семейного, наследственного или трудового конфликта', '', '(max-width: 768px) 100vw, 575px', 575, 260); ?>
                </div>
                <div class="expertise__content">
                    <h3 class="expertise__title">Участник семейного, наследственного или трудового конфликта</h3>
                    <p class="expertise__description">Грамотный раздел имущества, оспаривание завещания, восстановление на работе – обеспечу защиту ваших законных прав и&nbsp;интересов.</p>
                </div>
            </div>
        </div>
        <div class="section__button">
            <a href="<?php echo esc_url(home_url('/services/')); ?>" class="btn btn--primary btn--width btn--red btn--arrow">Выбрать услугу</a>
        </div>
    </div>
</section>
