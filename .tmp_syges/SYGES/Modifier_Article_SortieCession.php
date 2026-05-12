<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
{
	include("Connexion.php");
	include("fonctions.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire  de modification des articles d'une sortie cession.</title>
<style type="text/css">
label
{
	display:block;
	width:150px;
	float: left;
	}
</style>
<script src="JS/Ajout_Article_SortieCession.js" type="text/javascript"></script>
</head>
 
<body>

<form action="CTRL/Ctrl_Mod_Article_SortieCession.php" method="post" onsubmit="return verif_form()" >
<fieldset style=" width:750px; margin-left:150px;"><legend>Modifier des articles à une sortie Cession</legend>
<table>
<tr>
	<td><label for="codecession">Cession *</label> </td>
    <td><input type="text" id="codecession" name="codecession" value="<?php echo $_GET['SC']?>" readonly="readonly" style="background:#ECECEC; width:200px;"/></td>
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
    <td><input type="text" id="codeart" name="codeart" value="<?php echo $art ; ?>" readonly="readonly" style="background:#ECECEC; width:200px;"/></td>
	<td><label for="qtesortie"> Qte Sortie * </label></td>
    <td><input type="text" id="qtesortie" name="qtesortie" style="width:200px;" value="<?php echo $_GET['QTE']?>"/></td>
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
