<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"|| $_SESSION['habilitation']=="Caissier"))
{
	include("Connexion.php");
	include("fonctions.php");
	$sql2 = " select *  from sortie_stock_frigo  where id_sortiestock='".$_GET['Vte']."'";
	$reponse2= $DataBase->query($sql2);
	$rslt2= $reponse2->fetch();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire  de modification d'une Vente.</title>
<style type="text/css">
label
{
	display:block;
	width:110px;
	float: left;
	}
</style>
<script src="JS/Enreg_Vente_Frigo.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen" />
<script type="text/javascript" src="dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
</head>
 
<body>

<form action="CTRL/Ctrl_Mod_Perte.php" method="post" onsubmit="return verif_form()" >

<fieldset style=" width:1050px;"><legend>Informations sur la perte à modfifier</legend>
<table >
<tr>
	<td><label for="codevente"> Code * </label></td>
    <td><input type="text" id="codevente" name="codevente" readonly="readonly" style="width:200px; background-color:#ECECEC" value="<?php echo $rslt2['ID_SORTIESTOCK']; ?>"/></td>
    <td><label for="date_vente"> Date vente * </label> </td>
    <td><input type="text" id="date_vente" name="date_vente"style="width:100px;" value="<?php echo dateFormatFrancais($rslt2['DATESORTIESTOCK']); ?>"/> <input type="button" value="Calendrier" onclick="displayCalendar(document.forms[0].date_appro,'dd/mm/yyyy',this)" /></td>
	<td><label for="observationsortie"> Observation  </label></td>
    <td><input type="text" id="observationsortie" name="observationsortie" maxlength="35" style="width:200px;" value="<?php echo $rslt2['OBSERVATION']; ?>"/></td>
</tr>
<tr>
    <td colspan="6" align="right"><input type="submit" align="left" value="Modifier" id="Modifier" name="Modifier"/></td>
</tr>

</table>
</fieldset>
<table>
<tr>
	<td align="center" > <a href="index.php?formulaire=Validation_Perte&Vte=<?php echo $rslt2['ID_SORTIESTOCK'];?>"> <input type="button" name="Valider" id="Valider" value="Fin de Saisie" style="margin-left:10px; background:#F00;"/> </a></td>
	<td align="center" > <a href="index.php?formulaire=Ajout_Article_Perte&Vte=<?php echo $rslt2['ID_SORTIESTOCK'];?> "> <input type="button" name="ajoutart" id="ajoutart" value="Ajouter des articles" style="margin-left:680px; width:210px"/> </a></td>
</tr>
</table>
<table id='tarticle' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="7"><h5>Liste des Articles de la perte </h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	
            	<td align="center" width="20%"><h5>Code </h5></td>
                <td  align="center" ><h5>Libelle </h5></td>
                <td  align="center" ><h5>Qte </h5></td>
                <td  align="center" ><h5>Prix de Vente </h5></td>
                <td  align="center" ><h5>Observation </h5></td>
                <td  align="center" ><h5>Modifier</h5> </td>
                <td  align="center" ><h5>Supprimer</h5></td>
			</tr>
			
<?php
$couleur = "darkgray";
$i = 0;
$MT=0;			
$sql='SELECT  ARTICLE.ID_ARTICLE, ARTICLE.LIBELLE,ARTICLE.STOCKFRIGO, ARTICLEVENDU_FRIGO.ID_ARTICLE,ARTICLEVENDU_FRIGO.QTESORTIE, ARTICLEVENDU_FRIGO.ID_SORTIESTOCK,ARTICLEVENDU_FRIGO.PRIXVENTE, ARTICLEVENDU_FRIGO.OBSERVATION FROM ARTICLE, ARTICLEVENDU_FRIGO WHERE ARTICLE.ID_ARTICLE=ARTICLEVENDU_FRIGO.ID_ARTICLE AND ARTICLEVENDU_FRIGO.ID_SORTIESTOCK="'.$_GET['Vte'].'" ORDER BY ARTICLEVENDU_FRIGO.ID_ARTICLE ' ;
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
                        <td  align="center"> <?php echo $rslt3['LIBELLE'].' (Stock :'.$rslt3['STOCKFRIGO'].')'; ?> </td>
                        <td  align="center"> <?php echo $rslt3['QTESORTIE']; ?> </td>
                        <td  align="center"> <?php echo $rslt3['PRIXVENTE'].' FCFA'; ?> </td>
                        <td  align="center"> <?php echo $rslt3['OBSERVATION']; ?> </td>
                        <td  align="center"> <a href="index.php?formulaire=Modifier_Article_Perte&AR=<?php echo $rslt3['ID_ARTICLE'];?>&Vte=<?php echo $rslt3['ID_SORTIESTOCK']; ?>&Qte=<?php echo $rslt3['QTESORTIE'];?> "/> <img src="IMG/b_edit.png"/> </a></td>
                        <td  align="center"> <a href="index.php?formulaire=Supprimer_Article_Perte&AR=<?php echo $rslt3['ID_ARTICLE'];?>&Vte=<?php echo $rslt3['ID_SORTIESTOCK']; ?>"/> <img src="IMG/Supp.png"/> </a></td>
                     </tr>
                <?php
				$i++;
				$MT=$MT+$rslt3['PRIXVENTE'];
		 }
				?>
 <tr bgcolor="#E0E0E0"> 
    <td align="center" colspan="7"><h4>Montant Total :  <?php echo $MT; ?> FCFA</h4></td>
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
