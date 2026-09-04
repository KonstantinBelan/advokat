<?php
/**
 * Help Banner Section Template Part
 *
 * @package BelanAgency
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- help -->
<section class="section help">
    <div class="container">
        <div class="help__banner">
            <div class="help__content">
                <h2 class="help__title">Есть нестандартный вопрос? Спросите эксперта.</h2>
                <p class="help__subtitle">Если ваша ситуация не укладывается в стандартные рамки, задайте мне вопрос напрямую. Я лично проанализирую ситуацию и предложу стратегию защиты.</p>
                <div class="help__buttons">
                    <a href="<?php echo esc_url(home_url('/consultation/')); ?>" class="btn-ask btn--arrow">Задать вопрос адвокату</a>
                    <a href="<?php echo esc_url(home_url('/consultation/')); ?>" class="btn-faq">Посмотреть вопросы и ответы</a>
                </div>
            </div>
        </div>
    </div>
</section>
