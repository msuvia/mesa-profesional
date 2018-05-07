<form role="register" method="post" action="" class="col-xs-12 no-padding">
    <?php extract( $wp_query->query_vars );?>
    <input type="hidden" name="type" value="<?php echo $type;?>"/>

    <div class="col-xs-12 no-padding">
        <div class="col-xs-6 no-padding-left">
            <label class="col-xs-12 no-padding" for="first-name">Nombre <span class="asterisk">*</span></label>
            <input class="col-xs-12 form-control" type="text" value="" name="first-name" id="first-name" placeholder="Nombre"/>
            <small class="col-xs-12 no-padding error hidden">Campo obligatorio</small>
        </div>

        <div class="col-xs-6 no-padding-right">
            <label class="col-xs-12 no-padding" for="last-name">Apellido <span class="asterisk">*</span></label>
            <input class="col-xs-12 form-control" type="text" value="" name="last-name" id="last-name" placeholder="Apellido"/>
            <small class="col-xs-12 no-padding error hidden">Campo obligatorio</small>
        </div>
    </div>

    <div class="col-xs-12 no-padding">
        <label class="col-xs-12 no-padding" for="email">Email <span class="asterisk">*</span></label>
        <input class="col-xs-12 form-control" type="text" value="" name="email" id="email" placeholder="Email"/>
        <small class="col-xs-12 no-padding error hidden">Campo obligatorio</small>
        <small class="col-xs-12 no-padding error error-invalid-email hidden">El email es inválido</small>
    </div>

    <div class="col-xs-12 no-padding">
        <label class="col-xs-12 no-padding" for="question">Contraseña <span class="asterisk">*</span></label>
        <input class="col-xs-12 form-control" type="password" value="" name="password" id="password" placeholder="Contraseña"/>
        <small class="col-xs-12 no-padding error hidden">Campo obligatorio</small>
    </div>

    <div class="col-xs-12 no-padding">
        <label class="col-xs-12 no-padding" for="question">Confirmar contraseña <span class="asterisk">*</span></label>
        <input class="col-xs-12 form-control" type="password" value="" name="confirm-password" id="confirm-password" placeholder="Confirmar contraseña"/>
        <small class="col-xs-12 no-padding error hidden">Campo obligatorio</small>
    </div>    

    <div class="col-xs-12 no-padding terms">
        <input type="checkbox" name="terms" value="" id="terms">
        <label for="terms">Acepto los <a href="<?php echo home_url('/');?>" target="_blank">términos y condiciones</a> <span class="asterisk">*</span></label>
        <small class="col-xs-12 no-padding error hidden">Campo obligatorio</small>
    </div>

    <div class="col-xs-4 no-padding">
        <!--<input type="submit" value="Aceptar"/>-->
        <button class="col-xs-12 btn btn-success ld-ext-right hovering submit">
            Aceptar
            <div class="ld ld-ring ld-spin"></div>
        </button>
    </div>
</form>