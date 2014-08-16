<?php get_header(); ?>
    <div id="content" class="clearfix">
        <div class="content_left">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <div class="post" id="post-<?php the_ID(); ?>">
                        <div class="entry">
                            <h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>

                            <div class="clearfix"></div>
                            <?php the_post_thumbnail('medium'); ?>
                            <div class="clearfix"></div>

                            <br>

                            <?php the_content('[...]'); ?>

                        </div>
                        <div class="clear"></div>
                    </div>
                    <div>&nbsp;</div>

                    <div class="clear">&nbsp;</div>

                <?php endwhile; ?>
            <?php else : ?>
                h2>Oooops</h2>
                <div class="entry">
                    <p>So wie es aussieht gibt es hier keine Seite, die zu Ihrer Suchanfrage passt.</p>
                    <p>Bitte nutzen Sie die Navigation, um die richtige Seite zu finden.</p>
                </div>
            <?php endif; ?>

            <!--### Kommentare ###-->
        </div>

        <div class="clear"></div>
    </div><!--### ende content ###-->

<?php get_footer(); ?>