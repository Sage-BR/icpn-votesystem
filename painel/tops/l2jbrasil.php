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
//							 4TeamBR Fixes								 \\

$player_id = $row->top_token;
$topL2jbrURL = "https://top.l2jbrasil.com/votesystem/?hours=12&player_id={$player_id}&username={$row->top_id}&type=json";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $topL2jbrURL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'curl/7.68.0 ICPNetwork/2.8');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$can_vote = true; // Permitir votar por padrão
$ip_usuario = get_client_ip(); // Obtém o IP do usuário

if ($response !== false && $http_status == 200) {
    $json = json_decode($response, true);

    if ($json !== null && isset($json['vote'])) {
        $last_vote = null;

        foreach ($json['vote'] as $vote) {
            if ($vote['ip'] == $ip_usuario) {
                $last_vote = $vote; // Armazena o último voto do usuário com o mesmo IP
            }
        }

        if ($last_vote !== null) {
            $hours_since_vote = floatval($last_vote['hours_since_vote']);

            if (isset($last_vote['status']) && $last_vote['status'] == '1' && $hours_since_vote < 12) {
                $can_vote = false;
            } else {
                $can_vote = true;
            }
        }
    }
}

// Renderização do botão de votação
if ($can_vote):
    ?>
    <div style='width:87px; height:47px; border:1px solid #999; margin-top:5px; margin-left:5px; float:left;'>
        <a href='https://top.l2jbrasil.com/index.php?a=in&u=<?php echo $row->top_id; ?>&player_id=<?php echo $player_id; ?>' target='_blank'>
            <img src='images/buttons/<?php echo $row->top_img; ?>' title='Top L2JBrasil de Servidores de Lineage2' border='0' width='87' height='47'>
        </a>
    </div>
    <?php
else:
    // Define o tempo restante para o contador
    $hoursToVoteAgain = 12;
    $lastVoteTime = new DateTime($last_vote['date']);
    $nextVoteTime = clone $lastVoteTime;
    $nextVoteTime->modify("+{$hoursToVoteAgain} hours");

    // Obtém o tempo atual do servidor
    $currentTime = new DateTime();
    $timeRemaining = $currentTime->diff($nextVoteTime);

    // Tempo formatado corretamente
    $data_modificada = $nextVoteTime->format("Y-m-d H:i:s");
    $tops_voted = array_replace($tops_voted, array($i => array(1, $data_modificada)));

    ?>
    <script language="javascript">
        atualizaContador(<?php echo $row->id; ?>,
            <?php echo $nextVoteTime->format("Y"); ?>,
            <?php echo $nextVoteTime->format("m"); ?>,
            <?php echo $nextVoteTime->format("d"); ?>,
            <?php echo $nextVoteTime->format("H"); ?>,
            <?php echo $nextVoteTime->format("i"); ?>,
            <?php echo $nextVoteTime->format("s"); ?>);
    </script>
    <div style='background:url(images/buttons/<?php echo $row->top_img; ?>); background-repeat: no-repeat; background-size: 87px 47px; width:87px; height:47px; border:1px solid #999; margin-top:5px; margin-left:5px; float:left;'>
        <div style='width:89px; *width:87px; _width:87px; height:49px; *height:47px; _height:47px; font-size:10px; font-family:Arial; background: rgba(0,0,0,0.7); text-shadow:1px 1px #000; font-weight:bold;'>
            <?php echo $language_05; ?><br>
            <font size='3'><span id='contador<?php echo $row->id; ?>'></span></font><br>
            <?php echo $language_06; ?>
        </div>
    </div>
    <?php
endif;

