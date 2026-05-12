<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
{
 include ("connexion.php");
 include('fonctions.php');
 $sql='SELECT  * FROM CLIENT WHERE ID_CLIENT="'.$_GET['Id'].'"' ;
 $reponse= $DataBase->query($sql);
 $rslt= $reponse->fetch();
		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire de Modification d'un Client.</title>
<style type="text/css">
label
{
	display:block;
	width:150px;
	float: left;
}
</style>
</style>
<script src="JS/Enreg_Client.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen" />
	<script type="text/javascript" src="dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
</head>
<body>
<form action="CTRL/Ctrl_Mod_Client.php" method="post" onsubmit="return verif_form()">
<fieldset style="width:750px; margin-left:15%; border-color:#FFFBF0" ><h4><legend>Informations Client</legend></h4>
<table>
<tr>
	<td><label for="code"> Code * </label></td>
    <td><input type="text" id="code" name="code" style="width:200px; background-color:#ECECEC" value="<?php echo $rslt["ID_CLIENT"];?>" readonly="readonly"/> </td>
	<td><label for="categorie"> Categorie * </label></td>
    <td><select name="categorie" id="categorie" style="width:200px;">
         <?php
		$sql4 = " select id_categorie ,libelle from categorie  where statut='Actif' order by libelle  ";
		$reponse4= $DataBase->query($sql4);
		while($rslt4= $reponse4->fetch())
		{
		if ($rslt4["id_categorie"]==$rslt["ID_CATEGORIE"])
		{	
			 echo "<option selected value='".$rslt4["id_categorie"]."'>";
		}
		else
		{
			 echo "<option value='".$rslt4["id_categorie"]."'>";
		}
			 echo $rslt4["libelle"];
			 echo '</option>';
		
		 }
		 ?>
    </select> </td>
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
        </select> 
    </td>
</tr>
</table>
</fieldset>
<fieldset style="width:750px; margin-left:15%; border-color:#FFFBF0" ><h4><legend>Numero Indenfiant Unique et Registre de Commerce</legend></h4>
<table>
<tr>
	<td><label for="niu"> N.I.U  </label></td>
	<td><input type="text" id="niu" name="niu" style="width:200px" value="<?php echo $rslt["NIU"]; ?>"/> </td>
    <td><label for="rc">  R.C </label></td> 
    <td><input type="text" id="rc" name="rc" style="width:200px" value="<?php echo $rslt["RC"]; ?>"/></td>
</tr>
</table>
</fieldset>
<fieldset style="width:750px; margin-left:15%; border-color:#FFFBF0" ><h4><legend>Enregistrement des Frais d'enlevement </legend></h4>
<table>
<tr>
    <td><label for="fraisenlevement">  Frais Enlevement * </label></td>
    <td><input type="text" id="fraisenlevement" name="fraisenlevement" style="width:150px;" value="<?php echo $rslt["FRAISENLEVEMENT"];?>"/> </td>
    <td><label for="fraisenlevement_pet">  PET * </label></td>
    <td><input type="text" class="form-control" id="fraisenlevement_pet" name="fraisenlevement_pet" style="width:200px;" value="<?php echo $rslt["FRAISENLEVEMENT_PET"];?>"/> </td>  
</tr>
</table>
</fieldset>
<fieldset style="width:750px; margin-left:15%; border-color:#FFFBF0" ><h4><legend>Enregistrement des Paramètres Pour les Ristournes</legend></h4>
<table>
<tr>
    <td><label for="tauxristourneht"> Taux Risournes HT(%)* </label></td>
    <td><input type="text" id="tauxristourneht" name="tauxristourneht" style="width:150px;" value="<?php echo $rslt["TAUXRISTOURNEHT"];?>" /> </td>
	<td><label for="psaristournes"> PSA Ristournes(%)* </label></td>
    <td><input type="text" id="psaristournes" name="psaristournes" style="width:150px;" value="<?php echo $rslt["PSARISTOURNES"];?>" /> </td>
</tr>
<tr>
    <td colspan="2"><input type="submit" align="left" value="Modifer" id="Modifer" name="Modifer"/>
    <td colspan="2" align="right"><input type="button" align="right" value="Retour" id="Retour" name="Retour" onclick=" history.back ()"/>
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
				alert('Vous n\'etes pas habiliter à acceder a cette page.');
				history.back();
				</script>
<?php
}
?>
