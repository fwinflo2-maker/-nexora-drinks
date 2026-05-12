<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"|| $_SESSION['habilitation']=="OPS" ))
{
	include("Connexion.php");
	include("fonctions.php");
	
			//ON RECUPERE l'OBSERVATION ET LE PRIX DE VENTE DE L'ARTICLE 
		$sql = " select prixvente, observation from articlevendu_frigo where id_sortiestock='".$_GET['Vte']."' and id_article='".$_GET['AR']."'";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			$PV=$rslt['prixvente'];
			$OB=$rslt['observation'];
		 }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire  de modification des articles d'une perte.</title>
<style type="text/css">
label
{
	display:block;
	width:150px;
	float: left;
	}
</style>
<script src="JS/Ajout_Article_Vente.js" type="text/javascript"></script>
</head>
 
<body>

<form action="CTRL/Ctrl_Mod_Article_Perte.php" method="post" onsubmit="return verif_form()" >
<fieldset style=" width:750px; margin-left:150px;"><legend>Modifier des articles d'une perte</legend>
<table>
<tr>
	<td><label for="codevente">Perte *</label> </td>
    <td><input type="text" id="codevente" name="codevente" value="<?php echo $_GET['Vte']?>" readonly="readonly" style="background:#ECECEC; width:200px;"/></td>
    	<td><label for="qtevendu"> Quantite * </label></td>
    <td><input type="text" id="qtevendu" name="qtevendu" style="width:250px;" value="<?php echo $_GET['Qte']?>"/></td>
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
    <td><label for="observationvente"> Observation  </label></td>
    <td><input type="text" id="observationvente" name="observationvente" style="width:250px;" maxlength="35" value="<?php echo $OB; ?>"/></td>
</tr>
<tr>
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
