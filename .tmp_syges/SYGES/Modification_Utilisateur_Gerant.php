<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Gerant"))
{

	include('connexion.php');
	if (isset ($_GET['Login']))
	{
		$sql='SELECT  * FROM USER  WHERE LOGIN="'.$_GET['Login'].'"' ;
 		$reponse= $DataBase->query($sql);
		$rslt= $reponse->fetch();
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire de Modification d'un Utilisateur.</title>
<style type="text/css">
label
{
	display:block;
	width:150px;
	float: left;
}
</style>
<script src="JS/Enreg_utilisateur.js" type="text/javascript"></script>
<body>
<fieldset style="width:750px; margin-left:150px; border-color:#FFFBF0" ><legend>Modification d'un Utilisateur</legend>
<table>
<form action="CTRL/Ctrl_Mod_Utilisateur_Gerant.php" method="post" onsubmit="return verif_form()" >
<tr>
    <td><label for="Login"> Login </label></td>
    <td><input type="text" id="Login" name="Login" style="width:200px; background-color:#ECECEC;" readonly="readonly" value="<?php echo $rslt['LOGIN']?>" /> </td>
</tr>
<tr>
	<td><label for="nom"> Nom *</label></td>
	<td><input type="text" id="nom" name="nom" style="width:200px" value="<?php echo $rslt['NOM']?>"/> </td>
    <td><label for="prenom"> Prenom </label></td> 
    <td><input type="text" id="prenom" name="prenom" style="width:200px" value="<?php echo $rslt['PRENOM']?>"/></td>
</tr>
<tr>
    <td><label for="MDP"> PassWord *</label></td>
    <td><input type="password" id="MDP" name="MDP" style="width:200px" value="<?php echo $rslt['MDP']?>"/> </td>
    <td><label for="ConMDP"> Confirmation PassWord *</label></td>
    <td><input type="password" id="ConMDP" name="ConMDP" style="width:200px;" value="<?php echo $rslt['MDP']?>"></td>
</tr>
<tr>
    <td><label for="Habilitation"> Habilitation *</label></td>
	<td><input type="text" id="Habilitation" name="Habilitation" style="width:200px;" value="<?php echo $rslt['HABILITATION']?>" readonly="readonly"></td>
	<td><label for="statut"> Statut *</label></td>
	<td><select name="statut" id="statut" style="width:200px;">
            <option <?php if ($rslt["STATUT"]=='Actif') echo 'selected';?> > Actif</option>
            <option <?php if ($rslt["STATUT"]=='Bloqué') echo 'selected';?> > Bloqué</option>
        </select> </td>
</tr>
<tr>
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
