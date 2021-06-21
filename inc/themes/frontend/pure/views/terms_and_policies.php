<?php include 'top.php'?>
<?php include 'header.php'?>
<div class="main-content">
    <div class="bg-info w-100 h-76"></div>

    <section class="section-terms">
        <div class="container">
            <?php _e( htmlspecialchars_decode(get_option('terms_of_services', ''), ENT_QUOTES) , false)?>
        </div>
    </section>
</div>

<?php include 'footer.php'?>
<?php include 'bottom.php'?>