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
<title>Parametre pour liste appro d'un Fssr</title>
<style type="text/css">
label
{
	display:block;
	width:100px;
	float: left;
	}
</style>
<script src="JS/Consultation_Appro_Fournisseur.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen" />
	<script type="text/javascript" src="dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
</head>

<body>
<form method="post" action="CTRL/Controle_Appro_Fournisseur.php" onsubmit="return verif_form()">
<fieldset style="width:610px; margin-left:250px"><legend>Consultation de la liste des Approvisionnements d'un fournisseur</legend>
<table >
<tr>
	<td align="left"><label for="codefssr"> Fournisseur :</label></td>
    <td><select name="codefssr" id="codefssr" style="width:200px;">
    <?php
    	$sql = " select id_fournisseur ,nom from fournisseur order by nom  ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		 echo "<option value='".$rslt["id_fournisseur"]."'>";
		 echo $rslt["nom"];
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
