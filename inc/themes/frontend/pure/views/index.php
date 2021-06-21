<?php include 'top.php'?>
<?php include 'header.php'?>
<div class="main-content">
    <!-- Header (v1) -->
    <section class="header-1 section-rotate bg-section-secondary" data-offset-top="#header-main">
        <div class="section-inner bg-gradient-primary"></div>
        <!-- SVG illustration -->
        <div class="pt-7 position-absolute middle right-0 col-lg-7 col-xl-6 d-none d-lg-block">
            <figure class="w-100" style="max-width: 1000px;">
                <img alt="" src="<?php _e( get_theme_frontend_url("assets/img/work.svg") )?>" class="svg-inject img-fluid" style="height: 1000px;">
            </figure>
        </div>
        <!-- SVG background -->
        <div class="bg-absolute-cover bg-size--contain d-flex align-items-center">
            <figure class="w-100 d-none d-lg-block">
                <img alt="" src="<?php _e( get_theme_frontend_url("assets/img/bg.svg") )?>" class="svg-inject" style="height: 1000px;">
            </figure>
        </div>
        <!-- Hero container -->
        <div class="container py-5 pt-lg-6 d-flex align-items-center position-relative zindex-100">
            <div class="col">
                <div class="row">
                    <div class="col-lg-5 col-xl-6 text-center text-lg-left">
                        <div>
                            <h2 class="text-white mb-4">
                                <span class="fs-40 fw-7"><?php _e("#1 Marketing Platform for Social Network")?></span>
                                <span class="d-block"><?php _e('All in one')?> <strong class="font-weight-light"><?php _e("brand building tools")?></strong></span>
                            </h2>
                            <p class="lead text-white"><?php _e("With our service will help you save time and optimize profile management on social networks. Visually Plan, Schedule post and automation on social networks.")?></p>
                            <div class="mt-4">
                                <a href="<?php _e( get_url("signup") )?>" class="btn btn-white rounded-pill hover-translate-y-n3 btn-icon mr-sm-3 scroll-me p-l-30 p-r-30 p-t-15 p-b-15">
                                    <span class="btn-inner--text"><?php _e("Try it now")?></span>
                                    <span class="btn-inner--icon"><i class="far fa-angle-right"></i></span>
                                </a>
                                <a href="#features" class="btn btn-outline-white rounded-pill hover-translate-y-n3 btn-icon d-none d-xl-inline-block scroll-me p-l-30 p-r-30 p-t-15 p-b-15">
                                    <span class="btn-inner--icon"><i class="far fa-file-alt"></i></span>
                                    <span class="btn-inner--text"><?php _e("More Features")?></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="slice bg-section-secondary">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-sm-6">
                    <div class="card text-center hover-shadow-lg hover-translate-y-n10">
                        <div class="px-4 py-5">
                            <img alt="" src="<?php _e( get_theme_frontend_url("assets/img/download.svg") )?>" class="svg-inject" style="height: 70px;">
                        </div>
                        <div class="px-4 pb-5">
                            <h5 class="fs-16"><?php _e("No downloads")?></h5>
                            <p class="text-muted fw-3"><?php _e("You can use our service straight from the web on all browsers. You don't need to download or install anything to enjoy our service")?></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="card text-center hover-shadow-lg hover-translate-y-n10">
                        <div class="px-4 py-5">
                            <img alt="" src="<?php _e( get_theme_frontend_url("assets/img/save.svg") )?>" class="svg-inject" style="height: 70px;">
                        </div>
                        <div class="px-4 pb-5">
                            <h5 class="fs-16"><?php _e("Saving Time")?></h5>
                            <p class="text-muted fw-3"><?php _e("Dedicating just 10-20 minutes a day on your social media strategy can dramatically improve your customer relations and interactions")?></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="card text-center hover-shadow-lg hover-translate-y-n10">
                        <div class="px-4 py-5">
                            <img alt="" src="<?php _e( get_theme_frontend_url("assets/img/calendar.svg") )?>" class="svg-inject" style="height: 70px;">
                        </div>
                        <div class="px-4 pb-5">
                            <h5 class="fs-16"><?php _e("Schedule posts")?></h5>
                            <p class="text-muted fw-3"><?php _e("Select your date, time or whenever you want to publish on each your social accounts just need a few click to complete and enjoy")?></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="card text-center hover-shadow-lg hover-translate-y-n10">
                        <div class="px-4 py-5">
                            <img alt="" src="<?php _e( get_theme_frontend_url("assets/img/bar.svg") )?>" class="svg-inject" style="height: 70px;">
                        </div>
                        <div class="px-4 pb-5">
                            <h5 class="fs-16"><?php _e("Analytics performance")?></h5>
                            <p class="text-muted fw-3"><?php _e("You can see all your posts how it work and increase does. It will help you control your audiences and target")?></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="card text-center hover-shadow-lg hover-translate-y-n10">
                        <div class="px-4 py-5">
                            <img alt="" src="<?php _e( get_theme_frontend_url("assets/img/user.svg") )?>" class="svg-inject" style="height: 70px;">
                        </div>
                        <div class="px-4 pb-5">
                            <h5 class="fs-16"><?php _e("Influencer Marketing")?></h5>
                            <p class="text-muted fw-3"><?php _e("Focus on your top influencers & supporters so you don't miss their engagements follow them")?></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="card text-center hover-shadow-lg hover-translate-y-n10">
                        <div class="px-4 py-5">
                            <img alt="" src="<?php _e( get_theme_frontend_url("assets/img/shield.svg") )?>" class="svg-inject" style="height: 70px;">
                        </div>
                        <div class="px-4 pb-5">
                            <h5 class="fs-16"><?php _e("Safe and Secure")?></h5>
                            <p class="text-muted fw-3"><?php _e("Your data is safe with us. We're not one of those companies that gives your personal information away")?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="slice slice-lg">
        <div class="container">
            <div class="row row-grid justify-content-around align-items-center">
                <div class="col-lg-5 order-lg-2">
                    <div class=" pr-lg-4">
                        <h5 class=" h3"><?php _e("Streamline your social media processes & delivery for your clients")?></h5>
                        <p class="lead mt-4 mb-5"><?php _e("Whether focusing on a campaign for one brand or managing social across hundreds, Our service helps agency teams be more productive by managing all their client activities from a centralized hub. Our service is guaranteed to save your hours each day")?></p>
                    </div>
                </div>
                <div class="col-lg-6 order-lg-1">
                    <img alt="" src="<?php _e( get_theme_frontend_url("assets/img/presentation-1.png") )?>" class="img-fluid img-center">
                </div>
            </div>
        </div>
    </section>

    <section class="slice slice-lg">
        <div class="container">
            <div class="row row-grid justify-content-around align-items-center">
                <div class="col-lg-5">
                    <div class="">
                        <h5 class=" h3"><?php _e("A complete solution for your social marketing & save your time")?></h5>
                        <p class="lead my-4"><?php _e("With an intuitive interface and a lot of extra features to help you create articles that are interesting and easier")?></p>
                        <ul class="list-unstyled">
                            <li class="py-2">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <div class="icon icon-shape icon-primary icon-sm rounded-circle mr-3">
                                            <i class="fas fa-user-clock"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="h6 mb-0"><?php _e("Perfect for modern use")?></span>
                                    </div>
                                </div>
                            </li>
                            <li class="py-2">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <div class="icon icon-shape icon-warning icon-sm rounded-circle mr-3">
                                            <i class="far fa-palette"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="h6 mb-0"><?php _e("Image editing, easy uploading and watermark support")?></span>
                                    </div>
                                </div>
                            </li>
                            <li class="py-2">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <div class="icon icon-shape icon-success icon-sm rounded-circle mr-3">
                                            <i class="far fa-cog"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="h6 mb-0"><?php _e("Quality design and visualization")?></span>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img alt="" src="<?php _e( get_theme_frontend_url("assets/img/presentation-2.png") )?>" class="img-fluid img-center">
                </div>
            </div>
        </div>
    </section>

    <section class="slice slice-lg bg-section-secondary overflow-hidden">
        <div class="bg-absolute-cover bg-size--contain d-flex align-items-center">
            <figure class="w-100">
                <img alt="" src="<?php _e( get_theme_frontend_url("assets/img/bg-2.svg") )?>" class="svg-inject" style="height: 1000px;">
            </figure>
        </div>
        <div class="container position-relative zindex-100">
            <div class="mb-5 px-3 text-center">
                <span class="badge badge-soft-success badge-pill badge-lg">
                    <?php _e("Inspect Accounts")?>
                </span>
                <h3 class=" mt-4"><?php _e("What we Offer")?></h3>
                <div class="fluid-paragraph mt-3">
                    <p class="lead lh-180"><?php _e("We're more than a scheduling tool. Explore our features, and beat the algorithm.")?></p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <div class="card px-3">
                        <div class="card-body py-5">
                            <div class="d-flex align-items-center">
                                <div class="icon bg-gradient-primary text-white rounded-circle icon-shape shadow-primary">
                                    <i class="fas fa-award"></i>
                                </div>
                                <div class="icon-text pl-4">
                                    <h5 class="mb-0"><?php _e("Visually plan and schedule your social media campaigns")?></h5>
                                </div>
                            </div>
                            <p class="mt-4 mb-0"><?php _e("Coordinate creative campaigns to drive engagement on social")?></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card px-3">
                        <div class="card-body py-5">
                            <div class="d-flex align-items-center">
                                <div class="icon bg-gradient-warning text-white rounded-circle icon-shape shadow-warning">
                                    <i class="fas fa-chart-pie"></i>
                                </div>
                                <div class="icon-text pl-4">
                                    <h5 class="mb-0"><?php _e("Measure and report on the performance of your content")?></h5>
                                </div>
                            </div>
                            <p class="mt-4 mb-0"><?php _e("Get in-depth insights to grow your reach, engagement, and sales")?></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card px-3">
                        <div class="card-body py-5">
                            <div class="d-flex align-items-center">
                                <div class="icon bg-gradient-info text-white rounded-circle icon-shape shadow-info">
                                    <i class="far fa-thumbs-up"></i>
                                </div>
                                <div class="icon-text pl-4">
                                    <h5 class="mb-0"><?php _e("Monitor engagement across all your social channels")?></h5>
                                </div>
                            </div>
                            <p class="mt-4 mb-0"><?php _e("Engage with your audience & build a community that loves your brand.")?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="slice slice-xl has-floating-items bg-gradient-primary" id=sct-call-to-action><a href="#sct-call-to-action" class="tongue tongue-up tongue-section-secondary" data-scroll-to>
            <i class="far fa-angle-up"></i>
        </a>
        <div class="container text-center">
            <div class="row">
                <div class="col-12">
                    <h1 class="text-white m-b-40"><?php _e("Brand Success drives business success")?></h1>
                    <div class="row justify-content-center mt-4">
                        <div class="col-lg-8">
                            <p class="lead text-white">
                                <?php _e("When competition for attention is fierce, and every customer is an influencer, growing an inspiring brand is a key success factor for your organization. From SMEs to large corporations, agencies and non-profits, teams of all sizes need to embrace the processes, workflows and tools that foster Brand Success.")?>
                                <br/>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container floating-items">
            <div class="icon-floating bg-white floating">
                <span></span>
                <img alt="" src="<?php _e( get_theme_frontend_url("assets/img/apps.svg") )?>" class="svg-inject">
            </div>
            <div class="icon-floating icon-lg bg-white floating">
                <span></span>
                <img alt="" src="<?php _e( get_theme_frontend_url("assets/img/apple.svg") )?>" class="svg-inject">
            </div>
            <div class="icon-floating icon-sm bg-white floating">
                <span></span>
                <img alt="" src="<?php _e( get_theme_frontend_url("assets/img/ballance.svg") )?>" class="svg-inject">
            </div>
            <div class="icon-floating icon-lg bg-white floating">
                <span></span>
                <img alt="" src="<?php _e( get_theme_frontend_url("assets/img/book.svg") )?>" class="svg-inject">
            </div>
            <div class="icon-floating bg-white floating">
                <span></span>
                <img alt="" src="<?php _e( get_theme_frontend_url("assets/img/chat.svg") )?>" class="svg-inject">
            </div>
            <div class="icon-floating icon-sm bg-white floating">
                <span></span>
                <img alt="" src="<?php _e( get_theme_frontend_url("assets/img/coffee.svg") )?>" class="svg-inject">
            </div>
        </div>
    </section>

    <section class="slice slice-lg bg-section-secondary overflow-hidden faqHeader" id="faq">
        <div class="container position-relative zindex-100">
            <div class="mb-5 px-3 text-center">
                <h3 class=" mt-4"><?php _e("Frequently Asked Questions")?></h3>
                <div class="fluid-paragraph mt-3">
                    <p class="lead lh-180"><?php _e("We've crafted this FAQ page to answer many of your frequently asked questions")?></p>
                </div>
            </div>
            <div class="row justify-content-center">
              <div class="col-12 col-sm-10 col-lg-8">
                
                <div class="accordion faq-accordian" id="faqAccordion">
                    <?php foreach ($faqs as $key => $faq): ?>
                        <div class="card border-0 wow fadeInUp" data-wow-delay="0.2s">
                            <div class="card-header" id="heading<?php _e( $key )?>">
                                <h6 class="mb-0 collapsed" data-toggle="collapse" data-target="#collapse<?php _e( $key )?>" aria-expanded="true" aria-controls="collapse<?php _e( $key )?>"><?php _e($faq->name)?><span class="lni-chevron-up"></span></h6>
                            </div>
                            <div class="collapse" id="collapse<?php _e( $key )?>" aria-labelledby="headingOne" data-parent="#faqAccordion">
                                <div class="card-body">
                                    <?php _e( htmlspecialchars_decode( $faq->content , ENT_QUOTES) , false)?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>

              </div>
            </div>
        </div>
    </section>

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
                        <input type="text" class="form-control" placeholder="<?php _e("Enter your email")?>" name="email">
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