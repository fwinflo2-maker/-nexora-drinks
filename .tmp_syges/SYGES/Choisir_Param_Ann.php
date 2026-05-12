<?php 
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur"))
{

	include('Connexion.php');
	include('fonctions.php');
?>

<!DOCTYPE html >
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<style type="text/css">
label
{
	display:block;
	width:120px;
	float: left;
	}
</style>
<script src="JS/Consultation_Liste_Vente.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen" />
<script type="text/javascript" src="dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
</head>

<body>
<form method="post" action="CTRL/Controle_Liste_Ann.php" onSubmit="return verif_form()">
<fieldset style="margin-left:250px; width:600px;"><legend>Consultation des Annulations</legend>
<table>
<tr>
	<td align="left"><label for="typemouv"> Type d'operation :</label></td>
    <td><select name="typemouv" id="typemouv" style="width:200px;">
         <option>Toutes</option>;
		 <option>Ventes</option>;
		 <option>Entrées Cessions</option>;
         <option>Sorties Cessions</option>;
         <option>Approvisionnements</option>;
    </select> </td>
</tr>
<tr>
    	<td><label> Période /Début :</label></td>
        <td><input type="text" id="DateD" name="DateD" style="width:100px;"/> 
            <input type="button" value="Calendrier" onClick="displayCalendar(document.forms[0].DateD,'dd/mm/yyyy',this)" /></td>
        <td><label style="text-align: left;">Fin :</label></td>
        <td><input type="text" id="DateF" name="DateF" style="width:100px;"/> 
        <input type="button" value="Calendrier" onClick="displayCalendar(document.forms[0].DateF,'dd/mm/yyyy',this)" /></td>
</tr>
<tr>
   		<td><input type="submit" value="Valider" name="Valider" id="Valider"/> </td>	
        <td colspan="3" align="right"><input type="reset" value="Annuler" name="Annuler" id="Retour"/></td>
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
