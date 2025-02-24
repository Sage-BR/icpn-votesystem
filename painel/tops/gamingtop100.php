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

// Definir constante e IP do cliente
define("GamingTop100", gethostbyname("gamingtop100.net"));

// Verificar conexão ao servidor
$top_url = str_replace(["https://", "http://"], "", $row->top_url);
if (@fsockopen($top_url, 80, $errno, $errstr, 30)) {
    @header('Content-Type: text/html; charset=utf-8');
    
    // Fazer requisição com cURL
    $ch = curl_init("http://www.gamingtop100.net/ip_check/{$row->top_id}/" . get_client_ip());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $pagina = curl_exec($ch);
    curl_close($ch);
    
    $voted = ($pagina !== false && strlen($pagina) > 0 && $pagina[0] == '1');
    
    if ($voted) {
        $data_modificada = date('Y-m-d H:i:s', strtotime('+12 hours'));
        setcookie("gamingtop100", $data_modificada, time() + 43200, "/"); // Armazena cookie por 12 horas
    } else {
        $data_modificada = isset($_COOKIE["gamingtop100"]) ? $_COOKIE["gamingtop100"] : '0000-00-00 00:00:00';
    }
    
    if (strtotime($data_modificada) >= strtotime(date('Y-m-d H:i:s'))) {
        $data_voto = explode("-", substr($data_modificada, 0, 10));
        $hora_voto = explode(":", substr($data_modificada, 11));
        $tops_voted = array_replace($tops_voted, [$row->id => [1, $data_modificada]]);
        ?>
        <script>
            atualizaContador(<?php echo $row->id; ?>, <?php echo implode(',', array_merge($data_voto, $hora_voto)); ?>);
        </script>
        <div style="background:url(images/buttons/<?php echo $row->top_img; ?>); background-repeat: no-repeat; background-size: 87px 47px; width:87px; height:47px; border:1px solid #999; margin-top:5px; margin-left:5px; float:left;">
            <div style="width:89px; height:49px; font-size:10px; font-family:Arial; background: rgba(0,0,0,0.7); text-shadow:1px 1px #000; font-weight:bold;">
                <?php echo $language_05; ?><br>
                <font size="3"><span id="contador<?php echo $row->id; ?>"></span></font><br>
                <?php echo $language_06; ?>
            </div>
        </div>
        <?php
    } else {
        ?>
        <div style="width:87px; height:47px; border:1px solid #999; margin-top:5px; margin-left:5px; float:left;">
            <a href="http://www.gamingtop100.net/in-<?php echo $row->top_id; ?>" target="_blank">
                <img src="images/buttons/<?php echo $row->top_img; ?>" title="lineage 2 private servers" border="0" width="87" height="47" onclick="document.cookie = 'gamingtop100=' + new Date().toISOString();">
            </a>
        </div>
        <?php
    }
} else {
    $tops_voted = array_replace($tops_voted, [$row->id => [1, '0000-00-00 00:00:00']]);
}
?>
