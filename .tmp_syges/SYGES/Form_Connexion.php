<?php 
	include('connexion.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire de connexion</title>
</head>
<body>
<form action="CTRL/Controle_Connexion.php" method="post" onsubmit="return verif_form()">
  <fieldset style="width:10%; margin-left:35%; margin-top:15%; background:#09F; border-bottom-style:ridge; border:groove;">
<table>
<tr>
	<td colspan="2" align="center"> <h3>
	  <label style="margin-left:20%; font:'Times New Roman', Times, serif;" >Connexion SYGES 1.0 </label></h3></td>
</tr>
<tr>
    <td ><label for="Login"> Login </label></td>
    <td><input name="Login" type="text" id="Login" / style="width:200px;"></td>
    <td rowspan="2"><img src="IMG/encrypted.png" /></td>
</tr>
<tr>
    <td><label for="Password"> Password </label></td>
    <td><input  type="password" id="Password" name="Password" style="width:200px;" /> </td>
</tr>
<tr>
	<td colspan="2" align="right"><input type="submit" id="Connexion" name="Connexion" value="Connexion"/></td>
</tr>
</table>
</fieldset>
</form>
</body>
</html>