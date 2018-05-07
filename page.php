<?php


    if(!empty($_GET) && $_GET['p']==='p1'){
        $userPassDecoded = [
            '1' => 'mesa02',
            '2' => 'MESA03',
            '4' => 'asd',
            '5' => 'asd',
            '6' => 'asd'
        ];


        global $wpdb;
        $users = $wpdb->get_results('SELECT * FROM users');


        if(empty($userPassDecoded)){
            foreach($users as $user){
                if($user->password){
                    $pass = decryptIt($user->password);
                    d('User: '.$user->first_name.' '.$user->last_name. ', id: '.$user->id.', pass: ' . $pass);
                    $userPassDecoded[$user->id] = $pass;
                }
            }
        } else {
            foreach($userPassDecoded as $userId => $userPass){
                $wpdb->update('users',['password'=>encryptIt($userPass)],['id'=>$userId]);
                d('Updated user id: '.$userId);
            }
        }
    }



?>
<small>page.php</small>