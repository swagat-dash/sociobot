"use strict";
function Proxy_advance_manager(){
    var self= this;
    this.init= function(){
        self.action();
        self.import();
    };

    this.action = function(){
        $(document).on("change", ".proxy-advance-manager input[name='proxy']", function(){
            var proxy = $(this).val();
            if(proxy != ""){
                Core.ajax_post($(this), PATH+"proxy_advance_manager/proxy_info", { token: token, proxy: proxy}, function(result){
                    if(result.status == "success"){
                        $(".proxy-advance-manager select[name='location']").val(result.code);
                    }else{
                        $(".proxy-advance-manager select[name='location']").val("unknown");
                    }               
                });
            }
        });

        $(document).on("click", ".actionProxyAssign", function(){
            var that = $(this);
            var action = that.attr("href");

            $(".proxy-advance-manager-modal").remove();

            Core.ajax_post(that, action, { token: token }, function(result){
                $("body").append(result);
                $('#proxy-advance-manager-modal').modal('show');
                $(".select-proxy-assign").selectpicker();
            });

            return false;
        });
    };

    this.import = function(){
        var url = PATH + "proxy_advance_manager/do_import";
        $("#import_proxy").fileupload({
            url: url,
            dataType: 'json',
            formData: { token: token },
            done: function (e, data) {
                if(data.result.status == "success"){
                    Core.notify(data.result.message, data.result.status);
                    setTimeout("location.reload(true);", 3000);
                }else{
                    Core.overplay("hide");
                    Core.notify(data.result.message, data.result.status);
                }
            },
            progressall: function (e, data) {
                Core.overplay();
            }
        }).prop('disabled', !$.support.fileInput).parent().addClass($.support.fileInput ? undefined : 'disabled');
    };
}

var Proxy_advance_manager = new Proxy_advance_manager();
$(function(){
    Proxy_advance_manager.init();
});