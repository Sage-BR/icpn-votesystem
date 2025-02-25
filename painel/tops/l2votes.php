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

function getVoteStatus($ip, $api_key) {
    $url = "https://l2votes.com/api.php?apiKey=$api_key&ip=$ip";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT_MS, 4000);
    curl_setopt($ch, CURLOPT_USERAGENT, "Votes");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    if (!empty($data) && isset($data[0]['status']) && $data[0]['status'] == "1" && isset($data[0]['date'])) {
        return $data[0]['date']; // Retorna a data do último voto
    }
    return false;
}

$ip = get_client_ip();
$api_key = $row->top_token;

if (@fsockopen(str_replace(["https://", "http://"], "", $row->top_url), 80, $errno, $errstr, 30)) {
    $lastVote = getVoteStatus($ip, $api_key);

    if ($lastVote) {
        // Captura o fuso horário do servidor
        $timezone_server = date_default_timezone_get();
        
        // Converte a data da API (que está em UTC) para DateTime
        $voteDateTime = DateTime::createFromFormat('YmdHis', $lastVote, new DateTimeZone('UTC'));

        // Ajusta para o fuso horário local do servidor
        $voteDateTime->setTimezone(new DateTimeZone($timezone_server));
        
        // Adiciona 12 horas
        $voteDateTime->modify('+12 hours');
        $nextVoteTime = $voteDateTime->format('Y-m-d H:i:s');

        // Captura o horário atual no fuso do servidor
        $current_time = (new DateTime("now", new DateTimeZone($timezone_server)))->format("Y-m-d H:i:s");

        // Verifica se já passou o tempo para votar
        if ($current_time >= $nextVoteTime) {
            ?>
            <div style='width:87px; height:47px; border:1px solid #999; margin-top:5px; margin-left:5px; float:left;'>
                <a href='https://l2votes.com/votes.php?sid=<?php echo $row->top_id; ?>' target='_blank'>
                    <img src='images/buttons/<?php echo $row->top_img; ?>' title='l2votes.com' border='0' width='87' height='47'>
                </a>
            </div>
            <?php
        } else {
			// Se não for permitido votar, exibe o contador de tempo restante
			$tops_voted = array_replace($tops_voted, array($row->id => array(1, date("Y-m-d H:i:s"))));
			
            // Se não for permitido votar, exibe o contador de tempo restante
            $data_voto = explode("-", date("Y-m-d", strtotime($nextVoteTime)));
            $hora_voto = explode(":", date("H:i:s", strtotime($nextVoteTime)));
            ?>
            <script language="javascript">
                atualizaContador(<?php echo $row->id; ?>, <?php echo implode(",", $data_voto); ?>, <?php echo implode(",", $hora_voto); ?>);
            </script>
            <div style='background:url(images/buttons/<?php echo $row->top_img; ?>); background-repeat: no-repeat; background-size: 87px 47px; width:87px; height:47px; border:1px solid #999; margin-top:5px; margin-left:5px; float:left;'>
                <div style='width:89px; height:49px; font-size:10px; font-family:Arial; background: rgba(0,0,0,0.8); text-shadow:1px 1px #000; font-weight:bold;'>
                    <?php echo $language_05; ?><br>
                    <font size='3'><span id='contador<?php echo $row->id; ?>'></span></font><br>
                    <?php echo $language_06; ?>
                </div>
            </div>
            <?php
        }
    } else {
        ?>
        <div style='width:87px; height:47px; border:1px solid #999; margin-top:5px; margin-left:5px; float:left;'>
            <a href='https://l2votes.com/votes.php?sid=<?php echo $row->top_id; ?>' target='_blank'>
                <img src='images/buttons/<?php echo $row->top_img; ?>' title='l2votes.com' border='0' width='87' height='47'>
            </a>
        </div>
        <?php
    }
} else {
    $tops_voted = array_replace($tops_voted, array($row->id => array(0, '0000-00-00 00:00:00')));
}
?>