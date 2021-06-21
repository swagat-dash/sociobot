<section class="slice slice-lg">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="card bg-gradient-dark shadow hover-shadow-lg border-0 position-relative zindex-100">
                    <div class="card-body py-5">
                        <div class="d-flex align-items-start">
                            <div class="icon">
                                <i class="fas fa-file-alt text-white"></i>
                            </div>
                            <div class="icon-text">
                                <h3 class="text-white h4"><?php _e("Easy to use")?></h3>
                                <p class="text-white mb-0"><?php _e("The intuitive interface and clear and logical layout make it easy to use")?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-primary shadow hover-shadow-lg border-0 position-relative zindex-100">
                    <div class="card-body py-5">
                        <div class="d-flex align-items-start">
                            <div class="icon text-white">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="icon-text">
                                <h5 class="h4 text-white"><?php _e("Free support")?></h5>
                                <p class="mb-0 text-white"><?php _e("We give you the best support throughout the use of our service.")?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

</div>
<footer id="footer-main">
    <div class="footer footer-dark bg-gradient-primary footer-rotate">
        <div class="container">
            <div class="row align-items-center justify-content-md-between py-4 mt-4 delimiter-top">
                <div class="col-md-6">
                    <div class="copyright text-sm font-weight-bold text-center text-md-left">
                        <?php _e("&copy; 2021. All rights reserved.")?>
                    </div>
                </div>
                <div class="col-md-6">
                	<div class="text-sm font-weight-bold text-center text-md-right">
                        <a href="<?php _e( get_url("privacy_policy") )?>" class="mr-3 text-white"><?php _e("Privacy Policy")?></a>
                        <a href="<?php _e( get_url("terms_and_policies") )?>" class="mr-3 text-white"><?php _e("Terms of Services")?></a>
                    </div>
                    <ul class="nav justify-content-center justify-content-md-end mt-3 mt-md-0">
                        <?php if( get_option('social_page_facebook', '') ){?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php _e( get_option('social_page_facebook', '') )?>" target="_blank">
                                <i class="fab fa-facebook"></i>
                            </a>
                        </li>
                        <?php }?>
                        <?php if( get_option('social_page_instagram', '') ){?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php _e( get_option('social_page_instagram', '') )?>" target="_blank">
                                <i class="fab fa-instagram"></i>
                            </a>
                        </li>
                        <?php }?>
                        <?php if( get_option('social_page_twitter', '') ){?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php _e( get_option('social_page_twitter', '') )?>" target="_blank">
                                <i class="fab fa-twitter"></i>
                            </a>
                        </li>
                        <?php }?>
                        <?php if( get_option('social_page_youtube', '') ){?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php _e( get_option('social_page_youtube', '') )?>" target="_blank">
                                <i class="fab fa-youtube"></i>
                            </a>
                        </li>
                        <?php }?>
                        <?php if( get_option('social_page_pinterest', '') ){?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php _e( get_option('social_page_pinterest', '') )?>" target="_blank">
                                <i class="fab fa-pinterest"></i>
                            </a>
                        </li>
                        <?php }?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>