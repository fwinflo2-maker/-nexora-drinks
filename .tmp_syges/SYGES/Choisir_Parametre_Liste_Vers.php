<?php 
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" || $_SESSION['habilitation']=="Caissier"  || $_SESSION['habilitation']=="Comptable"))
{

	include('Connexion.php');
	include('fonctions.php');
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<style type="text/css">
label
{
	display:block;
	width:100px;
	float: left;
	}
</style>
<script src="JS/Consultation_Liste_Vente.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen" />
	<script type="text/javascript" src="dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
</head>

<body>
<form method="post" action="CTRL/Controle_Liste_Vers.php" onsubmit="return verif_form()">
<fieldset style="width:610px; margin-left:250px"><legend>Consultation des Versements</legend>
<table >
<tr>
	<td align="left"><label for="codeuser"> Utilisateur :</label></td>
    <td><select name="codeuser" id="codeuser" style="width:200px;">
    <option> TOUS </option>
    <?php
    	$sql = " select login ,nom from user order by nom  ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		 echo "<option value='".$rslt["login"]."'>";
		 echo $rslt["nom"];
		 echo '</option>';
		 }
		 ?>
    </select> </td>
</tr>
<tr>
    <td><label>Période Du :</label></td>
    <td><input type="text" id="DateD" name="DateD" style="width:100px;"/> 
        <input type="button" value="Calendrier" onclick="displayCalendar(document.forms[0].DateD,'dd/mm/yyyy',this)" /></td>
    <td ><label style="text-align: left;">Au :</label>
    <input type="text" id="DateF" name="DateF" style="width:100px;"/> 
    <input type="button" value="Calendrier" onclick="displayCalendar(document.forms[0].DateF,'dd/mm/yyyy',this)" /></td>
</tr>
<tr>
    <td><input type="submit" value="Valider" name="Valider" id="Valider"/> </td>	
    <td colspan="2" align="right"><input type="reset" value="Annuler" name="Annuler" id="Retour"/></td>
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
