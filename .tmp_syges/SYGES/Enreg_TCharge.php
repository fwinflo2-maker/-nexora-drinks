<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur")||($_SESSION['habilitation']=="Gerant")||($_SESSION['habilitation']=="Caissier" || $_SESSION['habilitation']=="Comptable"))
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire d'enregistrement d'un type de charge.</title>
<style type="text/css">
label
{
	display:block;
	width:150px;
	float: left;
}
</style>
<script src="JS/Enreg_TCharge.js" type="text/javascript"></script>
<body>
<fieldset style="width:750px; margin-left:150px; border-color:#FFFBF0" ><legend>Enregistrement d'un type de charge.</legend>
<table>
<form action="CTRL/Controle_TCharge.php" method="post" onsubmit="return verif_form()" >
<tr>
	<td><label for="Code"> Code *</label></td>
	<td><input type="text" id="Code" name="Code" style="width:200px;" /> </td>
</tr>
<tr>
    <td><label for="Libelle"> Libelle *</label></td> 
    <td><input type="text" id="Libelle" name="Libelle" style="width:200px"/></td>
    <td><label for="Statut"> Statut *</label></td>
    <td><input type="text" id="Statut" name="Statut" style="width:200px;  background-color:#ECECEC;" value="Actif" readonly="readonly"></td>
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
