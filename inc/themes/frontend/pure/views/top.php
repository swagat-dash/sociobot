<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title><?php _e( get_option('website_title', 'SocioBot - Social Media Management And Automation ') )?></title>
        <meta name="description" content="<?php _e( get_option('website_desc', '#1 Marketing Platform for Social Network') )?>">
        <meta name="keywords" content="<?php _e( get_option('website_keywords', 'social network, marketing, brands, businesses, agencies, individuals') )?>">
        <link rel="icon" type="image/png" href="<?php _e( get_option('website_favicon', get_url("inc/themes/backend/default/assets/img/favicon.png")) )?>" />
        <link rel="stylesheet" href="<?php _e( get_theme_frontend_url('assets/fonts/flags/flag-icon.css')) ?>">
        <link rel="stylesheet" href="<?php _e( get_theme_frontend_url('assets/plugins/fortawesome/fontawesome-pro/css/all.min.css')) ?>">
        <link rel="stylesheet" href="<?php _e( get_theme_frontend_url('assets/css/style.css'))?>" id="stylesheet">
        
        <script type="text/javascript">
            var token = '<?php _e( $this->security->get_csrf_hash() )?>',
                PATH  = '<?php _e(PATH)?>',
                BASE  = '<?php _e(BASE)?>';

            document.onreadystatechange = function () {
                var state = document.readyState
                if (state == 'complete') {
                    setTimeout(function(){
                        document.getElementById('interactive');
                        document.getElementById('loading-overplay').style.opacity ="0";
                    },500);

                    setTimeout(function(){
                        document.getElementById('loading-overplay').style.display ="none";
                        document.getElementById('loading-overplay').style.opacity ="1";
                    },1000);
                }
            }
        </script>
        <?php _e( htmlspecialchars_decode(get_option('embed_code', ''), ENT_QUOTES) , false)?>
    </head>
    <body>
        <div class="loading-overplay" id="loading-overplay"><div class='loader loader1'><div><div><div><div></div></div></div></div></div></div>
 