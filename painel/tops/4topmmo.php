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
// Verifica se o host está acessível e envia requisição via cURL.
$host = parse_url($row->top_url, PHP_URL_HOST);
$url = "https://top.4teambr.com/index.php?a=in&u=" . $row->top_id . "&ipc=" . get_client_ip();

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$pagina = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Inicializa o estado de `$tops_voted`.
$data_modificada = '0000-00-00 00:00:00';
$tops_voted = array_replace($tops_voted ?? [], [$i => [1, $data_modificada]]);

if ($http_code === 200 && mb_substr($pagina, 0, 1) === '1' && !isset($_COOKIE["4topservers"])) {
    // Configura o cookie para limitar votos.
    setcookie("4topservers", "value", time() + 3600);
    $data_modificada = date('Y-m-d H:i:s', strtotime('+1 hour')); // Exemplo de tempo até expirar.
}

// Valida o cookie e verifica data para exibir botão ou contador.
if (isset($_COOKIE["4topservers"])) {
    $data_modificada = pega_cookie($_COOKIE["4topservers"]) ?: '0000-00-00 00:00:00';
}

$dataAtual = new DateTime();
$dataModificada = new DateTime($data_modificada);

if ($dataModificada >= $dataAtual) {
    // Exibe contador se ainda não pode votar.
    $data_voto = explode("-", substr(str_replace(" ", "", $data_modificada), 0, 10));
    $hora_voto = explode(":", substr(str_replace(" ", "", $data_modificada), 10, 19));

    $tops_voted = array_replace($tops_voted, [$i => [1, $data_modificada]]);
    ?>
    <script>
        atualizaContador(
            <?= htmlspecialchars($row->id); ?>,
            <?= htmlspecialchars($data_voto[0]); ?>,
            <?= htmlspecialchars($data_voto[1]); ?>,
            <?= htmlspecialchars($data_voto[2]); ?>,
            <?= htmlspecialchars($hora_voto[0]); ?>,
            <?= htmlspecialchars($hora_voto[1]); ?>,
            <?= htmlspecialchars($hora_voto[2]); ?>
        );
    </script>
    <div style="background:url(images/buttons/<?php echo $row->top_img; ?>); background-repeat: no-repeat; background-size: 87px 47px; width:87px; height:47px; border:1px solid #999; margin-top:5px; margin-left:5px; float:left;">
        <div style="width:87px; height:47px; font-size:10px; font-family:Arial; background: rgba(0, 0, 0, 0.7); text-shadow:1px 1px #000; font-weight:bold; color: #fff; text-align: center;">
            <?= $language_05; ?><br>
            <font size="3"><span id="contador<?= htmlspecialchars($row->id); ?>"></span></font><br>
            <?= $language_06; ?>
        </div>
    </div>
    <?php
} else {
    // Exibe botão de votação se o usuário pode votar.
    ?>
    <div style="width:87px; height:47px; border:1px solid #999; margin-top:5px; margin-left:5px; float:left;">
        <a href="https://top.4teambr.com/index.php?a=in&u=<?= htmlspecialchars($row->top_id); ?>" target="_blank">
            <img src="images/buttons/<?= htmlspecialchars($row->top_img); ?>" 
                 title="4TOP Private Servers" 
                 border="0" width="87" height="47" 
                 onClick="javascript:SetCookie('4topmmo');">
        </a>
    </div>
    <?php
}
