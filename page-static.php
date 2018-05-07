<?php


/*
 * Template Name: Static Page
 */


get_header();

if(have_posts()):
    while (have_posts()): the_post(); ?>

        <article class="post page new">

            <div class="col-xs-12 no-padding">
                <div class="col-xs-12 title-column">
                    <h3><?php the_title();?></h3>
                </div>

                <div class="col-xs-12 text-column">
                    <?php the_content();?>
                </div>
            </div>

        </article>

    <?php endwhile;

else:
    echo '<p>No content found</p>';

endif;

get_footer();

?>