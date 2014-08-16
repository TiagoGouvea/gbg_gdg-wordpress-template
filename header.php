<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html class='v2' dir='ltr' xmlns='http://www.w3.org/1999/xhtml' xmlns:b='http://www.google.com/2005/gml/b'
      xmlns:data='http://www.google.com/2005/gml/data' xmlns:expr='http://www.google.com/2005/gml/expr'>
<head>

    <?php
    $group = get_theme_mod( 'theme_group', 'gdg');
    ?>

    <meta content='text/html; charset=utf-8' http-equiv='Content-Type'/>
    <meta content='text/html; charset=UTF-8' http-equiv='Content-Type'/>
    <link href='<?php echo get_template_directory_uri()."/img/".$group."_favicon.ico"; ?>' rel='icon' type='image/x-icon'/>
    <link href='http://www.gdgaracaju.com.br/' rel='canonical'/>
    <link rel="alternate" type="application/atom+xml" title="GDG Aracaju - Atom" href="http://www.gdgaracaju.com.br/feeds/posts/default"/>
    <link rel="alternate" type="application/rss+xml" title="GDG Aracaju - RSS" href="http://www.gdgaracaju.com.br/feeds/posts/default?alt=rss"/>
    <link rel="service.post" type="application/atom+xml" title="GDG Aracaju - Atom" href="http://www.blogger.com/feeds/8636717865016386665/posts/default"/>
    <link rel="openid.server" href="http://www.blogger.com/openid-server.g"/>
    <link rel="openid.delegate" href="http://www.gdgaracaju.com.br/"/>
    <link href='http://www.gdgaracaju.com.br/' rel='canonical'/>
    <meta content='width=1100' name='viewport'/>
    <meta content='text/html; charset=UTF-8' http-equiv='Content-Type'/>
    <meta content='blogger' name='generator'/>

    <link href='http://www.gdgaracaju.com.br/favicon.ico' rel='icon' type='image/x-icon'/>
    <link href='http://www.gdgaracaju.com.br/' rel='canonical'/>
    <link rel="alternate" type="application/atom+xml" title="GDG Aracaju - Atom" href="http://www.gdgaracaju.com.br/feeds/posts/default"/>
    <link rel="alternate" type="application/rss+xml" title="GDG Aracaju - RSS" href="http://www.gdgaracaju.com.br/feeds/posts/default?alt=rss"/>
    <link rel="service.post" type="application/atom+xml" title="GDG Aracaju - Atom" href="http://www.blogger.com/feeds/8636717865016386665/posts/default"/>
    <link rel="openid.server" href="http://www.blogger.com/openid-server.g"/>
    <link rel="openid.delegate" href="http://www.gdgaracaju.com.br/"/>

    <title><?php bloginfo('name'); ?></title>
    <link type='text/css' rel='stylesheet' href='<?php bloginfo('stylesheet_url'); ?>'/>
    <link type='text/css' rel='stylesheet' href='<?php echo get_template_directory_uri()."/style_".$group.".css"; ?>'/>

<!--    <script src='http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js' type='text/javascript'></script>-->

</head>

<body>
<header>
    <div class='container'>
        <div class='brand'>
            <?php if (get_theme_mod('themeslug_logo')) : ?>
                <div class='site-logo'>
                    <a href='<?php echo esc_url(home_url('/')); ?>' title='<?php echo esc_attr(get_bloginfo('name', 'display')); ?>' rel='home'>
                        <img src='<?php echo esc_url(get_theme_mod('themeslug_logo')); ?>' alt='<?php echo esc_attr(get_bloginfo('name', 'display')); ?>'>
                    </a>
                </div>
            <?php else : ?>
                <hgroup>
                    <h1 class='site-title'>
                        <a href='<?php echo esc_url(home_url('/')); ?>' title='<?php echo esc_attr(get_bloginfo('name', 'display')); ?>' rel='home'>
                            <?php bloginfo('name'); ?>
                        </a>
                    </h1>

                    <h2 class='site-description'><?php bloginfo('description'); ?></h2>
                </hgroup>
            <?php endif; ?>
        </div>
    </div>
</header>


<div class='header-nav'>
    <div class='navbar navbar-static container' id='navbar-example'>
        <?php
        $mainMenu = array(
            'theme_location' => 'main_menu',
            'menu' => '',
            'container' => 'div',
            'container_class' => 'menu_main_container',
            'container_id' => '',
            'menu_class' => 'menu_main',
            'menu_id' => 'menu_main',
            'echo' => true,
            'fallback_cb' => 'wp_page_menu',
            'before' => '',
            'after' => '',
            'link_before' => '',
            'link_after' => '',
            'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
            'depth' => 0,
            'walker' => ''
        );

        wp_nav_menu($mainMenu);

        ?>
    </div>
</div>


<div class='container'>
    <div class="date-section">