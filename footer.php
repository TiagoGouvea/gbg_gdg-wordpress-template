    </div>
</div>
<hr>
<?php

    $group = get_theme_mod( 'theme_group', 'gdg');
    $gsite = ($group=='gdg' ? 'https://developers.google.com/groups/' : 'https://www.google.com/landing/gbg/groups/directory/' );
    $gtitle = ($group=='gdg' ? 'Developers' : 'Business' );

    $footerMenu = array(
        'theme_location' => 'footer_menu',
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
    wp_nav_menu($footerMenu);
?>
<hr>

<footer>
    <div class="container">
        <div class="span3">
            <div class="footerLeft section" id="footerLeft">
                <div class="widget LinkList" id="LinkList1">
                    <ul class="nav">
                        <li><h6>Nosso <?php echo strtoupper($group); ?></h6></li>
                        <?php
                        $chapter = get_theme_mod( 'theme_chapter', null);
                        $ggroup = get_theme_mod( 'theme_ggroup', null);
                        $github = get_theme_mod( 'theme_github', null);

                        if ($chapter!=null) echo "<li><a href='$chapter'>".strtoupper($group)." Chapter</a></li>";
                        if ($ggroup!=null) echo "<li><a href='$ggroup'>Mailing List</a></li>";
                        if ($github!=null) echo "<li><a href='$github'>GitHub</a></li>";
                        ?>
                    </ul>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
        <div class="span3">
            <div class="footerMid section" id="footerMid">
                <div class="widget LinkList" id="LinkList2">
                    <ul class="nav">
                        <li><h6>Recursos</h6></li>
                        <li><a href="https://github.com/GDGAracaju">GDGAracaju no GitHub</a></li>
                        <li><a href="http://developers.google.com/">Google Developers</a></li>
                    </ul>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
        <div class="span3">
            <div class="footerRight section" id="footerRight">
                <div class="widget LinkList" id="LinkList3">
                    <ul class="nav">
                        <li><h6>Siga-nos!</h6></li>
                        <?php
                        $gplus = get_theme_mod( 'theme_gplus', null);
                        $facebook = get_theme_mod( 'theme_facebook', null);
                        $twitter = get_theme_mod( 'theme_twitter', null);
                        if ($gplus!=null) echo "<li><a href='$gplus' rel='publisher'>Google+</a></li>";
                        if ($facebook!=null) echo "<li><a href='$facebook'>Facebook</a></li>";
                        if ($twitter!=null) echo "<li><a href='$twitter'>Twitter</a></li>";
                        ?>
                    </ul>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="copyright-wrap">
        <div class="container">
            <p class="span10">
                <!--- If you're using gdg-blogger-template developed by semicolon developers please don't remove this line -->
                <a href="<?php echo $gsite; ?>">Google <?php echo $gtitle; ?> Group</a> |
                <a href="https://code.google.com/p/gdg-blogger-template/">Template Designed &amp; Developed by GDG
                    Kathmandu</a> |
                <a href="http://blogger.com/">Powered By Blogger</a>
            </p>
        </div>
    </div>
</footer>


<script src="https://apis.google.com/js/platform.js" async defer>
    {lang: 'pt-BR'}
</script>



</body>
</html>