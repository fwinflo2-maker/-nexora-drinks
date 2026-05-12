<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"  || $_SESSION['habilitation']=="OPS" ))
{
	include("Connexion.php");
	include("fonctions.php");
	
			//ON RECUPERE l'OBSERVATION ET LEPRIX DE VENTE DE L'ARTICLE 
		$sql = " select prixvente, observation from articlevendu where id_sortiestock='".$_GET['Vte']."' and id_article='".$_GET['AR']."'";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			$PV=$rslt['prixvente'];
			$OB=$rslt['observation'];
		 }
	    $sql='SELECT  ID_ARTICLE, LIBELLE FROM ARTICLE WHERE ID_ARTICLE="'.$_GET['AR'].'" ' ;
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
<title>Formulaire  de modification des articles d'une vente.</title>
<style type="text/css">
label
{
	display:block;
	width:100px;
	float: left;
	}
</style>
<script src="JS/Ajout_Article_Vente.js" type="text/javascript"></script>
</head>
 
<body>

<form action="CTRL/Ctrl_Mod_Article_Vente.php" method="post" onsubmit="return verif_form()" >
<fieldset style=" width:1050px;"><legend>Modifier un article d'une vente</legend>
<table>
<tr>
	<td><label for="codevente">Vente *</label> </td>
    <td><input type="text" id="codevente" name="codevente" value="<?php echo $_GET['Vte']?>" readonly="readonly" style="background:#ECECEC; width:200px;"/></td>
    <td><input type="text" id="codeclient" name="codeclient" value="<?php echo $_GET['Clt']?>" readonly="readonly" style="background:#ECECEC; width:100px; visibility:hidden"/></td>
    <td><input type="text" id="codeart" name="codeart" value="<?php echo $_GET['AR']?>" readonly="readonly" style="background:#ECECEC; width:100px; visibility:hidden"/></td>
</tr>
<tr>
    <td><label for="article"> Article * </label></td>
    <td><input type="text" id="article" name="article" value="<?php echo $art; ?>" readonly="readonly" style="background:#ECECEC; width:200px;"/></td>
    <td><label for="qtevendu"> Quantite * </label></td>
    <td><input type="text" id="qtevendu" name="qtevendu" style="width:200px;" value="<?php echo $_GET['Qte']?>"/></td>
    <td><label for="observationvente"> Observation  </label></td>
    <td><input type="text" id="observationvente" name="observationvente" style="width:250px;" maxlength="35" value="<?php echo $OB; ?>"/></td>
</tr>
<tr>
</tr>

<tr>
    <td colspan="2"><input type="submit" align="left" value="Modifier" id="Modifier" name="Modifier"/></td>
    <td colspan="4" align="right"><input type="reset" align="right" value="Retour" id="Retour" name="Retour" onclick="history.back()"/></td>
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
