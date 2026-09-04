<?php
/**
 * FAQ Section Template Part
 *
 * @package BelanAgency
 */

if (!defined('ABSPATH')) {
    exit;
}

$faq_items = belan_option('faq_items');
?>
<!-- faq -->
<section class="section faq" id="faq">
    <div class="container">
        <div class="section__header">
            <h2 class="section__title section__title--center">Ответы на вопросы</h2>
        </div>
        <div class="faq__wrapper">
            <?php if (!empty($faq_items) && is_array($faq_items)) : ?>
                <?php foreach ($faq_items as $index => $item) : ?>
                    <details class="faq__item" <?php echo ($index === 1) ? 'open' : ''; ?>>
                        <summary class="faq__question">
                            <?php echo esc_html($item['question']); ?>
                        </summary>
                        <div class="faq__answer">
                            <p><?php echo esc_html($item['answer']); ?></p>
                        </div>
                    </details>
                <?php endforeach; ?>
            <?php else : ?>
                <!-- 01 -->
                <details class="faq__item">
                    <summary class="faq__question">
                        Вы занимаетесь только уголовными делами?
                    </summary>
                    <div class="faq__answer">
                        <p>Нет, моя практика охватывает широкий спектр отраслей права: жилищные и земельные споры, семейное и наследственное право, банкротство, а также защита бизнеса и корпоративные споры в арбитражных судах.</p>
                    </div>
                </details>
                <!-- 02 -->
                <details class="faq__item" open>
                    <summary class="faq__question">
                        Вы беретесь за дела, которые уже ведут другие адвокаты без успеха?
                    </summary>
                    <div class="faq__answer">
                        <p>Да, часто ко мне обращаются за «вторым мнением» или для «спасения» уже проигрываемого дела. Мой опыт в высших судебных инстанциях позволяет находить новые аргументы.</p>
                    </div>
                </details>
                <!-- 03 -->
                <details class="faq__item">
                    <summary class="faq__question">
                        Можно ли нанять вас для участия в допросе, если я прохожу только как свидетель?
                    </summary>
                    <div class="faq__answer">
                        <p>Да, присутствие адвоката на допросе в статусе свидетеля критически важно, так как процессуальный статус может измениться в любой момент. Я обеспечу соблюдение ваших законных прав.</p>
                    </div>
                </details>
                <!-- 04 -->
                <details class="faq__item">
                    <summary class="faq__question">
                        Можете ли вы вести дело дистанционно?
                    </summary>
                    <div class="faq__answer">
                        <p>Да, современные технологии и электронное правосудие позволяют вести многие процессы и консультации дистанционно, независимо от вашего местонахождения.</p>
                    </div>
                </details>
                <!-- 05 -->
                <details class="faq__item">
                    <summary class="faq__question">
                        В чем ваше главное преимущество перед другими адвокатами?
                    </summary>
                    <div class="faq__answer">
                        <p>23 года непрерывной практики, личное участие в 4 делах Конституционного Суда РФ, нестандартный подход к сложным ситуациям и нацеленность на конкретный практический результат.</p>
                    </div>
                </details>
                <!-- 06 -->
                <details class="faq__item">
                    <summary class="faq__question">
                        Вы работаете только по Москве?
                    </summary>
                    <div class="faq__answer">
                        <p>Основная практика сосредоточена в Москве и Московской области, однако по сложным и значимым делам возможен выезд в любые регионы России и участие в высших судебных инстанциях.</p>
                    </div>
                </details>
                <!-- 07 -->
                <details class="faq__item">
                    <summary class="faq__question">
                        Работаете ли вы с юридическими лицами?
                    </summary>
                    <div class="faq__answer">
                        <p>Да, я оказываю полный комплекс юридических услуг для бизнеса: представительство в арбитраже, абонентское обслуживание, налоговые споры и сопровождение банкротства предприятий.</p>
                    </div>
                </details>
            <?php endif; ?>
        </div>
    </div>
</section>
