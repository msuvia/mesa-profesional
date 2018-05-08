<?php

function inicializeMercadoPago(){
    require_once ('mercadopago.php');
    //$mp = new MP('2727473275354197', '6GKVvNoTCgjOIGJ3uswy6YkdJeiQuZ0U');   // Marce
    //$mp = new MP('940055906080849', 'HKa2jhEnSWbMkIYGDFowR8Ue4WdIVqtu');  // Gaby
    $mp = new MP('645938759318228','qFGxblc8MUH1bplK9oUmLZpSbnqoQ8X7');     // vendedor test TETE5313202
    //$mp->sandbox_mode(TRUE);
    return $mp;
}


function getRefreshByType($type){
    global $wpdb;
    return $wpdb->get_row('SELECT * FROM refresh WHERE type = "'.$type.'"');
}

function theme_styles(){
    wp_enqueue_style('bootstrap',   get_template_directory_uri() . '/bootstrap.min.css');
    wp_enqueue_style('loading',     get_template_directory_uri() . '/loading.css');
    wp_enqueue_style('loading-btn', get_template_directory_uri() . '/loading-btn.css');
    //wp_enqueue_style('awesomerating', get_template_directory_uri() . '/awesomeRating.css');
    wp_enqueue_style('bootstrap-toggle', '/wp-includes/css/bootstrap/bootstrap-toggle.min.css');

    $cssRefresh = getRefreshByType('css');
    wp_enqueue_style('style', get_template_directory_uri() . '/style.css',[],$cssRefresh->refresh);
}
add_action('wp_enqueue_scripts', 'theme_styles');



function theme_scripts(){
    wp_enqueue_script('script',             get_template_directory_uri() . '/jquery-1.8.3.min.js');
    wp_enqueue_script('bootstrap',          get_template_directory_uri() . '/bootstrap.min.js');
    wp_enqueue_script('fontawesome',        get_template_directory_uri() . '/fontawesome-all.js');
    wp_enqueue_script('bootstrap-toggle', '/wp-includes/js/bootstrap/bootstrap-toggle.min.js');


    /*wp_localize_script( 'jquery', 'my_ajax_vars', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' )
    );*/
    //wp_enqueue_script('awesomerating', get_template_directory_uri() . '/awesomeRating.js');

    
    $jsRefresh = getRefreshByType('js');
    wp_enqueue_script('header',     get_template_directory_uri() . '/header.js',    [], $jsRefresh->refresh);
    wp_enqueue_script('main',       get_template_directory_uri() . '/main.js',      [], $jsRefresh->refresh);

    //if(strpos($_SERVER['REDIRECT_URL'],'/consulta') !== false || 
    //    strpos($_SERVER['REDIRECT_URL'],'/login') !== false ||
    //    strpos($_SERVER['REDIRECT_URL'],'/register') !== false){
    wp_enqueue_script('forms',      get_template_directory_uri() . '/forms.js',     [], $jsRefresh->refresh, true);
    wp_enqueue_script('facebook',   get_template_directory_uri() . '/facebook.js',  [], $jsRefresh->refresh, true);
    //}
    if(strpos($_SERVER['REDIRECT_URL'],'/dashboard') !== false){
        wp_enqueue_script('dashboard', get_template_directory_uri() . '/dashboard.js', [], $jsRefresh->refresh, true);
    }

    if(strpos($_SERVER['REDIRECT_URL'],'/altas') !== false){
        wp_enqueue_script('dashboard', get_template_directory_uri() . '/abm.js', [], $jsRefresh->refresh, true);
    }

    if(strpos($_SERVER['REDIRECT_URL'],'/questions') !== false){
        wp_enqueue_script('questions', get_template_directory_uri() . '/questions.js', [], $jsRefresh->refresh, true);
    }
}
add_action('wp_enqueue_scripts', 'theme_scripts');


/* Posts functions */

function getPostType(){
	if(is_category()){
        return single_cat_title();
    } elseif(is_tag()){
        return single_tag_title();
    } elseif(is_author()){
        the_post();
        rewind_posts();
        return 'Author Archives: ' . get_the_author();
    } elseif(is_day()){
        return 'Day Archives: ' . get_the_date();
    } elseif(is_month()){
        return 'Montly Archives: ' . get_the_date('F Y');
    } elseif(is_year()){
        return 'Yearly Archives: ' . get_the_date('Y');
    } else {
        return 'Simple';
    }
}

