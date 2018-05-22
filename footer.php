    
    </div> <!-- container -->
</main> <!-- main -->

<?php if(empty($_SERVER['REDIRECT_URL']) || !empty($_SERVER['REDIRECT_URL']) && strpos($_SERVER['REDIRECT_URL'],'/dashboard') === false):?>
<footer class="col-xs-12 no-padding site-footer">
    <div class="col-xs-12 no-padding footer-bar"></div>
    <div class="container">
        <div class="col-xs-12 footer-widgets">
            <?php if(is_active_sidebar('footer1')):?>
            <div class="col-xs-3 footer-widget-area">
                <?php dynamic_sidebar('footer1');?>
            </div>
            <?php endif;?>

            <?php $userData = isLoggedIn();?>
            <?php $showClientSidebar = ($userData && (isRol('admin',$userData) || isRol('professional',$userData))) ? false : true;?>

            <?php if($showClientSidebar):?>
                <?php if(is_active_sidebar('footer2')):?>
                <div class="col-xs-3 footer-widget-area">
                    <?php dynamic_sidebar('footer2');?>
                </div>
                <?php endif;?>
            <?php endif;?>

            <?php if(is_active_sidebar('footer3')):?>
            <div class="col-xs-3 footer-widget-area">
                <?php dynamic_sidebar('footer3');?>
            </div>
            <?php endif;?>

            <?php if(is_active_sidebar('footer4')):?>
            <div class="col-xs-3 footer-widget-area">
                <?php dynamic_sidebar('footer4');?>
            </div>
            <?php endif;?>
        </div>
        <div class="col-xs-12 signature"><?php bloginfo('name');?> - &copy <?php echo date('Y');?></div>
    </div>
</footer>
<?php endif;?>

<?php wp_footer();?>
</body>

</html>