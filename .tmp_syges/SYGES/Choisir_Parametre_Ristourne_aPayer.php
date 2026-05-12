  <?php 
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur"))
{

	include('Connexion.php');
	include('fonctions.php');
?>

<!DOCTYPE html PUBLIC>
<html>
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
<form method="post" action="CTRL/Controle_Ristourne_aPayer.php" onsubmit="return verif_form()">
<fieldset style="width:800px; margin-left:150px"><legend align="center">Etat des Ristournes à Payer </legend>
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