/*
 *	@return March 8th, 2018 3:16 am | por admin | Posted in New Category
 */
function getPostInfo(){
	$categories = get_the_category();
    $separator = ', ';
    $catsReturn = '';

    if($categories){
        foreach($categories as $category){
            $catsReturn .= '<a href="'.get_category_link($category->term_id).'">'.$category->cat_name.'</a>'.$separator;
        }
        $catsReturn = trim($catsReturn,$separator);
    }
	return the_time('F jS, Y g:i a').' | por <a href="'.get_author_posts_url(get_the_author_meta('ID')).'">'.get_the_author().'</a> | Posted in '.$catsReturn;
}


/* Customize excerpt word count length */
function custom_excerpt_length(){
	return 25;
}
add_filter('excerpt_length', 'custom_excerpt_length');



//add_theme_support('post-thumbnails'); // isn't no correct by WordPress
// Theme setup
function learningWordPress_setup(){
	// Navigation Menus
	register_nav_menus([
	    'primary'   => __('Primary Menu'),
        'admin'     => __('Admin Menu')
	    //'footer'    => __('Footer Menu')
	]);

	/* Add feature image support */
	add_theme_support('post-thumbnails');
	add_image_size('small-thumbnail', 180, 120, true);
	add_image_size('banner-image', 920, 610, array('left', 'top'));

	add_theme_support('post-formats', ['aside', 'gallery', 'link']);
}

add_action('after_setup_theme', 'learningWordPress_setup');


/*
 *	Add widget to theme
 */
function wpb_widgets_init() {
 
    register_sidebar( array(
        'name'          => 'Custom Header Widget Area',
        'id'            => 'custom-header-widget',
        'before_widget' => '<div class="chw-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="chw-title">',
        'after_title'   => '</h2>',
    ) );
 
}
add_action( 'widgets_init', 'wpb_widgets_init' );


/*
 *  Add a widget to WordPress panel
 */
function ourWidgetsInit(){
    register_sidebar([
        'name'          => 'Sidebar',
        'id'            => 'sidebar1',
        'before_widget' => '<div class="widget-item">',
        'after_widget'  => '</div>',
        'before_title'  => '<h5 class="widget-title">',
        'after_title'   => '</h5>'
    ]);

    register_sidebar([
        'name'          => 'Footer Area 1',
        'id'            => 'footer1',
        'before_widget' => '<div class="widget-item">',
        'after_widget'  => '</div>',
        'before_title'  => '<h5 class="widget-title">',
        'after_title'   => '</h5>'
    ]);

    register_sidebar([
        'name'          => 'Footer Area 2',
        'id'            => 'footer2',
        'before_widget' => '<div class="widget-item">',
        'after_widget'  => '</div>',
        'before_title'  => '<h5 class="widget-title">',
        'after_title'   => '</h5>'
    ]);

    register_sidebar([
        'name'          => 'Footer Area 3',
        'id'            => 'footer3',
        'before_widget' => '<div class="widget-item">',
        'after_widget'  => '</div>',
        'before_title'  => '<h5 class="widget-title">',
        'after_title'   => '</h5>'
    ]);

    register_sidebar([
        'name'          => 'Footer Area 4',
        'id'            => 'footer4',
        'before_widget' => '<div class="widget-item">',
        'after_widget'  => '</div>',
        'before_title'  => '<h5 class="widget-title">',
        'after_title'   => '</h5>'
    ]);
}
add_action('widgets_init','ourWidgetsInit');


/*
 *  Function that generate a new token 
 */
function generateToken($length = 10){
    return bin2hex(random_bytes($length));
}

/*
 * Encrypt a string
 */
function encryptIt($q) {
    //$cryptKey   = 'qJB0rGtIn5UB1xG03efyCp';
    //$cryptKey   = 'qJB0rGtIn5UB1xG';
    //$cryptKey   = 'nDk6dSh3vcX8';
    $cryptKey   = '97438027782374892';
    $qEncoded   = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), $q, MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ) );
    return( $qEncoded );
}


