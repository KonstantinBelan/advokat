<?php
/**
 * Main Template File (Fallback)
 *
 * @package BelanAgency
 */

get_header();
?>

<section class="section" style="padding: 100px 0;">
    <div class="container">
        <?php if (have_posts()) : ?>
            <div class="blog-page__grid">
                <?php while (have_posts()) : the_post(); ?>
                    <article class="articles-card">
                        <h2 class="articles-card__title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        <div class="articles-card__description">
                            <?php the_excerpt(); ?>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            <?php the_posts_pagination(); ?>
        <?php else : ?>
            <h2>Ничего не найдено</h2>
            <p>Записей по вашему запросу не обнаружено.</p>
        <?php endif; ?>
    </div>
</section>

<?php
get_footer();
