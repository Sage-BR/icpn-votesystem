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

function getHopeStatus($ip, $api_key) {
    $url = "https://api.hopzone.net/lineage2/vote?token=$api_key&ip_address=$ip";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignora SSL se necessário
    curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT_MS, 4000);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl/7.68.0 ICPNetwork/2.8');
    
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    // Verifica se a resposta contém o status esperado
    return isset($data['voted']) && $data['voted'] === true;
}

$ip = get_client_ip();
$api_key = $row->top_token;

if (@fsockopen(str_replace(["https://", "http://"], "", $row->top_url), 80, $errno, $errstr, 30)) {
    $voted = getHopeStatus($ip, $api_key);

    if ($voted) {
        $nextVoteTime = strtotime("+12 hours");
        $data_modificada = date("Y-m-d H:i:s", $nextVoteTime);
        $data_voto = explode("-", substr(str_replace(" ", "", $data_modificada), 0, 10));
        $hora_voto = explode(":", substr(str_replace(" ", "", $data_modificada), 10, 19));
        $tops_voted = array_replace($tops_voted, array($i => array(1, $data_modificada)));
        ?>
        <script language="javascript">
                atualizaContador(
                    <?php echo $row->id; ?>,
                    <?php echo $data_voto[0]; ?>,
                    <?php echo $data_voto[1]; ?>,
                    <?php echo $data_voto[2]; ?>,
                    <?php echo $hora_voto[0]; ?>,
                    <?php echo $hora_voto[1]; ?>,
                    <?php echo $hora_voto[2]; ?>
                );
            </script>
            <div style='background:url(images/buttons/<?php echo $row->top_img; ?>); background-repeat: no-repeat; background-size: 87px 47px; width:87px; height:47px; border:1px solid #999; margin-top:5px; margin-left:5px; float:left;'>
                <div style='width:89px; height:49px; font-size:10px; font-family:Arial; background: rgba(0,0,0,0.7); text-shadow:1px 1px #000; font-weight:bold;'>
                    <?php echo $language_05; ?><br><font size='3'><span id='contador<?php echo $row->id; ?>'></span></font><br><?php echo $language_06; ?>
                </div>
            </div>
            <?php
        } else {
            ?>
            <div style='width:87px; height:47px; border:1px solid #999; margin-top:5px; margin-left:5px; float:left;'>
                <a href='https://l2.hopzone.net/site/vote/<?php echo $row->top_id; ?>/1' target='_blank'>
                    <img src='images/buttons/<?php echo $row->top_img; ?>' title='Vote our server on HopZone.Net' border='0' width='87' height='47'>
                </a>
            </div>
            <?php
		}
} else {
    $tops_voted = array_replace($tops_voted, array($i => array(1, '0000-00-00 00:00:00')));
}
?>