/*
 * Decrypt a string
 */
function decryptIt($q) {
    //$cryptKey     = 'qJB0rGtIn5UB1xG03efyCp';
    //$cryptKey   = 'qJB0rGtIn5UB1xG';
    //$cryptKey   = 'nDk6dSh3vcX8';
    $cryptKey       = '97438027782374892';
    $qDecoded   = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), base64_decode( $q ), MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ), "\0");
    return( $qDecoded );
}

/*
 * Debug functions
 */
function d($s){
    echo '<pre style="color:#666">';
    print_r($s);
    echo '</pre>';
    $backtrace = debug_backtrace();
    $file = $backtrace[0]['file'];
    $line = $backtrace[0]['line'];
    echo "Called from the file: {$file}, Line: {$line}<br><br>";
}

function dd($s){
    d($s);
    die;
}



/*
 * Function that allow include HTML code format into mails
 */
function setContentType(){
    return "text/html";
}
add_filter( 'wp_mail_content_type','setContentType' );



/*
 * Check if user is logged in
 */
function isLoggedIn(){
    if (session_status() == PHP_SESSION_NONE){
        session_start();
    }

    $sessionVar = getLoginSessionVar();
    //d($sessionVar);
    //dd($_SESSION[$sessionVar]);

    if(empty($_SESSION[$sessionVar])){
        return false;
    }

    return decryptIt($_SESSION[$sessionVar]);
}



function isAdmin($userData = null){
    if(!$userData){
        return false;
    }

    global $wpdb;
    $userData = explode('-',$userData);
    $user = $wpdb->get_row('SELECT * FROM users WHERE email="'.$userData[3].'"');
    if(!$user){
        return false;
    }

    return ($user->rol == 1);
}


/*
 *  Login function
 */
function logIn($loginData){
    $return = ['error' => false, 'message' => ''];
    if(empty($loginData)){
        $return['error'] = 'Los datos ingresados son inexistentes';
        return $return;
    }

    if(empty($loginData['email'])){
        $return['error'] = 'Los datos ingresados son inexistentes';
        return $return;
    }

    if(!empty($loginData['loginType']) && $loginData['loginType'] == 'local' && empty($loginData['password'])){
        $return['error'] = 'Los datos ingresados son inexistentes';
        return $return;
    }

    //d($loginData);
    // checking user email
    global $wpdb;
    $user = $wpdb->get_row( 'SELECT * FROM users WHERE email = "'.$loginData['email'].'"');

    //d($user);
    if(!$user){
        $return['error'] = 'Los datos ingresados son inexistentes';
        return $return;
    }

    if(!$user->actived_account){
        $return['error'] = 'Su cuenta no se encuentra activa. Por favor, verifique su correo y siga las instrucciones para su activación';
        return $return;
    }

    //dd(decryptIt($user->password));

    // checking user password
    if(!empty($loginData['loginType']) && $loginData['loginType'] == 'local' && decryptIt($user->password) !== $loginData['password']){
        $return['error'] = 'Los datos ingresados son incorrectos';
        return $return;
    }
    
    if (session_status() == PHP_SESSION_NONE){
        session_start();
        $_SESSION['starting'] = 'login';
    }
    
    $_SESSION[getLoginSessionVar()] = encryptIt($user->id.'-'.$user->first_name.'-'.$user->last_name.'-'.$user->email);
    return $return;
}


/*
 *  Login function
 */
function getLoginSessionVar(){
    $randKey = '0iQx5oBk66oVZep';
    $retVar = md5($randKey);
    $retVar = 'usr_'.substr($retVar,0,10);
    return $retVar;
}


/*
 *  Login function
 */
function logOut(){
    if (session_status() == PHP_SESSION_NONE){
        session_start();
    }
    
    $sessionVar = getLoginSessionVar();
    
    $_SESSION[$sessionVar]=NULL;
    
    unset($_SESSION[$sessionVar]);
}


//allow redirection, even if my theme starts to send output to the browser
function do_output_buffer() {
    ob_start();
}
add_action('init', 'do_output_buffer');


/*
 *  Taxonomies
 */
