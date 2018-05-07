<article class="post <?php if(has_post_thumbnail()) { ?> has-thumbnail <?php } ?>">
    <div class="post-thumbnail">
        <a href="<?php the_permalink();?>"><?php the_post_thumbnail('small-thumbnail');?></a>
    </div>

    <h2><a href="<?php the_permalink();?>"><?php the_title();?></a></h2>

    <p class="post-info">
        <?php print_r(getPostInfo());?>
    </p>

    <?php if(is_search() || is_archive()){ ?>       <!-- search.php || archive.php -->

        <p>
            <!--<?php the_content();?>-->
            <?php echo get_the_excerpt();?>
            <a href="<?php the_permalink();?>">Leer más &raquo;</a>
        </p>


    <?php } else {

        if($post->post_excerpt()) { ?>
            <p>
                <!--<?php the_content();?>-->
                <?php echo get_the_excerpt();?>
                <a href="<?php the_permalink();?>">Leer más &raquo;</a>
            </p>
        <?php } else { 
            the_content();
        }


    }?>
</article>