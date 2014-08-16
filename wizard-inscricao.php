<?php 
get_header(); 
?>
<div id="content" class="clearfix">
    <div class="content_left">

<?php
$evento = Eventos::obterPorId($post->ID);

// URL atual
$urlForm =  $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
if (strpos($urlForm, 'ttp://')==false){
    $urlForm="http://".$urlForm;
}

// Determinar etapa
$etapa = $_POST['etapa'];
if ($etapa == null){
    $etapa = 1;
}
$avancarTexto="Avançar";


// Se estivermos recebendo um ticket, localizar a inscrição
$ticket=$_GET['ticket'];
if ($ticket!=null){
    $idInscricao=$ticket/13;
    $inscricao = Inscricao::obterPorId($idInscricao);
    if ($inscricao==null){
        die('A URL informada não é válida.');
    }
    //var_dump($inscricao);
    
    //'confirmado'
    $pessoa = Pessoa::obterPorId($inscricao->id_pessoa);
    $_SESSION['inscricao']=$inscricao;
    $_SESSION['pessoa']=$pessoa;
    // Na verdade.. só pode pagar.
    $etapa=4;
    // De acordo com a situação da inscrição, ir para uma etapa
    $textoTicket = $pessoa->nome.", confirme agora sua inscrição!";
    // Retirar ticket da URL, senão vai voltar pra cá a todo tempo
    $urlForm = substr($urlForm,0,  strpos($urlForm, '&ticket'));
}

//die($url);

// Tipos de template
$exibirTitulo=false;
$exibirResumo=false;
$exibirResumo=$template==3;
$exibirTitulo=$template==3;

// Ter evento em mãos
$evento = Eventos::obterPorId(get_the_ID());
//var_dump($evento);

// Estamos recebendo a confirmação do pagamento?
$transaction_id=$_GET['transaction_id'];