function people_init() {
    // create a new taxonomy
    register_taxonomy(
        'people',
        'post',
        array(
            'label' => __( 'People' ),
            'rewrite' => array( 'slug' => 'person' ),
            'capabilities' => array(
                'assign_terms' => 'edit_guides',
                'edit_terms' => 'publish_guides'
            )
        )
    );
}
add_action( 'init', 'people_init' );


/*
 * Validate register form function
 */
function validateRegisterForm(){
    $response = ['data' => [], 'error' => false];
    if (empty($_POST)){
        $response['error'] = true;
        return $response;
    }

    $formErrorMessages = [
        'emptyFirstName'        => 'Por favor, ingrese su nombre',
        'emptyLastName'         => 'Por favor, ingrese su apellido',
        'emptyEmail'            => 'Por favor, ingrese su email',
        'invalidEmail'          => 'Por favor, ingrese un email válido',
        'emptyPassword'         => 'Por favor, ingrese una contraseña',
        'requiredField'         => 'Campo obligatorio'
    ];

    if(empty($_POST['first-name']) || trim($_POST['first-name']) === '') {
        $response['data']['firstNameError'] = $formErrorMessages['emptyFirstName'];
        $response['error'] = true;
    } else {
        $response['data']['firstName'] = trim($_POST['first-name']);
    }

    if(empty($_POST['last-name']) || trim($_POST['last-name']) === '') {
        $response['data']['lastNameError'] = $formErrorMessages['emptyLastName'];
        $response['error'] = true;
    } else {
        $response['data']['lastName'] = trim($_POST['last-name']);
    }

    if(empty($_POST['email']) || trim($_POST['email']) === '')  {
        $response['data']['emailError'] = $formErrorMessages['emptyEmail'];
        $response['error'] = true;
    } else if (!preg_match("/^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$/i", trim($_POST['email']))) {
        $response['data']['invalidEmailError'] = $formErrorMessages['invalidEmail'];
        $response['error'] = true;
    } else {
        $response['data']['email'] = trim($_POST['email']);
    }

    if(empty($_POST['password']) || trim($_POST['password']) === '') {
        $response['data']['passwordError'] = $formErrorMessages['emptyPassword'];
        $response['error'] = true;
    } else {
        $response['data']['password'] = trim($_POST['password']);
    }

    if(empty($_POST['type']) || trim($_POST['type']) === '') {
        $response['data']['type'] = encryptIt('user');
    } else {
        $response['data']['type'] = trim($_POST['type']);
    }

    return $response;
}


/*
 * Validate login form function
 */
function validateLoginForm(){
    $response = ['data' => [], 'error' => false];
    if (empty($_POST)){
        $response['error'] = true;
        return $response;
    }
        
    $formErrorMessages = [
        'emptyEmail'            => 'Por favor, ingrese su email',
        'invalidEmail'          => 'Por favor, ingrese un email válido',
        'emptyPassword'         => 'Por favor, ingrese una contraseña',
        'requiredField'         => 'Campo obligatorio',
        'incompleteForm'        => 'Por favor, complete los campos obligatorios'
    ];

    //d($_POST);
    if(empty($_POST['email']) || trim($_POST['email']) === '')  {
        $response['data']['emailError'] = $formErrorMessages['emptyEmail'];
        $response['error'] = $formErrorMessages['incompleteForm'];
    } else if (!preg_match("/^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$/i", trim($_POST['email']))) {
        $response['data']['invalidEmailError'] = $formErrorMessages['invalidEmail'];
        $response['error'] = $formErrorMessages['incompleteForm'];;
    } else {
        $response['data']['email'] = trim($_POST['email']);
    }

    if(empty($_POST['password']) || trim($_POST['password']) === '') {
        $response['data']['passwordError'] = $formErrorMessages['emptyPassword'];
        $response['error'] = $formErrorMessages['incompleteForm'];
    } else {
        $response['data']['password'] = trim($_POST['password']);
    }

    $response['data']['loginType'] = 'local';

    //d($data);
    return $response;
}


/*
 * Validation rules
 */
