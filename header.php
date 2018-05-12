<!DOCTYPE html>
<html <?php language_attributes();?>>

    <head>
        <meta charset="<?php bloginfo('charset');?>">
        <meta name="viewport" content="width=device-width">
        <title><?php bloginfo('name');?></title>
        <?php wp_head();?>
    </head>

    <body <?php body_class();?>>

    <?php checkRules();?>

    <script>
    window.fbAsyncInit = function() {
      FB.init({
        appId      : '1641925725889638',
        cookie     : true,
        xfbml      : true,
        version    : 'v2.12'
      });
      FB.AppEvents.logPageView();
      FB.getLoginStatus(function(response) {
          console.log(response);
          if (response.status === 'connected') {
            FB.api('/me?fields=first_name,last_name,picture,age_range', function (user) {
                  console.log('estado actual');
                  console.log(user);
            });
          }
      }, {scope: 'email,public_profile'});
    };

    (function(d, s, id){
       var js, fjs = d.getElementsByTagName(s)[0];
       if (d.getElementById(id)) {return;}
       js = d.createElement(s); js.id = id;
       js.src = "https://connect.facebook.net/es_LA/sdk.js";
       fjs.parentNode.insertBefore(js, fjs);
     }(document, 'script', 'facebook-jssdk'));
    </script>

    <!--<script>
      function checkLoginState(){
          FB.getLoginStatus(function(response) {
              statusChangeCallback(response);
          });
      }

    </script>-->

    <?php error_reporting(E_ALL);ini_set('display_errors', 1);?>

    <header class="site-header">

        <div class="col-xs-12 no-padding">
            <div class="col-xs-2 pull-left">
              <a class="pull-left" href="<?php echo home_url();?>"><img class="mp-logo" src="<?php echo home_url('/wp-content/uploads/2018/03/mp-logo-transparency2.png');?>" title="Mesa Profesional" alt="Mesa Profesional"/></a>
            </div>



        <!--                --><?php //if(is_page('acerca-de')) { ?>
        <!--                    - Como empezamos...-->
        <!--                --><?php //};?>

            <?php if($userData = isLoggedIn()):?>
            <div class="col-xs-3 pull-right user-data">
                <div class="pull-right dropdown">
                    <div class="pull-right dropbtn" id="user-data-menu">
                        <?php $userDataArray = explode('-', $userData);?>
                        <?php global $wpdb;$user = $wpdb->get_row('SELECT * FROM users WHERE id = "'.$userDataArray[0].'"');?>
                        <?php if($user->picture_url):?>
                        <img src="<?php echo home_url($user->picture_url);?>" width="20"/>
                        <?php else:?>
                        <i class="fas fa-user pull-left"></i>
                        <?php endif;?>
                        <span class="user-name">
                            <?php echo $userDataArray[1].' '.$userDataArray[2];?>
                        </span>
                        <i class="fas fa-angle-down pull-right"></i>
                        <?php if(isRol('professional',$userData)):?>
                            <button class="btn btn-primary btn-lg hidden" id="uploadModalBtn" data-toggle="modal" data-target="#uploadModal"></button>
                            <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
                                <div class="vertical-alignment-helper">
                                    <div class="modal-dialog vertical-align-center upload-modal">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"></span></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="col-xs-4 no-padding" id="profile-image">
                                                    <?php if($user->picture_url):?>
                                                        <img src="<?php echo home_url($user->picture_url);?>" id="profile-image-img"/>
                                                    <?php else:?>
                                                        <img src="" class="hidden" id="profile-image-img"/>
                                                        <i class="far fa-user"></i>
                                                    <?php endif;?>
                                                    <span class="ld ld-ring ld-spin hidden"></span>
                                                </div>
                                                <div class="col-xs-8 no-padding profile-image-form">
                                                    <form method="post" enctype="multipart/form-data" id="upload-profile-image-form">
                                                        <label for="profile-image-input">Seleccione una imagen para subir</label>
                                                        <small class="col-xs-12 no-padding"><span class="asterisk">*</span><i> Sólo se permiten formatos .jpg, .jpeg y .png</i></small>
                                                        <input type="file" class="pull-left" id="profile-image-input" name="profile-image-input" accept=".jpg, .jpeg, .png" value="Buscar imagen">
                                                        <div class="col-xs-12 no-padding action-buttons">
                                                            <button class="col-xs-4 btn btn-success ld-ext-right hovering submit">
                                                                Aceptar
                                                                <span class="ld ld-ring ld-spin"></span>
                                                            </button>
                                                            <a href="" class="col-xs-4 close-link"><small>Cerrar ventana</small></a>
                                                            <small class="col-xs-12 no-padding status"></small>
                                                        </div>

                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif;?>
                    </div>
                    <div class="dropdown-content" aria-labelledby="user-data-menu">
                        <?php $key = encryptIt('userid-'.$userDataArray[0]);?>
                        <?php if(isRol('professional',$userData)):?>
                        <a class="col-xs-12 dropdown-item dropdown-picture-item">Subir una foto de perfil</a>
                        <?php endif;?>
                        <a class="col-xs-12 dropdown-item" href="<?php echo home_url('/questions?key='.$key);?>">Mis consultas</a>
                        <a class="col-xs-12 dropdown-item" href="<?php echo home_url('/logout');?>">Cerrar sesión</a>
                    </div>
                </div>
            </div>
            <?php else:?>
            <div class="col-xs-3 pull-right sign">
                <div class="pull-right dropdown" id="sign-block">
                  <a class="pull-left dropbtn" href="<?php echo site_url('/login');?>">Iniciar sesión | Crear cuenta</a>
                  <div class="dropdown-content" aria-labelledby="sign-block" role="menu">
                    <div class="form-wrap">
                      <?php set_query_var('modal',1);?>
                      <?php get_template_part('login-form-post');?>
                      <?php get_template_part('login-form');?>
                      <?php set_query_var('modal',0);?>
                    </div>
                  </div>
                </div>
            </div>
            <?php endif;?>
        </div>

        <div class="col-xs-12 no-padding site-nav-wrap">
            <nav class="site-nav">
                <div class="logo hidden">
                    <i class="fas fa-trademark"></i>
                    <span>Mesa Profesional</span>
                </div>
                <?php if($userData && isRol('admin',$userData)):?>
                  <?php wp_nav_menu(['theme_location' => 'admin']);?>
                <?php else:?>
                  <?php wp_nav_menu(['theme_location' => 'primary']);?>
                <?php endif;?>
            </nav>
        </div>

        <?php if(!empty($_GET)):?>
            <?php foreach($_GET as $key => $value):?>
                <?php $_GET[$key] = filter_var($value, FILTER_SANITIZE_STRING);?>
            <?php endforeach;?>
        <?php endif;?>

    </header>

    <main class="col-xs-12 no-padding" id="content">
        <div class="container <?php echo (!empty($_SERVER['REDIRECT_URL']) && strpos($_SERVER['REDIRECT_URL'],'/dashboard') !== false) ? 'dashboard' : '';?>">

            <?php get_template_part('breadcrumb');?>

            
            		