// Postando
if (count($_POST) > 0) {
    // Sanitizar e validar entrada de dados
    $dados = false;
    $erro = null;
    $avancar = false;
    // Etapa 1
    if ($etapa == 1) {
        $email = is_email(sanitize_email($_POST['email']));
        $dados = $email != null;
        if ($dados){
            $_SESSION['inscricao_email']=$email;
        } else {
            if (!$email)
                $erro = "Informe um email válido";
        }
    }
    // Etapa 2
    if ($etapa == 2) {
        $nome = sanitize_text_field($_POST['nome']);
        if (!$nome)
            $erro = "Informe seu nome";
        $dados = $nome != null;
    }

    // Trabalhar com dados
    // Etapa 1
    if ($etapa == 1 && $dados) {
        // Tentar localizar pessoa
        $pessoa = Pessoa::obterPorEmail($email);
        // Encontramos alguém?
        if ($pessoa != null) {
            // Trazer dados para sessão
            $_SESSION['pessoa']=$pessoa;
            $pessoaNome=$pessoa->nome;
        } else {
            // Nova pessoa
            $pessoa = new stdClass();
            $pessoa->newsletter = true;
        }

        $avancar = true;
        
        // Se confirmação imediata
        if ($evento->confirmacao=='imediata' || $evento->confirmacao=='posterior')
            $avancarTexto="Concluir Inscrição";
        
        // Se é apenas pré-inscricao
        if ($evento->confirmacao=='preinscricao')
            $avancarTexto="Concluir pré-inscrição";
    }
    // Etapa 2
    if ($etapa == 2 && $dados) {
        // Ok. Temos todos os dados da pessoa
        // Adicionar cada campo 
        $email=$_SESSION['inscricao_email'];
        $pessoaArray=array();
        $pessoaArray['nome']=sanitize_text_field($_POST['nome']);
        $pessoaArray['celular']=sanitize_text_field($_POST['celular']);
        $pessoaArray['newsletter']=($_POST['newsletter']!=null ? '1':'0');
        $pessoaArray['end_cep']=sanitize_text_field($_POST['end_cep']);
        $pessoaArray['end_logradouro']=sanitize_text_field($_POST['end_logradouro']);
        $pessoaArray['end_numero']=sanitize_text_field($_POST['end_numero']);
        $pessoaArray['end_complemento']=sanitize_text_field($_POST['end_complemento']);
        $pessoaArray['end_bairro']=sanitize_text_field($_POST['end_bairro']);
        $pessoaArray['end_cidade']=sanitize_text_field($_POST['end_cidade']);
        $pessoaArray['end_estado']=sanitize_text_field($_POST['end_estado']);
        
        $extras=array();
        
        if ($_SESSION['pessoa']!=null){
            $pessoa = Pessoa::obterPorId($_SESSION['pessoa']->id);
            $pessoaExtras = $pessoa->extras;
            if ($pessoaExtras!=null){
                $extras=json_decode($pessoaExtras,true);
            }
        
        }
        
        
        // Existem campos extras?
        if ($evento->camposExtras!=null){
            foreach ($evento->camposExtras as $extraIndice => $extraTitulo){
                if ($_POST[$extraIndice]!=null){
                    $extraValor=sanitize_text_field($_POST[$extraIndice]);
                    //echo "$extraIndice = $extraValor<br>";
                    $extras[$extraIndice]=array();
                    $extras[$extraIndice]['titulo']=$extraTitulo;
                    $extras[$extraIndice]['valor']=$extraValor;
                }
            }
            if (count($extras)>0){
                $extras=json_encode($extras);
                $pessoaArray['extras']=$extras;
            }
            //var_dump($extras);die();
        }
        
        //var_dump($extras);die();
        
        //var_dump($pessoaArray);
        // Garantir que não seja incluida duas vezes (mover tudo isso pra classe)
        $pessoa = Pessoa::certificarPessoa($email,null,$pessoaArray);
        
        //var_dump($pessoa);die();
        
        // Obter Preço atual
        $preco = PrecoEvento::obterAtualPorEvento($evento->ID);
        if ($preco!=null)
            $id_preco=$preco->id;
        
        // Já salvar registro de inscrição
        $inscricao = Inscricao::certificarInscricao($evento,$pessoa,$evento->confirmacao=="preinscricao",$id_preco);
        $_SESSION['inscricao']=$inscricao;
        
        // O evento tem categorias? Se não pular uma etapa
        $etapa = $etapa + 1;
        
        // O evento é gratuito? Se for pular uma etapa
        if ($evento->pago=='gratuito'){
            $etapa = $etapa + 1;
        }
        
        // Se é apenas pré-inscricao - Pular duas etapas
        if ($evento->confirmacao=='preinscricao')
            $etapa = $etapa + 1;
        
        // Se é confirmação imediata e estamos indo para a última etapa, confirmar a inscrição no banco de dados
        if ($evento->confirmacao=='imediata' && $etapa==4)
            Inscricao::confirmarInscricao($inscricao);
        
        $avancar = true;
    }

    if ($avancar)
        $etapa = $etapa + 1;
} 

if ($_SESSION['inscricao']!=null && $inscricao==null)
    $inscricao=$_SESSION['inscricao'];

// Ajustes e tratamentos

