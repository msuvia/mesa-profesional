<?php get_header();?>

<div class="row">


        <div class="col-xs-12">
            <?php
            if(have_posts()): ?>

                <h6>single.php</h6>

                <h2><?php print_r(getPostType());?></h2>

                <?php
                while (have_posts()): the_post(); ?>

                    <article class="post">
                        <h2><a href="<?php the_permalink();?>"><?php the_title();?></a></h2>

                        <p class="post-info">
                            <?php print_r(getPostInfo());?>
                        </p>

                        <?php the_post_thumbnail('banner-image');?>

                        <p>
                            <?php the_content();?>
                        </p>
                    </article>

                <?php endwhile;

            else:
                echo '<p>Index.php: No content found</p>';

            endif;
            ?>
        </div>


</div>


<?php get_footer();?>