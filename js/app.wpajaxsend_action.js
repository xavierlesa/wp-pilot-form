jQuery(function($){
    if(window.dynaForm) {
        dynaForm
            .on("fail", function(jqXHR, textStatus, errorThrown){
                if(window.grecaptcha){
                    grecaptcha.reset();
                }
            })
        .on("done", function(jqXHR, textStatus, errorThrown){ 
            var redirect_url = $("input[name=_object_form_success_url]", this).val();
            console.log("redirect to: ", redirect_url);
            $(this)[0].reset();
            if(!redirect_url || redirect_url == "/"){
                window.location.href = "?do=gracias&dt=" + data.data.dt;
            } else {
                var _url = /(\??)([^\?]+)/.exec(redirect_url);
                var final_url, parts = _url[2].split("&");
                if(parts.length == 1 && _url[1] == ""){
                    parts.push("dt="+data.data.dt);
                    final_url = parts.join("?");
                }
                else {
                    parts.push("dt="+data.data.dt);
                    final_url = _url[1] + parts.join("&");
                }
                window.location.href = final_url; 
            }
        })
        .on("sending", function(jqXHR, textStatus, errorThrown){
            $(".error", this).removeClass("error");
        });

    }
});