// Estamos recebendo um pagamento?
if ($transaction_id){
    //echo "<h1>$transaction_id</h1>";
    //?transaction_id=E884542-81B3-4419-9A75-BCC6FB495EF1
    // Validar o pagamento
    
    // Incluir PagSeguro
    include_once ABSPATH . 'wp-content/plugins/Eventos/vendor/PagSeguro/PagSeguroLibrary.php';
    // Obter a transação
    // Verificar situação
    $credentials = new PagSeguroAccountCredentials($evento->organizador->pagseguro_email,$evento->organizador->pagseguro_token);  
    $transaction = PagSeguroTransactionSearchService::searchByCode(  
        $credentials,  
        $transaction_id  
    );  
    
    //echo "<pre>"; var_dump($transaction);echo "</pre>";
    // Gross - Valor
    // Net - Taxa
    
    //'INITIATED' => 0,
    //'WAITING_PAYMENT' => 1,
    //'IN_ANALYSIS' => 2,
    //'PAID' => 3,
    //'AVAILABLE' => 4,
    //'IN_DISPUTE' => 5,
    //'REFUNDED' => 6,
    //'CANCELLED' => 7
    
    $status=$transaction->getStatus()->getValue();
    $reference=$transaction->getReference();
    $valor=$transaction->getGrossAmount();
    $valor_liquido = $transaction->getNetAmount();
    $taxa = $transaction->getFeeAmount();
    $data = $transaction->getDate();
    $data_pagseguro = $transaction->getLastEventDate();
    $codigo = $transaction->getCode();
    $forma_pagamento = $transaction->getPaymentMethod()->getCode()->getValue();
    //var_dump($forma_pagamento);die();

    
//    echo "reference: ".$transaction->getReference()."<br>";
//    echo "Gross: ".$transaction->getGrossAmount()."<br>";
//    echo "Status: ".$transaction->getStatus()->getValue()."<br>";
//    echo "Date: ".$transaction->getDate()."<br>";
//    echo "Net: ".$transaction->getNetAmount()."<br>";
//    echo "Codigo: ".$transaction->getCode()."<br>";
//    
    // Obter inscrição
    $inscricao = Inscricao::obterPorId($reference);
    
    // Pago?
    if ($status==3 || $status==4){
        // Sim
        if ($inscricao->confirmado!=1){
            //var_dump($inscricao);
            //echo "<h3>confirmar</h3>";
            // Registrar pagamento
            $inscricao = Inscricao::confirmarPagamento($reference,$status, $data,$valor,$valor_liquido,$taxa,$codigo,$data_pagseguro,$forma_pagamento);
            // Enviar emails de confirmação
            //var_dump($inscricao);
        }
    } else {
        // Ainda não - Registrar o que? A situação né...
        $inscricao = Inscricao::registrarDadosPagseguro($reference,$data,$codigo,$status,$data_pagseguro,$forma_pagamento);
    }
    
    // Direcionar a etapa condizente
    $etapa=5;
    
}


if ($etapa==1)
    $etapaTitulo='Identificação';
if ($etapa==2){
    $etapaTitulo='Informações de Contato';
}
if ($etapa==3) $etapaTitulo='Escolha de Categoria';

if ($etapa==4){
    $etapaTitulo='Pagamento';
    
   //var_Dump($pessoa);
    
    // Preparar pagamento PagSeguro
    $valor= number_format($evento->valor_atual(), 2, '.', '');
    //$valor= number_format('1.23', 2, '.', '');
    $reference=$inscricao->id;
    $nome=utf8_decode(trim($pessoa->nome));
    $email=trim($pessoa->email);
    $celular=PLib::somenteNumeros($pessoa->celular);
    if (strlen($celular)<=8) 
        $ddd=32;
    else
        $ddd=substr($celular,0,2);
    $celular=substr($celular,2);
    //var_Dump($ddd);var_Dump($celular);
    //http://inspirardigital.com.br/evento/treinamento-php/?inscricao=1&transaction_id=BC8A1049-D8FA-4698-BEFB-50050C30764D
    $url = get_permalink()."?inscricao=1";
    
    // Incluir PagSeguro
    include_once ABSPATH . 'wp-content/plugins/Eventos/vendor/PagSeguro/PagSeguroLibrary.php';

    // Criar requisição PagSeguro
    $paymentRequest = new PagSeguroPaymentRequest();
    $paymentRequest->setCurrency("BRL");
    $paymentRequest->setRedirectURL($url);
    $paymentRequest->addItem('0001', utf8_decode('Inscrição para '.$evento->post_title), 1, $valor);
    $paymentRequest->setReference($reference);
    // Dados da Pessoa
    $paymentRequest->setSender($nome, $email, $ddd, $celular);
    $paymentRequest->setRedirectUrl($url);

    if ($reference==null || $reference<1){
        die("Erro na inscrição. Falta de referencia para pagamento.");
    }
    try {
        // Criar credenticias
        $credentials = new PagSeguroAccountCredentials($evento->organizador->pagseguro_email,$evento->organizador->pagseguro_token);
        $urlPagSeguro = $paymentRequest->register($credentials);
    } catch (PagSeguroServiceException $e) {
        die('Erro no pagseguro: '.$e->getMessage());
    }
}

