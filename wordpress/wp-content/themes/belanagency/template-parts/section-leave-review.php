<?php
/**
 * Leave Review Section Template Part
 *
 * @package BelanAgency
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- leave review -->
<section class="section leave-review">
    <div class="container">
        <div class="leave-review__card">
            <h2 class="leave-review__title">Оставьте свой отзыв</h2>
            <form class="leave-review__form belan-review-form" action="#" method="POST">
                <div class="leave-review__fields">
                    <div class="leave-review__inputs">
                        <input type="text" name="author" placeholder="Введите имя" required autocomplete="name">
                        <input type="text" name="role" placeholder="Ваш статус / город / род занятий" autocomplete="organization">
                    </div>
                    <div class="leave-review__textarea">
                        <textarea name="text" placeholder="Напишите ваш отзыв" required rows="4"></textarea>
                    </div>
                </div>
                <div class="leave-review__submit">
                    <button type="submit" class="btn btn--primary btn--red btn--arrow btn--width">
                        Оставить отзыв
                    </button>
                </div>
                <div class="form-feedback" style="display:none; margin-top:10px; font-size:14px; text-align:center;"></div>
                <p class="leave-review__consent">
                    Отправляя форму, Вы даете <a href="<?php echo esc_url(belan_option('site_privacy_url', '#')); ?>" target="_blank">Согласие на&nbsp;обработку персональных данных</a> и&nbsp;соглашаетесь с&nbsp;<a href="#" target="_blank">Пользовательским соглашением сайта</a>
                </p>
            </form>
        </div>
    </div>
</section>