function checkRules(){
    global $wpdb;

    //  1st rule: if is logged user:
    //  "/login" redirect to "/"  
    //  "/profesionales/login" redirect to "profesionales/dashboard"
    if($userData = isLoggedIn()){
        $userData = explode('-', $userData);
        $userData = $wpdb->get_row('SELECT * FROM users WHERE id='.$userData[0]);

        if($userData->rol == 2){
            //  professionals
            if(!empty($_SERVER['REDIRECT_URL']) && strpos($_SERVER['REDIRECT_URL'],'/profesionales/login') !== false){
                wp_redirect('/profesionales/dashboard', 301);
            }
        }

        if($userData->rol == 3){
            //  users
            if(!empty($_SERVER['REDIRECT_URL']) && strpos($_SERVER['REDIRECT_URL'],'/login') !== false){
                wp_redirect('/', 301);
            }
        }
    }
    else {
        
    }
}


/*
 * Send answer confirmation email
 */
function sendAnswerEmail($question){
    $emailTo = $question->email;
    if (!isset($emailTo) || ($emailTo == '') ){
        $emailTo = get_option('admin_email');
    }

    $key = encryptIt('token-'.$question->token);
    $questionUrl = home_url('/questions?key='.$key);

    $subject = '¡Su pregunta ha sido respondida en Mesa Profesional!';
    
    $body  = 'Hola '.$question->first_name.' '.$question->last_name. '!<br><br>';
    $body .= 'Hemos detectado que uno de nuestros profesionales ha respondido su pregunta.<br><br>';
    $body .= 'Puede ver y calificar la respuesta haciendo click <a href="'.$questionUrl.'">aquí</a>.<br><br>';
    $body .= 'Si el link no funciona, copie este código en la barra del navegador:<br>';
    $body .= $questionUrl.'<br><br>';
    $body .= 'Recuerde que puede calificar la respuesta de nuestro profesional para que, de esta manera, podamos brindarle la mejor información.<br><br>';
    $body .= 'Lo saluda atentamente<br>';
    $body .= 'El equipo de <a href="'.home_url('/').'">Mesa Profesional</a>.<br><br>';
    $body = htmlspecialchars_decode($body);

    wp_mail($emailTo, $subject, $body, $headers);
    $success = true;

}


/*function breadcrumb() {
    if (!is_home()) {
        echo '<a class="removed_link" href="'.home_url('/').'" title="'.get_option('home').'">'.the_title().'</a>';
        //echo '<a class="removed_link" href="'.home_url('/').'" title="'.get_option('home').'">'.bloginfo('name').'</a> » ';
        if (is_category() || is_single()) {
            the_category('title_li=');
            if (is_single()) {
                echo " » ";
                the_title();
            }
        }
        elseif (is_page()) {
            echo the_title();
        }
    }
}*/


/*
 * Check if user has still questions to do
 */
function hasQuestions(){
    $limitFreeQuestions = 5;

    global $wpdb;
    if(!$userData = isLoggedIn()){
        return false;
    }

    $userDataArray = explode('-',$userData);
    $questionResponse = $wpdb->get_row('SELECT count(*) as questions_qty FROM questions q LEFT JOIN users u ON u.id = q.user_id WHERE email = "'.$userDataArray[3].'" GROUP BY email');

    if(!$questionResponse){
        return true;
    }

    if($questionResponse->questions_qty < $limitFreeQuestions){
        return $limitFreeQuestions - $questionResponse->questions_qty;
    }

    // check payments for questions
    $paymentsResponse = $wpdb->get_results('SELECT * FROM payments pa LEFT JOIN products pr ON pa.product_id = pr.id WHERE user_id = "'.$userDataArray[0].'"');

    if(!$paymentsResponse){
        return false;
    }

    $topQuestionsLimit = $limitFreeQuestions;
    foreach($paymentsResponse as $payment){
        if($payment->questions_qty){
            $topQuestionsLimit += intval($payment->questions_qty);
        }
    }

    if($questionResponse->questions_qty < $topQuestionsLimit){
        return $topQuestionsLimit - $questionResponse->questions_qty;
    }

    return false;
}




/*
 * Create a MercadoPago preference data
 */