if ($etapa==5){
    $etapaTitulo='Confirmação de inscrição';
    // Limpar dados de inscrição da sessão
    $_SESSION['inscricao']=null;
    $_SESSION['pessoa']=null;
}

$temoInscricao = "inscrição"; 
if ($evento->confirmacao=="preinscricao") $temoInscricao = "pré-inscrição"; 



$estados = array("AC"=>"Acre", "AL"=>"Alagoas", "AM"=>"Amazonas", "AP"=>"Amapá","BA"=>"Bahia","CE"=>"Ceará","DF"=>"Distrito Federal","ES"=>"Espírito Santo","GO"=>"Goiás","MA"=>"Maranhão","MT"=>"Mato Grosso","MS"=>"Mato Grosso do Sul","MG"=>"Minas Gerais","PA"=>"Pará","PB"=>"Paraíba","PR"=>"Paraná","PE"=>"Pernambuco","PI"=>"Piauí","RJ"=>"Rio de Janeiro","RN"=>"Rio Grande do Norte","RO"=>"Rondônia","RS"=>"Rio Grande do Sul","RR"=>"Roraima","SC"=>"Santa Catarina","SE"=>"Sergipe","SP"=>"São Paulo","TO"=>"Tocantins");

// Wizard
// 1 - Identificar (cpf ou email)
// 2 - Completar dados
// 3 - Escolher plano de inscrição
// 4 - Pagar
// 5 - Confirmação final

