<?php
/**
 * 404 Error Template
 *
 * @package BelanAgency
 */

get_header();
?>

<section class="section" style="padding: 140px 0; text-align: center;">
    <div class="container">
        <h1 style="font-size: 72px; font-weight: 700; color: #ce494c; margin-bottom: 20px;">404</h1>
        <h2 class="section__title" style="margin-bottom: 20px;">Страница не найдена</h2>
        <p style="font-size: 18px; color: #555; max-width: 600px; margin: 0 auto 30px;">
            Возможно, страница была удалена или перенесена по новому адресу. Перейдите на главную страницу или воспользуйтесь меню.
        </p>
        <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn--primary btn--red btn--arrow">Вернуться на главную</a>
    </div>
</section>

<?php
get_footer();