function buildPreferenceData($data){
    $preference_data = [
        "items" => [
            [
                "id"            => $data['itemId'],//$subscription->id,
                "title"         => $data['itemTitle'],//$subscription->title,
                "description"   => $data['itemDescription'],//$subscription->description,
                "picture_url"   => $data['itemPictureUrl'],//$subscription->picture_url,
                "quantity"      => 1,
                "currency_id"   => "ARS", // Available currencies at: https://api.mercadopago.com/currencies
                "unit_price"    => floatval($data['itemPrice']),//$subscription->price)
            ]
        ],
        "payer" => [
            "name"      => $data['payerFirstName'],//$user->first_name,//$userDataArray[1],
            "surname"   => $data['payerLastName'],//$user->last_name,//$userDataArray[2],
            "email"     => $data['payerEmail']//$userEmail,//$user->email//$userDataArray[3]
        ],
        "back_urls" => [
            "success" => $data['urlSuccess'],//home_url('/mercadopago/callback'),
            "pending" => $data['urlPending'],//home_url('/mercadopago/callback'),
            "failure" => $data['urlFailure']//home_url('/mercadopago/callback')
        ],
        "auto_return" => "all",
        "payment_methods" => [
            "excluded_payment_methods"  => [],
            "excluded_payment_types"    => [],
            "installments"              => 12,
            "default_payment_method_id" => null,
            "default_installments"      => null
        ],
        "notification_url" => home_url('/mercadopago/notifications'),
        "external_reference" => $data['userId']//$user->id
    ];

    return $preference_data;
}



function sendClientFacebookRegisterEmail($data) {
    $link       = home_url('/');
    $subject    = '¡Gracias por registrarse a Mesa Profesional!';

//    $headers = "Reply-To: marcelo.suvia@gmail.com \r\n";
//    //$headers .= "CC: test@gmail.com\r\n";
//    $headers .= "MIME-Version: 1.0\r\n";
//    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

    $body  = 'Hola '.$data['firstName'].' '.$data['lastName']. '!<br><br>';
    $body .= 'Gracias por registrarte a Mesa Profesional.<br><br>';
    $body .= 'Usted ya se encuentra habilitado para realizar consultas a través de nuestra página web y obtener la mejor respuesta a su consulta.<br><br>';
    $body .= 'Si el link no funciona, copia este código en la barra del navegador:<br>';
    $body .= $link.'<br><br>';
    $body .= 'Lo saluda atentamente<br>';
    $body .= 'El equipo de <a href="'.home_url('/').'">Mesa Profesional</a>';
    $body = htmlspecialchars_decode($body);

    return wp_mail($data['to'], $subject, $body);
}


function sendClientRegisterEmail($data){
    $link = home_url('/accounts?token='.$data['accountToken']);
    $subject = '¡Gracias por registrarse a Mesa Profesional!';

    $body  = 'Hola '.$data['firstName'].' '.$data['lastName']. '!<br><br>';
    $body .= 'Gracias por registrarte a Mesa Profesional.<br><br>';
    $body .= 'Antes de empezar a realizar consultas en nuestro sitio es necesario que actives tu cuenta haciendo click <a href="'.$link.'">aquí</a>.<br><br>';
    $body .= 'Si el link no funciona, copia este código en la barra del navegador:<br>';
    $body .= $link.'<br><br>';
    $body .= 'Lo saluda atentamente<br>';
    $body .= 'El equipo de <a href="'.home_url('/').'">Mesa Profesional</a>';
    $body = htmlspecialchars_decode($body);

    return wp_mail($data['to'], $subject, $body);
}


function sendProfessionalRegisterEmail($data){
    $subject = '¡Gracias por registrarse a Mesa Profesional!';

    $body  = 'Hola '.$data['firstName'].' '.$data['lastName']. '!<br><br>';
    $body .= 'Muchas gracias por registrarse a Mesa Profesional.<br><br>';
    $body .= 'Recibimos su solicitud, en este momento se encuentra pendiente de moderación por parte de nuestros administradores y le enviaremos un email cuando los mismos aprueben su solicitud.<br><br>';
    $body .= 'Una vez aprobada, usted podrá abonar a través de MercadoPago la suscripción para comenzar a utilizar las funcionalidades de Mesa Profesional.<br><br>';
    $body .= 'Lo saluda atentamente<br>';
    $body .= 'El equipo de <a href="'.home_url('/').'">Mesa Profesional</a>';
    $body = htmlspecialchars_decode($body);

    return wp_mail($data['to'], $subject, $body);
}


