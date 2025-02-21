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

class GameBytes {
    public static function Check($ip, $username) {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://www.gamebytes.net/api.php?ip=" . $ip . "&username=" . $username);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $output = curl_exec($ch);
            curl_close($ch);
            
            return trim($output) === 'TRUE';
        } catch (ErrorException $e) {
            return false;
        }
    }
}

$ip = get_client_ip();
$username = $row->top_id;
$hasVoted = GameBytes::Check($ip, $username);
?>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let lastVoteTime = localStorage.getItem("gamebytes_voted");
        let now = new Date();

        if (<?= $hasVoted ? 'true' : 'false' ?>) {
            // API retornou TRUE (usuário já votou)
            if (!lastVoteTime) {
                let nextVoteTime = new Date();
                nextVoteTime.setHours(nextVoteTime.getHours() + 12);
                localStorage.setItem("gamebytes_voted", nextVoteTime.toISOString());
            } else {
                let voteDate = new Date(lastVoteTime);
                if (now < voteDate) {
                    // Ainda dentro do tempo de espera, exibe o contador
                    atualizaContador(<?= htmlspecialchars($row->id); ?>, voteDate.getFullYear(), voteDate.getMonth() + 1, voteDate.getDate(), voteDate.getHours(), voteDate.getMinutes(), voteDate.getSeconds());
                    document.getElementById("vote-box").classList.add("disabled");
                } else {
                    // Tempo expirou, libera o botão
                    localStorage.removeItem("gamebytes_voted");
                    document.getElementById("vote-box").classList.remove("disabled");
                }
            }
        } else {
            // API retornou FALSE, limpa o localStorage e libera o botão de votação
            localStorage.removeItem("gamebytes_voted");
            document.getElementById("vote-box").classList.remove("disabled");
        }
    });

    function registerVote() {
        let nextVoteTime = new Date();
        nextVoteTime.setHours(nextVoteTime.getHours() + 12);
        localStorage.setItem("gamebytes_voted", nextVoteTime.toISOString());
    }
</script>

<?php if ($hasVoted) : ?>
    <div id="vote-box" style="background:url(images/buttons/<?php echo $row->top_img; ?>); background-repeat: no-repeat; background-size: 87px 47px; width:87px; height:47px; border:1px solid #999; margin-top:5px; margin-left:5px; float:left;">
        <div style="width:87px; height:47px; font-size:10px; font-family:Arial; background: rgba(0, 0, 0, 0.8); text-shadow:1px 1px #000; font-weight:bold; color: #fff; text-align: center;">
            <?= $language_05; ?><br>
            <font size="3"><span id="contador<?= htmlspecialchars($row->id); ?>"></span></font><br>
            <?= $language_06; ?>
        </div>
    </div>
<?php else: ?>
    <div id="vote-box" style="width:87px; height:47px; border:1px solid #999; margin-top:5px; margin-left:5px; float:left;">
        <a href="https://www.gamebytes.net/index.php?a=in&u=<?= htmlspecialchars($row->top_id); ?>" target="_blank" onclick="registerVote()">
            <img src="images/buttons/<?= htmlspecialchars($row->top_img); ?>" 
                 title="GameBytes - Best Lineage 2 Toplist" 
                 border="0" width="87" height="47">
        </a>
    </div>
<?php endif; ?>
