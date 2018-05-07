<?php 

    //dd($_SESSION);
    /*
        Template Name: MercadoPago Notifications Callback
    */
    date_default_timezone_set("America/Argentina/Buenos_Aires");
    setlocale(LC_TIME, 'es_ES');
    

    // mercadopago callback
    if(!empty($_GET)){
        /* https://mesaprofesional.com/consulta/
         *  ?collection_id=3648530423
         *  &collection_status=approved
         *  &preference_id=315309602-ae81829c-16ac-4d1a-9083-9d3cda4d08b5
         *  &external_reference=null
         *  &payment_type=credit_card
         *  &merchant_order_id=713174452
        */
        $mp = inicializeMercadoPago();

        global $wpdb;
        $allowedFields = ['collection_id','collection_status','preference_id','external_reference','payment_type','merchant_order_id'];
        foreach($_GET as $field => $value){
            if(in_array($field, $allowedFields)){
                if($field == 'external_reference'){
                    $_GET['user_id'] = filter_var($_GET[$field], FILTER_SANITIZE_STRING);
                    unset($_GET[$field]);
                } else {
                    $_GET[$field] = filter_var($_GET[$field], FILTER_SANITIZE_STRING);
                }
                
            } else {
                unset($_GET[$field]);
            }
        }

        $_GET['timestamp'] = date('Y-m-d H:i:s', time());

        $paymentInfo = $mp->get_payment_info($_GET['collection_id']);
        if(!empty($paymentInfo['status']) && $paymentInfo['status'] == 200){
            $product = $wpdb->get_row('SELECT * FROM products WHERE title = "'.$paymentInfo['response']['description'].'"');
            $_GET['product_id'] = $product->id;
        }

        $_GET['payment_info'] = json_encode($paymentInfo);
        
        //dd($_GET);
        $hasPayment = $wpdb->get_row('SELECT * FROM payments WHERE collection_id = "'.$_GET['collection_id'].'"');
        if(!$hasPayment){
            $success = $wpdb->insert('payments', $_GET);
            $paymentStatus = (!empty($paymentInfo['status']) && $paymentInfo['status'] == 200) ? $paymentInfo['response']['status'] : '-';
            if (session_status() == PHP_SESSION_NONE){
                session_start();
            }
            $_SESSION['paymentStatus'] = $paymentStatus;
            $_SESSION['paymentStatusTime'] = time();
            //setcookie('paymentStatus', $paymentStatus, time()+300);
        }
        unset($paymentInfo);

        if(!empty($product) && $product->type == 'subscription'){
            wp_redirect(home_url('/'),301);
        } elseif(!empty($product) && $product->type == 'pack'){
            wp_redirect(home_url('/consulta'),301);
        }
            

    }




    // mercadopago callback
    /*if(!empty($_GET)){
        /* https://mesaprofesional.com/consulta/
         *  ?collection_id=3648530423
         *  &collection_status=approved
         *  &preference_id=315309602-ae81829c-16ac-4d1a-9083-9d3cda4d08b5
         *  &external_reference=null
         *  &payment_type=credit_card
         *  &merchant_order_id=713174452
        */
    /*    global $wpdb;
        $allowedFields = ['collection_id','collection_status','preference_id','external_reference','payment_type','merchant_order_id'];
        foreach($_GET as $field => $value){
            if(in_array($field, $allowedFields)){
                if($field == 'external_reference'){
                    $_GET['user_id'] = filter_var($_GET[$field], FILTER_SANITIZE_STRING);
                    unset($_GET[$field]);
                } else {
                    $_GET[$field] = filter_var($_GET[$field], FILTER_SANITIZE_STRING);
                }
                
            } else {
                unset($_GET[$field]);
            }
        }

        $_GET['timestamp'] = date('Y-m-d H:i:s', time());

        $paymentInfo = $mp->get_payment_info($_GET['collection_id']);
        if(!empty($paymentInfo['status']) && $paymentInfo['status'] == 200){
            $product = $wpdb->get_row('SELECT * FROM products WHERE title = "'.$paymentInfo['response']['description'].'"');
            $_GET['product_id'] = $product->id;
        }

        $_GET['payment_info'] = json_encode($paymentInfo);
        unset($paymentInfo);

        
        //dd($_GET);
        $hasPayment = $wpdb->get_row('SELECT * FROM payments WHERE collection_id = "'.$_GET['collection_id'].'"');
        if(!$hasPayment){
            $success = $wpdb->insert('payments', $_GET);    
        }

    }*/






?>
