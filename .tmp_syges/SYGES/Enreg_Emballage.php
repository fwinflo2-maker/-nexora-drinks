<?php
if (isset ($_SESSION['habilitation']) && (($_SESSION['habilitation']=="Administrateur")|| ($_SESSION['habilitation']=="Gerant")))
{
	include('fonctions.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire d'enregistrement d'un Emballage.</title>
<style type="text/css">
label
{
	display:block;
	width:150px;
	float: left;
}
</style>
<script src="JS/Enreg_Emballage.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen" />
	<script type="text/javascript" src="dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
</head>

<body>
<fieldset style="width:800px; margin-left:10%; border-color:#FFFBF0" ><legend>Enregistrement d'un emballage</legend>
<table>
<form action="CTRL/Controle_Emballage.php" method="post" onsubmit="return verif_form()">
<tr>
	<td><label for="code"> Code * </label></td>
    <td><input type="text" id="code" name="code" style="width:200px; background-color:#ECECEC" value="<?php echo generer_code_emb();?>" readonly="readonly"/> </td>
</tr>

<tr>
    <td><label for="libelle"> Libellé * </label></td>
	<td><input type="text" id="libelle" name="libelle" style="width:200px" maxlength="32"/> </td>
    <td><label for="mt"> Montant Consigne * </label></td> 
    <td><input type="text" id="mt" name="mt" style="width:200px"/></td>
</tr>
<tr>
    <td><label for="qte"> Quantité * </label></td> 
    <td><input type="text" id="qte" name="qte" style="width:200px"/></td>
	<td><label for="statut"> Statut * </label></td>
	<td><input type="text" id="statut" name="statut" style="width:200px; background-color:#ECECEC" readonly="readonly" value="Actif"/> </td>
</tr>
<tr>
    <td colspan="2"><input type="submit" align="left" value="Enregistrer" id="Enregistrer" name="Enregistrer"/></td>
    <td colspan="2" align="right"><input type="button" align="right" value="Retour" id="Retour" name="Retour" onclick="history.back()"/></td>
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
