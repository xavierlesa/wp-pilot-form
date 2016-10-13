/*
 * Auto-attach ajax for forms
 *
 * usage:
 *
 */

jQuery(function($){

    $("form[data-wpajaxsend]").each(function(){

        var options = $(this).data('wpajaxsend-options') || "loading_img:/static/img/loading.gif";
        if(options){
            options = options.split(';');
            var r = {};
            options.forEach(function(a){ 
                var b = a.trim().split(':'); 
                r[b[0]] = (b[1]||'').trim();
                r[b[0]] = r[b[0]] == 'true' ? true : r[b[0]];
                r[b[0]] = r[b[0]] == 'false' ? false : r[b[0]];
                return r
            });
        }

        options = $.extend({
            done_element: this,
            done_event: 'done',
            fail_element: this,
            fail_event: 'fail',
            //complete_element: this,
            //complete_event: 'complete',
            sending_element: this,
            sending_event: 'sending',
            beforesend_element: this,
            beforesend_event: 'beforesend',
            loading_img: '/static/img/loading.gif'
        }, r);

        var i = new Image();
        i.src = decodeURIComponent(options.loading_img);

        $(this).append($($("<div class='loading' style='text-align:center' />").append(i)).hide());

        function disabled(form){
            form.find('input,textarea,select').attr('disabled', true);
        }

        function enabled(form){
            form.find('input,textarea,select').attr('disabled', false);
        }

        $(this)
            .on('submit', function(event){
                var $form = $(this);
                event.preventDefault();

                // Si es un wizard valida y continua con el step siguiente
                //if(options.is_wizard){
                //    alert('salta al step 2');
                //    return false;
                //}

                var xhr = $.ajax({
                    type: 'POST',
                    url: $form.attr('action'),
                    data: $form.serialize(),
                    dataType: 'json',
                    global: false,
                    beforeSend: function(jqXHR){
                        $form.find('.loading').show();
                        disabled($form);
                        console.log('loading...');
                        $(options.beforesend_element).trigger(options.beforesend_event, jqXHR);
                    }
                });

                xhr.always(function(response){
                    $form.find('.loading').hide();
                    console.log('stop');
                    enabled($form);
                    $(options.sending_element).trigger(options.sending_event, response);
                });

                xhr.done(function(data, textStatus, jqXHR){
                    $(options.done_element).trigger(options.done_event, {data:data, textStatus:textStatus, jqXHR:jqXHR});
	            $(this)[0].reset();
	             console.log('clear form');
                });

                xhr.fail(function(jqXHR, textStatus, errorThrown){
                    $(options.fail_element, $form).trigger(options.fail_event, {jqXHR:jqXHR, textStatus:textStatus, errorThrown:errorThrown});
                    try {
                        $.each(jqXHR.responseJSON.errors, function(key, val){
                            $form
                                .find("*[name="+key+"]")
                                .addClass('error')
                                .next('.error')
                                .css('display', 'block')
                                .text(val[0]);
                        });
                    } catch(E){};
                });

                return false;
            });

    });

});




