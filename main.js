$(document).ready(function(){

    // data toggles
    /*if($('.toggle[data-toggle="toggle"]').length > 0){
        $('.toggle[data-toggle="toggle"]').on('click',function(ev){
            if(!confirm('¿Está seguro de realizar esta acción?')){
                ev.preventDefault();
            }
        })
        .change(function(){
            var elem    = $(this);
            var status  = (elem.find('input[type="checkbox"]').is(':checked')) ? 1 : 2;
            var token   = elem.siblings('input[name="token"]').val();
            var email   = elem.siblings('input[name="email"]').val();

            elem.siblings('.ld-spin').removeClass('hidden');
            $.post('/profesionales/altas',{status: status, token: token, email: email},function(data){
                elem.siblings('.ld-spin').addClass('hidden');
                if(data.status=='OK'){
                    elem.siblings('.status').html('<i class="fas fa-check text-success"></i><span class="text-success">Guardado</span>').fadeOut(5000,function(){
                        elem.siblings('.status').empty().css('display','inline-block');
                    });
                } else {
                    elem.siblings('.status').html('<i class="fas fa-times text-danger"></i><span class="text-danger">Error al guardar</span>').fadeOut(5000,function(){
                        elem.siblings('.status').empty().css('display','inline-block');
                    });
                }
            },'json');
        });         
    }*/


    if($('textarea[name="question"]').length > 0){
        $('textarea[name="question"]').on('keyup',function(){
            updateTextAreaCounter(this);
        });

        $('textarea[name="question"]').on('change',function(){
            updateTextAreaCounter(this);
        });

        function updateTextAreaCounter(textarea){
            var limit = 500;
            if($(textarea).val().length > limit){
                return false;
            }

            $('.char-count span').html(limit-$(textarea).val().length);
        }
    }



    // ********** modals ********** //
    if($('.modal').length > 0){
        if($('#payQuestionsModal').length > 0 || $('#loginModal').length > 0){
            // automatic show
            $('button#modal').trigger('click');
        }

        if($('#uploadModal').length > 0){
            // add events
            $("#profile-image-input").change(function() {
                $(this).siblings('error').empty();
                var file = this.files[0];
                var imagefile = file.type;
                var match= ["image/jpeg","image/png","image/jpg"];
                if(!((imagefile==match[0]) || (imagefile==match[1]) || (imagefile==match[2])))
                {
                    $(this).siblings('error').html("<p id='error'>Por favor, seleccione una imagen válida</p>"+"<h4>Nota</h4>"+"<span id='error_message'>Sólo los tipos de imagen jpeg, jpg y png están permitidos</span>");
                    return false;
                }
                else
                {
                    $('#profile-image .ld-spin').removeClass('hidden');
                    $('#profile-image .fa-user').addClass('disabled');
                    $('#profile-image img').addClass('disabled');
                    var reader = new FileReader();
                    reader.onload = imageIsLoaded;
                    reader.readAsDataURL(this.files[0]);
                }
            });

            function imageIsLoaded(e) {
                setTimeout(function(){
                    $("#profile-image-input").css("color","green");
                    $('#profile-image img').attr('src', e.target.result);
                    $('#profile-image img').attr('width', '250px');
                    $('#profile-image img').attr('height', '230px');
                    $('#profile-image img').removeClass('disabled').removeClass('hidden').fadeIn(1000);
                    $('#profile-image .ld-spin').addClass('hidden');
                    $('#profile-image .fa-user').remove();
                }, 2000);
            }

            $('#uploadModal .close-link').on('click',function(ev){
                ev.preventDefault();ev.stopPropagation();
                checkModal(ev);
            })
        }


        // **** all modals - begin
        $('.modal .close').on('click',function(ev){
            ev.preventDefault();ev.stopPropagation();
            checkModal(ev);
        });

        $('.modal .btn-success').on('click',function(ev){
            ev.preventDefault();ev.stopPropagation();
            checkModal(ev);
        });

        if($('.modal').find('.loading').length > 0){
            var timer = setTimeout(function() {
                checkModal();
            }, 3000);    
        }
        // **** all modals - end

        function checkModal(ev){

            // login modal
            if($('.modal').find('.login-modal').length > 0){
                $('.modal').removeClass('in').hide();
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                window.location.href='https://mesaprofesional.com';
            }


            // packs modal
            if($('.modal').find('.packs-modal').length > 0){
                $('.modal').removeClass('in').hide();
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');

                // check MP
                if($('.modal').find('.mercado-pago-result').length > 0){
                    if($('.modal').find('.mercado-pago-result').val() == 'approved'){
                        $('.alert-payment-approved').show();
                        return true;
                    } else {
                        window.location.href='https://mesaprofesional.com';
                    }
                }
                else {
                    window.location.href='https://mesaprofesional.com';
                }
            }



            // upload profile image modal
            if($('.modal').find('.upload-modal').length > 0){
                if(ev.currentTarget.className.indexOf('submit') > 0){
                    $('#uploadModal button.submit').addClass('running');
                    var formData = new FormData();

                    //Append files infos
                    jQuery.each($("#profile-image-input")[0].files, function(i, file) {
                        formData.append('userfile-'+i, file);
                    });

                    $.ajax({
                        url: '/uploads',
                        type: 'POST',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                        dataType: 'json',
                        success: function(data){
                            $('#uploadModal button.submit').removeClass('running');
                            if(data.status=='OK'){
                                $('#uploadModal').find('.status').html('<span>Foto de perfil guardada satisfactoriamente.</span>');
                                $('#uploadModal').find('.status').addClass('text-success');
                                $('#uploadModal .close-link').on('click',function(){
                                    $('.modal').removeClass('in').hide();
                                    $('.modal-backdrop').remove();
                                    $('body').removeClass('modal-open');
                                });
                            }
                            else{
                                var messageError = (typeof data.message !== typeof undefined) ? data.message : 'Se ha producido un error desconocido, por favor contáctese con nuestros administradores.';
                                $('#uploadModal').find('.status').addClass('text-error');
                                $('#uploadModal').find('.status').html(messageError);
                            }

                        },
                        error: function(data){
                            var messageError = (typeof data.message !== typeof undefined) ? data.message : 'Se ha producido un error desconocido, por favor contáctese con nuestros administradores.';
                            $('#uploadModal button.submit').removeClass('running');
                            $('#uploadModal').find('.status').addClass('text-error');
                            $('#uploadModal').find('.status').html(messageError);
                        }
                    });
                }
                else {
                    $('.modal').removeClass('in').hide();
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');
                }
            }
        }
    }
    

    


});