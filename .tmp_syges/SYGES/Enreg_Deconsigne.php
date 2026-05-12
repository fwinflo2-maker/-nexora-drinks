<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"))
{
	include("Connexion.php");
	include("fonctions.php");
	
		$sql = " select id_sortiestock, id_emballage, montant, qte from consigne where id_sortiestock='".$_GET['Vte']."' and id_emballage='".$_GET['Emb']."'";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			$QTE=$rslt['qte'];
			$mt=$rslt['montant'];
		 }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire  de deconsignation.</title>
<style type="text/css">
label
{
	display:block;
	width:130px;
	float: left;
	}
</style>
<script src="JS/Enreg_Deconsigne.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen" />
<script type="text/javascript" src="dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
</head>
 
<body>

<form action="CTRL/Ctrl_Deconsigne.php" method="post" onsubmit="return verif_form()" >
<fieldset style=" width:750px; margin-left:150px;"><legend>Déconsigner un emballage</legend>
<table>
<tr>
	<td><label for="codevente">Vente *</label> </td>
    <td><input type="text" id="codevente" name="codevente" value="<?php echo $_GET['Vte'];?>" readonly="readonly" style="background:#ECECEC; width:200px;"/></td>
        <?php
	    	$sql='SELECT  ID_EMBALLAGE, LIBELLE FROM EMBALLAGE WHERE ID_EMBALLAGE="'.$_GET['Emb'].'" ' ;
			$reponse= $DataBase->query($sql);
			while($rslt1= $reponse->fetch())
			{
				$emb = $rslt1['LIBELLE']; 
			}
		 ?>
    <td><label for="emb"> Emballage * </label></td>
    <td><input type="text" id="emb" name="emb" value="<?php echo $emb; ?>" readonly="readonly" style="background:#ECECEC; width:250px;"/></td>
</tr>
<tr>
	<td><label for="qte"> Quantite * </label></td>
    <td><input type="text" id="qte" name="qte" style="width:200px;background:#ECECEC;" value="<?php echo $QTE; ?>" readonly="readonly"/></td>
    <td><label for="qte"> Montant * </label></td>
    <td><input type="text" id="mt" name="mt" style="width:250px;background:#ECECEC;" value="<?php echo $mt.' FCFA'; ?>" readonly="readonly"/></td>
</tr>
<tr>
    <td ><label>Date deconsigne *</label></td>
    <td ><input type="text" id="Dat" name="Dat" style="width:110px;"/> 
        <input type="button" value="Calendrier" onclick="displayCalendar(document.forms[0].Dat,'dd/mm/yyyy',this)" /></td>
    <td><label for="obs"> Observation  </label></td>
    <td><input type="text" id="obs" name="obs" style="width:250px;" maxlength="32"/></td>
</tr>
<tr>
    <td colspan="2"><input type="submit" align="left" value="Deconsigner" id="Deconsigner" name="Deconsigner"/></td>
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
