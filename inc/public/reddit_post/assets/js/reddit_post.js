"use strict";
function Reddit_Post(){
    var self= this;
    this.init= function(){
        if($(".reddit-post").length > 0){
            var post_type = $(".reddit-post input[name='post_type']:checked").val();

            if(post_type == "text"){
                $(".reddit-post input[name=caption]").attr("disabled", "");
            }else{
                $(".reddit-post input[name=caption]").removeAttr("disabled");
            }

            $(document).on("click", ".reddit-post .post-type a", function(){

                var post_type = $(this).find("input").val();

                if(post_type == "text"){
                    $(".reddit-post input[name=caption]").attr("disabled", "");
                }else{
                    $(".reddit-post input[name=caption]").removeAttr("disabled");
                }

            });
        }

        //Review title
        var title = $(".post input.reddit-title").val();
        if(title != ""){
            $(".preview-title").html(title);
        }

        $(document).on("change keyup", ".post input.reddit-title", function(){
            var data = $(this).val();
            if(data != ""){
                $(".preview-title").html(data);
            }else{
                $(".preview-title").html('<div class="line-no-text">');
            }
        });

        //Review title
        var link = $(".post input[name='link']").val();
        if(link != "" && link != undefined){
            link = link.replace("https://", "");
            link = link.replace("http://", "");
            if(link.length > 20){
                link = link.substring(0, 20)+"...";
            }
            $(".preview-reddit .preview-link").html(link+' <i class="fas fa-external-link-alt open"></i>');
        }

        $(document).on("change keyup", ".post input[name='link']", function(){
            var data = $(this).val();
            
            if(data != "" && data != undefined){
                data = data.replace("https://", "");
                data = data.replace("http://", "");
                if(data.length > 20){
                    data = data.substring(0, 20)+"...";
                }


                $(".preview-reddit .preview-link").html(data+' <i class="fas fa-external-link-alt open"></i>');
            }else{
                $(".preview-reddit .preview-link").html('<div class="line-no-text">');
            }
        });
    };
}

var Reddit_Post = new Reddit_Post();
$(function(){
    Reddit_Post.init();
});