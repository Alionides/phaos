<?php
include "../config.php";
include "aup.php";
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../styles/phaos.css" >
</head>
<body leftmargin="0" topmargin="0">
<div align="center">
	<form action="admin_chat.php" method="post">
		<textarea cols="20" rows="3" name="usertext"></textarea>
		<br>
		<?php destination(); ?>
		<input type="submit" value="Post Message">
	</form>
</div>
<div align="left">
<?php
if (isset($_POST['usertext']) and $_POST['usertext'] != "") {
	write_shout_post();
}
$result = mysqli_query($db, 'SELECT * FROM phaos_shout ORDER BY id DESC LIMIT 0, 10');

while ($row = mysqli_fetch_array($result)) {
	$color = '';
	if($row['destname'] == 'admin') {
		$color = "red";
	}
	if($row['postname'] == 'admin' and $row['destname'] != 'admin') {
		$color = "yellow";
	}
	if ($color == '') {
		$color = "white";
	}
	print '<hr><div align="left"><font color="'.$color.'">' . $row['postname'] . ', posted at ' . $row['postdate'] . '<br><br> '.$row['posttext'] .' <br></font>';
}
?>
</div>
</body> 
</html>
<?php

### --- Functions --- ###
function write_shout_post() {
		global $db, $PHP_ADMIN_USER;
		
		$ts = date("Y-m-d H:i:s");
		//$ts = time("H:i:s"); //FIXME: Change to time only.
		$text = strip_tags($_POST['usertext']);
		$bb_replace =      array('[b]', '[B]', '[/b]', '[/B]', '[i]', '[I]', '[/i]', '[/I]', '[u]', '[U]', '[/u]', '[/U]');
		$bb_replacements = array('<b>', '<b>', '</b>', '</b>', '<i>', '<i>', '</i>', '</i>', '<u>', '<u>', '</u>', '</u>');
		$text = str_replace($bb_replace, $bb_replacements, $text);
		mysqli_query($db, "INSERT INTO phaos_shout (location, postname, postdate, posttext, destname)
						   VALUES ('0', '$PHP_ADMIN_USER', '$ts', '$text', '".$_POST['destname']."')");	
		echo mysqli_error();
}
	
function destination() {
		global $db;
		
		print '<select name="destname">
				<option value="admin">To everyone</option>';
				
		//$current_time = time();
		//$active_min = $current_time-500;
		//$active_max = $current_time+500;
		
		//$result = mysqli_query($db, 'SELECT * FROM phaos_characters WHERE regen_time >= \'' . $active_min . '\' AND regen_time <= \'' . $active_max . '\' ORDER by name ASC');
		
		//FIXME: Show only who is ONLINE. 
		$result = mysqli_query($db, "SELECT * FROM phaos_characters WHERE username NOT LIKE 'phaos_%' ORDER by username ASC");
		
		if (mysqli_num_rows($result) != 0) {
			 while ($row = mysqli_fetch_assoc($result)) {
				print '<option value="'.$row['username'].'">Private to '.$row['username'].'</option>';
			}
		}
		print '</select>';
}
?>
