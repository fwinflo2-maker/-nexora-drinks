<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="DGA" || $_SESSION['habilitation']=="Superviseur"))
{
	include("Connexion.php");
	include("fonctions.php");
?>
<!DOCTYPE html >
<html>
<head>
        <!-- Custom styles for this template-->
<link href="css/sb-admin-2.min.css" rel="stylesheet">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire  de saisie d'inventaire.</title>
<style type="text/css">
label
{
	display:block;
	width:140px;
	float: left;
	}
</style>
<script src="JS/Enreg_Inv.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen" />
<script type="text/javascript" src="dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
</head>
 
<body>

<form action="CTRL/Ctrl_Inv.php" method="post" onSubmit="return verif_form()" >
<fieldset style=" width:650px; margin:auto"><legend>SAISIE INVENTAIRE</legend>
<table >
<tr>
	<td><label for="code"> Code Inv. * </label></td>
    <td><input type="text" id="code" name="code" style="width:200px; background:#BFBFBF" value="<?php echo generer_code_inv();?>" readonly/></td>

</tr>
<tr>
	<td><label for="code"> <h3>CREDIT </h3></label></td>
</tr>
<tr>
    <td><label for="caisse">Solde Caisse *</label></td>
    <td><input type="text" id="caisse" name="caisse" style="width:200px; " /></td>
    <td><label for="soldesabc">Solde Brasseries *</label></td>
    <td><input type="text" id="soldesabc" name="soldesabc" style="width:200px; "/></td>
</tr>
<tr>
    <td><label for="soldeom">Solde OM *</label></td>
    <td><input type="text" id="soldeom" name="soldeom" style="width:200px; " /></td>
    <td><label for="soldemomo">Solde MOMO *</label></td>
    <td><input type="text" id="soldemomo" name="soldemomo" style="width:200px; "  /></td>
</tr>
<tr>
    <td><label for="creditclient">Credit Client *</label></td>
    <td><input type="text" id="creditclient" name="creditclient" style="width:200px; " /></td>
    <td><label for="creditemballage">Credit Emballages *</label></td>
    <td><input type="text" id="creditemballage" name="creditemballage" style="width:200px; "  /></td>
</tr>
<tr>
    <td><label for="soldebanque">Solde Banque *</label></td>
    <td><input type="text" id="soldebanque" name="soldebanque" style="width:200px; " /></td>
    <td><label for="autrecredit"> Autres  *</label></td>
    <td><input type="text" id="autrecredit" name="autrecredit" style="width:200px; " /></td>
</tr>
<tr>
	<td><label> <h3>DEBIT </h3></label></td>
</tr>
<tr>
    <td><label for="creditsabc">Credit Brasseries *</label></td>
    <td><input type="text" id="creditsabc" name="creditsabc" style="width:200px; " /></td>
    <td><label for="creditbanque"> Credit Banque *</label></td>
    <td><input type="text" id="creditbanque" name="creditbanque" style="width:200px; "  /></td>
</tr>
<tr>
    <td><label for="ristournes">Ristournes Clients *</label></td>
    <td><input type="text" id="ristournes" name="ristournes" style="width:200px; " /></td>
    <td><label for="autredebit"> Autres *</label></td>
    <td><input type="text" id="autredebit" name="autredebit" style="width:200px; "  /></td>
</tr>
<tr>
	<td><label for="code"> <h3>EMBALLAGES </h3></label></td>
</tr>
<tr>
    <td><label for="palettebois"> Palettes Bois(Qte) *</label></td>
    <td><input type="text" id="palettebois" name="palettebois" style="width:200px; " /></td>
    <td><label for="pupalettebois">PU *</label></td>
    <td><input type="text" id="pupalettebois" name="pupalettebois" style="width:200px; " value="40000" /></td>
</tr>
<tr>
    <td><label for="paletteplastique"> Palettes Plastiques(Qte) *</label></td>
    <td><input type="text" id="paletteplastique" name="paletteplastique" style="width:200px; " /></td>
    <td><label for="pupaletteplastique">PU *</label></td>
    <td><input type="text" id="pupaletteplastique" name="pupaletteplastique" style="width:200px; " value="60000" /></td>
</tr>
<tr>
    <td><label for="emb_plein"> Emb. Pleins(Qte) *</label></td>
    <td><input type="text" id="emb_plein" name="emb_plein" style="width:200px; " /></td>
    <td><label for="pu_emb_plein">PU *</label></td>
    <td><input type="text" id="pu_emb_plein" name="pu_emb_plein" style="width:200px; " value="3600" /></td>
</tr>
<tr>
    <td><label for="emb_vide"> Emb. Vides(Qte) *</label></td>
    <td><input type="text" id="emb_vide" name="emb_vide" style="width:200px; " /></td>
    <td><label for="pu_emb_vide">PU *</label></td>
    <td><input type="text" id="pu_emb_vide" name="pu_emb_vide" style="width:200px; " value="1200" /></td>
</tr>
<tr>
    <td colspan="3"><input type="submit" class="btn btn-primary btn-user btn-block" align="left" value="Enregistrer" id="Enregistrer" name="Enregistrer" style="width:40%;"/></td>
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
