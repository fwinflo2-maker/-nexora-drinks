<?php

if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur")||($_SESSION['habilitation']=="Gerant")||($_SESSION['habilitation']=="Caissier"))
{
	include("Connexion.php");
	include("fonctions.php");
	if (isset ($_GET['Id']))
	{
		$sql='SELECT  * FROM CHARGE  WHERE ID_CHARGE="'.$_GET['Id'].'"' ;
 		$reponse= $DataBase->query($sql);
		$rslt= $reponse->fetch();
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire de modification d'une charge.</title>
<style type="text/css">
label
{
	display:block;
	width:150px;
	float: left;
}
</style>
<script src="JS/Enreg_Charge.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen" />
<script type="text/javascript" src="dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
<body>
<fieldset style="width:750px; margin-left:150px; border-color:#FFFBF0" ><legend>Modification d'une charge.</legend>
<table>
<form action="CTRL/Ctrl_Mod_Charge.php" method="post" onsubmit="return verif_form()" >
<tr>
	<td><label for="Code"> Code *</label></td>
	<td><input type="text" id="Code" name="Code" style="width:200px; background-color:#ECECEC;" readonly="readonly" value="<?php echo $rslt["ID_CHARGE"];?>"/> </td>
</tr>
<tr>
    <td><label for="Typecharge"> Type Charge *</label></td>
    <td><select name="Typecharge" id="Typecharge" style="width:200px;">
      <?php
	    $sql2 = " select id_typecharge,libelle from type_charge where statut='Actif' order by libelle ";
		$reponse2= $DataBase->query($sql2);
		while($rslt2= $reponse2->fetch())
		{
		if($rslt["ID_TYPECHARGE"]==$rslt2["id_typecharge"])
		{	
			 echo "<option selected value='".$rslt2["id_typecharge"]."'>";
		}
		else
		{
			 echo "<option value='".$rslt2["id_typecharge"]."'>";
		}
			 echo $rslt2["libelle"];
			 echo '</option>';
		
		 }
		 ?>
    </select> </td>
    <td><label for="Description"> Description *</label></td> 
    <td><input type="text" id="Description" name="Description" style="width:200px" maxlength="20" value="<?php echo $rslt["DESCRIPTION"];?>"/>   </td>
</tr>
<tr>
    <td><label for="Montant"> Montant *</label></td>
    <td><input type="text" id="Montant" name="Montant" style="width:200px;" value="<?php echo $rslt["MONTANT"];?>"></td>
    <td><label for="Date"> Date d'enregistrement * </label> </td>
    <td><input type="text" id="Date" name="Date"style="width:110px;" value="<?php echo dateFormatFrancais($rslt["DATE_CHARGE"]);?>"/> <input type="button" value="Calendrier" onclick="displayCalendar(document.forms[0].Date,'dd/mm/yyyy',this)" /></td>
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
