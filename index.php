<?php

    // activing accounts /accounts?token=fb7b0c251e03cf2ce1744cac1ebf38fd0154be80
    global $wpdb;
    $usersTable = 'users';
    if(strpos($_SERVER['REDIRECT_URL'],'/accounts') !== false && !empty($_GET['token'])){
        $userToken = filter_var($_GET['token'], FILTER_SANITIZE_STRING);
        $user = $wpdb->get_row( 'SELECT * FROM '.$usersTable.' WHERE token = "'.$userToken.'"');
        if($user){
            $wpdb->update($usersTable, ['actived_account'=>1], ['token'=>$userToken]);

            if($user->first_time){
                if (session_status() == PHP_SESSION_NONE){ session_start(); }
                $_SESSION['welcome'] = true;
                $wpdb->update($usersTable, ['first_time'=>0], ['token'=>$userToken]);
            }
        }
        wp_redirect(home_url('/'),301);     // Permanently redirect
    }



    // logout
    if(strpos($_SERVER['REDIRECT_URL'],'/logout') !== false){
        logOut();
        wp_redirect(home_url('/login'),301);    // Permanently redirect
    }


    get_header();

?>





<div class="row">


        <div class="col-xs-12">
            <?php
            if(have_posts()): ?>

                <h6>index.php</h6>

                <h2><?php print_r(getPostType());?></h2>

                <?php
                while (have_posts()): the_post();

                    get_template_part('content', get_post_format());
                
                endwhile;

            else:
                
                echo 'index.php: no posts';
                

            endif;
            ?>
        </div>


</div>


<?php get_footer();?>