<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur")||($_SESSION['habilitation']=="Gerant")||($_SESSION['habilitation']=="Caissier" || $_SESSION['habilitation']=="Comptable"))
{

	include('connexion.php');
	if (isset ($_GET['Code']))
	{
		$sql='SELECT  * FROM TYPE_CHARGE  WHERE ID_TYPECHARGE="'.$_GET['Code'].'"' ;
 		$reponse= $DataBase->query($sql);
		$rslt= $reponse->fetch();
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire de Modification d'un type charge.</title>
<style type="text/css">
label
{
	display:block;
	width:150px;
	float: left;
}
</style>
<script src="JS/Enreg_TCharge.js" type="text/javascript"></script>
<body>
<fieldset style="width:750px; margin-left:150px; border-color:#FFFBF0" ><legend>Modification d'un type charge</legend>
<table>
<form action="CTRL/Ctrl_Mod_TCharge.php" method="post" onsubmit="return verif_form()" >
<tr>
	<td><label for="Code"> Code *</label></td>
	<td><input type="text" id="Code" name="Code" style="width:200px;background-color:#ECECEC;" readonly="readonly" value="<?php echo $rslt['ID_TYPECHARGE']?>"/> </td>
</tr>
<tr>
    <td><label for="Libelle"> Libelle *</label></td> 
    <td><input type="text" id="Libelle" name="Libelle" style="width:200px" value="<?php echo $rslt['LIBELLE']?>"/></td>
    <td><label for="Statut"> Statut *</label></td>
    <td><select name="Statut" id="Statut" style="width:200px;">
    <?php 
        echo "<option selected value='".$rslt["STATUT"]."'>";
        echo $rslt["STATUT"];
        echo '</option>';
    ?>
            <option>Actif</option>
    		<option>Archive</option>
	</select> </td>
</tr
><tr>
    <td colspan="2"><input type="submit" align="left" value="Modifier" id="Modifier" name="Modifier"/></td>
    <td colspan="2" align="right"><input type="button" align="right" value="Retour" id="Retour" name="Retour" onclick="history.back()"/></td>
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
				alert('Vous n\'etes pas habiliter a  acceder a cette page.');
				history.back();
				</script>
<?php
}
?>
