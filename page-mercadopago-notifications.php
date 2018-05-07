<?php 

/*
 *   Template Name: MercadoPago Notifications Template
*/
    //require_once ('mercadopago.php');
    //$mp = new MP('2727473275354197', '6GKVvNoTCgjOIGJ3uswy6YkdJeiQuZ0U');   // Marce
    //$mp = new MP('940055906080849', 'HKa2jhEnSWbMkIYGDFowR8Ue4WdIVqtu');  // Gaby
    //$mp = new MP('645938759318228','qFGxblc8MUH1bplK9oUmLZpSbnqoQ8X7');     // vendedor test TETE5313202
    //$mp->sandbox_mode(TRUE);

    //dd($mp->create_test_user());


    if(!empty($_GET)){
        $topic      = (!empty($_GET['topic']))  ? $_GET['topic']    : "";
        $paymentId  = (!empty($_GET['id']))     ? $_GET['id']       : "";

        $response   = [
            'topic'     => $topic,
            'paymentId' => $paymentId
        ];

        global $wpdb;
        $success=$wpdb->insert('payment_log',[
            'application'   => 'MERCADOPAGO',
            'operation'     => 'NOTIFICATION',
            'response'      => json_encode($response)
        ]);

    }
?>
