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
$URL = "https://api.hopzone.eu/v1/?api_key={$row->top_token}&ip=" . get_client_ip() . "&type=json";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'curl/7.68.0 ICPNetwork/2.8');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$can_vote = false;
$tops_voted = array_replace($tops_voted, array($i => array(1, '0000-00-00 00:00:00')));

if ($response === false || $http_status != 200) {
    // Caso a API não esteja acessível, permitir o voto como fallback
    $can_vote = true;
} else {
    $json = json_decode($response, true);

    if ($json !== null) {
        // Verificar o status do voto
        if (isset($json['status']) && $json['status'] === 'completed') {
            // Se o status for "completed", verificar o tempo do último voto
            if (isset($json['vote_time']) && is_numeric($json['vote_time'])) {
                $hours_since_vote = (time() - intval($json['vote_time'])) / 3600;

                if ($hours_since_vote >= 12) {
                    // Permitir o voto se passaram 12 horas desde o último
                    $can_vote = true;
                } else {
                    // Bloquear o voto se o tempo for menor que 12 horas
                    $can_vote = false;
                }
            } else {
                // Se não houver um `vote_time`, presumir que o voto é válido
                $can_vote = true;
            }
        } elseif (isset($json['status']) && $json['status'] === 'pending') {
            // Se o status for "pending", permitir o voto
            $can_vote = true;
        } else {
            // Qualquer outro status, permitir o voto como fallback
            $can_vote = true;
        }
    }
}

// Atualizar o array de votos com o estado atual
$tops_voted = array_replace($tops_voted, array($i => array($can_vote ? 1 : 0, date('Y-m-d H:i:s'))));

// use the value of can_vote as needed
if ($can_vote):
	?>
		<div style='width:87px; height:47px; border:1px solid #999; margin-top:5px; margin-left:5px; float:left;'>
			<a href='https://hopzone.eu/vote/<?php echo $row->top_id; ?>' target='_blank'><img src='images/buttons/<?php echo $row->top_img; ?>' title='Top L2JBrasil de Servidores de Lineage2' border='0' width='87' height='47'></a>
		</div>
		<?php
else:
	$hoursToVoteAgain = 12;
	
	//Legacy Code
	$data_modificada = $data_modificada = date("Y-m-d H:i:s",strtotime($last_vote['date']." + {$hoursToVoteAgain} hours"));
	$data_voto = explode("-", substr(str_replace(" ", "", $data_modificada), 0, 10));
	$hora_voto = explode(":", substr(str_replace(" ", "", $data_modificada), 10, 19));
	$tops_voted = array_replace($tops_voted, array($i => array(1, $data_modificada)));

	?>
		<script language="javascript">
			atualizaContador(<?php echo $row->id; ?>,<?php echo $data_voto[0]; ?>,<?php echo $data_voto[1]; ?>,<?php echo $data_voto[2]; ?>,<?php echo $hora_voto[0]; ?>,<?php echo $hora_voto[1]; ?>,<?php echo $hora_voto[2]; ?>);
		</script>
		<div style='background:url(images/buttons/<?php echo $row->top_img; ?>); background-repeat: no-repeat; background-size: 87px 47px; width:87px; height:47px; border:1px solid #999; margin-top:5px; margin-left:5px; float:left;'>
			<div style='width:89px; *width:87px; _width:87px; height:49px; *height:47px; _height:47px; font-size:10px; font-family:Arial; background: rgba(0,0,0,0.7); text-shadow:1px 1px #000; font-weight:bold;'>
				<?php echo $language_05; ?><br><font size='3'><span id='contador<?php echo $row->id; ?>'></span></font><br><?php echo $language_06; ?>
			</div>
		</div>
		<?php
endif;
