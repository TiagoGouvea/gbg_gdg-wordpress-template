<?php get_header(); ?>

<?php if (have_posts()) : ?>


    <?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
    <?php
    if (is_page()){
        require_once 'single.php';
    } elseif (is_post_type_archive('tgo_evento')) {
        // Evento
    } elseif (is_category()) { ?>
        /* If this is a category archive */
        <h4>Archiv der Kategorie &#8216;<?php echo single_cat_title(); ?>&#8216; </h4>
        <?php /* If this is a daily archive */
    } elseif (is_day()) { ?>
        <h4>Tagesarchiv f&uuml;r den <?php the_time('j. F Y'); ?></h4>
        <?php /* If this is a monthly archive */
    } elseif (is_month()) { ?>
        <h4>Monatsarchiv f&uuml;r <?php the_time('F Y'); ?></h4>
        <?php /* If this is a yearly archive */
    } elseif (is_year()) { ?>
        <h4>Jahresarchiv f&uuml;r <?php the_time('Y'); ?></h4>
        <?php /* If this is an author archive */
    } elseif (is_author()) { ?>
        <h4>Autoren Archiv</h4>
        <?php /* If this is a paged archive */
    } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>

    <?php } ?>

    <?php while (have_posts()) : the_post(); ?>
        <div class="post" id="post-<?php the_ID(); ?>">
            <div class="entry">
                    $evento = Eventos::obterPorId(get_the_ID()); ?>
                    <h2>
                        <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>">
                            <?php the_title(); ?>
                        </a>
                    </h2>
                    <?php if ($evento->noFuturo) echo "<h3 style='padding:0 0 0 0;'>" . PLib::dataRelativa($evento->data, false, false) . " as " . $evento->hora . " no " . $evento->local()->post_title . "</h3>"; ?></a>
                    <a href="<?php the_permalink() ?>" title="<?php the_title(); ?>">
                        <?php the_post_thumbnail('medium'); ?>
                    </a>
                    <?php the_excerpt(); ?>
            </div>
            <div class="clear"></div>
        </div>
    <?php endwhile; ?>
<?php else : ?>
    <div class="post" id="post-<?php the_ID(); ?>">
        <h2>Oooops</h2>

        <div class="entry">

            <p>So wie es aussieht gibt es hier keine Seite, die zu Ihrer Suchanfrage passt.</p>

            <p>Bitte nutzen Sie die Navigation, um die richtige Seite zu finden.</p>
        </div>
        <div class="clear"></div>
    </div>
<?php endif; ?>

<?php get_footer(); ?>