?>

    <div class="">
        <div class="">
            <h1><?php echo $evento->post_title; ?></h1><br>&nbsp;<br>
            <?php if ($evento->confirmacao=='preinscricao'): ?>
                <h1>Me avise!</h1>
                <p>Ao realizar a pré-inscrição você deixa sua vaga reservada, ainda sem compromisso com a compra.</p>
                <p>Quando abrirmos as inscrições você receberá nosso contato lhe convidado para participar conosco deste treinamento.</p>
            <?php elseif ($evento->id_organizador!=597): ?>
                
            <?php else: ?>
                <?php if ($etapa == 1): ?>
                    <h1>Parabéns pela escolha!</h1>
                    <p>O mercado carece de pessoas de atitude como você!</p>
                    <p>Estamos certo que este é mais um passo rumo ao seu sucesso profissional!</p>
                <?php elseif ($etapa == 2): ?>
                    <h1>Mantendo contato</h1>
                    <p>Você receberá em seu email ou por telefone as informações necessárias sobre o treinamento.</p>
                    <p>Gostaríamos também de manter contato com você para eventos futuros. Se concordar, marque esta opção no formulário.</p>
                <?php elseif ($etapa == 3): ?>
                    <h1>Planos</h1>
                <?php elseif ($etapa == 4): ?>
                    <h1>Pagamento sem complicações</h1>
                    <p>Ao optar pelo pagamento com cartão de crédito você será direcionado a uma página do PagSeguro para realizar a transação, 
                        podendo inclusive dividir o valor do treinamento em várias parcelas. Fique tranquilo pois esta operação é totalmente segura.</p>
                    <p>Caso não consiga realizar o pagamento pelo PagSeguro, entre em contato conosco para realizar seu pagamento em dinheiro ou
                    por depósito bancário.</p>
                <?php elseif ($etapa == 5): ?>
                    <?php if ($evento->confirmacao=='pagamento'): ?>
                        <h1>Confirmação com PagSeguro</h1>
                        <p>Após voce voltar do PagSeguro, aguardamos a confirmação do seu pagamento. Dependendo da forma de pagamento selecionada a 
                        confirmação é imediata. Para pagamentos com boleto, a confirmação costuma chegar de dois a três dias após o pagamento.</p>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <div class="">
            <?php if ($exibirTitulo): ?>
                <h1><?php echo $evento->post_title; ?></h1>
            <?php endif; ?>

            <?php if ($exibirResumo): ?>
                <p><?php echo $evento->post_excerpt; ?></p>
            <?php endif; ?>

            <?php if ($erro): ?>
                <?php echo "<b>" . $erro . "</b>"; ?>
            <?php endif; ?>

            <div class="">
                <div class="">
                    <h3 class=""><?php echo $etapaTitulo; ?></h3></div>
                <div class="">

                    <form method="post" class="formatted-form" action="<?php echo $urlForm; ?>">
                        <input type="hidden" name="etapa" value="<?php echo $etapa; ?>">

                        <?php if ($etapa == 1): ?>
                            <?php if ($evento->inscricaoAberta()): ?>
                                <p>Para iniciar sua <?php echo $temoInscricao; ?> informe seu email no campo abaixo</p>
                                <div class="field-wrapper">
                                    <?php echo input_texto_simples('email', 'Email', 30); ?>
                                </div>
                            <?php else: ?>
                                <p>As inscrições para este evento já se encerraram.</P>
                                <?php $etapa=7; ?>
                            <?php endif ?>

                        <?php endif; ?>

                        <?php if ($etapa == 2): ?>
                            <h4>Nome</h4>
                            <div class="field-wrapper">
                                <?php echo input_texto_simples('nome', '', 30, $pessoa->nome); ?>
                            </div>

                            <h4>Email</h4>
                            <div class="field-wrapper">
                                <?php echo input_texto_simples('email', '', 30,$_SESSION['inscricao_email']); ?>
                            </div>
                            
                            <h4>Celular</h4>
                            <div class="field-wrapper">
                                <?php echo input_texto_simples('celular', '', 30, $pessoa->celular,'onkeypress="javascript:MascaraTelefone(this);"'); ?>
                            </div>
                            
                            <?php if ($evento->confirmacao!='preinscricao' && $evento->id_organizador==597): ?>
                                <h4>Endereço</h4>
                                <div class="field-wrapper">
                                    <?php echo input_texto_simples('end_cep', 'CEP', 30,$pessoa->end_cep,'onkeypress="javascript:MascaraCep(this);"' ); ?>
                                </div>
                                <div class="field-wrapper">
                                    <?php echo input_texto_simples('end_logradouro', 'Logradouro', 30,$pessoa->end_logradouro ); ?>
                                </div>
                                <div class="fourcol column">
                                    <div class="field-wrapper">
                                        <?php echo input_texto_simples('end_numero', 'Numero', 30 ,$pessoa->end_numero); ?>
                                    </div>
                                </div>
                                <div class="">
                                    <div class="field-wrapper">
                                        <?php echo input_texto_simples('end_complemento', 'Complemento', 30,$pessoa->end_complemento ); ?>
                                    </div>
                                </div>
                                <div class="">
                                    <div class="field-wrapper">
                                        <?php echo input_texto_simples('end_bairro', 'Bairro', 30,$pessoa->end_bairro ); ?>
                                    </div>
                                </div>
                                <div class="">
                                    <div class="field-wrapper">
                                        <?php echo input_texto_simples('end_cidade', 'Cidade', 30,$pessoa->end_cidade); ?>
                                    </div>
                                </div>
                                <div class="">
                                    <div class="field-wrapper">
                                        <?php echo input_select_simples('end_estado', 'Estado', $estados, $pessoa->end_estado); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                                
                            <?php 
                                    // Existem campos extras?

                            /*
                            empresa/Empresa ou instituição de ensino
cargo/Cargo ou curso
busca_gbg/O que busco no Google Developers Group
linguagens_programa/Em quais linguagens programo atualmente
linguagens_deseja/Linguagens ou tecnologias que desejo aprender mais
deseja_apresentar/Gostaria de apresentar conteúdos em outros eventos [ ]
freelancer/Desenvolvo projetos como freelancer [ ]
                            */
                                var_dump($evento->camposExtras);
                                if ($evento->camposExtras!=null){
                                    echo "<br><h3>Informações extra</h3><p>Por favor, preencha adequadamente os campos abaixo, pois em determinados eventos aprovaremos a inscrição de acordo com estes dados.</p>";

                                    $camposExtra='';
                                    // Obter extras da pessoa
                                    
                                     // Obter extras para exibição
                                    
                                    $pessoa = Pessoa::obterPorId($pessoa->id);
                                    $pessoaExtras = $pessoa->extras;
                                    if ($pessoaExtras!=null) $pessoaExtras=  json_decode ($pessoaExtras);
                                    //var_dump($pessoaExtras);
                
                                    foreach ($evento->camposExtras as $extraIndice => $extraTitulo):?>
                                            <?php
                                            // Este dado já existe nesta pessoa?
                                            $pessoaExtra = $pessoaExtras->$extraIndice;
                                            if ($pessoaExtra!=null)
                                                $pessoaExtra = $pessoaExtra->valor;
                                            ?>

                                            <div class="field-wrapper">
                                                <?php
                                                if (strpos($extraTitulo,'[ ]')==false){
                                                    echo "<h4>$extraTitulo</h4>";
                                                    echo input_texto_simples($extraIndice, '', 30, $pessoaExtra);
                                                } else {
                                                    $extraTitulo = str_replace('[ ]','',$extraTitulo);
                                                    echo input_checkbox_padrao($extraIndice,$extraTitulo,$pessoaExtra);
                                                }
                                                ?>
                                            </div>
                                    <?php
                                    endforeach;
                                }
                            ?>
                                
                            <div class="field-wrapper">
                                <?php echo input_checkbox_padrao('newsletter', 'Desejo receber avisos dos próximos eventos', $pessoa->newsletter); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($etapa == 3): ?>
                            <h2>Escolha de plano</h2>

                        <?php endif; ?>

                        <?php if ($etapa == 4): ?>
                            <?php if ($textoTicket!=null) echo "<h4>$textoTicket</h4><br>"; ?>
                            O valor da inscrição para "<?php echo $evento->post_title; ?>" é <b><?php echo PLib::formatarGrana($evento->valor); ?></b><Br><br>
                            <?php if ($evento->descontoSessao()): ?>
                                <p>Aplicado desconto de <?php echo $evento->getDescontoSessao(); ?>%. Valor de seu investimento: 
                                <b><?php echo PLib::formatarGrana($evento->valor_atual); ?></b></p>
                            <?php endif; ?>
                         
                            <div class="clearfix"></div>
                            
                            <div class="">
                                <div class="">
                                    <div class="">
                                        <h1 class="">PagSeguro</h1>
                                    </div>
                                    <div class="">
                                      

                                        <div class="">
                                            <ul class="">
                                                <li style="">Você será direcionado ao site do PagSeguro e após conclusão do pagamento chegará na página de confirmação da inscrição.</li>
                                                <li style="">A inscrição é confirmada no momento em que o PagSeguro recebe o "ok" de sua operadora e avisa ao nosso sistema.</li>
                                            </ul>
                                        </div>
                                        <span><span class="">Valor: <?php echo PLib::formatarGrana($evento->valor_atual); ?></span></span><br>
                                        <a href="<?php echo $urlPagSeguro; ?>" class="botaoInscrever">Pagar agora</a>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </div>

                            <br>&nbsp;<br>
                            <div class="">
                                <div class="">
                                    <div class="">
                                        <h1 class="">Pagamento a Vista</h1>
                                    </div>
                                    <div class="">
                                        <div class="">
                                            <ul class="">
                                            <?php if ($evento->organizador()->inscricao_locais_pagamento!=""): ?>
                                                  <li style="">Vá até um de nossos parceiros e pague em dinheiro. Assim que o parceiro nos avisar confirmaremos sua inscrição.</li>
                                            <?php endif; ?>  
                                            <?php if ($evento->organizador()->inscricao_dados_conta!=""): ?>
                                                  <li style="">Se preferir, transfira o valor de sua inscrição diretamente para nossa conta. Após a transferência, nos avise para que possamos identificar seu pagamento e confirmar a inscrição.</li>
                                            <?php endif; ?>
                                            </ul>
                                        </div>
                                        <span><span class="amount">Valor: <?php echo PLib::formatarGrana($evento->valor_atual*0.97); ?></span></span><br>
                                        <a href="#" onclick="javascript:return informacoesVista();" class="botaoInscrever">Mais informações</a>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </div>
                            
                        <?php endif; ?>

                        <?php if ($etapa == 5): ?>
                            <?php if ($evento->confirmacao=='imediata'): ?>
                                Sua inscrição está confirmada!<br><br>
                                Nos vemos lá.<br><Br>
                                Até mais!
                            <?php elseif ($evento->confirmacao=='posterior'): ?>
                                Sua inscrição <b>ainda não foi confirmada</b>. Três dias antes do evento iremos totalizar os inscritos e confirmar as incrições possíveis. <br><br>
                                Aguarde a chegada do email de confirmação.<br><br>
                                Até mais!
                            <?php elseif ($evento->confirmacao=='pagamento'): ?>
                                <?php if ($inscricao->confirmado==1): ?>
                                    Seu pagamento foi realizado com sucesso!<br><br>
                                    Você receberá logo mais um email com todos os detalhes do evento.<br><br>
                                    O número de seu ticket de inscrição é "<?php echo $inscricao->id*13; ?>".<br><Br>
                                    Até mais!
                                <?php else: ?>
                                    Seu pagamento ainda não foi confirmado!<br><br>
                                    Assim que o PagSeguro nos enviar a confirmação, lhe enviaremos um email com todos os detalhes do evento.<br><br>
                                    Caso tenha dificuldades em utilizar o cartão, page a <a href="#" onclick="javascript:return informacoesVista();">vista em dinheiro</a> ou através de <a href="#" onclick="javascript:return informacoesVista();">depósito bancário</a> e ainda ganhe um pequeno desconto.<br><br>
                                    O número de seu ticket de inscrição é "<?php echo $inscricao->id*13; ?>".<br><Br>
                                    Até mais!
                                    <br><Br>
                                <?php endif; ?>
                            <?php elseif ($evento->confirmacao=='preinscricao'): ?>
                                Ótimo! Registramos seus dados de contato e te incluímos na lista de espera do treinamento.<br><br>
                                Assim que abrirmos a próxima turma você será avisado e poderá realizar sua inscrição!<br><br>
                            <?php endif; ?>
                            <?php if ($evento->id_organizador==597): ?>
                                <br><Br>
                                Continue navegando na <a href="<?php echo SITE_URL; ?>">Inspirar Digital</a>!      
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($etapa < 4): ?>
                            <br>
                            <input type="submit" value="<?php echo $avancarTexto; ?>">
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function informacoesVista(){
            alert('Pagando a vista ou com transferência, entre em contato conosco para que possamos confirmar sua inscrição e garantir sua vaga.');
            if ("<?php echo ($evento->organizador()->inscricao_dados_conta!=""? 1 : 0); ?>"=="1")
                informacoesConta();
            if ("<?php echo ($evento->organizador()->inscricao_locais_pagamento!=""? 1 : 0); ?>"=="1")
                informacoesParceiros()();
            return false;
        }
        function informacoesParceiros(){
            alert('Locais para pagamento em dinheiro:\r\n<?php echo str_replace("\r\n",'\r',$evento->organizador()->inscricao_locais_pagamento); ?>');
            return false;
        }
        function informacoesConta(){
            alert('Dados para depósito em conta:\r\n<?php echo str_replace("\r\n",'\r',$evento->organizador()->inscricao_dados_conta); ?>:');
            return false;
        }
    </script>

 
    </div>

    <div class="clear"></div>
</div><!--### ende content ###-->

<?php get_footer(); ?>