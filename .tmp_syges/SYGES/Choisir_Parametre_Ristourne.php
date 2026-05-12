<?php 
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
{

	include('Connexion.php');
	include('fonctions.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Parametre Pour Ristourne</title>
<style type="text/css">
label
{
	display:block;
	width:180px;
	float: left;
	}
</style>
<script src="JS/Consultation_Ristourne.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen" />
	<script type="text/javascript" src="dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
</head>

<body>
<form method="post" action="CTRL/Controle_Ristourne.php" onsubmit="return verif_form()">
<fieldset style="width:800px; margin-left:150px"><legend align="center">Consultation des Ristournes</legend>
<table >
	<tr>
    	<td> <h4><label>Période Début :</label> </h4></td>
        <td><input type="text" id="DateD" name="DateD" style="width:90px;"/> 
            <input type="button" value="Calendrier" onclick="displayCalendar(document.forms[0].DateD,'dd/mm/yyyy',this)" /></td>
        <td ><label style="text-align: left;">Fin :</label></td>
        <td ><input type="text" id="DateF" name="DateF" style="width:90px;"/> 
        <input type="button" value="Calendrier" onclick="displayCalendar(document.forms[0].DateF,'dd/mm/yyyy',this)" /></td>
    </tr>
<tr>
	<td align="left"><h4><label for="retenue"> Retenues</label></h4></td>
</tr>
<tr>
    <td align="left"><label for="Retfrigo"> Ret. Frigo :</label></td>
 	<td><input type="text" id="Retfrigo" name="Retfrigo" style="width:180px;" value="0"/> </td>
</tr>
<tr>
    <td align="left"><label for="RetDA"> Ret. Droit d'Auteur :</label></td>
 	<td><input type="text" id="RetDA" name="RetDA" style="width:180px;" value="0"/> </td>
    <td align="left"><label for="RetCGA"> Ret. CGA à la Source :</label></td>
 	<td><input type="text" id="RetCGA" name="RetCGA" style="width:180px;" value="0"/> </td>
</tr>

<tr>
	<td align="left"><h4><label> Regularisations</label></h4></td>
</tr>
<tr>
    <td align="left"><label for="RegRistourne"> Reg. Ristourne :</label></td>
 	<td><input type="text" id="RegRistourne" name="RegRistourne" style="width:180px;" value="0"/> </td>
    <td align="left"><label for="RegPSAEC"> Reg. PSA Exc Encours :</label></td>
 	<td><input type="text" id="RegPSAEC" name="RegPSAEC" style="width:180px;" value="0"/> </td>
</tr>
<tr>
    <td align="left"><label for="RegPSAAnt"> Reg. PSA Exc Anterieur :</label></td>
 	<td><input type="text" id="RegPSAAnt" name="RegPSAAnt" style="width:180px;" value="0"/> </td>
    <td align="left"><label for="RegDA"> Reg. Droit d'Auteur :</label></td>
 	<td><input type="text" id="RegDA" name="RegDA" style="width:180px;" value="0"/> </td>
</tr>
<tr>
    <td align="left"><label for="RegEntfrigo"> Reg. Entretien Frigo :</label></td>
 	<td><input type="text" id="RegEntfrigo" name="RegEntfrigo" style="width:180px;" value="0"/> </td>
    <td align="left"><label for="RegCGA"> Reg. CGA :</label></td>
 	<td><input type="text" id="RegCGA" name="RegCGA" style="width:180px;" value="0"/> </td>
</tr>
<tr>
   		<td><input type="submit" value="Valider" name="Valider" id="Valider"/> </td>	
        <td colspan="3" align="right"><input type="button" value="Retour" name="Retour" id="Retour" onclick="history.back()"/></td>
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
