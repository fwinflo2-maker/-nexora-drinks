<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" || $_SESSION['habilitation']=="Caissier"))
{
	include("Connexion.php");
	include("fonctions.php");
	
			//ON RECUPERE 
		$sql = " select id_sortiestock, id_emballage, montant, qte from rtrembvte where id_rtremb='".$_GET['Id']."'";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			$QTE=$rslt['qte'];
			$Vte=$rslt['id_sortiestock'];
			$EM=$rslt['id_emballage'];
		 }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire  de suppression d'une deconsignation emballage</title>
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

<form action="CTRL/Ctrl_Supp_Emb_Rtr_Vte.php" method="post">
<fieldset style=" width:750px; margin-left:150px;"><legend>Supprimer une deconsignation</legend>
<table>
<tr>
	<td><label for="codertr">Deconsignation *</label> </td>
    <td><input type="text" id="codertr" name="codertr" value="<?php echo $_GET['Id'];?>" readonly="readonly" style="background:#ECECEC; width:100px;"/></td>
    <td><label for="codevente">Vente *</label> </td>
    <td><input type="text" id="codevente" name="codevente" value="<?php echo $Vte;?>" readonly="readonly" style="background:#ECECEC; width:250px;"/></td>
</tr>
<tr>
     <?php
	   $sql='SELECT  ID_EMBALLAGE, LIBELLE FROM EMBALLAGE WHERE ID_EMBALLAGE="'.$EM.'" ' ;
		$reponse= $DataBase->query($sql);
		while($rslt1= $reponse->fetch())
		{
			$emb = $rslt1['LIBELLE']; 
		}
		 ?>
    <td><label for="emb"> Emballage * </label></td>
    <td><input type="text" id="emb" name="emb" value="<?php echo $emb; ?>" readonly="readonly" style="background:#ECECEC; width:200px;"/></td>
	<td><label for="qte"> Quantite * </label></td>
    <td><input type="text" id="qte" name="qte" style="width:250px;background:#ECECEC;" value="<?php echo $QTE; ?>" readonly="readonly"/></td>
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
