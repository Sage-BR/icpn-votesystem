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

function getVoteStatus($ip, $api_key) {
    $url = "https://l2votes.com/api.php?apiKey=$api_key&ip=$ip";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT_MS, 4000);
    curl_setopt($ch, CURLOPT_USERAGENT, "Votes");
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    return isset($data[0]['status']) && $data[0]['status'] == "1";
}

$ip = get_client_ip();
$api_key = $row->top_token;

if (@fsockopen(str_replace(["https://", "http://"], "", $row->top_url), 80, $errno, $errstr, 30)) {
    $voted = getVoteStatus($ip, $api_key);
    
    if ($voted) {
        $nextVoteTime = strtotime("+12 hours");
        $data_modificada = date("Y-m-d H:i:s", $nextVoteTime);
    } else {
        $data_modificada = '0000-00-00 00:00:00';
    }

    if (strtotime($data_modificada) >= strtotime(date('Y-m-d H:i:s'))) {
        $data_voto = explode("-", substr(str_replace(" ", "", $data_modificada), 0, 10));
        $hora_voto = explode(":", substr(str_replace(" ", "", $data_modificada), 10, 19));
        ?>
        <script language="javascript">
            atualizaContador(<?php echo $row->id; ?>, <?php echo implode(",", $data_voto); ?>, <?php echo implode(",", $hora_voto); ?>);
        </script>
        <div style='background:url(images/buttons/<?php echo $row->top_img; ?>); background-repeat: no-repeat; background-size: 87px 47px; width:87px; height:47px; border:1px solid #999; margin-top:5px; margin-left:5px; float:left;'>
            <div style='width:89px; height:49px; font-size:10px; font-family:Arial; background: rgba(0,0,0,0.8); text-shadow:1px 1px #000; font-weight:bold;'>
                <?php echo $language_05; ?><br><font size='3'><span id='contador<?php echo $row->id; ?>'></span></font><br><?php echo $language_06; ?>
            </div>
        </div>
        <?php
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
    $tops_voted = array_replace($tops_voted, array($i => array(1, '0000-00-00 00:00:00')));
}
?>
