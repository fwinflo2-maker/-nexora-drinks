<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"))
{
	include("Connexion.php");
	include("fonctions.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Enregistrement Reglement Vente.</title>
<style type="text/css">
label
{
	display:block;
	width:130px;
	float: left;
	}
</style>
<script src="JS/Enreg_Reglement.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen" />
<script type="text/javascript" src="dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
</head>
 
<body>

<form action="CTRL/Ctrl_Ajout_Reglement.php" method="post" onsubmit="return verif_form()" >
<fieldset style=" width:800px; margin-left:150px;"><legend>Enregistrer Reglement Vente</legend>
<table>
<tr>
	<td><label for="codevente">Vente *</label> </td>
    <td><input type="text" id="codevente" name="codevente" style="width:200px; background-color:#ECECEC;" value="<?php echo $_GET['Vte'];?>" readonly="readonly"/></td>
</tr>
<tr>
     <td><label for="date"> Date Versement * </label> </td>
    <td><input type="text" id="date" name="date"style="width:110px;" value="<?php echo date("d/m/Y");?>"/> <input type="button" value="Calendrier" onclick="displayCalendar(document.forms[0].date,'dd/mm/yyyy',this)" /></td>
    
	<td><label for="avance"> Avance * </label></td>
    <td><input type="text" id="avance" name="avance" style="width:300px;"/></td>
</tr>
<tr>
    <td colspan="2"><input type="submit" align="left" value="Enregistrer" id="Enregistrer" name="Enregistrer"/></td>
    <td colspan="4" align="right"><input type="reset" align="right" value="Fin Saisie" id="Retour" name="Retour" onclick="history.back()"/></td>
</tr>
</table>
</fieldset>
</form>
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
