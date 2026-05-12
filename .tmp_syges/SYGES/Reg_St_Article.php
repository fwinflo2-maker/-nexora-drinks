<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur"))
{
 include ("connexion.php");
 include('fonctions.php');
 $sql='SELECT  * FROM ARTICLE WHERE ID_ARTICLE="'.$_GET['Id'].'"' ;
 $reponse= $DataBase->query($sql);
 $rslt= $reponse->fetch();
		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire de Regularisation du stock d'un Article.</title>
<style type="text/css">
label
{
	display:block;
	width:150px;
	float: left;
}
</style>
</style>
<script src="JS/Reg_St_Article.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen" />
	<script type="text/javascript" src="dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
</head>

<body>
<fieldset style="width:750px; margin-left:150px; border-color:#FFFBF0" ><legend><h5>Regularisation du Stock d'un article</h5></legend>
<table>
<form action="CTRL/Ctrl_Reg_St_Article.php" method="post" onsubmit="return verif_form()">
<tr>
	<td><label for="code"> Code * </label></td>
    <td><input type="text" id="code" name="code" style="width:200px; background-color:#ECECEC" value="<?php echo $rslt["ID_ARTICLE"]; ?>" readonly="readonly"/> </td>
</tr>
<tr>
    <td><label for="libelle"> Libellé * </label></td>
	<td><input type="text" id="libelle" name="libelle" style="width:200px; background-color:#ECECEC" value="<?php echo $rslt["LIBELLE"]; ?>" readonly="readonly"/> </td>
    <td><label for="marque"> Conditionnement * </label></td> 
    <td><input type="text" id="marque" name="marque" style="width:200px; background-color:#ECECEC" value="<?php echo $rslt["MARQUE"].' '.$rslt["NBREBTE"]; ?>" readonly="readonly"/></td>
</tr>
<tr>

    <td><label for="stmagav"> Stock Magasin Actuel * </label></td> 
    <td><input type="text" id="stmagav" name="stmagav" style="width:200px; background-color:#ECECEC" value="<?php echo $rslt["QTESTOCK"]; ?>" readonly="readonly"/></td>
    <td><label for="stmag"> Nouveau Stock Magasin*</label></td>
    <td><input type="text" id="stmag" name="stmag" style="width:200px"/> </td>
</tr>
<tr>
    <td><label for="stfrav"> Stock Frigo Actuel * </label></td>
    <td><input type="text" id="stfrav" name="stfrav" style="width:200px; background-color:#ECECEC" value="<?php echo $rslt["STOCKFRIGO"]; ?>" readonly="readonly"/> </td>
    <td><label for="stfrigo"> Nouveau Stock Frigo * </label></td>
    <td><input type="text" id="stfrigo" name="stfrigo" style="width:200px"/> </td>
</tr>
<tr>
    <td colspan="2"><input type="submit" align="left" value="Modifer" id="Modifer" name="Modifer"/>
    <td colspan="2" align="right"><input type="button" align="right" value="Retour" id="Retour" name="Retour" onclick=" history.back ()"/>
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
				alert('Vous n\'etes pas habiliter à acceder a cette page.');
				history.back();
				</script>
<?php
}
?>