function sendProfessionalSuccessfulAuthorizationEmail($data){
    $subject = '¡Su inscripción ha sido aprobada en Mesa Profesional!';

    $body  = 'Hola '.$data['firstName'].' '.$data['lastName']. '!<br><br>';
    $body .= 'Le damos la bienvenida a Mesa Profesional.<br><br>';
    $body .= 'Usted ha sido autorizado por nuestros administradores para empezar a contestar preguntas en Mesa Profesional. Recuerde que una persona es un potencial cliente suyo a corto plazo, por lo que las respuestas que usted brinde pueden ser muy importante en la consideración del cliente.<br><br>';
    $body .= 'Antes de empezar a contestar preguntas, es necesario que usted abone una suscripción a nuestro sitio, que le permitirá empezar a responder las consultas de nuestros clientes. Para abonar la suscripción haga click <a href="'.$data['linkForPayment'].'">aquí</a>.<br><br>';
    $body .= 'Si el link no funciona, por favor copie este código en la barra del navegador:<br>';
    $body .= $data['linkForPayment'].'<br><br>';
    $body .= 'Lo saluda atentamente<br>';
    $body .= 'El equipo de <a href="'.home_url('/').'">Mesa Profesional</a>';
    $body = htmlspecialchars_decode($body);

    return wp_mail($data['to'], $subject, $body);
}


function sendProfessionalDeniedAuthorizationEmail($data){
    $subject = 'Su inscripción ha sido denegada en Mesa Profesional';

    $body  = 'Hola '.$data['firstName'].' '.$data['lastName']. '!<br><br>';
    $body .= 'Antes que nada, muchas gracias por registrarte a Mesa Profesional.<br><br>';
    $body .= 'Lamentablemente le informamos que su inscripción ha sido denegada por nuestros administradores por motivos desconocidos. Lamentamos este inconveniente.<br><br>';
    $body .= 'Para saber el detalle de los motivos de su rechazo, por favor, contáctese con nuestros administradores.<br><br>';
    $body .= 'Lo saluda atentamente<br>';
    $body .= 'El equipo de <a href="'.home_url('/').'">Mesa Profesional</a>';
    $body = htmlspecialchars_decode($body);

    return wp_mail($data['to'], $subject, $body);
}


function sendClientConsultationEmail($data){
    $subject = '¡Gracias por su consulta en Mesa Profesional!';

    $body  = 'Hola '.$data['firstName'].' '.$data['lastName']. '!<br><br>';
    $body .= 'Gracias por consultarnos, hemos recibido su consulta y la estaremos analizando a la brevedad.<br><br>';
    $body .= 'Su pregunta fue la siguiente:<br><br>';
    $body .= '<i>'.$data['question'].'</i><br><br><br>';
    $body .= 'Cuando uno de nuestros profesionales responda su consulta, le avisaremos con un correo electrónico. A continuación, le damos un link a nuestra página donde podrá ver su consulta y verificar si le han respondido o no:<br>';
    $body .= $data['linkToDetail'].'<br><br>';
    $body .= '<b>Importante: Recuerde que las primeras 5 preguntas son gratuitas</b>, después puede comprar packs de 5 o 10 preguntas para seguir consultándonos.<br>';

    if($remainingQuestions = hasQuestions()){
        if($remainingQuestions == 1){
            $body .= 'Aún tiene '.$remainingQuestions.' pregunta restante.<br><br>';
        } else {
            $body .= 'Aún tiene '.$remainingQuestions.' preguntas restantes.<br><br>';
        }

    } else {
        $body .= 'Usted ya no dispone de preguntas gratuitas, puede aprovechar y comprar nuestros packs de 5 y 10 preguntas.<br><br>';
    }

    $body .= 'Lo saluda atentamente,<br>';
    $body .= 'El equipo de <a href="'.home_url('/').'">Mesa Profesional</a>';

    $body = htmlspecialchars_decode($body);

    return wp_mail($data['to'], $subject, $body);
}



;?>