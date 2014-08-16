<?php get_header(); ?>


        <div class="span7 pull-left" style="margin-bottom:20px;">
            <div class="widget Blog">
                <?php if (have_posts()) : ?>
                    <?php while (have_posts()) : the_post(); ?>
                        <div class="post" id="post-<?php the_ID(); ?>">
                            <div class="entry">

                                <?php if (get_post_type(get_the_ID()) == "tgo_evento"):

                                    $evento = Eventos::obterPorId(get_the_ID()); ?>
                                    <h3>
                                        <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>">
                                            <?php the_title(); ?>
                                        </a>
                                    </h3>
                                    <?php if ($evento->noFuturo) echo "<h3 style='padding:0 0 0 0;'>" . PLib::dataRelativa($evento->data, false, false) . " as " . $evento->hora . " no " . $evento->local()->post_title . "</h3>"; ?></a>
                                    <a href="<?php the_permalink() ?>" title="<?php the_title(); ?>">
                                        <?php the_post_thumbnail('medium'); ?>
                                    </a>
                                    <?php the_excerpt(); ?>

                                <?php else: ?>

                                    <h3>
                                        <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>">
                                            <?php the_title(); ?></a>
                                    </h3>

                                    <div class=" span7 date-section-wrap">
                                        <div class="span7 pull-left margin0">
                                            <p class="pull-left">
                                                <strong><?php echo __("Postado por", "posted_by"); ?></strong>
                                                <a href="<?php the_author_link();?>"><?php the_author(); ?></a>
                                                <strong><?php echo __("em", "on"); ?></strong>
                                                <?php the_date(); ?>
                                                <strong> | Tags:</strong>
                                                <a href="http://www.gdgaracaju.com.br/search/label/dojo" rel="tag">dojo</a>
                                                ,
                                                <a href="http://www.gdgaracaju.com.br/search/label/encontro" rel="tag">encontro</a>
                                                ,
                                                <a href="http://www.gdgaracaju.com.br/search/label/ruby" rel="tag">ruby</a>
                                            </p>
                                        </div>
                                    </div>

                                    <a href="<?php the_permalink() ?>" title="<?php the_title(); ?>">
                                        <?php the_post_thumbnail('medium'); ?>
                                    </a>

                                    <div class="post_excerpt">
                                        <?php the_excerpt(); ?>
                                    </div>
                                <?php endif; ?>

                            </div>
                            <div class="clear"></div>
                        </div>
                    <?php endwhile; ?>
                <?php else : ?>
                    <div class="post">
                        <h2>Ooops</h2>

                        <div class="entry">
                            <?php echo __("Nada encontrado...", "nothing_found"); ?>
                        </div>
                        <div class="clear"></div>
                    </div>
                <?php endif; ?>

                <div class="clear"></div>

            </div>
        </div>



        <?php require_once 'sidebar.php'; ?>




<?php get_footer(); ?>