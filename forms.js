$(document).ready(function(){

    $('form button.submit').on('click',function(ev){
        ev.preventDefault();ev.stopPropagation();
        validateForm($(this).parents('form'));
    });


    $('form input[type="submit"]').on('click',function(ev){
        ev.preventDefault();ev.stopPropagation();
        validateForm($(this).parents('form'));
    });


    function validateForm(form){
        var scrollElement   = '';
        var valid           = true;
        var form_data       = new FormData();
        var firstName       = (form.find("input[name='first-name']").length > 0)    ? form.find("input[name='first-name']")     : false;
        var lastName        = (form.find("input[name='last-name']").length > 0)     ? form.find("input[name='last-name']")      : false;
        var email           = (form.find("input[name='email']").length > 0)         ? form.find("input[name='email']")          : false;
        var password        = (form.find("input[name='password']").length > 0)      ? form.find("input[name='password']")       : false;
        var question        = (form.find("textarea[name='question']").length > 0)   ? form.find("textarea[name='question']")    : false;

        if(firstName){
            if(firstName.val() == ''){
                valid = false;
                if(scrollElement == '') scrollElement = firstName;

                firstName.addClass('error');
                firstName.siblings('small.error').removeClass('hidden');
                firstName.blur(function(){
                    if($(this).val() == ''){
                        $(this).addClass('error');
                        $(this).siblings('small.error').removeClass('hidden');
                    }else{
                        $(this).removeClass('error');
                        $(this).siblings('small.error').addClass('hidden');
                    }
                });
            }else{
                firstName.removeClass('error');
                firstName.siblings('small.error').addClass('hidden');
            }
        }

        if(lastName){
            if(lastName.val() == ''){
                valid = false;
                if(scrollElement == '') scrollElement = lastName;

                lastName.addClass('error');
                lastName.siblings('small.error').removeClass('hidden');
                lastName.blur(function(){
                    if($(this).val() == ''){
                        $(this).addClass('error');
                        $(this).siblings('small.error').removeClass('hidden');
                    }else{
                        $(this).removeClass('error');
                        $(this).siblings('small.error').addClass('hidden');
                    }
                });
            }else{
                lastName.removeClass('error');
                lastName.siblings('small.error').addClass('hidden');
            }
        }

        if(email){
            if(email.val() == '' || !validateEmail(email.val())){
                valid = false;
                if(scrollElement == '') scrollElement = email;

                email.addClass('error');
                if(email.val() != ''){
                    email.siblings('small.error').not('.invalid-email').addClass('hidden').end().siblings('small.error.invalid-email').removeClass('hidden');
                } else {
                    email.siblings('small.error.invalid-email').addClass('hidden').end().siblings('small.error').not('.invalid-email').removeClass('hidden');
                }
                    
                email.blur(function(){
                    if($(this).val() == '' || !validateEmail($(this).val())){
                        $(this).addClass('error');
                        $(this).siblings('small.error').removeClass('hidden');
                    }else{
                        $(this).removeClass('error');
                        $(this).siblings('small.error').addClass('hidden');
                    }
                });
            }else{
                email.removeClass('error');
                email.siblings('small.error').addClass('hidden');
            }
        }

        if(question){
            if(question.val() == ''){
                valid = false;
                if(scrollElement == '') scrollElement = question;

                question.addClass('error');
                question.siblings('small.error').removeClass('hidden');
                question.blur(function(){
                    if($(this).val() == ''){
                        $(this).addClass('error');
                        $(this).siblings('small.error').removeClass('hidden');
                    }else{
                        $(this).removeClass('error');
                        $(this).siblings('small.error').addClass('hidden');
                    }
                });
            }else{
                question.removeClass('error');
                question.siblings('small.error').addClass('hidden');
            }
        }


        if(password){
            if(password.val() == ''){
                valid = false;
                if(scrollElement == '') scrollElement = password;

                password.addClass('error');
                password.siblings('small.error').removeClass('hidden');
                password.blur(function(){
                    if($(this).val() == ''){
                        $(this).addClass('error');
                        $(this).siblings('small.error').removeClass('hidden');
                    }else{
                        $(this).removeClass('error');
                        $(this).siblings('small.error').addClass('hidden');
                    }
                });
            }else{
                password.removeClass('error');
                password.siblings('small.error').addClass('hidden');
            }
        }




        if(!valid){
            if(scrollElement != '') $('html, body').animate({ scrollTop: (scrollElement.offset().top - 70) }, 500);
        }
        else{
            // dropdown ?
            if(form.parent().parent().hasClass('dropdown-content')){
                form.find('button.submit').addClass('running');
                $.post('/login',{email: email.val(), password: password.val(), from:"js"}, function(data){
                    form.find('.alert-warning').remove();
                    if(data.status == 'ERROR'){
                        form.find('button.submit').removeClass('running');
                        var alert = '<div class="col-xs-12 alert alert-warning" role="alert">' + 
                                    data.message + 
                                    '</div>';
                        form.prepend(alert);
                    }
                    else {
                        form.submit();
                    }
                },"json");
            }
            else{
                form.find('button.submit').addClass('running');
                form.submit();
            }
        }
    }


    function validateEmail(email){
        if(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email)) return true;
        return false;
    }


});