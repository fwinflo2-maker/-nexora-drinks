<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Comptable" || $_SESSION['habilitation']=="Comptable" || $_SESSION['habilitation']=="Magasinier"))
{
	include("Connexion.php");
	include("fonctions.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire  d'ajout d'une consigne à un Appro.</title>
<style type="text/css">
label
{
	display:block;
	width:150px;
	float: left;
	}
</style>
<script src="JS/Ajout_Consigne_Appro.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen" />
<script type="text/javascript" src="dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
</head>
 
<body>

<form action="CTRL/Ctrl_Ajout_Consigne_Appro.php" method="post" onsubmit="return verif_form()" >
<fieldset style=" width:750px; margin-left:150px;"><legend>Ajout d'une Consigne à un Approvisionnement</legend>
<table>
<tr>
	<td><label for="codeappro">Appro *</label> </td>
    <td><input type="text" id="codeappro" name="codeappro" value="<?php echo $_GET['Ap']?>" readonly="readonly" style="background:#ECECEC; width:200px;"/></td>
    <td ><label>Date Consigne :</label></td>
    <td><input type="text" id="Date" name="Date" style="width:140px;" value="<?php echo dateFormatFrancais($_GET['DateA'])?>"/><input type="button" value="Calendrier" onclick="displayCalendar(document.forms[0].Date,'dd/mm/yyyy',this)" />
    </td>
</tr>
<tr>
	<td><label for="Emb"> Emballage * </label></td>
    <td><select name="Emb" id="Emb" style="width:200px;">
     <?php
	    $sql = " select id_emballage,libelle from emballage  where statut='Actif'";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		 echo "<option value='".$rslt["id_emballage"]."'>";
		 echo $rslt["libelle"];
		 echo '</option>';
		 }
		 ?>
    </select> </td>
	<td><label for="qte"> Quantite * </label></td>
    <td><input type="text" id="qte" name="qte" style="width:230px;"/></td>
</tr>
<tr>
    <td colspan="2"><input type="submit" align="left" value="Enregistrer" id="Enregistrer" name="Enregistrer"/></td>
    <td colspan="2" align="right"><input type="reset" align="right" value="Fin de Saisie" id="Retour" name="Retour" onclick="history.back()"/></td>
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
