

<?php
/**
 * The Header template for our theme
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */
?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) & !(IE 8)]><!-->
<html <?php language_attributes(); ?>>
    <!--<![endif]-->
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width">
        <!--<title><?php // wp_title('|', true, 'right'); ?></title>-->
        <title>Blog | My Closet Concierge</title>
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
        <!--[if lt IE 9]>
        <script src="<?php echo get_template_directory_uri(); ?>/js/html5.js"></script>
        <![endif]-->
        <?php wp_head(); ?>
        <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/bootstrap.min.css" />
        <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/all.css" />
        <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/responsive.css" />
        <script src="<?php echo get_template_directory_uri(); ?>/js/jquery.js"></script>
        <script src="<?php echo get_template_directory_uri(); ?>/js/bootstrap.min.js"></script>
        <script src="<?php echo get_template_directory_uri(); ?>/js/custom.js"></script>
    </head>
    <?php
    $siteurl = 'http://'.$_SERVER['SERVER_NAME'].'/' ;
    ?>
    <body <?php body_class(); ?>>




        <div id="page" class="allWrapper">
            <div class="footFix">


                <header class="header">
                    <div class="container-fluid loginDetails clearfix">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <div class="search">
                            <?php get_search_form(); ?>
                        </div>
                        <div class="links">
                            <a href="<?php echo $siteurl; ?>membership/account/">My Account</a> 
                            <a href="<?php echo $siteurl; ?>checkout/cart/">My Bag</a> 
                            <a href="<?php echo $siteurl; ?>/checkout">Checkout</a> 
                            <?php if ( is_user_logged_in() ) {  
                                echo 'yes'; 
                                }else{
//                                    echo 'no'.Mage::getSingleton('customer/session')->getId();
                                } ?> 
                            <a href="<?php echo $siteurl; ?>membership/account/">Sign In</a>
                        </div>
                        
                    </div>
                    <div class="container quick-access">
                        <a href="<?php echo $siteurl; ?>home" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" class="logo">
                            <img src="<?php echo get_site_url(); ?>/wp-content/themes/twentythirteen/images/logo.png" alt="logo"/>
                            <h1><?php bloginfo('name'); ?></h1>
                            <h2><?php bloginfo('description'); ?></h2>                       
                        </a> 
                    </div>


                    <nav class="mainMenu">
                        <div class="container">
                            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">                 
                                <ul class="nav navbar-nav">

                                    <?php wp_nav_menu(array('theme_location' => 'primary', 'menu_class' => 'nav-menu', 'menu_id' => 'primary-menu')); ?>
                                </ul>



                                <ul class="nav navbar-nav mobileFooter">

                                    <li><a href="<?php echo $siteurl; ?>testimonials/?___store=default">Testimonials</a></li>                        
                                    <li><a href="<?php echo $siteurl; ?>press/?___store=default">Press</a></li>                   
                                    <li><a href="<?php echo $siteurl; ?>careers/?___store=default">Careers </a></li>                       
                                    <li><a href="mailto:info@myclosetconcierge.com?subject=Message from My Closet Concierge Visitor">Contact </a></li>                       
                                    <li><a href="<?php echo $siteurl; ?>termservices/?___store=default">Terms and Conditions </a></li>
                                    <li><a href="<?php echo $siteurl; ?>privacy/?___store=default">Privacy </a></li>
                                </ul>

                            </div>
                        </div>
                    </nav>



                </header>



                <div class="clearfix"></div>



                <div id="main" class="contentWrapper">
                    <div class="container">
                        <?php if (is_front_page()) : ?>
                            <div class="subhead">
                                <h2>BLOG</h2>
                            </div>

                            <style>
                                .hentry {
                                    padding: 0 0 40px 0 !important;
                                }
                            </style>
                        <?php endif; // is_front_page()  ?>

                        <style>             
                            span.logo{
                                background-image: url("<?php echo get_site_url(); ?>/wp-content/themes/twentythirteen/images/logo.png");
                                height: 134px;
                                width: 100%;
                                display: inline-block;
                                background-repeat: no-repeat;
                                background-size: 135px;
                            }

                            .tagcloud a{
                                font-size:14px !important;
                            }
                        </style>
