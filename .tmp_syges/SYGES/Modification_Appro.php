<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" || $_SESSION['habilitation']=="Comptable" || $_SESSION['habilitation']=="Magasinier"))
{
	include("Connexion.php");
	include("fonctions.php");
	$sql2 = " select *  from approvisionnement  where id_appro='".$_GET['Ap']."'";
	$reponse2= $DataBase->query($sql2);
	$rslt2= $reponse2->fetch();
	
	$sql = " select *  from fournisseur  where id_fournisseur='".$rslt2['ID_FOURNISSEUR']."'";
	$reponse= $DataBase->query($sql);
	$rslt= $reponse->fetch();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire  de modification d'un Appro.</title>
<style type="text/css">
label
{
	display:block;
	width:110px;
	float: left;
	}
</style>
<script src="JS/Enreg_Appro.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen" />
<script type="text/javascript" src="dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
</head>
 
<body>

<form action="CTRL/Ctrl_Mod_Appro.php" method="post" onsubmit="return verif_form()" >
<fieldset style=" width:1050px;"><legend>Informations sur l'approvisionnement à modifier</legend>
<table >
<tr>
	<td><label for="codeappro"> Code * </label></td>
    <td><input type="text" id="codeappro" name="codeappro" readonly="readonly" style="width:200px; background-color:#ECECEC" value="<?php echo $rslt2['ID_APPRO']; ?>"/></td>
	<td><label for="codefournisseur"> Fournisseur * </label></td>
    <td><select name="codefournisseur" id="codefournisseur" style="width:200px;">
         <?php
		$sql4 = " select id_fournisseur ,nom from fournisseur  where statut='Actif' order by nom  ";
		$reponse4= $DataBase->query($sql4);
		while($rslt4= $reponse4->fetch())
		{
		if ($rslt4["id_fournisseur"]==$rslt["ID_FOURNISSEUR"])
		{	
			 echo "<option selected value='".$rslt4["id_fournisseur"]."'>";
		}
		else
		{
			 echo "<option value='".$rslt4["id_fournisseur"]."'>";
		}
			 echo $rslt4["nom"];
			 echo '</option>';
		
		 }
		 ?>
    </select> </td>
    <td><label for="nbrecolis"> Nbre de Colis *</label></td>
    <td><input type="text" id="nbrecolis" name="nbrecolis" style="width:185px; " value="<?php echo $rslt2['NBRECOLIS']; ?>"/></td>
</tr>
<tr>
<td><label for="liquideht"> MT Liquide HT * </label></td>
    <td><input type="text" id="liquideht" name="liquideht"  style="width:200px;" value="<?php echo $rslt2['LIQUIDEHT']; ?>"/></td>
    <td><label for="date_appro"> Date de l'appro * </label> </td>
    <td><input type="text" id="date_appro" name="date_appro"style="width:100px;" value="<?php echo dateFormatFrancais($rslt2['DATE_APPRO']); ?>"/> <input type="button" value="Calendrier" onclick="displayCalendar(document.forms[0].date_appro,'dd/mm/yyyy',this)" /></td>
	<td><label for="observationappro"> Observation  </label></td>
    <td><input type="text" id="observationappro" name="observationappro" maxlength="40" style="width:185px;" value="<?php echo $rslt2['OBSERVATION']; ?>"/></td>

    <td colspan="6" align="right"><input type="submit" align="left" value="Modifier" id="Modifier" name="Modifier"/></td>
</tr>

</table>
</fieldset>
<table>
<tr>
	<td align="center" ><a href="index.php?formulaire=Validation_Appro&Ap=<?php echo $rslt2['ID_APPRO'];?>&Fs=<?php echo $rslt['ID_FOURNISSEUR'];?> "><input type="button" name="Valider" id="Valider" value="Fin de Saisie" style="margin-left:10px; background:#F00;"/> </a></td>
    <td align="center" > <a href="index.php?formulaire=Ajout_Consigne_App&Ap=<?php echo $rslt2['ID_APPRO'];?>&DateV=<?php echo $rslt2['DATE_APPRO'];?> "> <input type="button" name="ajout" id="ajout" value="Ajouter des consignes" style="margin-left:100px; width:210px"/> </a></td>
    <td align="center" > <a href="index.php?formulaire=Rtr_Emb_Fssr&Ap=<?php echo $rslt2['ID_APPRO'];?>&DateV=<?php echo $rslt2['DATE_APPRO'];?> "> <input type="button" name="Rtr" id="Rtr" value="Ajouter des Deconsignations" style="margin-left:50px; width:210px"/> </a></td>
	<td align="center" > <a href="index.php?formulaire=Ajout_Article_Appro&AP=<?php echo $rslt2['ID_APPRO'];?> "> <input type="button" name="ajoutart" id="ajoutart" value="Ajouter des articles " style="margin-left:100px;"/> </a></td>
</tr>
</table>
<table id='tarticle' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="8"><h5>Liste des Articles de l'approvisionnement</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	
            	<td align="center" width="20%"><h5>Code </h5></td>
                <td  align="center"><h5>Conditionnement </h5></td>
                <td  align="center" ><h5>Libelle </h5></td>
                <td  align="center" ><h5>Qte reçu </h5></td>
                <td  align="center" ><h5>Modifier</h5> </td>
                <td  align="center" ><h5>Supprimer</h5></td>
			</tr>
			
<?php
$couleur = "darkgray";
$i = 0;
$nbrear=0;
$sql='SELECT  ARTICLE.ID_ARTICLE, ARTICLE.LIBELLE, ARTICLE.MARQUE,ARTICLE.QTESTOCK, ARTICLE_RECU.ID_ARTICLE,ARTICLE_RECU.QTERECU, ARTICLE_RECU.ID_APPRO FROM ARTICLE, ARTICLE_RECU WHERE ARTICLE.ID_ARTICLE=ARTICLE_RECU.ID_ARTICLE AND ARTICLE_RECU.ID_APPRO="'.$_GET['Ap'].'" ORDER BY ARTICLE_RECU.ID_ARTICLE ' ;
	$reponse= $DataBase->query($sql);
		while($rslt3= $reponse->fetch())
		{
			if ($i%2 == 0)
					$couleur = '#E0E0E0';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                        <td  align="center"> <?php echo $rslt3['ID_ARTICLE']; ?> </td>
                        <td  align="center"> <?php echo $rslt3['MARQUE']; ?> </td>
                        <td  align="center"> <?php echo $rslt3['LIBELLE'].'(Stock :'.$rslt3['QTESTOCK'].')'; ?> </td>
                        <td  align="center"> <?php echo $rslt3['QTERECU']; ?> </td>
                        <td  align="center"> <a href="index.php?formulaire=Modifier_Article_Appro&AR=<?php echo $rslt3['ID_ARTICLE'];?>&AP=<?php echo $rslt3['ID_APPRO']; ?>&QTE=<?php echo $rslt3['QTERECU'];?> "/> <img src="IMG/b_edit.png"/> </a></td>
                        <td  align="center"> <a href="index.php?formulaire=Supprimer_Article_Appro&AR=<?php echo $rslt3['ID_ARTICLE'];?>&AP=<?php echo $rslt3['ID_APPRO']; ?>&QTE=<?php echo $rslt3['QTERECU'];?> "/> <img src="IMG/Supp.png"/> </a></td>
                     </tr>
                <?php
				$i++;
				$nbrear=$nbrear+$rslt3['QTERECU'];
		 }
				?>
<tr bgcolor="#E0E0E0"> 
    <td align="center" colspan="8"><h4>Total Colis :  <?php echo $nbrear; ?> </h4></td>
</tr>
</table>
<table id='tconsigne' border="0" width="100%" align="center">
          <tr align="center" >
          	<td colspan="6"><h5>Liste des Consignes de l'Approvisionnement</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	<td align="center"><h5>Code </h5></td>
                <td  align="center"><h5>Libelle </h5></td>
                <td  align="center" ><h5>Quantite </h5></td>
                <td  align="center" ><h5>PU Cons.</h5></td>
                <td  align="center" ><h5>Montant Cons.</h5></td>
                <td  align="center" ><h5>Supprimer</h5></td>
			</tr>
<?php
$color = "darkgray";
$i = 0;
$MTC=0;			
$sql='SELECT  E.ID_EMBALLAGE, E.LIBELLE, E.MT_CONSIGNE, CA.ID_CONSIGNE, CA.MONTANT, E.ID_EMBALLAGE ,CA.ID_APPRO ,CA.QTE, CA.STATUT FROM EMBALLAGE E, CONSIGNEAPP CA WHERE CA.ID_EMBALLAGE =E.ID_EMBALLAGE AND CA.ID_APPRO="'.$_GET['Ap'].'" ORDER BY CA.ID_EMBALLAGE ' ;
	$reponse= $DataBase->query($sql);
		while($rslt3= $reponse->fetch())
		{
			$mtconsigne=0;
			$mtconsigne=$rslt3['MT_CONSIGNE'];
			
			if ($i%2 == 0)
					$color = '#E0E0E0';
				else
					$color = "white";
				echo "<tr bgcolor=$color>";
				?>
                        <td  align="center"> <?php echo $rslt3['ID_EMBALLAGE']; ?> </td>
                        <td  align="center"> <?php echo $rslt3['LIBELLE']; ?> </td>
                        <td  align="center"> <?php echo $rslt3['QTE']; ?> </td>
                        <td  align="center"> <?php echo number_format($rslt3['MT_CONSIGNE'], 0, ',', ' ').' F'; ?> </td>
                        <td  align="center"> <?php echo number_format($rslt3['MONTANT'], 0, ',', ' ').' F'; ?> </td>
                        <td  align="center"> <a href="index.php?formulaire=Supp_Consigne_App&Id=<?php echo $rslt3['ID_CONSIGNE']; ?>"/> <img src="IMG/Supp.png"/> </a></td>
                     </tr>
                <?php
				$i++;
				$MTC=$MTC+$rslt3['MONTANT'];
		 }
				?>
 <tr bgcolor="#E0E0E0"> 
    <td align="center" colspan="6"><h4>Montant Total Consigne :  <?php echo number_format($MTC, 0, ',', ' ').' Franc CFA'; ?></h4></td>
</tr>
</table>

<table id='trtr' border="0" width="100%" align="center">
          <tr align="center" >
          	<td colspan="6"><h5>Liste des emballages retournés au fournisseur</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	<td align="center"><h5>Code </h5></td>
                <td  align="center"><h5>Libelle </h5></td>
                <td  align="center" ><h5>Quantite </h5></td>
                <td  align="center" ><h5>PU.</h5></td>
                <td  align="center" ><h5>Montant</h5></td>
                <td  align="center" ><h5>Supprimer</h5></td>
			</tr>
<?php
$color = "darkgray";
$i = 0;
$MTR=0;			

$sql='SELECT  E.ID_EMBALLAGE, E.LIBELLE, E.MT_CONSIGNE, RE.ID_RTREMB, RE.MONTANT, RE.ID_EMBALLAGE ,RE.ID_APPRO ,RE.QTE, RE.STATUT FROM EMBALLAGE E, RTREMBFSSR RE WHERE RE.ID_EMBALLAGE =E.ID_EMBALLAGE AND RE.ID_APPRO="'.$_GET['Ap'].'" ORDER BY RE.ID_EMBALLAGE ' ;
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
                        <td  align="center"> <?php echo $rslt3['MT_CONSIGNE'].' F'; ?> </td>
                        <td  align="center"> <?php echo number_format($rslt3['MONTANT'], 0, ',', ' ').' F'; ?> </td>
                        <td  align="center"> <a href="index.php?formulaire=Supp_Emb_Rtr_Fssr&Id=<?php echo $rslt3['ID_RTREMB']; ?>"/> <img src="IMG/Supp.png"/> </a></td>
                     </tr>
                <?php
				$i++;
				$MTR=$MTR+$rslt3['MONTANT'];
		 }
				?>
 <tr bgcolor="#E0E0E0"> 
    <td align="center" colspan="6"><h4>Montant Total Retour Emballage :  <?php echo number_format($MTR, 0, ',', ' ').' Franc CFA'; ?></h4></td>
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
