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
if(@fsockopen(str_replace("https://","",str_replace("http://","",$row->top_url)), 80 , $errno , $errstr , 30)){
	if(isset($_COOKIE["l2network"])){
		$data_modificada = pega_cookie($_COOKIE["l2network"]);
	}else{
		$data_modificada = '0000-00-00 00:00:00';
	}
	if(strtotime($data_modificada) >= strtotime(date('Y-m-d H:i:s'))){
		$data_voto = explode("-", substr(str_replace(" ", "", $data_modificada), 0, 10));
		$hora_voto = explode(":", substr(str_replace(" ", "", $data_modificada), 10, 19));
		$tops_voted = array_replace($tops_voted, array($i => array(1, $data_modificada)));
		?>
		<script language="javascript">
			atualizaContador(<?php echo $row->id; ?>,<?php echo $data_voto[0]; ?>,<?php echo $data_voto[1]; ?>,<?php echo $data_voto[2]; ?>,<?php echo $hora_voto[0]; ?>,<?php echo $hora_voto[1]; ?>,<?php echo $hora_voto[2]; ?>);
		</script>
		<div style='background:url(images/buttons/<?php echo $row->top_img; ?>); background-repeat: no-repeat; background-size: 87px 47px; width:87px; height:47px; border:1px solid #999; margin-top:5px; margin-left:5px; float:left;'>
			<div style='width:89px; *width:87px; _width:87px; height:49px; *height:47px; _height:47px; font-size:10px; font-family:Arial; background: rgba(0,0,0,0.8); text-shadow:1px 1px #000; font-weight:bold;'>
				<?php echo $language_05; ?><br><font size='3'><span id='contador<?php echo $row->id; ?>'></span></font><br><?php echo $language_06; ?>
			</div>
		</div>
		<?php
	}else{
		?>
		<div style='width:87px; height:47px; border:1px solid #999; margin-top:5px; margin-left:5px; float:left;'><a href='https://l2network.eu/index.php?a=in&u=<?php echo $row->top_id; ?>' target='_blank'><img src='images/buttons/<?php echo $row->top_img; ?>' title='L2Network' border='0' width='87' height='47' onClick="SetCookie('l2network');"></a></div>
		<?php
	}
}else{
	$tops_voted = array_replace($tops_voted, [$row->id => [1, '0000-00-00 00:00:00']]);
}