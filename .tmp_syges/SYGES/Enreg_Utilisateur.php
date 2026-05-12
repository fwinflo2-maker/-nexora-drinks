<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur"))
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire d'enregistrement d'un Utilisateur.</title>
<style type="text/css">
label
{
	display:block;
	width:150px;
	float: left;
}
</style>
<script src="JS/Enreg_utilisateur.js" type="text/javascript"></script>
<body>
<fieldset style="width:750px; margin-left:150px; border-color:#FFFBF0" ><legend>Enregistrement d'un Utilisateur</legend>
<table>
<form action="CTRL/Controle_Utilisateur.php" method="post" onsubmit="return verif_form()" >
<tr>
	<td><label for="nom"> Nom *</label></td>
	<td><input type="text" id="nom" name="nom" style="width:200px"/> </td>
    <td><label for="prenom"> Prenom </label></td> 
    <td><input type="text" id="prenom" name="prenom" style="width:200px"/></td>
</tr>
<tr>
    <td><label for="Login"> Login *</label></td>
    <td><input type="text" id="Login" name="Login" style="width:200px"/> </td>
    <td><label for="MDP"> Mot de passe *</label></td>
    <td><input type="password" id="MDP" name="MDP" style="width:200px"/> </td>
</tr>
<tr>
    <td><label for="Habilitation"> Habilitation *</label></td>
    <td><select name="Habilitation" id="Habilitation" style="width:200px;">
            <option>Administrateur</option>
    		<option>Gerant</option>
            <option>Caissier</option>
            <option>OPS</option>
            <option>Comptable</option>
            <option>Magasinier</option>
	</select> </td>
    <td><label for="ConMDP"> Confirmation Mot de passe *</label></td>
    <td><input type="password" id="ConMDP" name="ConMDP" style="width:200px;"></td>
</tr>
<tr>
    <td colspan="2"><input type="submit" align="left" value="Enregistrer" id="Enreg" name="Enreg"/></td>
    <td colspan="2" align="right"><input type="reset" align="right" value="Annuler" id="Annuler" name="Annuler"/></td>
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
