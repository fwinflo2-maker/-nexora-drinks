<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
{
	include('fonctions.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire d'enregistrement d'un Fournisseur.</title>
<style type="text/css">
label
{
	display:block;
	width:150px;
	float: left;
}
</style>
<script src="JS/Enreg_Fournisseur.js" type="text/javascript"></script>
</head>
<body>
<fieldset style="width:750px; margin-left:15%; border-color:#FFFBF0" ><legend>Enregistrement d'un Fournisseur</legend>
<table>
<form action="CTRL/Controle_Fournisseur.php" method="post" onsubmit="return verif_form()">
<tr>
	<td><label for="code"> Code * </label></td>
    <td><input type="text" id="code" name="code" style="width:200px; background-color:#ECECEC" value="<?php echo generer_code_fournisseur();?>" readonly="readonly"/> </td>
	<td><label for="nom"> Nom * </label></td>
	<td><input type="text" id="nom" name="nom" style="width:200px"/> </td>
</tr>
<tr>
    <td><label for="numtel"> N° Tel  </label></td> 
    <td><input type="text" id="numtel" name="numtel" style="width:200px"/></td>
    <td><label for="email"> E-mail  </label> </td>
    <td><input type="text" name="email" id="email" style="width:200px"/> </td>
</tr>
<tr>
    <td colspan="2"><input type="submit" align="left" value="Enregistrer" id="Enregistrer" name="Enregistrer"/></td>
    <td><input type="reset" value="Annuler" id="Annuler" name="Annuler"/></td>
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
