<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur"))
{
	include('fonctions.php');
	include('Connexion.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire d'enregistrement d'un Article.</title>
<style type="text/css">
label
{
	display:block;
	width:150px;
	float: left;
}
</style>
<script src="JS/Enreg_Article.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen" />
	<script type="text/javascript" src="dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
</head>

<body>
<fieldset style="width:750px; margin-left:15%; border-color:#FFFBF0" ><legend>Enregistrement d'un article</legend>
<table>
<form action="CTRL/Controle_Article.php" method="post" onsubmit="return verif_form()">
<tr>
	<td><label for="code"> Code * </label></td>
    <td><input type="text" id="code" name="code" style="width:200px; background-color:#ECECEC" value="<?php echo generer_code_article();?>" readonly="readonly"/> </td>
</tr>
<tr>
    <td><label for="libelle"> Libellé * </label></td>
	<td><input type="text" id="libelle" name="libelle" style="width:200px"/> </td>
    <td><label for="marque"> Conditionnement * </label></td> 
    <td><input type="text" id="marque" name="marque" style="width:200px"/></td>
</tr>
<tr>
	<td><label for="nbrebte"> Nbre Bouteille * </label></td>
	<td><input type="text" id="nbrebte" name="nbrebte" style="width:200px"/> </td>
	<td><label for="prixrevient"> Prix de Revient *</label></td>
    <td><input type="text" id="prixrevient" name="prixrevient" style="width:200px"/> </td>
</tr>
<tr>
    <td><label for="prixvente"> Prix de vente * </label></td>
    <td><input type="text" id="prixvente" name="prixvente" style="width:200px"/> </td>
	<td><label for="prixdetail"> Prix au detail *</label></td>
    <td><input type="text" id="prixdetail" name="prixdetail" style="width:200px"/> </td>
</tr>
<tr>
	<td><label for="famille"> Famille * </label></td>
    <td><select name="famille" id="famille" style="width:200px;">
     <?php
	    $sql = "select id_famille, libelle from famille  where statut='Actif' order by libelle ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		 echo "<option value='".$rslt["id_famille"]."'>";
		 echo $rslt["libelle"];
		 echo '</option>';
		 }
		 ?>
    </select> </td>
    <td><label for="tauxremise"> Taux Ristourne *</label></td>
    <td><input type="text" id="tauxristourne" name="tauxristourne" style="width:200px"/> </td>
</tr>
<tr>
    <td><label for="tauxremise"> Taux Remise HT *</label></td>
    <td><input type="text" id="tauxremise" name="tauxremise" style="width:200px"/> </td>
    <td><label for="statut"> Statut * </label></td>
    <td><input type="text" id="statut" name="statut" style="width:200px; background-color:#ECECEC" readonly="readonly" value="Actif"/> </td>
</tr>
<tr>
    <td colspan="2"><input type="submit" align="left" value="Enregistrer" id="Enregistrer" name="Enregistrer"/></td>
    <td colspan="2" align="right"><input type="reset" align="right" value="Annuler" id="Annuler" name="Annuler"/></td>
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
				alert('Vous n\'etes pas habiliter a acceder a cette page.');
				history.back();
				</script>
<?php
}
?>
