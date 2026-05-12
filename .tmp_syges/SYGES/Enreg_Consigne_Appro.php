<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"|| $_SESSION['habilitation']=="Caissier" || $_SESSION['habilitation']=="Magasinier"))
{
	include("Connexion.php");
	include("fonctions.php");
	$sql = " select *  from fournisseur where id_fournisseur='".$_GET['Fs']."'";
	$reponse= $DataBase->query($sql);
	$rslt= $reponse->fetch();
	$sql2 = " select *  from approvisionnement  where id_appro='".$_GET['Ap']."'";
	$reponse2= $DataBase->query($sql2);
	$rslt2= $reponse2->fetch();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire d'enreg. d'une consigne vente.</title>
<style type="text/css">
label
{
	display:block;
	width:110px;
	float: left;
	}
</style>
<script src="JS/Enreg_Consigne.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen" />
<script type="text/javascript" src="dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
</head>
 
<body>

<form action="CTRL/Controle_Consigne_Appro.php" method="post" onsubmit="return verif_form()" >
<fieldset style="width: 1050px;"> <legend>Informations sur l'approvisionnement </legend>
<table>
<tr>
    <td><label for="nomF"> Fournisseur  </label></td>
    <td><input type="text" id="nomF" name="nomF"  value="<?php echo $rslt['NOM']; ?> / <?php echo $rslt['ID_FOURNISSEUR']; ?>" readonly="readonly" style="width:200px; background:#ECECEC;"/></td>
	<td><label for="numtel"> N° Tel  </label></td>
    <td><input type="text" id="numtel" name="numtel"  value="<?php echo $rslt['NUMTEL']; ?>" readonly="readonly" style="width:200px; background:#ECECEC;" /></td>
</tr>
<tr>
	<td><label for="codeappro"> Code * </label></td>
    <td><input type="text" id="codeappro" name="codeappro" readonly="readonly" style="width:200px; background-color:#ECECEC" value="<?php echo $rslt2['ID_APPRO']; ?>"/></td>
    <td><label for="date_appro"> Date Appro * </label> </td>
    <td><input type="text" id="date_appro" name="date_appro" style="width:200px; background:#ECECEC;" value="<?php echo dateFormatFrancais($rslt2['DATE_APPRO']); ?>"/>
	<td><label for="observation"> Observation  </label></td>
    <td><input type="text" id="observation" name="observation" maxlength="35" style="width:200px; background:#ECECEC;" value="<?php echo $rslt2['OBSERVATION']; ?>" readonly="readonly"/></td>
</tr>
</table>
</fieldset>

<table>
<tr>
	<td align="center" > <a href="index.php?formulaire=Ajout_Consigne_Appro&Ap=<?php echo $rslt2['ID_APPRO'];?>&DateA=<?php echo $rslt2['DATE_APPRO'];?> "><input type="button" name="ajoutcons" id="ajoutcons" value="Ajouter des consignes à l'appro" style="margin-left:750px; width:210px"/> </a></td>
</tr>
</table>

<table id='tconsigne' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="8"><h5>Liste des Consignes de l'Approvisionnement</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	<td align="center"><h5>Code </h5></td>
                <td  align="center"><h5>Libelle </h5></td>
                <td  align="center" ><h5>Quantite </h5></td>
                <td  align="center" ><h5>PU Cons.</h5></td>
                <td  align="center" ><h5>Montant Cons.</h5></td>
                <td  align="center" ><h5>Statut</h5></td>
                <td  align="center" ><h5>Deconsigner</h5></td>
                <td  align="center" ><h5>Supprimer</h5></td>
			</tr>
			
<?php
$color = "darkgray";
$i = 0;
$MTC=0;			
$sql='SELECT  E.ID_EMBALLAGE, E.LIBELLE, E.MT_CONSIGNE, C.MONTANT, C.ID_CONSIGNE, E.ID_EMBALLAGE ,C.ID_APPRO ,C.QTE, C.STATUT FROM EMBALLAGE E, CONSIGNEAPP C WHERE C.ID_EMBALLAGE =E.ID_EMBALLAGE AND C.ID_APPRO="'.$_GET['Ap'].'" ORDER BY C.ID_EMBALLAGE ' ;
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
                        <td  align="center"> <?php echo $rslt3['MT_CONSIGNE'].' FCFA'; ?> </td>
                        <td  align="center"> <?php echo $rslt3['MONTANT'].' FCFA'; ?> </td>
                        <td  align="center"> <?php echo $rslt3['STATUT']; ?> </td>
                        <td  align="center"> <a href="index.php?formulaire=Enreg_Deconsigne_App&Id=<?php echo $rslt3['ID_CONSIGNE']; ?>"/> <img src="IMG/b_edit.png"/> </a></td>
                        <td  align="center"> <a href="index.php?formulaire=Supp_Consigne_Appro&Id=<?php echo $rslt3['ID_CONSIGNE']; ?>"/> <img src="IMG/Supp.png"/> </a></td>
                     </tr>
                <?php
				$i++;
				$MTC=$MTC+$rslt3['MONTANT'];
		 }
				?>
 <tr bgcolor="#E0E0E0"> 
    <td align="center" colspan="8"><h4>Montant Total Consigne :  <?php echo number_format($MTC, 0, ',', ' ').' Franc CFA'; ?></h4></td>
</tr>
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
