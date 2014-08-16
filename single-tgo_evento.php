<?php 
get_header(); 

if(!is_user_logged_in()){
    // Registrar visita
    $visitante = Referer::getVisitante();
    // Se não for Bot, registrar no banco de visualizações de evento
    if (!$visitante->bot)
        Referer::registrarVisitaEvento($visitante,$post->ID);
}

$evento = Eventos::obterPorId($post->ID);

?>


<div id="content" class="clearfix">
    <div class="content_left">
        
        
        <div class="">
            <h1><?php the_title(); ?></h1>
            <br>&nbsp;<br>
            <?php the_post_thumbnail('medium'); ?>
            <br>&nbsp;<br>
        </div>

        
        <?php if ($evento->release!=""): ?>
            Este evento foi organizado por <?php echo $evento->instrutor()->nome; ?> e realizado em <?php echo PLib::dataRelativa($evento->data); ?> no <?php echo $evento->local->post_title; ?>.
            
            <h2>Release do evento</h2>
            <p><?php 
                $content = apply_filters( 'the_content', $evento->release );
                $content = str_replace( ']]>', ']]&gt;', $content );
                echo $content;
            ?></p>
            
            
        
        <?php else: ?>
        
            <p><?php 
                $content = apply_filters( 'the_content', $evento->descricao3 );
                $content = str_replace( ']]>', ']]&gt;', $content );
                echo $content;
            ?></p>

            <?php if ($evento->noFuturo): ?>
                <div class="">
                    <h2>Data</h2>
                    <p><?php echo PLib::dataRelativa($evento->data." ".$evento->hora,false,false); ?></p>
                </div>
            <?php elseif ($evento->acontecendo): ?>
                <div class="">
                    <h2>Data e hora</h2>
                    <p>Evento em andamento agora! Término <?php echo strtolower(PLib::dataRelativa($evento->dataFim." ".$evento->horaFim)); ?>.</p>
                </div>
            <?php else: ?>
                <div class="">
                    <h2>Data</h2>
                    <p>Evento realizado <?php echo PLib::dataRelativa($evento->data." ".$evento->hora,false,false); ?></p>
                </div>
            <?php endif; ?>
                        
            <?php if (!$evento->preInscricao): ?>
                <?php if ($evento->instrutor!=null): ?>
                    <div class="">
                        <h2>Organizador</h2>
                        <p><?php 
                        $instrutor = Instrutor::obterPorId($evento->id_instrutor);
                        echo $instrutor->display_name; 
                        ?>
                        </p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php 
            if ($evento->inscricaoAberta): ?>
                <div class=""> 
                    <h2>Inscrição</h2>
                    <?php if ($evento->pago=='gratuito'): ?>
                        <p>Evento gratuito e aberto ao público, necessário apenas realizar a inscrição!</p>
                    <?php else: ?>
                        <p><?php 
                        $precos = PrecoEvento::obterPorEvento($evento->ID);
                        foreach ($precos as $preco){
                            if ($preco->encerrado==1){
                                echo "<del>";
                                echo $preco->titulo.' - encerrado!<Br>';
                                echo "</del>";
                            } else {
                                echo $preco->titulo.' - '.PLib::formatarGrana($evento->valor_atual).'<Br>';
                                break;
                            }
                        }
                        ?></p>
                    <?php endif; ?>
                    <br>
                    <p><a  href="<?php echo the_permalink()."?inscricao=1"; ?>" class="botaoInscrever">Realizar Inscrição!</a></p>
                </div>
            <?php endif; ?>

            <div class="clearfix"></div>

            <?php if (!$evento->preInscricao && $evento->local!=null): ?>
                <div class="clear"></div>
                <div class="widget">
                    <div class="widget-title">
                        <h2 class="nomargin">Local do Evento</h2>
                    </div>
                    <div class="widget-content">
                        <p><strong><?php echo $evento->local->post_title; ?></strong><br>
                        <?php echo nl2br($evento->local->endereco); ?><Br>
                        <?php echo $evento->local->telefone; ?><Br>
                        <?php echo $evento->local->site; ?>
                        </p>

                        <?php 
                        $pos=array();
                        $pos['latitude']=$evento->local->latitude;
                        $pos['place']=$evento->local->post_title;
                        $pos['cidade']=$evento->local->cidade;
                        $pos['longitude']=$evento->local->longitude;
                        $pos['zoom']=16;
                        $pos['height']=200;
                        $pos['description']=$evento->local->post_title;
                        //echo shortcode_mapa($pos);
                        ?>
                        
                        <?php echo get_the_post_thumbnail($evento->id_local); ?>
                    </div>
                </div>
             <?php else: ?>
                <h2>Local do Evento</h2>
                <p>Este evento será realizado em Juiz de Fora. O local exato ainda será definido.</p>
             <?php endif; ?>
        <?php endif; ?>

            
            
            
            
    </div>

    <div class="clear"></div>
</div><!--### ende content ###-->

<?php get_footer(); ?>