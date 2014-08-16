<?php
/**
 * Created by PhpStorm.
 * User: TiagoGouvea
 * Date: 15/08/14
 * Time: 17:44
 */
//echo "tiago";
//var_dump($evento);
//echo "tiago";
?>
<div itemscope itemtype="http://schema.org/Event" class="evento">
    <h1>
        <a itemprop="url" href="<?php echo get_permalink($evento->ID) ?>">
            <span itemprop="name"><?php echo $evento->post_title; ?></span>
        </a>
        <div class="plusone-container">
           +1
        </div>
    </h1>

    <meta itemprop="startDate" content="2016-04-21T20:00">

    <?php if ($evento->noFuturo): ?>
        Data: 16 de Agosto de 2014 — 16 de Agosto de 2014
        Hora: 10:00 (IST)
        URL de evento do Google+: https://plus.google.com/u/0/events/cm9p4hnlnec2o64b1plrjgom1lg
        Local:

        <h4 style='padding:0 0 0 0;'>
            <?php echo PLib::dataRelativa($evento->data, false, false) . " as " . $evento->hora . " no " . $evento->local()->post_title; ?>
        </h4>
    <?php else: ?>

    <?php endif; ?>

    <a href="<?php echo get_permalink($evento->ID) ?>" title="<?php echo $evento->post_title; ?>">
        <?php echo get_the_post_thumbnail($evento->ID); ?>
    </a>
    <?php echo $evento->post_excerpt; ?>

    <header itemprop="location" itemscope itemtype="http://schema.org/Place">Local:</header>

    <div id="map_canvas" class="details-map" style="height: 360px; width: 100%;"></div>
    <script type="text/javascript">
        $(document).ready(function() {
            var event_map = new devsite.events.EventMap();
            event_map.CreateMapDetails('Lakshman Kadirgamar Institute, Horton Place, Colombo, Western Province, Sri Lanka');
        });
    </script>

    <br><br>
    Mais detalhes
</div>