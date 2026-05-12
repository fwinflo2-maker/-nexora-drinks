<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
{
 include ("connexion.php");
 include('fonctions.php');
 $sql='SELECT  * FROM FOURNISSEUR WHERE ID_FOURNISSEUR="'.$_GET['Id'].'"' ;
 $reponse= $DataBase->query($sql);
 $rslt= $reponse->fetch();
		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire de Modification d'un Fournisseur.</title>
<style type="text/css">
label
{
	display:block;
	width:150px;
	float: left;
}
</style>
</style>
<script src="JS/Enreg_Fournisseur.js" type="text/javascript"></script>
</head>

<body>
<fieldset style="width:750px; margin-left:150px; border-color:#FFFBF0" ><legend><h5>Modification d'un fournisseur</h5></legend>
<table>
<form action="CTRL/Ctrl_Mod_Fournisseur.php" method="post" onsubmit="return verif_form()">
<tr>
	<td><label for="code"> Code * </label></td>
    <td><input type="text" id="code" name="code" style="width:200px; background-color:#ECECEC" value="<?php echo $rslt["ID_FOURNISSEUR"];?>" readonly="readonly"/> </td>
</tr>
<tr>
	<td><label for="nom"> Nom * </label></td>
	<td><input type="text" id="nom" name="nom" style="width:200px" value="<?php echo $rslt["NOM"]; ?>"/> </td>
    <td><label for="numtel"> N° Tel  </label></td> 
    <td><input type="text" id="numtel" name="numtel" style="width:200px" value="<?php echo $rslt["NUMTEL"]; ?>"/></td>
</tr>
<tr>
    <td><label for="email"> E-mail  </label> </td>
    <td><input type="text" name="email" id="email" style="width:200px" value="<?php echo $rslt["EMAIL"]; ?>"/> </td>
    <td><label for="statut"> Statut *</label></td>
	<td><select name="statut" id="statut" style="width:200px;">
            <option <?php if ($rslt["STATUT"]=='Actif') echo 'selected';?> > Actif</option>
            <option <?php if ($rslt["STATUT"]=='Archive') echo 'selected';?> > Archive</option>
        </select> </td>
</tr>
<tr>
    <td colspan="2"><input type="submit" align="left" value="Modifer" id="Modifer" name="Modifer"/>
    <td colspan="2" align="right"><input type="button" align="right" value="Retour" id="Retour" name="Retour" onclick=" history.back ()"/>
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
