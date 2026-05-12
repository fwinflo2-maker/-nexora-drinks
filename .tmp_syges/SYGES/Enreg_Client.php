<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" || $_SESSION['habilitation']=="Caissier"))
{
	include('fonctions.php');
	include('Connexion.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire d'enregistrement d'un Client.</title>
<style type="text/css">
label
{
	display:block;
	width:150px;
	float: left;
}
</style>
<script src="JS/Enreg_Client.js" type="text/javascript"></script>
</head>
<body>
<form action="CTRL/Controle_Client.php" method="post" onsubmit="return verif_form()">
<fieldset style="width:750px; margin-left:15%; border-color:#FFFBF0" ><h4><legend>Informations Client</legend></h4>
<table>
<tr>
	<td><label for="code"> Code * </label></td>
    <td><input type="text" id="code" name="code" style="width:200px; background-color:#ECECEC" value="<?php echo generer_code_client();?>" readonly="readonly"/> </td>
</tr>
<tr>
	<td><label for="nom"> Nom * </label></td>
	<td><input type="text" id="nom" name="nom" style="width:200px"/> </td>
    <td><label for="numtel"> N° Tel  </label></td> 
    <td><input type="text" id="numtel" name="numtel" style="width:200px"/></td>
</tr>
<tr>
    <td><label for="email"> E-mail  </label> </td>
    <td><input type="text" name="email" id="email" style="width:200px"/> </td>
	<td><label for="categorie"> Categorie * </label></td>
    <td><select name="categorie" id="categorie" style="width:200px;">
     <?php
	    $sql = " select id_categorie, libelle from categorie  where statut='Actif' order by libelle  ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		 echo "<option value='".$rslt["id_categorie"]."'>";
		 echo $rslt["libelle"];
		 echo '</option>';
		 }
		 ?>
    </select> </td>
</tr>
</table>
</fieldset>
<fieldset style="width:750px; margin-left:15%; border-color:#FFFBF0" ><h4><legend>Numero Indenfiant Unique et Registre de Commerce</legend></h4>
<table>
<tr>
	<td><label for="niu"> N.I.U  </label></td>
	<td><input type="text" id="niu" name="niu" style="width:200px" value="000000000000000"/> </td>
    <td><label for="rc">  R.C </label></td> 
    <td><input type="text" id="rc" name="rc" style="width:200px" value="00000000000000000"/></td>
</tr>
</table>
</fieldset>
<fieldset style="width:750px; margin-left:15%; border-color:#FFFBF0" ><h4><legend>Enregistrement des Frais d'enlevement </legend></h4>
<table>
<tr>
    <td><label for="fraisenlevement">  Frais Enlevement * </label></td>
    <td><input type="text" id="fraisenlevement" name="fraisenlevement" style="width:150px;" value="0"/> </td>
    <td><label for="fraisenlevement_pet">   PET * </label></td>
    <td><input type="text" class="form-control" id="fraisenlevement_pet" name="fraisenlevement_pet" style="width:150px;" value="0"/> </td>
</tr>
</table>
</fieldset>
<fieldset style="width:750px; margin-left:15%; border-color:#FFFBF0" ><h4><legend>Enregistrement des Paramètres Pour le calcul des Ristournes</legend></h4>
<table>
<tr>
    <td><label for="tauxristourneht"> Taux Risournes HT(%)* </label></td>
    <td><input type="text" id="tauxristourneht" name="tauxristourneht" style="width:200px;" value="0" /> </td>
	<td><label for="psaristournes"> PSA Ristournes(%)* </label></td>
    <td><input type="text" id="psaristournes" name="psaristournes" style="width:200px;" value="0" /> </td>
</tr>

<tr>
    <td colspan="2"><input type="submit" align="left" value="Enregistrer" id="Enregistrer" name="Enregistrer"/></td>
    <td><input type="reset" value="Annuler" id="Annuler" name="Annuler"/></td>
    <td align="right"><input type="button" value="Retour" id="Retour" name="Retour" onclick="history.back()"/></td>
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
				alert('Vous n\'etes pas habiliter a acceder a cette page.');
				history.back();
				</script>
<?php
}
?>
