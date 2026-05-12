<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" || $_SESSION['habilitation']=="Caissier"))
{
	include("Connexion.php");
	include("fonctions.php");
?>
<!DOCTYPE html PUBLIC>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire  d'enregistrement d'une vente.</title>
<style type="text/css">
label
{
	display:block;
	width:130px;
	float: left;
	}
</style>
<script src="JS/Enreg_Vers.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen" />
<script type="text/javascript" src="dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>

</head>
 
<body>

<form action="CTRL/Controle_Vers.php" method="post" onSubmit="return verif_form()" >
<fieldset style=" width:750px; margin:auto"><legend>Informations sur le versement</legend>
<table >

<tr>
<?php if ($_SESSION['habilitation']=="Administrateur")
{
?>
    <td><label for="date"> Date  * </label> </td>
    <td><input type="text" id="date" name="date" style="width:90px;" value="<?php echo date("d/m/Y");?>"/> <input type="button" value="Calendrier" onClick="displayCalendar(document.forms[0].date,'dd/mm/yyyy',this)" /></td>
 <?php 
}
else
{
?>
    <td><label for="date"> Date * </label> </td>
    <td><input type="text" id="date" name="date" style="width:150px; ; background-color:#ECECEC" value="<?php echo date("d/m/Y");?>" readonly="readonly"/></td>
<?php 
}
?>
	<td><label for="vendeur"> Vendeur * </label></td>
    <td colspan="3"> <select name="vendeur" id="vendeur" style="width:280px;">
         <?php
		$sql4 = " select login ,nom from user  where statut='Actif' order by nom  ";
		$reponse4= $DataBase->query($sql4);
		while($rslt4= $reponse4->fetch())
		{
			 echo "<option value='".$rslt4["login"]."'>";
			 echo $rslt4["nom"];
			 echo '</option>';
		 }
		 ?>
    </select> </td>
</tr>
<tr>
    <td><label for="Montant"> Montant * </label></td>
    <td><input type="text" id="Montant" name="Montant" style="width:175px"></td>
	<td><label for="observationsortie"> Observation  </label></td>
    <td><input type="text" id="observation" name="observation" maxlength="35" style="width:280px;"/></td>
</tr>
<tr>

</tr>
</table>
</fieldset>
<fieldset style=" width:750px; margin:auto"><legend>Informations sur les emballages</legend>
<table>
<tr>
	<td><label for="codeemb"> Emballage * </label></td>
    <td><select name="codeemb" id="codeemb" style="width:315px;">
     <?php
	    $sql = " select id_emballage,libelle from emballage  where statut='Actif' order by libelle  ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		 echo "<option value='".$rslt["id_emballage"]."'>";
		 echo $rslt["libelle"];
		 echo '</option>';
		 }
		 ?>
    </select> </td>
	<td><label for="qte"> Quantite  1* </label></td>
    <td><input type="text" id="qte" name="qte" style="width:150px;"/></td>
</tr>
<tr>
<tr>

	<td><label for="codeemb2"> Emballage 2 </label></td>
    <td><select name="codeemb2" id="codeemb2" style="width:315px;">
     <?php
	    $sql = " select id_emballage,libelle from emballage  where statut='Actif' order by libelle  ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		 echo "<option value='".$rslt["id_emballage"]."'>";
		 echo $rslt["libelle"];
		 echo '</option>';
		 }
		 ?>
    </select> </td>
	<td><label for="qte2"> Quantité 2 </label></td>
    <td><input type="text" id="qte2" name="qte2" style="width:150px;"/></td>
</tr>
<tr>
	<td><label for="codeemb3"> Emballage 3 </label></td>
    <td><select name="codeemb3" id="codeemb3" style="width:315px;">
     <?php
	    $sql = " select id_emballage,libelle from emballage  where statut='Actif' order by libelle  ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		 echo "<option value='".$rslt["id_emballage"]."'>";
		 echo $rslt["libelle"];
		 echo '</option>';
		 }
		 ?>
    </select> </td>
	<td><label for="qte3"> Quantité 3 </label></td>
    <td><input type="text" id="qte3" name="qte3" style="width:150px;"/></td>
</tr>
    <td colspan="2"><input type="submit" align="left" value="Enregistrer" id="Enregistrer" name="Enregistrer"/></td>
    <td colspan="4" align="right"><input type="reset" align="right" value="Retour" id="Retour" name="Retour" onClick="history.back()"/></td>
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
