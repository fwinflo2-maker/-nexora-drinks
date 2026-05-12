<?php
if (isset ($_SESSION['habilitation'])&& ($_SESSION['login']))
{
	include('connexion.php');
	$sql='SELECT  * FROM USER  WHERE LOGIN="'.$_SESSION['login'].'"' ;
 	$reponse= $DataBase->query($sql);
	$rslt= $reponse->fetch();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire de Modification d'un Mot de Passe.</title>
<style type="text/css">
label
{
	display:block;
	width:150px;
	float: left;
}
</style>
<script src="JS/Modification_MDP.js" type="text/javascript"></script>
<body>
<fieldset style="width:400px; margin-left:300px; border-color:#FFFBF0" ><legend>Modification du Mot de Passe</legend>
<table>
<form action="CTRL/Ctrl_Mod_MDP.php" method="post" onsubmit="return verif_form()" >
<tr>
    <td><label for="Login"> Compte Utilisateur </label></td>
    <td><input type="text" id="Login" name="Login" style="width:200px; background-color:#ECECEC;" readonly="readonly" value="<?php echo $rslt['LOGIN']?>" /> </td>
</tr>
<tr>
	<td><label for="AncPassword"> Ancien PassWord *</label></td>
	<td><input type="password" id="AncPassword" name="AncPassword" style="width:200px"/> </td>
</tr>
<tr>
    <td><label for="NvoPassword"> Nouveau PassWord *</label></td> 
    <td><input type="password" id="NvoPassword" name="NvoPassword" style="width:200px"/></td>
</tr>
<tr>
    <td><label for="ConPassword"> Confirmation PassWord *</label></td>
    <td><input type="password" id="ConPassword" name="ConPassword" style="width:200px;"></td>
</tr>
<tr>
    <td><input type="submit" align="left" value="Modifier" id="Modifier" name="Modifier"/></td>
    <td align="right"><input type="reset" align="right" value="Annuler" id="Annuler" name="Annuler" /></td>
</tr>
</form>
</table>
</fieldset>
</body>
</html>
<?php 
}
else
{
?>
				<script language="javascript" type="text/javascript">
				alert('Vous n\'etes pas habiliter a  acceder a cette page.');
				history.back();
				</script>
<?php
}
?>
