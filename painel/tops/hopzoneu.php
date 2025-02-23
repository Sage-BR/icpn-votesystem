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

$URL = "https://api.hopzone.eu/v1/?api_key={$row->top_token}&ip=" . get_client_ip() . "&type=json";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'curl/7.68.0 ICPNetwork/2.8');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);

$json = json_decode($response, true);
$next_vote_time = $json['vote_time'] + 43200; // 12 horas em segundos
$can_vote = (time() >= $next_vote_time);

if ($can_vote):
    ?>
    <div style="width:87px; height:47px; border:1px solid #999; margin-top:5px; margin-left:5px; float:left;">
        <a href="https://hopzone.eu/vote/<?php echo $row->top_id; ?>" target="_blank">
            <img src="images/buttons/<?php echo $row->top_img; ?>" title="HopZone EU" border="0" width="87" height="47">
        </a>
    </div>
    <?php
else:
    $data_voto = date("Y-m-d H:i:s", $next_vote_time);
    $data_voto_exploded = explode(" ", $data_voto);
    $data_partes = explode("-", $data_voto_exploded[0]);
    $hora_partes = explode(":", $data_voto_exploded[1]);
    ?>
    <script language="javascript">
        atualizaContador(
            <?php echo $row->id; ?>,
            <?php echo $data_partes[0]; ?>,
            <?php echo $data_partes[1]; ?>,
            <?php echo $data_partes[2]; ?>,
            <?php echo $hora_partes[0]; ?>,
            <?php echo $hora_partes[1]; ?>,
            <?php echo $hora_partes[2]; ?>
        );
    </script>
    <div style="background:url(images/buttons/<?php echo $row->top_img; ?>); background-repeat: no-repeat; background-size: 87px 47px; width:87px; height:47px; border:1px solid #999; margin-top:5px; margin-left:5px; float:left;">
        <div style="width:87px; height:47px; font-size:10px; font-family:Arial; background: rgba(0,0,0,0.7); text-shadow:1px 1px #000; font-weight:bold; color: #fff; text-align: center;">
            <?php echo $language_05; ?><br>
            <font size="3"><span id="contador<?php echo $row->id; ?>"></span></font><br>
            <?php echo $language_06; ?>
        </div>
    </div>
    <?php
endif;
?>