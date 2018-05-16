<?php

    $active = false;

    if($active) {


        $json = '{"1":"mesa02","2":"MESA03","3":"mesa02","37":"Loren01","4":"asd","5":"asd","6":"asd","36":"asd"}';


        if (!empty($json)) {
            $userPassDecoded = json_decode($json, true);
            global $wpdb;
            foreach ($userPassDecoded as $userId => $userPass) {
                $wpdb->update('users', ['password' => encryptIt($userPass)], ['id' => $userId]);
                d('Updated user id: ' . $userId);
            }
        } else {
            global $wpdb;
            $users = $wpdb->get_results('SELECT * FROM users');

            foreach ($users as $user) {
                if ($user->password) {
                    $pass = decryptIt($user->password);
                    $userPassDecoded[$user->id] = $pass;
                }
            }

            d(json_encode($userPassDecoded));
        }

    }

?>
<small>page.php</small>