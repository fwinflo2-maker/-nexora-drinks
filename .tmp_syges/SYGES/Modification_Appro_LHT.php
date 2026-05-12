<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" || $_SESSION['habilitation']=="Magasinier"))
{
	include("Connexion.php");
	include("fonctions.php");
	$sql = " select *  from fournisseur  where id_fournisseur='".$_GET['Fs']."'";
	$reponse= $DataBase->query($sql);
	$rslt= $reponse->fetch();
	$sql2 = " select *  from approvisionnement  where id_appro='".$_GET['Ap']."'";
	$reponse2= $DataBase->query($sql2);
	$rslt2= $reponse2->fetch();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire  de modification du Liquide HT d'un Appro.</title>
<style type="text/css">
label
{
	display:block;
	width:110px;
	float: left;
	}
</style>
<script src="JS/Enreg_Appro.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen" />
<script type="text/javascript" src="dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
</head>
 
<body>

<form action="CTRL/Ctrl_Mod_Appro_LHT.php" method="post" onsubmit="return verif_form()" >
<fieldset style="width: 950px; margin-left:70px;"> <legend>Informations sur le fournisseur</legend>
<table>
<tr>
	<td><label for="codefournisseur"> Code  </label></td>
    <td><input type="text" id="codefournisseur" name="codefournisseur"  value="<?php echo $rslt['ID_FOURNISSEUR']; ?>" readonly="readonly" style="width:150px; background:#ECECEC;"/></td>
    <td><label for="nomfournisseur"> Nom  </label></td>
    <td><input type="text" id="nomfournisseur" name="nomfournisseur"  value="<?php echo $rslt['NOM']; ?>" readonly="readonly" style="width:185px; background:#ECECEC;"/></td>
	<td><label for="numtel"> N° Tel  </label></td>
    <td><input type="text" id="numtel" name="numtel"  value="<?php echo $rslt['NUMTEL']; ?>" readonly="readonly" style="width:200px;background:#ECECEC;" /></td>
</tr>
</table>
</fieldset>
<fieldset style=" width:950px; margin-left:70px;"><legend>Informations sur l'approvisionnement à modifier</legend>
<table >
<tr>
	<td><label for="codeappro"> Code * </label></td>
    <td><input type="text" id="codeappro" name="codeappro" readonly="readonly" style="width:150px; background-color:#ECECEC" value="<?php echo $rslt2['ID_APPRO']; ?>"/></td>
    <td><label for="nbrecolis"> Nbre de Colis *</label></td>
    <td><input type="text" id="nbrecolis" name="nbrecolis" style="width:185px; " value="<?php echo $rslt2['NBRECOLIS']; ?>"/></td>
</tr>
<tr>
<td><label for="liquideht"> MT Liquide HT * </label></td>
    <td><input type="text" id="liquideht" name="liquideht"  style="width:150px;" value="<?php echo $rslt2['LIQUIDEHT']; ?>"/></td>
    <td><label for="date_appro"> Date de l'appro * </label> </td>
    <td><input type="text" id="date_appro" name="date_appro"style="width:100px;" value="<?php echo dateFormatFrancais($rslt2['DATE_APPRO']); ?>"/> <input type="button" value="Calendrier" onclick="displayCalendar(document.forms[0].date_appro,'dd/mm/yyyy',this)" /></td>
	<td><label for="observationappro"> Observation  </label></td>
    <td><input type="text" id="observationappro" name="observationappro" maxlength="40" style="width:200px;" value="<?php echo $rslt2['OBSERVATION']; ?>"/></td>

<tr>
    <td colspan="" align="left"><input type="submit" align="left" value="Modifier" id="Modifier" name="Modifier"/></td>
    <td colspan="5" align="right"><input type="button" align="right" value="Retour" id="Retour" name="Retour" onclick=" history.back ()"/>
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
