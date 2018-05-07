<?php 
    
    if(!empty($_POST)){
        
        $loginType=(!empty($_POST['loginType'])) ? $_POST['loginType'] : 'local';
        if($loginType=='local'){
            $validate = validateLoginForm();
            if(!$validate['error']){
                $login = logIn($validate['data']);
                if(!$login['error']){
                    // redirect to ?
                    if(!empty($_POST['from']) && $_POST['from'] == 'js'){
                        echo json_encode(['status'=>'OK']);die;
                    }
                    wp_redirect(home_url('/'),301);
                }
                else{
                    if(!empty($_POST['from']) && $_POST['from'] == 'js'){
                        echo json_encode(['status'=>'ERROR', 'message'=>$login['error']]);die;
                    }
                }
            }
        }
        elseif($loginType=='facebook'){
            $login = logIn($_POST['user']);
            if(!$login['error']){
                echo json_encode(['status'=>'OK']);die;
            }
            else {
                echo json_encode(['status'=>'ERROR', 'message' => $login['error']]);die;
            }
        }
    }
    
    get_header();

?>


<?php

if(have_posts()):
    while (have_posts()): the_post();?>
        
        <article class="page sign">
            <?php if(isset($login['error'])):?>
            <div class="col-xs-10 col-xs-offset-1 alert alert-warning" role="alert">
                <?php echo $login['error'];?>
            </div>
            <?php endif;?>

            <div class="col-xs-10 col-xs-offset-1 form-wrap box">
                <div class="col-xs-12">
                    <h4>Acceda a su cuenta y empiece a consultarnos</h4>
                </div>
                <div class="col-xs-12">
                    <div class="col-xs-6 mp-login-form no-padding">
                        <?php if(!empty($validate['data'])):?>
                            <?php set_query_var('data', $validate['data']);?>
                        <?php endif;?>
                        <?php get_template_part('login-form');?>
                    </div>

                    <div class="col-xs-6">
                        <button type="button" id="fb-login-btn">
                            <i class="fab fa-facebook pull-left"></i>
                            <span class="pull-left">Iniciar sesión con Facebook</span>
                        </button>
                    </div>
                </div>
            </div>
        </article>

    <?php endwhile;

endif;

get_footer();

?>