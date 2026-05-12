<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
{
 include ("connexion.php");
 include('fonctions.php');
 $sql='SELECT  * FROM FAMILLE WHERE ID_FAMILLE="'.$_GET['Id'].'"' ;
 $reponse= $DataBase->query($sql);
 $rslt= $reponse->fetch();
		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire de Modification d'une Famille d'Article.</title>
<style type="text/css">
label
{
	display:block;
	width:150px;
	float: left;
}
</style>
</style>
<script src="JS/Enreg_Famille.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen" />
	<script type="text/javascript" src="dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
</head>

<body>
<fieldset style="width:350px; margin-left:350px; border-color:#FFFBF0" ><legend><h5>Modification d'une Categorie</h5></legend>
<table>
<form action="CTRL/Ctrl_Mod_Famille.php" method="post" onsubmit="return verif_form()">
<tr>
	<td><label for="code"> Code * </label></td>
    <td><input type="text" id="code" name="code" style="width:250px; background-color:#ECECEC" value="<?php echo $rslt["ID_FAMILLE"];?>" readonly="readonly"/> 
</tr>
<tr>
	<td><label for="libelle"> Libelle * </label></td>
	<td><input type="text" id="libelle" name="libelle" style="width:250px" value="<?php echo $rslt["LIBELLE"]; ?>"/> </td>
</tr>
<tr>
    <td><label for="statut"> Statut *</label></td>
	<td><select name="statut" id="statut" style="width:250px;">
            <option <?php if ($rslt["STATUT"]=='Actif') echo 'selected';?> > Actif</option>
            <option <?php if ($rslt["STATUT"]=='Archive') echo 'selected';?> > Archive</option>
        </select> 
    </td>
</tr>
<tr>
    <td ><input type="submit" align="left" value="Modifer" id="Modifer" name="Modifer"/>
    <td align="right"><input type="button" align="right" value="Retour" id="Retour" name="Retour" onclick=" history.back ()"/>
</tr>
</form>
</table>
</fieldset>
</body>
</html>
<?php 
}
else
{
?>
				<script language="javascript" type="text/javascript">
				alert('Vous n\'etes pas habiliter à acceder a cette page.');
				history.back();
				</script>
<?php
}
?>
