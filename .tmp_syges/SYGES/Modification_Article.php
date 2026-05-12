<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur"))
{
 include ("connexion.php");
 include('fonctions.php');
 $sql='SELECT  * FROM ARTICLE WHERE ID_ARTICLE="'.$_GET['Id'].'"' ;
 $reponse= $DataBase->query($sql);
 $rslt= $reponse->fetch();
		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire de Modification d'un Article.</title>
<style type="text/css">
label
{
	display:block;
	width:150px;
	float: left;
}
</style>
</style>
<script src="JS/Enreg_Article.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen" />
	<script type="text/javascript" src="dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
</head>

<body>
<fieldset style="width:750px; margin-left:150px; border-color:#FFFBF0" ><legend><h5>Modification d'un article</h5></legend>
<table>
<form action="CTRL/Ctrl_Mod_Article.php" method="post" onsubmit="return verif_form()">
<tr>
	<td><label for="code"> Code * </label></td>
    <td><input type="text" id="code" name="code" style="width:200px; background-color:#ECECEC" value="<?php echo $rslt["ID_ARTICLE"]; ?>" readonly="readonly"/> </td>
</tr>
<tr>
    	<td><label for="libelle"> Libellé * </label></td>
	<td><input type="text" id="libelle" name="libelle" style="width:200px" value="<?php echo $rslt["LIBELLE"]; ?>"/> </td>
    <td><label for="marque"> Conditionnement * </label></td> 
    <td><input type="text" id="marque" name="marque" style="width:200px" value="<?php echo $rslt["MARQUE"]; ?>"/></td>
</tr>
<tr>
    <td><label for="nbrebte"> Nbre Bouteille * </label></td> 
    <td><input type="text" id="nbrebte" name="nbrebte" style="width:200px" value="<?php echo $rslt["NBREBTE"]; ?>"/></td>
   	<td><label for="prixrevient"> Prix de Revient *</label></td>
    <td><input type="text" id="prixrevient" name="prixrevient" style="width:200px" value="<?php echo $rslt["PRIXREVIENT"]; ?>"/> </td>
</tr>
<tr>
    <td><label for="prixvente"> Prix de vente * </label></td>
    <td><input type="text" id="prixvente" name="prixvente" style="width:200px" value="<?php echo $rslt["PRIXVENTE"]; ?>"/> </td>
    <td><label for="prixdetail"> Prix au detail * </label></td>
    <td><input type="text" id="prixdetail" name="prixdetail" style="width:200px" value="<?php echo $rslt["PRIXDETAIL"]; ?>"/> </td>
</tr>
<tr>
    <td><label for="famille"> Famille * </label></td>
    <td><select name="famille" id="famille" style="width:200px;">
         <?php
		$sql4 = " select id_famille,libelle from famille  where statut='Actif' order by libelle";
		$reponse4= $DataBase->query($sql4);
		while($rslt4= $reponse4->fetch())
		{
		if ($rslt4["id_famille"]==$rslt["ID_FAMILLE"])
		{	
			 echo "<option selected value='".$rslt4["id_famille"]."'>";
		}
		else
		{
			 echo "<option value='".$rslt4["id_famille"]."'>";
		}
			 echo $rslt4["libelle"];
			 echo '</option>';
		
		 }
		 ?>
    </select> </td>
    <td><label for="tauxremise"> Taux Ristourne *</label></td>
    <td><input type="text" id="tauxristourne" name="tauxristourne" style="width:200px" value="<?php echo $rslt["TAUXRISTOURNE"]; ?>"/> </td>
</tr>
<tr>
    <td><label for="tauxremise"> Taux Remise HT *</label></td>
    <td><input type="text" id="tauxremise" name="tauxremise" style="width:200px" value="<?php echo $rslt["TAUXREMISE"]; ?>"/> </td>
    <td><label for="statut"> Statut *</label></td>
	<td><select name="statut" id="statut" style="width:200px;">
            <option <?php if ($rslt["STATUT"]=='Actif') echo 'selected';?> > Actif</option>
            <option <?php if ($rslt["STATUT"]=='Archive') echo 'selected';?> > Archive</option>
        </select> 
    </td>
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
