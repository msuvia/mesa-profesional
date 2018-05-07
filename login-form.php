<form role="login" method="post" action="" class="col-xs-12 no-padding">
    <?php extract( $wp_query->query_vars );?>
    <?php $modal = (!empty($modal)) ? $modal : 0;?>
    <div class="col-xs-12 no-padding">
        <label class="col-xs-12 no-padding" for="email">Email <span class="asterisk">*</span></label>
        <input class="col-xs-12 form-control <?php if(!empty($data['emailError']) || !empty($data['invalidEmailError'])):?> error <?php endif;?>" type="text" value="" name="email" placeholder="Email"/>
        <small class="col-xs-12 no-padding error <?php if(empty($data['emailError'])):?> hidden <?php endif;?>">Campo obligatorio</small>
        <small class="col-xs-12 no-padding error invalid-email <?php if(empty($data['invalidEmailError'])):?> hidden <?php endif;?>">Email inválido, por favor, elija otro</small>
    </div>

    <div class="col-xs-12 no-padding">
        <label class="col-xs-12 no-padding" for="last-name">Contraseña <span class="asterisk">*</span></label>
        <input class="col-xs-12 form-control <?php if(!empty($data['passwordError'])):?> error <?php endif;?>" type="password" value="" name="password" placeholder="Contraseña"/>
        <small class="col-xs-12 no-padding error <?php if(empty($data['passwordError'])):?> hidden <?php endif;?>">Campo obligatorio</small>
    </div>

    <div class="col-xs-12 no-padding sign-link">
        <a href="<?php echo home_url('/clientes/registro');?>">No tengo usuario</a>
    </div>

    <div class="<?php echo ($modal==1) ? 'col-xs-12' : 'col-xs-4';?> no-padding">
        <!--<input class="col-xs-12" type="submit" value="Aceptar"/>-->
        <button class="col-xs-12 btn btn-success ld-ext-right hovering submit">
            Aceptar
            <div class="ld ld-ring ld-spin"></div>
        </button>
    </div>
</form>