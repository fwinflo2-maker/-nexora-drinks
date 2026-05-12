<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Comptable"))
{
	include ("connexion.php");
 $sql='SELECT  * FROM PARAMETRE ' ;
 $reponse= $DataBase->query($sql);
 $rslt= $reponse->fetch();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire d'enregistrement des parametres.</title>
<style type="text/css">
label
{
	display:block;
	width:200px;
	float: left;
}
</style>
<script src="JS/Enreg_Param.js" type="text/javascript"></script>
</head>
<body>

<form action="CTRL/Controle_Param.php" method="post" onsubmit="return verif_form()">
<fieldset style="width:750px; margin-left:15%; border-color:#FFFBF0" ><h4><legend>Enregistrement des Paramètres du Brouillard des Ventes</legend></h4>
<table>
<tr>
 	
	<td><label for="psa"> PSA (%)* </label></td>
    <td><input type="text" id="psa" name="psa" style="width:150px;" value="<?php echo $rslt["PSA"];?>" /> </td>
    <td><label for="tva"> TVA (%) *</label></td> 
    <td><input type="text" id="tva" name="tva" style="width:150px" maxlength="15" value="<?php echo $rslt["TVA"];?>"/></td>
</tr>
<tr>
	<td><label for="exercice"> Exercice Encours * </label></td>
	<td><input type="text" id="exercice" name="exercice" style="width:150px" value="<?php echo $rslt["EXERCICE"];?>" maxlength="4"/> </td>
	<td><label for="tauxacompteib"> ACOMPTE I.B (%)* </label></td>
    <td><input type="text" id="tauxacompteib" name="tauxacompteib" style="width:150px;" value="<?php echo $rslt["TAUXACOMPTEIB"];?>" /> </td>
</tr>
<tr>
    <td><label for="tauxcacorrespondant"> Taux CA Correspondant (%)* </label> </td>
    <td align="right"><input type="tauxcacorrespondant" name= "tauxcacorrespondant" id="tauxcacorrespondant" style="width:150px" value="<?php echo $rslt["TAUXCACORRESPONDANT"];?>"/> </td>
    <td><label> Precompte (%)*</label></td>
    <td><input type="text" id="precompte" name="precompte" style="width:150px;" value="<?php echo $rslt["PRECOMPTE"];?>" /> </td>
</tr>
</table>
</fieldset>

<fieldset style="width:750px; margin-left:15%; border-color:#FFFBF0" ><h4><legend>Enregistrement des Paramètres Pour les Remises sur Achat</legend></h4>
<table>
<tr>
	<td><label for="tauxepargne"> Taux Epargne(%)* </label></td>
    <td><input type="text" id="tauxepargne" name="tauxepargne" style="width:150px;" value="<?php echo $rslt["TAUXEPARGNE"];?>" /> </td>
    <td><label for="tauxremisesht"> Taux Remises HT(%)* </label></td>
    <td><input type="text" id="tauxremisesht" name="tauxremisesht" style="width:150px;" value="<?php echo $rslt["TAUXREMISESHT"];?>" /> </td>
</tr>
<tr>
 	
	<td><label for="psaremise"> PSA Remise(%)* </label></td>
    <td><input type="text" id="psaremise" name="psaremise" style="width:150px;" value="<?php echo $rslt["PSAREMISE"];?>" /> </td>
    <td><label for="RetFiscPro"> Ret. Fisc. Pro.(%) *</label></td> 
    <td><input type="text" id="RetFiscPro" name="RetFiscPro" style="width:150px" maxlength="15" value="<?php echo $rslt["TAUXRETFISCPRO"];?>"/></td>
</tr>
<tr>
	<td><label for="bonuscasse"> Bonus Casse (%)* </label></td>
	<td><input type="text" id="bonuscasse" name="bonuscasse" style="width:150px" value="<?php echo $rslt["BONUSCASSE"];?>" /> </td>
	<td><label for="depotgarantie"> Depot Garantie (%)* </label></td>
    <td><input type="text" id="depotgarantie" name="depotgarantie" style="width:150px;" value="<?php echo $rslt["DEPOTGARANTIE"];?>" /> </td>
</tr>
</table>
</fieldset>

<fieldset style="width:750px; margin-left:15%; border-color:#FFFBF0" ><h4><legend>Enregistrement des Paramètres Pour les Ristournes</legend></h4>
<table>
<tr>
    <td><label for="tauxristournesht"> Taux Risournes HT(%)* </label></td>
    <td><input type="text" id="tauxristournesht" name="tauxristournesht" style="width:150px;" value="<?php echo $rslt["TAUXRISTOURNESHT"];?>" /> </td>
	<td><label for="psaristournes"> PSA Ristournes(%)* </label></td>
    <td><input type="text" id="psaristournes" name="psaristournes" style="width:150px;" value="<?php echo $rslt["PSARISTOURNES"];?>" /> </td>
</tr>
</table>
</fieldset>
<fieldset style="width:750px; margin-left:15%; border-color:#FFFBF0" ><h4><legend>Enregistrement des Objectifs (Colis)</legend></h4>
<table>
<tr>
 	
	<td><label for="annuel"> Annuel * </label></td>
    <td><input type="text" id="annuel" name="annuel" style="width:150px;" value="<?php echo $rslt["OBANNU"];?>"/> </td>
</tr>
<tr>
    <td><label for="janv"> Janvier *</label></td> 
    <td><input type="text" id="janv" name="janv" style="width:150px" maxlength="15" value="<?php echo $rslt["OBJANV"];?>"/></td>
	<td><label for="fevr"> Fevrier * </label></td>
	<td><input type="text" id="fevr" name="fevr" style="width:150px" value="<?php echo $rslt["OBFEVR"];?>" /> </td>
</tr>
<tr>
    <td><label for="mars"> Mars *</label></td> 
    <td><input type="text" id="mars" name="mars" style="width:150px" maxlength="15" value="<?php echo $rslt["OBMARS"];?>"/></td>
	<td><label for="avri"> Avril * </label></td>
	<td><input type="text" id="avri" name="avri" style="width:150px" value="<?php echo $rslt["OBAVRI"];?>" /> </td>
</tr>
<tr>
    <td><label for="mai"> Mai *</label></td> 
    <td><input type="text" id="mai" name="mai" style="width:150px" maxlength="15" value="<?php echo $rslt["OBMAI"];?>"/></td>
	<td><label for="juin"> Juin * </label></td>
	<td><input type="text" id="juin" name="juin" style="width:150px" value="<?php echo $rslt["OBJUIN"];?>" /> </td>
</tr>
<tr>
    <td><label for="juil"> Juillet *</label></td> 
    <td><input type="text" id="juil" name="juil" style="width:150px" maxlength="15" value="<?php echo $rslt["OBJUIL"];?>"/></td>
	<td><label for="aout"> Aout * </label></td>
	<td><input type="text" id="aout" name="aout" style="width:150px" value="<?php echo $rslt["OBAOUT"];?>" /> </td>
</tr>
<tr>
    <td><label for="sept"> Septembre *</label></td> 
    <td><input type="text" id="sept" name="sept" style="width:150px" maxlength="15" value="<?php echo $rslt["OBSEPT"];?>"/></td>
	<td><label for="octo"> Octobre * </label></td>
	<td><input type="text" id="octo" name="octo" style="width:150px" value="<?php echo $rslt["OBOCTO"];?>" /> </td>
</tr>
<tr>
    <td><label for="nove"> Novembre *</label></td> 
    <td><input type="text" id="nove" name="nove" style="width:150px" maxlength="15" value="<?php echo $rslt["OBNOVE"];?>"/></td>
	<td><label for="dece"> Decembre * </label></td>
	<td><input type="text" id="dece" name="dece" style="width:150px" value="<?php echo $rslt["OBDECE"];?>" /> </td>
</tr>
<tr>
    <td colspan="2"><input type="submit" align="left" value="Enregistrer" id="Enregistrer" name="Enregistrer"/></td>
    <td><input type="text" id="code" name="code" style="width:200px; background-color:#ECECEC; visibility:hidden" readonly="readonly" value="<?php echo $rslt["ID_PARAMETRE"];?>"/> </td>
    <td colspan=""align="right"><input type="button" value="Retour" id="Retour" name="Retour" onclick="history.back()"/></td>
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
				alert('Vous n\'etes pas habiliter a acceder a cette page.');
				history.back();
				</script>
<?php
}
?>
