<?php get_header();?>

<div class="row">


        <div class="col-xs-12">
            <?php
            if(have_posts()): ?>



                <h2>Search results for <?php the_search_query();?></h2>

                <h2><?php print_r(getPostType());?></h2>

                <?php
                while (have_posts()): the_post();

                    get_template_part('content', get_post_format());

                endwhile;

            else:
                echo '<p>Index.php: No content found</p>';

            endif;
            ?>
        </div>


</div>


<?php get_footer();?>