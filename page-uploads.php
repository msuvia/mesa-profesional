<?php
/**
 * Created by PhpStorm.
 * User: Marcelo Suvia
 * Date: 11/5/18
 * Time: 1:24 PM
 */

/*
 *  Template Name: Uploads Template
 */

// upload profile image
if(!empty($_FILES)){

    $userFile = $_FILES['userfile-0'];


    $sourcePath = $_FILES['file']['tmp_name'];       // Storing source path of the file in a variable
    $targetPath = "upload/" . $_FILES['file']['name']; // Target path where file is to be stored
    move_uploaded_file($sourcePath, $targetPath);






    if(isset($userFile["type"]))
    {
        $validextensions= ["jpeg", "jpg", "png"];
        $mimesAllowed   = ['image/png','image/jpg','image/jpeg'];
        $fileSize       = filter_var($userFile["size"], FILTER_SANITIZE_NUMBER_INT);
        $fileError      = filter_var($userFile["error"], FILTER_SANITIZE_NUMBER_INT);
        $fileType       = filter_var($userFile["type"], FILTER_SANITIZE_STRING);
        $fileFrom       = filter_var($userFile["tmp_name"], FILTER_SANITIZE_STRING);
        $fileName       = filter_var($userFile["name"], FILTER_SANITIZE_STRING);
        $fileLimitSize  = 100000;                    //Approx. 100kb
        $fileTo         = 'wp-content/uploads/profile-images/';


        list($fileName, $fileExtension) = explode(".", $userFile["name"]);
        unset($temp);

        if(in_array($fileType, $mimesAllowed) && in_array($fileExtension, $validextensions) && $fileSize < $fileLimitSize) {
            if ($fileError > 0){
                echo json_encode(['status'=>'ERROR','message'=>'El archivo no es válido']);
                die;
            }
            else
            {
                $userData = isLoggedIn();
                $userDataArray = explode('-',$userData);
                global $wpdb;
                $user = $wpdb->get_row('SELECT * FROM users WHERE id = "'.$userDataArray[0].'"');


//                if (file_exists($fileTo . $_FILES["file"]["name"])) {
//                    echo $_FILES["file"]["name"] . " <span id='invalid'><b>already exists.</b></span> ";
//                }

                $targetPath = $fileTo.implode('.',['user-'.$user->id,$fileExtension]);
                move_uploaded_file($fileFrom,$targetPath);
                $success = $wpdb->update('users',['picture_url'=>$targetPath],['id'=>$user->id]);
                if($success){
                    echo json_encode(['status'=>'OK']);
                    die;
                }
            }
        }
        else
        {
            echo json_encode(['status'=>'ERROR','message'=>'Formato de archivo no válido']);
            die;
        }
    }

} else {
    wp_redirect(home_url('/'),301);
}