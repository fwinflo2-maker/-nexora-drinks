<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" || $_SESSION['habilitation']=="Caissier"))
{
	include('fonctions.php');
	include('Connexion.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire d'enregistrement d'une Famille d'Article.</title>
<style type="text/css">
label
{
	display:block;
	width:150px;
	float: left;
}
</style>
<script src="JS/Enreg_Famille.js" type="text/javascript"></script>
</head>
<body>
<fieldset style="width:400px; margin-left:30%; border-color:#FFFBF0" ><legend>Enregistrement d'une Famille d'Article</legend>
<table>
<form action="CTRL/Controle_Famille.php" method="post" onsubmit="return verif_form()">
<tr>
	<td><label for="code"> Code * </label></td>
    <td><input type="text" id="code" name="code" style="width:250px;"s/> </td>
</tr>
<tr>
	<td><label for="libelle"> Libelle * </label></td>
	<td><input type="text" id="libelle" name="libelle" style="width:250px"/> </td>
</tr>
<tr>
    <td><label for="statut"> Statut * </label></td> 
    <td><input type="text" id="statut" name="statut" style="width:250px; background:#CCC" value="Actif" readonly="readonly"/></td>
</tr>
<tr>
    <td colspan=""><input type="submit" align="left" value="Enregistrer" id="Enregistrer" name="Enregistrer"/></td>
    <td align="right"><input type="button" value="Retour" id="Retour" name="Retour" onclick="history.back()"/></td>
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
				alert('Vous n\'etes pas habiliter a acceder a cette page.');
				history.back();
				</script>
<?php
}
?>
