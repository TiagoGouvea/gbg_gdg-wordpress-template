<?php


// Register menus
function register_menu()
{
    register_nav_menus(
        array(
            'main_menu' => 'Main Menu',
            'footer_menu' => 'Footer Menu'
        ));
}

add_action('init', 'register_menu');


// Register Sidebar
register_sidebar(array(
    'name' => 'Sidebar',
    'id' => 'sidebar',
    'before_widget' => '<li id="%1$s" class="widget %2$s">',
    'after_widget' => '</li>',
    'before_title' => '<h2 class="widgettitle">',
    'after_title' => '</h2>',
));

// Suport post thumbnails
add_theme_support('post-thumbnails');


// Customize Theme
function theme_customizer($wp_customize)
{
    require_once 'classes.php';
    // GBG/GDG
    $wp_customize->add_section('theme_group', array(
        'title' => __('Google Group', 'themesgorup'),
        'priority' => 20,
        'description' => 'Define if it\'s a Bussines or Developers Group',
    ));
    $wp_customize->add_setting( 'theme_group');
    $wp_customize->add_control( new WP_Customize_Control_Group( $wp_customize, 'theme_group', array(
        'label'   => 'Group',
        'section' => 'theme_group',
        'settings'   => 'theme_group',
    ) ) );

    // Chapter
    $wp_customize->add_setting('theme_chapter');
    $wp_customize->add_control('theme_chapter', array(
        'label'      => __('Chapter URL', 'theme_chapter'),
        'section'    => 'theme_group',
        'settings'   => 'theme_chapter',
    ));

    // MailingList
    $wp_customize->add_setting('theme_ggroup');
    $wp_customize->add_control('theme_ggroup', array(
        'label'      => __('GGroup Mailing List URL', 'theme_ggroup'),
        'section'    => 'theme_group',
        'settings'   => 'theme_ggroup',
    ));

    // GitHub
    $wp_customize->add_setting('theme_github');
    $wp_customize->add_control('theme_github', array(
        'label'      => __('GitHub URL', 'theme_github'),
        'section'    => 'theme_group',
        'settings'   => 'theme_github',
    ));

    // Logo
    $wp_customize->add_section('themeslug_logo_section', array(
        'title' => __('Logo', 'themeslug'),
        'priority' => 30,
        'description' => 'Upload a logo to replace the default site name and description in the header',
    ));
    $wp_customize->add_setting('themeslug_logo');

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'themeslug_logo', array(
        'label' => __('Logo', 'themeslug'),
        'section' => 'themeslug_logo_section',
        'settings' => 'themeslug_logo',
    )));



    // Social
    $wp_customize->add_section('theme_social', array(
        'title'    => __('Social', 'theme_gplus'),
        'priority' => 31,
    ));

    // G+
    $wp_customize->add_setting('theme_gplus');
    $wp_customize->add_control('theme_gplus', array(
        'label'      => __('Google+ Profile', 'theme_gplus'),
        'section'    => 'theme_social',
        'settings'   => 'theme_gplus',
    ));

     // Facebook
    $wp_customize->add_setting('theme_facebook');
    $wp_customize->add_control('theme_facebook', array(
        'label'      => __('Facebook Profile', 'theme_facebook'),
        'section'    => 'theme_social',
        'settings'   => 'theme_facebook',
    ));

    // Twitter
    $wp_customize->add_setting('theme_twitter');
    $wp_customize->add_control('theme_twitter', array(
        'label'      => __('Twitter Profile', 'theme_twitter'),
        'section'    => 'theme_social',
        'settings'   => 'theme_twitter',
    ));
}

add_action('customize_register', 'theme_customizer');

$group = get_theme_mod( 'theme_group', 'gdg');

function Ari_customize_register( $wp_customize ) {
    require_once 'classes.php';
}
add_action( 'customize_register', 'Ari_customize_register' );

error_reporting( E_ALL ^ E_NOTICE );