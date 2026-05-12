<?php

if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur")||($_SESSION['habilitation']=="Gerant"))
{
	include("Connexion.php");
	include("fonctions.php");
	if (isset ($_GET['Id']))
	{
		$sql='SELECT  * FROM APPORT  WHERE ID_APPORT="'.$_GET['Id'].'"' ;
 		$reponse= $DataBase->query($sql);
		$rslt= $reponse->fetch();
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire de modification d'un Mouvement de fonds.</title>
<style type="text/css">
label
{
	display:block;
	width:150px;
	float: left;
}
</style>
<script src="JS/Enreg_Apport.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen" />
<script type="text/javascript" src="dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
<body>
<fieldset style="width:750px; margin-left:150px; border-color:#FFFBF0" ><legend>Modification d'un Mouvement de fonds.</legend>
<table>
<form action="CTRL/Ctrl_Mod_Apport.php" method="post" onsubmit="return verif_form()" >
<tr>
	<td><label for="Code"> Code *</label></td>
	<td><input type="text" id="Code" name="Code" style="width:200px; background-color:#ECECEC;" readonly="readonly" value="<?php echo $rslt["ID_APPORT"];?>"/> </td>
    <td><label for="Libelle"> Libelle *</label></td> 
    <td><input type="text" id="Libelle" name="Libelle" style="width:200px" maxlength="32" value="<?php echo $rslt["LIBELLE"];?>"/>   </td>
</tr>
<tr>
    <td><label for="Montant"> Montant *</label></td>
    <td><input type="text" id="Montant" name="Montant" style="width:200px;" value="<?php echo $rslt["MONTANT"];?>"></td>
    <td><label for="Date"> Date d'enregistrement * </label> </td>
    <td><input type="text" id="Date" name="Date"style="width:110px;" value="<?php echo dateFormatFrancais($rslt["DATE_APPORT"]);?>"/> <input type="button" value="Calendrier" onclick="displayCalendar(document.forms[0].Date,'dd/mm/yyyy',this)" /></td>
</tr>
<tr>
    <td colspan="2"><input type="submit" align="left" value="Modifier" id="Modifier" name="Modifier"/></td>
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
				alert('Vous n\'etes pas habiliter a  acceder a cette page.');
				history.back();
				</script>
<?php
}

?>
