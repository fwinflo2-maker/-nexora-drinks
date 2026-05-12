<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" || $_SESSION['habilitation']=="Caissier"))
{
	include("Connexion.php");
	include("fonctions.php");
	$sql2 = " select *  from versement  where num_vers='".$_GET['Vers']."'";
	$reponse2= $DataBase->query($sql2);
	$rslt2= $reponse2->fetch();
	

?>
<!DOCTYPE html >
<html >
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire  de modification d'un versement.</title>
<style type="text/css">
label
{
	display:block;
	width:110px;
	float: left;
	}
</style>
<script src="JS/Enreg_Vers.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen" />
<script type="text/javascript" src="dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
</head>
 
<body>

<form action="CTRL/Ctrl_Mod_Vers.php" method="post" onSubmit="return verif_form()" >
<fieldset style=" width:1050px;"><legend>Informations sur le versement à modifier</legend>
<table >
<tr>
	<td><label for="num_vers"> N° Versement * </label></td>
    <td><input type="text" id="num_vers" name="num_vers" readonly="readonly" style="width:200px; background-color:#ECECEC" value="<?php echo $rslt2['NUM_VERS']; ?>"/></td>

<?php if ($_SESSION['habilitation']=="Administrateur")
{
?>
    <td><label for="date"> Versement du * </label> </td>
    <td><input type="text" id="date" name="date" style="width:100px;" value="<?php echo dateFormatFrancais($rslt2['DATE_VERS']); ?>"/> <input type="button" value="Calendrier" onClick="displayCalendar(document.forms[0].date,'dd/mm/yyyy',this)" /></td>
 <?php 
}
else
{
?>
    <td><label for="date"> Date * </label> </td>
    <td><input type="text" id="date" name="date" style="width:200px ; background-color:#ECECEC" value="<?php echo dateFormatFrancais($rslt2['DATE_VERS']); ?>" readonly="readonly"/>
<?php 
}
?>

</tr>
<tr>
<td><label for="vendeur"> Vendeur * </label></td>
    <td><select name="vendeur" id="vendeur" style="width:200px;">
    
         <?php
		$sql4 = " select login ,nom from user  where statut='Actif' order by nom  ";
		$reponse4= $DataBase->query($sql4);
		while($rslt4= $reponse4->fetch())
		{
			
		if ($rslt4["login"]==$rslt2["VENDEUR"])
		{	
			 echo "<option selected value='".$rslt4["login"]."'>";
		}
		else
		{
			 echo "<option value='".$rslt4["login"]."'>";
		}
			 echo $rslt4["nom"];
			 echo '</option>';
		
		 }
		 ?>
    </select> </td>
    <td><label for="Montant">Montant * </label></td>
    <td><input type="text" id="Montant" name="Montant" style="width:190px" value="<?php echo $rslt2['MONTANT']; ?>"></td>
	<td><label for="observation"> Observation  </label></td>
    <td><input type="text" id="observation" name="observation" maxlength="35" style="width:280px;" value="<?php echo $rslt2['OBSERVATION']; ?>"/></td>
    
</tr>
<tr>
    <td colspan="6" align="right"><input type="submit" align="left" value="Modifier" id="Modifier" name="Modifier"/></td>
</tr>

</table>
</fieldset>
<table>
<tr>
		<td align="center" > <a href="index.php?formulaire=Validation_Vers&Vers=<?php echo $rslt2['NUM_VERS'];?>&VD=<?php echo $rslt2['VENDEUR'];?>"> <input type="button" name="Valider" id="Valider" value="Fin de Saisie" style="margin-left:10px; background:#F00;"/> </a></td>
    	<td align="center" > <a href="index.php?formulaire=Ajout_Emb_Vers&Vers=<?php echo $rslt2['NUM_VERS'];?>&VD=<?php echo $rslt2['VENDEUR'];?> "> <input type="button" name="ajoutemb" id="ajoutemb" value="Ajouter Emballage" style="margin-left:750px; width:210px"/> </a></td>
</tr>
</table>
<table id='tconsigne' border="0" width="100%" align="center">
          <tr align="center" >
          	<td colspan="6"><h5>Liste de(s) Emballage(s)</h5></td>
          </tr>
          <tr bgcolor="#CCCCCC">
            	<td align="center"><h5>Code </h5></td>
                <td  align="center"><h5>Libelle </h5></td>
                <td  align="center" ><h5>Quantite </h5></td>
                <td  align="center" ><h5>Modifier</h5></td>
                <td  align="center" ><h5>Supprimer</h5></td>
		</tr>
			
<?php
$color = "darkgray";
$i = 0;
$TTEMB=0;			
$sql='SELECT  E.ID_EMBALLAGE, E.LIBELLE, EV.QTE FROM EMBALLAGE E, EMBALLAGE_VERS EV WHERE E.ID_EMBALLAGE= EV.ID_EMBALLAGE AND EV.NUM_VERS="'.$_GET['Vers'].'" ' ;
	$reponse= $DataBase->query($sql);
		while($rslt3= $reponse->fetch())
		{
			if ($i%2 == 0)
					$color = '#E0E0E0';
				else
					$color = "white";
				echo "<tr bgcolor=$color>";
				?>
                        <td  align="center"> <?php echo $rslt3['ID_EMBALLAGE']; ?> </td>
                        <td  align="center"> <?php echo $rslt3['LIBELLE']; ?> </td>
                        <td  align="center"> <?php echo $rslt3['QTE']; ?> </td>
                        <td  align="center"> <a href="index.php?formulaire=Modifier_Emb_Vers&Vers=<?php echo $rslt2['NUM_VERS'];?>&EMB=<?php echo  $rslt3['ID_EMBALLAGE'];?>&QTE=<?php echo  $rslt3['QTE'];?>&VD=<?php echo $rslt2['VENDEUR'];?> "/> <img src="IMG/b_edit.png"/> </a></td>
                        <td  align="center"> <a href="index.php?formulaire=Supp_Emb_Vers&Vers=<?php echo $rslt2['NUM_VERS'];?>&EMB=<?php echo  $rslt3['ID_EMBALLAGE'];?>&QTE=<?php echo  $rslt3['QTE'];?>&VD=<?php echo $rslt2['VENDEUR'];?> "/> <img src="IMG/Supp.png"/> </a></td>
                     </tr>
                <?php
				$i++;
				$TTEMB=$TTEMB+$rslt3['QTE'];
		 }
				?>
 <tr bgcolor="#E0E0E0"> 
    <td align="center" colspan="6"><h4> Total Emballage(s) :  <?php echo number_format($TTEMB, 0, ',', ' '); ?></h4></td>
</tr>
</table>
</table>
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
