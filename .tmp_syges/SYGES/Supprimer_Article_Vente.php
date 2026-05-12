<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" || $_SESSION['habilitation']=="Comptable" || $_SESSION['habilitation']=="OPS" ))
{
	include("Connexion.php");
	include("fonctions.php");
	
			//ON RECUPERE l'OBSERVATION ET LEPRIX DE VENTE DE L'ARTICLE 
		$sql = " select prixvente, observation, qtesortie from articlevendu where id_sortiestock='".$_GET['Vte']."' and id_article='".$_GET['AR']."'";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			$PV=$rslt['prixvente'];
			$OB=$rslt['observation'];
			$qtesortie=$rslt['qtesortie'];
		 }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire  de suppression des articles d'une vente.</title>
<style type="text/css">
label
{
	display:block;
	width:150px;
	float: left;
	}
</style>
</head>
 
<body>

<form action="CTRL/Ctrl_Supp_Article_Vente.php" method="post">
<fieldset style=" width:750px; margin-left:150px;"><legend>Supprimer un article à une vente</legend>
<table>
<tr>
	<td><label for="codevente">Vente *</label> </td>
    <td><input type="text" id="codevente" name="codevente" value="<?php echo $_GET['Vte']?>" readonly="readonly" style="background:#ECECEC; width:200px;"/></td>
</tr>
<tr>
     <?php
	   $sql='SELECT  ID_ARTICLE, LIBELLE FROM ARTICLE WHERE ID_ARTICLE="'.$_GET['AR'].'" ' ;
		$reponse= $DataBase->query($sql);
		while($rslt1= $reponse->fetch())
		{
			$art = $rslt1['LIBELLE']; 
		}
		 ?>
    <td><label for="codeart"> Article * </label></td>
    <td><input type="text" id="codeart" name="codeart" value="<?php echo $art; ?>" readonly="readonly" style="background:#ECECEC; width:200px;"/></td>
	<td><label for="qtevendu"> Quantite * </label></td>
    <td><input type="text" id="qtevendu" name="qtevendu" style="width:250px;background:#ECECEC;" value="<?php echo $qtesortie?>" readonly="readonly"/></td>
</tr>
<tr>
	<td><label for="prixvente"> Prix de Vente ToTal * </label></td>
    <td><input type="text" id="prixvente" name="prixvente" style="width:200px;background:#ECECEC;" value="<?php echo $PV; ?>" readonly="readonly"/></td>
    <td><label for="observationvente"> Observation  </label></td>
    <td><input type="text" id="observationvente" name="observationvente" style="width:250px;background:#ECECEC;" maxlength="50" value="<?php echo $OB; ?>" readonly="readonly"/></td>
</tr>

<tr>
    <td colspan="2"><input type="submit" align="left" value="Supprimer" id="Supprimer" name="Supprimer"/></td>
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
