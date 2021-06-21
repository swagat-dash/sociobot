		<?php if(get_option('google_recaptcha_status', 0)){?>
	    	<script src="https://www.google.com/recaptcha/api.js" async defer></script>
	    <?php }?>

        <script src="<?php _e( get_theme_frontend_url('assets/js/core.js'))?>"></script>
        <script src="<?php _e( get_theme_frontend_url('assets/js/pure.js'))?>"></script>
    </body>
</html>