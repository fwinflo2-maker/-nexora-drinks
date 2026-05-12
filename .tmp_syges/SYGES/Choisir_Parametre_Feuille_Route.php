<?php 
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="OPSC"|| $_SESSION['habilitation']=="Caissier" ||$_SESSION['habilitation']=="Comptable" || $_SESSION['habilitation']=="DGA" || $_SESSION['habilitation']=="Superviseur"))
{

	include('Connexion.php');
	include('fonctions.php');
?>

<!DOCTYPE html PUBLIC >
<html >
<head>
        <!-- Custom styles for this template-->
<link href="css/sb-admin-2.min.css" rel="stylesheet">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Parametre pour Feuille de Route</title>
<style type="text/css">
label
{
	display:block;
	width:100px;
	float: left;
	}
</style>
<script src="JS/Consultation_Feuille_Route.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen" />
	<script type="text/javascript" src="dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
</head>

<body>
<form method="post" action="CTRL/Controle_Feuille_Route.php" onSubmit="return verif_form()">
<fieldset style="width:710px; margin:auto"><legend>Consultation d'une Feuille de Route</legend>
<table >
<tr>

	<td align="left"><label for="codeuser"> Utilisateur :</label></td>
    <td><select class="form-control" name="codeuser" id="codeuser" style="width:250px;">
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

	<tr height="70">
    	<td><label> Période Début :</label></td>
        <td><input type="text" id="DateD" name="DateD" style="width:150px;"/> 
            <input type="button" value="Calendrier" onClick="displayCalendar(document.forms[0].DateD,'dd/mm/yyyy',this)" /></td>
        <td ><label style="text-align: left;">Fin :</label></td>
        <td><input type="text" id="DateF" name="DateF" style="width:150px;"/> 
        <input type="button" value="Calendrier" onClick="displayCalendar(document.forms[0].DateF,'dd/mm/yyyy',this)" /></td>
    </tr>
<tr height="70">
   		<td><input type="submit" class="btn btn-primary btn-user btn-block" value="Valider" name="Valider" id="Valider" style="width:100%;"/> </td>	
        <td colspan="3" align="right"><input type="button" class="btn btn-primary btn-user btn-block" value="Retour" name="Retour" id="Retour" onClick="history.back()" style="width:30%;"/></td>
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
