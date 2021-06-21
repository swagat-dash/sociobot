<?php include 'top.php'?>
<?php include 'header.php'?>
<div class="main-content">
    <div class="bg-info w-100 h-76"></div>

    <?php 
    $posts = get_ci_value("post_package");
    $addons = get_ci_value("addon_package");
    $packages = get_ci_value("packages");
    ?>

    <?php if(!empty($packages)){?>
    <section class="pricing">
        <div class="container">
            <div class="mb-5 text-center">
                <h3 class=" mt-4"><?php _e("Pricing Plan")?></h3>
                <div class="fluid-paragraph mt-3">
                    <p class="lead lh-180"><?php _e("Get started today with your full stack of brand building tools")?></p>
                </div>
            </div>

            <nav class="pricing-tab">
                <span class="tab-btn monthly_tab_title"><?php _e("Monthly")?> </span>
                <span class="pricing-tab-switcher"></span>
                <span class="tab-btn annual_tab_title"><?php _e("Annually")?></span>
            </nav>
            <div class="row no-gutters">
                
                <?php
                foreach ($packages as $key => $row) {

                    $file_type = ["photo" => __("Photo"), "video" => __("Video")];
                    $cloud_import = ["google_drive" => __("Google Drive"), "dropbox" => __("Dropbox"), "one_drive" => __("One Drive")];

                    if( !isset($row->permissions['file_manager_photo']) ) unset($file_type["photo"]);
                    if( !isset($row->permissions['file_manager_video']) ) unset($file_type["video"]);

                    if( !isset($row->permissions['file_manager_google_drive']) ) unset($cloud_import["google_drive"]);
                    if( !isset($row->permissions['file_manager_dropbox']) ) unset($cloud_import["dropbox"]);
                    if( !isset($row->permissions['file_manager_onedrive']) ) unset($cloud_import["one_drive"]);

                    if(!empty($file_type)){
                        $file_type = implode(", ", $file_type);
                    }else{
                        $file_type = __("Unsupported");
                    }

                    if(!empty($cloud_import)){
                        $cloud_import = implode(", ", $cloud_import);
                    }else{
                        $cloud_import = __("Unsupported");
                    }
                ?>
                <div class="col-lg-4">
                    <div class="pricing-table <?php _e( $key%3 == 0 ?"br-left":"" )?> <?php _e( $row->popular==1?"popular":"" )?>">
                        <?php if($row->popular==1){?>
                        <div class="pricing-popuplar bg-info"><?php _e("Best value")?></div>
                        <?php }?>
                        <div class="pricing-header pricing-amount">
                            <div class="annual_price">
                                <h2 class="price"><?php _e( sprintf("%s%s", get_option("payment_symbol"), $row->price_annually) )?><span class="fw-4 fs-18"><?php _e("/month")?></span></h2>
                            </div>
                            <div class="monthly_price">
                                <h2 class="price"><?php _e( sprintf("%s%s", get_option("payment_symbol"), $row->price_monthly) )?><span class="fw-4 fs-18"><?php _e("/month")?></span></h2>
                            </div>
                            <h3 class="price-title"><?php _e($row->name)?></h3>
                            <p><?php _e($row->description)?></p>
                        </div>
                        <ul class="price-feture">
                            <li class="pl-0 text-center">
                                <?php
                                $social_networks_allowed = 0;
                                if(!empty($posts)){
                                    foreach ($posts as $value){
                                        if( isset($row->permissions[ $value['id']."_enable" ]) ){
                                            $social_networks_allowed++;
                                        }
                                    } 
                                }
                                ?>

                                <div class="text-info fw-6 fs-16"><?php _e( sprintf( sprintf(__("Add up to %s social accounts"),  __( $social_networks_allowed * $row->number_accounts ) ) ) )?></div>
                                <div class="small"><?php _e( sprintf( sprintf(__("%s social account on each platform"),  __( $row->number_accounts ) ) ) )?> </div>
                            </li>
                        </ul>
                        <?php if(!empty($posts)){?>
                        <ul class="price-feture">
                            <li class="title"><span><?php _e("Scheduling & Report")?></span></li>
                            <?php foreach ($posts as $value): ?>
                                <li class="<?php _e( isset($row->permissions[ $value['id']."_enable" ]) ? "have":"not" )?>"><?php _e( sprintf( sprintf(__("%s scheduling & report"),  __( $value['group'] ) ) ) )?></li>
                            <?php endforeach ?>
                        </ul>
                        <?php }?>
                        <?php if(!empty($addons)){?>
                        <ul class="price-feture">
                            <li class="title"><span><?php _e("Modules & Addons")?></span></li>
                            <?php foreach ($addons as $value): ?>
                                <li class="<?php _e( isset($row->permissions[ $value['id']."_enable" ]) ? "have":"not" )?>"><?php _e( $value['sub_name'] )?></li>
                            <?php endforeach ?>
                        </ul>
                        <?php }?>
                        <ul class="price-feture">
                            <li class="title"><span><?php _e("Advance features")?></span></li>
                            <li class="have">Spintax support</li>
                            <li class="<?php _e( isset($row->permissions[ "watermark_enable" ]) ? "have":"not" )?>"><?php _e("Watermark support")?></li>
                            <li class="<?php _e( isset($row->permissions[ "file_manager_image_editor" ]) ? "have":"not" )?>"><?php _e("Image Editor support")?></li>
                            <li class="have"><?php _e( sprintf( __( "Cloud import: %s"), $cloud_import ) )?></li>
                            <li class="have"><?php _e( sprintf( __( "File type: %s"), $file_type ) )?></li>
                            <li class="have"><?php _e( sprintf( __( "Storage: %sMB"), $row->permissions['max_storage_size'] ) )?></li>
                            <li class="have"><?php _e( sprintf( __( "Max. file size: %sMB"), $row->permissions['max_file_size'] ) )?></li>
                        </ul>
                        <div class="action text-center">
                            <a href="<?php _e( get_url("payment/index/".$row->ids."/1" ))?>" data-tmp="<?php _e( get_url("payment/index/".$row->ids."/2" ))?>" class="btn btn-dark btn-payment btn-block p-t-15 p-b-15"><?php _e("Get Started")?></a>
                        </div>
                    </div>
                </div>
                <?php }?>

            </div>
        </div>
    </section>
    <?php }?>

    <section class="slice slice-lg">
        <div class="container">
            <div class="mb-5 text-center">
                <h3 class=" mt-4"><?php _e("Ready To Try?")?></h3>
                <div class="fluid-paragraph mt-3">
                    <p class="lead lh-180"><?php _e("Start your free trial. Are you ready to try service reign? ! No contract. No credit card")?></p>
                </div>
            </div>
            <div class="text-center">
                <form action="<?php _e( get_url("signup") )?>" method="GET">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Enter your email" name="email">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-outline-primary" type="submit"><?php _e("Get Start Now")?></button>
                        </div>
                    </div>
                </form>
                <div class="small"><?php _e("Do not hesitate to try it out with just a few minutes of setup")?></div>
            </div>
        </div>
    </section>

<?php include 'footer.php'?>
<?php include 'bottom.php'?>