<?php
//=======================================================================\\
//  ## ####### #######                                                   \\
//  ## ##      ##   ##                                                   \\
//  ## ##      ## ####  |\  | |¯¯¯ ¯¯|¯¯ \      / |¯¯¯| |¯¯¯| | / |¯¯¯|  \\
//  ## ##      ##       | \ | |--    |    \    /  | | | | |_| |<   ¯\_   \\
//  ## ####### ##       |  \| |___   |     \/\/   |___| | |\  | \ |___|  \\
// --------------------------------------------------------------------- \\
//       Brazillian Developer / WebSite: http://www.icpfree.com.br       \\
//=======================================================================\\

// Faz requisição para a API.
if (@fsockopen(str_replace(["https://", "http://"], "", $row->top_url), 80, $errno, $errstr, 30)) {
    @header('Content-Type: text/html; charset=utf-8');

    $url = "https://www.top100arena.com/check_ip/" . $row->top_id . "?ip=" . get_client_ip();

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 200) {
        $data = json_decode($response, true);

        if (isset($data['voted']) && $data['voted'] === true) {
            if (!isset($_COOKIE["top100arena"])) {
                ?>
                <script>
                    SetCookie('top100arena');
                </script>
                <?php
            }
            $data_modificada = pega_cookie($_COOKIE["top100arena"] ?? '0000-00-00 00:00:00');
        } else {
            $data_modificada = '0000-00-00 00:00:00';
        }
    } else {
        $data_modificada = '0000-00-00 00:00:00'; // Se a API não responder corretamente
    }

    // Atualiza o array $tops_voted com o status de voto e a data de modificação
    $tops_voted = array_replace($tops_voted ?? [], [$row->id => [1, $data_modificada]]);

    if (strtotime($data_modificada) <= strtotime(date('Y-m-d H:i:s'))) {
        ?>
        <div style='width:87px; height:47px; border:1px solid #999; margin-top:5px; margin-left:5px; float:left;'>
            <a href='https://www.top100arena.com/listing/<?php echo $row->top_id; ?>/vote' target='_blank' onclick="SetCookie('top100arena');">
                <img src='images/buttons/<?php echo htmlspecialchars($row->top_img); ?>' title='Lineage 2 Private Servers' border='0' width='87' height='47'>
            </a>
        </div>
        <?php
    } else {
        $data_voto = explode("-", substr($data_modificada, 0, 10));
        $hora_voto = explode(":", substr($data_modificada, 11, 8));
        ?>
        <script>
            atualizaContador(<?php echo $row->id; ?>, <?php echo implode(',', $data_voto); ?>, <?php echo implode(',', $hora_voto); ?>);
        </script>
        <div style='background:url(images/buttons/<?php echo htmlspecialchars($row->top_img); ?>); background-repeat: no-repeat; background-size: 87px 47px; width:87px; height:47px; border:1px solid #999; margin-top:5px; margin-left:5px; float:left;'>
            <div style='width:89px; height:49px; font-size:10px; font-family:Arial; background: rgba(0,0,0,0.7); text-shadow:1px 1px #000; font-weight:bold;'>
                <?php echo $language_05; ?><br>
                <font size='3'><span id='contador<?php echo $row->id; ?>'></span></font><br>
                <?php echo $language_06; ?>
            </div>
        </div>
        <?php
    }
} else {
    // Atualiza o array $tops_voted caso não consiga conectar
    $tops_voted = array_replace($tops_voted ?? [], [$row->id => [1, '0000-00-00 00:00:00']]);
}
?>