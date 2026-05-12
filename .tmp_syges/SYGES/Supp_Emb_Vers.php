<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" || $_SESSION['habilitation']=="Caissier"))
{
	include("Connexion.php");
	include("fonctions.php");
	
		$emb="";
	    $sql='SELECT   LIBELLE FROM EMBALLAGE WHERE ID_EMBALLAGE="'.$_GET['EMB'].'" ' ;
		$reponse= $DataBase->query($sql);
		while($rslt1= $reponse->fetch())
		{
			$emb = $rslt1['LIBELLE']; 
		}
?>
<!DOCTYPE html PUBLIC>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire  de modification de l'emballage d'un versement.</title>
<style type="text/css">
label
{
	display:block;
	width:100px;
	float: left;
	}
</style>
<script src="JS/Ajout_Emb_Vers.js" type="text/javascript"></script>
</head>
 
<body>

<form action="CTRL/Ctrl_Supp_Emb_Vers.php" method="post" onSubmit="return verif_form()" >
<fieldset style=" width:1050px;"><legend>Modifier un emballage d'un versement</legend>
<table>
<tr>
	<td><label for="num_vers">N° Versement *</label> </td>
    <td><input type="text" id="num_vers" name="num_vers" value="<?php echo $_GET['Vers']?>" readonly="readonly" style="background:#ECECEC; width:200px;"/></td>
    <td><label for="emballage"> Emballage * </label></td>
    <td><input type="text" id="emballage" name="emballage" value="<?php echo $emb; ?>" readonly="readonly" style="background:#ECECEC; width:200px;"/></td>
    <td><label for="qte"> Quantite * </label></td>
    <td><input type="text" id="qte" name="qte" style="background:#ECECEC; width:200px;" readonly="readonly" value="<?php echo $_GET['QTE']; ?>"/></td>
</tr>
<tr>
</tr>

<tr>
    <td colspan="2"><input type="submit" align="left" value="Supprimer" id="Modifier" name="Supprimer"/></td>
    <td><input type="text" id="EMB" name="EMB" value="<?php echo $_GET['EMB']?>" readonly="readonly" style="background:#ECECEC; width:100px; visibility:hidden"/></td>
    <td><input type="text" id="vendeur" name="vendeur" value="<?php echo $_GET['VD']?>" readonly="readonly" style="background:#ECECEC; width:100px; visibility:hidden"/></td>
    <td colspan="2" align="right"><input type="reset" align="right" value="Retour" id="Retour" name="Retour" onClick="history.back()"/></td>
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
