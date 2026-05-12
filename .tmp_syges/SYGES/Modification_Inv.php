<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
{
	include("Connexion.php");
	include("fonctions.php");
	
	$sql2 = " select *  from inventaire  where id_inv='".$_GET['Id']."'";
	$reponse2= $DataBase->query($sql2);
	$rslt2= $reponse2->fetch();
?>
<!DOCTYPE html >
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire  de saisie d'inventaire.</title>
<style type="text/css">
label
{
	display:block;
	width:130px;
	float: left;
	}
</style>
<script src="JS/Enreg_Inv.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen" />
<script type="text/javascript" src="dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
</head>
 
<body>

<form action="CTRL/Ctrl_Mod_Inv.php" method="post" onSubmit="return verif_form()" >
<fieldset style=" width:650px; margin:auto"><legend><h3>Modification Inventaire</h3></legend>
<table >
<tr>
</tr>
	<td><label for="code"> Code Inv. * </label></td>
    <td><input type="text" id="code" name="code" style="width:200px; background-color:#ECECEC" value="<?php echo $rslt2['ID_INV']; ?>" readonly/></td>
    <td><label for="mag">  *</label></td>
    <td><input type="text" id="mag" name="mag" style="width:200px; background-color:#ECECEC" value="<?php echo $rslt2['ID_MAGASIN']; ?>"/></td>
</tr>

<tr>
	<td><label> <h3>CREDIT </h3></label></td>
</tr>
<tr>
    <td><label for="caisse">Solde Caisse *</label></td>
    <td><input type="text" id="caisse" name="caisse" style="width:200px; " value="<?php echo $rslt2['SOLDECAISSE']; ?>"/></td>
    <td><label for="soldesabc">Solde Brasseries *</label></td>
    <td><input type="text" id="soldesabc" name="soldesabc" style="width:200px; " value="<?php echo $rslt2['SOLDESABC']; ?>"/></td>
</tr>
<tr>
    <td><label for="soldeom">Solde OM *</label></td>
    <td><input type="text" id="soldeom" name="soldeom" style="width:200px; " value="<?php echo $rslt2['SOLDEOM']; ?>"/></td>
    <td><label for="soldemomo">Solde MOMO *</label></td>
    <td><input type="text" id="soldemomo" name="soldemomo" style="width:200px; " value="<?php echo $rslt2['SOLDEMOMO']; ?>" /></td>
</tr>
<tr>
    <td><label for="creditclient">Credit Client *</label></td>
    <td><input type="text" id="creditclient" name="creditclient" style="width:200px; " value="<?php echo $rslt2['CREDITCLIENT']; ?>"/></td>
    <td><label for="creditemballage">Credit Emballages *</label></td>
    <td><input type="text" id="creditemballage" name="creditemballage" style="width:200px; " value="<?php echo $rslt2['CREDITEMBALLAGE']; ?>" /></td>
</tr>
<tr>
    <td><label for="soldebanque">Solde Banque *</label></td>
    <td><input type="text" id="soldebanque" name="soldebanque" style="width:200px; " value="<?php echo $rslt2['SOLDEBANQUE']; ?>"/></td>
    <td><label for="autrecredit"> Autres  *</label></td>
    <td><input type="text" id="autrecredit" name="autrecredit" style="width:200px; " value="<?php echo $rslt2['AUTRECREDIT']; ?>"/></td>
</tr>
<tr>
	<td><label> <h3>DEBIT </h3></label></td>
</tr>
<tr>
    <td><label for="creditsabc">Credit Brasseries *</label></td>
    <td><input type="text" id="creditsabc" name="creditsabc" style="width:200px; " value="<?php echo $rslt2['CREDITBRASSERIES']; ?>"/></td>
    <td><label for="creditbanque"> Credit Banque *</label></td>
    <td><input type="text" id="creditbanque" name="creditbanque" style="width:200px; " value="<?php echo $rslt2['CREDITBANQUE']; ?>" /></td>
</tr>
<tr>
    <td><label for="ristournes">Ristournes Clients *</label></td>
    <td><input type="text" id="ristournes" name="ristournes" style="width:200px; " value="<?php echo $rslt2['RISTOURNESCLIENTS']; ?>"/></td>
    <td><label for="autredebit"> Autres *</label></td>
    <td><input type="text" id="autredebit" name="autredebit" style="width:200px; " value="<?php echo $rslt2['AUTRESDEBIT']; ?>" /></td>
</tr>
<tr>
	<td><label for="code"> <h3>EMBALLAGES </h3></label></td>
</tr>
<tr>
    <td><label for="palettebois"> Palettes Bois(Qte) *</label></td>
    <td><input type="text" id="palettebois" name="palettebois" style="width:200px; " value="<?php echo $rslt2['PALETTEBOIS']; ?>"/></td>
    <td><label for="pupalettebois">PU *</label></td>
    <td><input type="text" id="pupalettebois" name="pupalettebois" style="width:200px; " value="<?php echo $rslt2['PU_PALETTEBOIS']; ?>"/></td>
</tr>
<tr>
    <td><label for="paletteplastique"> Palettes Plastiques(Qte) *</label></td>
    <td><input type="text" id="paletteplastique" name="paletteplastique" style="width:200px; " value="<?php echo $rslt2['PALETTEPLASTIQUE']; ?>"/></td>
    <td><label for="pupaletteplastique">PU *</label></td>
    <td><input type="text" id="pupaletteplastique" name="pupaletteplastique" style="width:200px; " value="<?php echo $rslt2['PU_PALETTEPLASTIQUE']; ?>"/></td>
</tr>
<tr>
    <td><label for="emb_plein"> Emb. Pleins(Qte) *</label></td>
    <td><input type="text" id="emb_plein" name="emb_plein" style="width:200px; " value="<?php echo $rslt2['EMB_PLEIN']; ?>"/></td>
    <td><label for="pu_emb_plein">PU *</label></td>
    <td><input type="text" id="pu_emb_plein" name="pu_emb_plein" style="width:200px; " value="<?php echo $rslt2['PU_EMB_PLEIN']; ?>"/></td>
</tr>
<tr>
    <td><label for="emb_vide"> Emb. Vides(Qte) *</label></td>
    <td><input type="text" id="emb_vide" name="emb_vide" style="width:200px; " value="<?php echo $rslt2['EMB_VIDE']; ?>"/></td>
    <td><label for="pu_emb_vide">PU *</label></td>
    <td><input type="text" id="pu_emb_vide" name="pu_emb_vide" style="width:200px; " value="<?php echo $rslt2['PU_EMB_VIDE']; ?>"/></td>
</tr>

<tr>
    <td colspan="3"><input type="submit" class="btn btn-primary btn-user btn-block" align="left" value="Modifier" id="Modifier" name="Enregistrer" style="width:40%;"/></td>
    <td colspan="3" align="right"><input type="reset" class="btn btn-primary btn-user btn-block" align="right" value="Retour" id="Retour" name="Retour" onClick="history.back()"/></td>
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
