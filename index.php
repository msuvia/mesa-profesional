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


?>
