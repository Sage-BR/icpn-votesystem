<?php
//=======================================================================\\
//  ## ####### #######                                                   \\
//  ## ##      ##   ##                                                   \\
//  ## ##      ## ####  |\  | |¯¯¯ ¯¯|¯¯ \      / |¯¯¯| |¯¯¯| | / |¯¯¯|  \\
//  ## ##      ##       | \ | |--    |    \    /  | | | | |_| |<   ¯\_   \\
//  ## ####### ##       |  \| |___   |     \/\/   |___| | |\  | \ |___|  \\
// --------------------------------------------------------------------- \\
//       Brazillian Developer / WebSite: http://www.icpfree.com.br       \\
//                 Email & Skype: ivan1507@gmail.com.br                  \\
//=======================================================================\\
//				      4TeamBR https://4teambr.com/						 \\

// Definir URL da API
$URL = "https://api.hopzone.eu/v1/?api_key={$row->top_token}&ip=" . get_client_ip() . "&type=json";

// Inicializar cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'curl/7.68.0 ICPNetwork/2.8');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// Executar a requisição e obter resposta
$response = curl_exec($ch);
$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$can_vote = false;
$vote_time_local = '0000-00-00 00:00:00';

// Se a API não responder, permitir o voto como fallback
if ($response === false || $http_status != 200) {
    $can_vote = true;
} else {
    $json = json_decode($response, true);

    if ($json !== null && isset($json['status'])) {
        if ($json['status'] === 'completed' && isset($json['vote_time'])) {
            // Capturar o fuso horário do servidor
            $timezone_server = date_default_timezone_get();

            // Configurar UTC para processar vote_time corretamente
            date_default_timezone_set('Europe/Athens'); // Exemplo GMT+3

            // Converter vote_time (Unix Timestamp) para UTC
            $vote_time_utc = date("Y-m-d H:i:s", $json['vote_time']);

            // Ajustar para o fuso horário do servidor
            date_default_timezone_set($timezone_server);
            $vote_time_local = date("Y-m-d H:i:s", strtotime($vote_time_utc));

            // Somar 12 horas ao horário local do voto
            $next_vote_time = date("Y-m-d H:i:s", strtotime($vote_time_local . " +12 hours"));

            // Capturar o horário atual no fuso do servidor
            $current_time = date("Y-m-d H:i:s");

            // Verificar se o tempo atual já passou do próximo voto permitido
            $can_vote = $current_time >= $next_vote_time;
        } else {
            $can_vote = true;
        }
    } else {
        $can_vote = true;
    }
}

// Atualizar `$tops_voted`
if ($can_vote) {
    $tops_voted = array_replace($tops_voted, array($row->id => array(0, '0000-00-00 00:00:00'))); 
} else {
    $tops_voted = array_replace($tops_voted, array($row->id => array(1, date('Y-m-d H:i:s'))));
}

// Exibir botão ou contador de tempo restante
if ($can_vote):
    ?>
    <div style='width:87px; height:47px; border:1px solid #999; margin-top:5px; margin-left:5px; float:left;'>
        <a href='https://hopzone.eu/vote/<?php echo $row->top_id; ?>' target='_blank'>
            <img src='images/buttons/<?php echo htmlspecialchars($row->top_img); ?>' title='Top L2JBrasil de Servidores de Lineage2' border='0' width='87' height='47'>
        </a>
    </div>
    <?php
else:
    ?>
    <script>
        atualizaContador(<?php echo $row->id; ?>, <?php echo implode(',', explode('-', substr($next_vote_time, 0, 10))); ?>, <?php echo implode(',', explode(':', substr($next_vote_time, 11, 8))); ?>);
    </script>
    <div style='background:url(images/buttons/<?php echo htmlspecialchars($row->top_img); ?>); background-repeat: no-repeat; background-size: 87px 47px; width:87px; height:47px; border:1px solid #999; margin-top:5px; margin-left:5px; float:left;'>
        <div style='width:89px; height:49px; font-size:10px; font-family:Arial; background: rgba(0,0,0,0.7); text-shadow:1px 1px #000; font-weight:bold;'>
            <?php echo $language_05; ?><br>
            <font size='3'><span id='contador<?php echo $row->id; ?>'></span></font><br>
            <?php echo $language_06; ?>
        </div>
    </div>
    <?php
endif;
?>

