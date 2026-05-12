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
<title>Parametre pour etat psa collectes</title>
<style type="text/css">
label
{
	display:block;
	width:150px;
	float: left;
	}
</style>
<script src="JS/Consultation_PSA.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen" />
	<script type="text/javascript" src="dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
</head>

<body>
<form method="post" action="CTRL/Controle_liste_psa.php" onsubmit="return verif_form()">
<fieldset style="width:710px; margin:auto"><legend>Consultation de l'etat des PSA et de la TVA </legend>
<table >
<tr>
	<td align="left"><label for="codecat"> Categorie / Regime :</label></td>
    <td><select name="codecat" id="codecat" style="width:200px;">
    <?php
    	$sql = " select id_categorie, libelle ,tauxtva, tauxretfiscpro from categorie order by libelle  ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		 echo "<option value='".$rslt["id_categorie"]."'>";
		 echo $rslt["libelle"];
		 echo '</option>';
		 }
		 ?>
    </select> </td>
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
