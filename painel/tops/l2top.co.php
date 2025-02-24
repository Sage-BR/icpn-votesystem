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

class L2Top {
    public static function CheckVote($ip, $topId, $topUrl) {
        try {
            // Remove http:// ou https:// da URL antes de testar conexão
            $host = str_replace(["https://", "http://"], "", $topUrl);
            
            if (@fsockopen($host, 80, $errno, $errstr, 30)) {
                $url = "https://l2top.co/reward/VoteCheck.php?id=" . $topId . "&ip=" . $ip;
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                
                $output = curl_exec($ch);
                curl_close($ch);
                
                return trim($output) === "TRUE";
            }
        } catch (Exception $e) {
            return false;
        }
        return false;
    }
}

$ip = get_client_ip();
$topId = $row->top_id;
$topUrl = $row->top_url;
$hasVoted = L2Top::CheckVote($ip, $topId, $topUrl);
?>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let lastVoteTime = localStorage.getItem("l2top_vote_time");
        let now = new Date();

        if (<?= $hasVoted ? 'true' : 'false' ?>) {
            // Se a API retorna TRUE, verifica se já tem tempo salvo
            if (!lastVoteTime) {
                let nextVoteTime = new Date();
                nextVoteTime.setHours(nextVoteTime.getHours() + 12);
                localStorage.setItem("l2top_vote_time", nextVoteTime.toISOString());
            } else {
                let voteDate = new Date(lastVoteTime);
                if (now < voteDate) {
                    // Ainda dentro do tempo de espera, exibe o contador
                    atualizaContador(<?= htmlspecialchars($row->id); ?>, voteDate.getFullYear(), voteDate.getMonth() + 1, voteDate.getDate(), voteDate.getHours(), voteDate.getMinutes(), voteDate.getSeconds());
                    document.getElementById("vote-box").classList.add("disabled");
                } else {
                    // Se já passou do tempo, libera o botão para votar
                    localStorage.removeItem("l2top_vote_time");
                    document.getElementById("vote-box").classList.remove("disabled");
                }
            }
        } else {
            // Se a API retorna FALSE, limpa o localStorage e libera o botão de votação
            localStorage.removeItem("l2top_vote_time");
            document.getElementById("vote-box").classList.remove("disabled");
        }
    });

    function registerVote() {
        let nextVoteTime = new Date();
        nextVoteTime.setHours(nextVoteTime.getHours() + 12);
        localStorage.setItem("l2top_vote_time", nextVoteTime.toISOString());
    }
</script>

<?php if ($hasVoted) : ?>
    <div id="vote-box" style="background:url(images/buttons/<?php echo $row->top_img; ?>); background-repeat: no-repeat; background-size: 87px 47px; width:87px; height:47px; border:1px solid #999; margin-top:5px; margin-left:5px; float:left;">
        <div style="width:89px; height:49px; font-size:10px; font-family:Arial; background: rgba(0,0,0,0.7); text-shadow:1px 1px #000; font-weight:bold;">
            <?= $language_05; ?><br>
            <font size="3"><span id="contador<?= htmlspecialchars($row->id); ?>"></span></font><br>
            <?= $language_06; ?>
        </div>
    </div>
<?php else: ?>
    <div id="vote-box" style="width:87px; height:47px; border:1px solid #999; margin-top:5px; margin-left:5px; float:left;">
        <a href="http://l2top.co/vote/server/<?php echo $row->top_id; ?>" target="_blank" onclick="registerVote()">
            <img src="images/buttons/<?php echo $row->top_img; ?>" 
                 title="Vote in L2Top.CO - Lineage 2" 
                 border="0" width="87" height="47">
        </a>
    </div>
<?php endif; ?>
