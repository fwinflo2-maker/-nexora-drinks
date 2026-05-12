<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
{
	include("Connexion.php");
	include("fonctions.php");
	$sql2 = " select *  from sortie_stock_cession  where id_sortiestock='".$_GET['SC']."'";
	$reponse2= $DataBase->query($sql2);
	$rslt2= $reponse2->fetch();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire  de modification d'un sortie Cession.</title>
<style type="text/css">
label
{
	display:block;
	width:110px;
	float: left;
	}
</style>
<script src="JS/Enreg_SortieCession.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen" />
<script type="text/javascript" src="dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
</head>
 
<body>

<form action="CTRL/Ctrl_Mod_SortieCession.php" method="post" onsubmit="return verif_form()" >
<fieldset style=" width:1050px;"><legend>Informations sur la sortie cession à modifier</legend>
<table >
<tr>
	<td><label for="codecession"> Code * </label></td>
    <td><input type="text" id="codecession" name="codecession" readonly="readonly" style="width:150px; background-color:#ECECEC" value="<?php echo $rslt2['ID_SORTIESTOCK']; ?>"/></td>
    <td><label for="date"> Date Cession * </label> </td>
    <td><input type="text" id="date" name="date"style="width:100px;" value="<?php echo dateFormatFrancais($rslt2['DATESORTIESTOCK']); ?>"/> <input type="button" value="Calendrier" onclick="displayCalendar(document.forms[0].date,'dd/mm/yyyy',this)" /></td>
	<td><label for="observation"> Observation  </label></td>
    <td><input type="text" id="observation" name="observation" maxlength="40" style="width:200px;" value="<?php echo $rslt2['OBSERVATION']; ?>"/></td>
    <td colspan="6" align="right"><input type="submit" align="left" value="Modifier" id="Modifier" name="Modifier"/></td>
</tr>

</table>
</fieldset>
<table>
<tr>
	<td align="center" ><a href="index.php?formulaire=Validation_SortieCession&SC=<?php echo $rslt2['ID_SORTIESTOCK'];?>"><input type="button" name="Valider" id="Valider" value="Fin de Saisie" style="margin-left:10px; background:#F00;"/> </a></td>
    <td align="center" > <a href="index.php?formulaire=Ajout_Emballage_SC&SC=<?php echo $rslt2['ID_SORTIESTOCK'];?>"> <input type="button" name="ajout" id="ajout" value="Ajouter des Emballages" style="margin-left:220px; width:230px"/> </a></td>
	<td align="center" > <a href="index.php?formulaire=Ajout_Article_SortieCession&SC=<?php echo $rslt2['ID_SORTIESTOCK'];?> "> <input type="button" name="ajoutart" id="ajoutart" value="Ajouter des articles à la sortie" style="margin-left:160px;"/> </a></td>
</tr>
</table>
<table id='tarticle' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="8"><h5>Liste des Articles de la sortie cession</h5></td>
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
$sql='SELECT  ARTICLE.ID_ARTICLE, ARTICLE.LIBELLE, ARTICLE.MARQUE, ARTICLE.QTESTOCK, ARTICLESORTIE_CESSION.ID_ARTICLE,ARTICLESORTIE_CESSION.QTESORTIE, ARTICLESORTIE_CESSION.ID_SORTIESTOCK FROM ARTICLE, ARTICLESORTIE_CESSION WHERE ARTICLE.ID_ARTICLE=ARTICLESORTIE_CESSION.ID_ARTICLE AND ARTICLESORTIE_CESSION.ID_SORTIESTOCK="'.$_GET['SC'].'" ORDER BY ARTICLESORTIE_CESSION.ID_ARTICLE ' ;
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
                        <td  align="center"> <?php echo $rslt3['QTESORTIE']; ?> </td>
                        <td  align="center"> <a href="index.php?formulaire=Modifier_Article_SortieCession&AR=<?php echo $rslt3['ID_ARTICLE'];?>&SC=<?php echo $rslt3['ID_SORTIESTOCK']; ?>&QTE=<?php echo $rslt3['QTESORTIE'];?> "/> <img src="IMG/b_edit.png"/> </a></td>
                        <td  align="center"> <a href="index.php?formulaire=Supprimer_Article_SortieCession&AR=<?php echo $rslt3['ID_ARTICLE'];?>&SC=<?php echo $rslt3['ID_SORTIESTOCK']; ?>&QTE=<?php echo $rslt3['QTESORTIE'];?> "/> <img src="IMG/Supp.png"/> </a></td>
                     </tr>
                <?php
				$i++;
				$nbrear=$nbrear+$rslt3['QTESORTIE'];
		 }
				?>
<tr bgcolor="#E0E0E0"> 
    <td align="center" colspan="6"><h4>Total Colis :  <?php echo $nbrear; ?> </h4></td>
</tr>
</table>

<table id='temballage' border="0" width="100%" align="center">
          <tr align="center" >
          	<td colspan="4"><h5>Liste des Emballages de la Cession</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	<td align="center" width="20%"><h5>Code </h5></td>
                <td  align="center"><h5>Libelle </h5></td>
                <td  align="center" ><h5>Quantite </h5></td>
                <td  align="center" ><h5>Supprimer</h5></td>
			</tr>
<?php
$color = "darkgray";
$i = 0;
$NBRECC=0;			
$sql='SELECT  E.ID_EMBALLAGE, E.LIBELLE, SC.ID_CONSIGNE, E.ID_EMBALLAGE ,SC.ID_SORTIESTOCK ,SC.QTE FROM EMBALLAGE E, EMBALLAGESORTIECESSION SC WHERE SC.ID_EMBALLAGE =E.ID_EMBALLAGE AND SC.ID_SORTIESTOCK="'.$_GET['SC'].'" ORDER BY SC.ID_EMBALLAGE ' ;
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
                        <td  align="center"> <a href="index.php?formulaire=Supp_Emballage_SC&Id=<?php echo $rslt3['ID_CONSIGNE']; ?>"/> <img src="IMG/Supp.png"/> </a></td>
                     </tr>
                <?php
				$i++;
				$NBRECC=$NBRECC+$rslt3['QTE'];
		 }
				?>
 <tr bgcolor="#E0E0E0"> 
    <td align="center" colspan="4"><h4> Total Emballages :  <?php echo number_format($NBRECC, 0, ',', ' '); ?></h4></td>
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
