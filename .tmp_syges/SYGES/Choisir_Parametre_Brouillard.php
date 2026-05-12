<?php 
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"  || $_SESSION['habilitation']=="Comptable"))
{

	include('Connexion.php');
	include('fonctions.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Parametre Pour Brouillard</title>
<style type="text/css">
label
{
	display:block;
	width:130px;
	float: left;
	}
</style>
<script src="JS/Consultation_Brouillard.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen" />
	<script type="text/javascript" src="dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
</head>

<body>
<form method="post" action="CTRL/Controle_Brouillard.php" onsubmit="return verif_form()">
<fieldset style="width:750px; margin-left:150px"><legend>Consultation Brouillard</legend>
<table >
<tr>
	<td align="left"><label for="solde"> Solde Initial/Veille :</label></td>
 	<td><input type="text" id="solde" name="solde" style="width:180px;"/> FCFA</td>
</tr>

	<tr>
    	<td><label> Période Début :</label></td>
        <td><input type="text" id="DateD" name="DateD" style="width:100px;"/> 
            <input type="button" value="Calendrier" onclick="displayCalendar(document.forms[0].DateD,'dd/mm/yyyy',this)" /></td>
        <td ><label style="text-align: left;">Fin :</label>
        <input type="text" id="DateF" name="DateF" style="width:100px;"/> 
        <input type="button" value="Calendrier" onclick="displayCalendar(document.forms[0].DateF,'dd/mm/yyyy',this)" /></td>
    </tr>
   <tr>
   		<td><input type="submit" value="Valider" name="Valider" id="Valider"/> </td>	
        <td colspan="2" align="right"><input type="button" value="Retour" name="Retour" id="Retour" onclick="history.back()"/></td>
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
