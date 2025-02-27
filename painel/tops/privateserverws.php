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

// Obtém todos os IPs do domínio para evitar falhas de DNS
$dns_records = dns_get_record("private-server.ws", DNS_A);
$server_ips = array_column($dns_records, 'ip');

define("PRIVATESERVERWS", !empty($server_ips) ? $server_ips[0] : "0.0.0.0"); // Usa o primeiro IP obtido

// Verifica a conexão com o site listado
$top_url = parse_url($row->top_url, PHP_URL_HOST) ?? '';

if (!empty($top_url)) {
    $connection = @fsockopen($top_url, 80, $errno, $errstr, 30);
} else {
    $connection = false;
}

if ($connection) {
    @header('Content-Type: text/html; charset=utf-8');
    fclose($connection);

    // Requisição cURL para rastrear o voto
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://private-server.ws/index.php?a=in&u=" . $row->top_id . "&ipc=" . get_client_ip());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl/7.68.0 ICPNetwork/2.8');
    
    $pagina = curl_exec($ch);
    $curl_error = curl_error($ch); // Captura erro do cURL
    curl_close($ch);

    if ($pagina !== false && strlen($pagina) > 0 && $pagina[0] == '1' && !isset($_COOKIE["privateserverws"])) { 
        echo "<script>SetCookie('privateserverws');</script>";
    }

    // Gerenciamento de cookies
    $data_modificada = isset($_COOKIE["privateserverws"]) ? pega_cookie($_COOKIE["privateserverws"]) : '0000-00-00 00:00:00';

    if (strtotime($data_modificada) >= strtotime(date('Y-m-d H:i:s'))) {
        $data_voto = explode("-", substr(str_replace(" ", "", $data_modificada), 0, 10));
        $hora_voto = explode(":", substr(str_replace(" ", "", $data_modificada), 10, 19));
        $tops_voted = array_replace($tops_voted, [$row->id => [1, $data_modificada]]);
        
        echo "<script>atualizaContador({$row->id}, {$data_voto[0]}, {$data_voto[1]}, {$data_voto[2]}, {$hora_voto[0]}, {$hora_voto[1]}, {$hora_voto[2]});</script>";
        echo "<div style='background:url(images/buttons/{$row->top_img}); background-repeat: no-repeat; background-size: 87px 47px; width:87px; height:47px; border:1px solid #999; margin-top:5px; margin-left:5px; float:left;'>
                <div style='width:87px; height:47px; font-size:10px; font-family:Arial; background: rgba(0,0,0,0.7); text-shadow:1px 1px #000; font-weight:bold;'>
                    {$language_05}<br><font size='3'><span id='contador{$row->id}'></span></font><br>{$language_06}
                </div>
              </div>";
    } else {
        echo "<div style='width:87px; height:47px; border:1px solid #999; margin-top:5px; margin-left:5px; float:left;'>
                <a href='https://private-server.ws/index.php?a=in&u={$row->top_id}' target='_blank'>
                    <img src='images/buttons/{$row->top_img}' title='Private-Server.ws' border='0' width='87' height='47' onClick=\"javascript:SetCookie('privateserverws');\">
                </a>
              </div>";
    }
} else {
    $tops_voted = array_replace($tops_voted, [$row->id => [0, '0000-00-00 00:00:00']]);
    echo "Falha na conexão com $top_url ($errstr)";
}
?>
