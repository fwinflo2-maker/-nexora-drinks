<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
{
	include("Connexion.php");
	include("fonctions.php");
	$sql='SELECT  LIBELLE FROM CATEGORIE WHERE ID_CATEGORIE="'.$_GET['Cat'].'" ' ;
	$reponse= $DataBase->query($sql);
	while($rslt1= $reponse->fetch())
	{
		$libcat = $rslt1['LIBELLE']; 
	}
	$sql='SELECT  ID_ARTICLE, LIBELLE FROM ARTICLE WHERE ID_ARTICLE="'.$_GET['Ar'].'" ' ;
	$reponse= $DataBase->query($sql);
	while($rslt1= $reponse->fetch())
	{
		$art = $rslt1['LIBELLE']; 
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire  de modification des prix de vente</title>
<style type="text/css">
label
{
	display:block;
	width:150px;
	float: left;
	}
</style>
<script src="JS/Ajout_Tarif.js" type="text/javascript"></script>
</head>
 
<body>

<form action="CTRL/Ctrl_Mod_Tarif.php" method="post" onsubmit="return verif_form()" >
<fieldset style=" width:750px; margin-left:150px;"><legend>Modifier un Prix de Vente</legend>
<table>
<tr>
	<td><label for="codecat">Categorie *</label> </td>
    <td><input type="text" id="codecat" name="codecat" value="<?php echo $libcat; ?>" readonly="readonly" style="background:#ECECEC; width:200px;"/></td>
    <td><input type="text" id="categorie" name="categorie" style="width:200px; visibility:hidden" value="<?php echo $_GET['Cat']?>"/></td>
    <td><input type="text" id="codeart" name="codeart" style="width:200px; visibility:hidden" value="<?php echo $_GET['Ar']?>"/></td>
</tr>
<tr>
    <td><label for="article"> Article * </label></td>
    <td><input type="text" id="article" name="article" value="<?php echo $art ; ?>" readonly="readonly" style="background:#ECECEC; width:200px;"/></td>
	<td><label for="prixvente"> Prix Vente * </label></td>
    <td><input type="text" id="prixvente" name="prixvente" style="width:200px;" value="<?php echo $_GET['Pv']?>"/></td>
</tr>
<tr>
    <td colspan="2"><input type="submit" align="left" value="Modifier" id="Modifier" name="Modifier"/></td>
    <td colspan="2" align="right"><input type="reset" align="right" value="Retour" id="Retour" name="Retour" onclick="history.back()"/></td>
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
