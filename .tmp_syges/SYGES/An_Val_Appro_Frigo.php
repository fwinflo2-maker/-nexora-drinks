<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur"))
{
	include("Connexion.php");
	include("fonctions.php");
	$sql2 = " select *  from approfrigo  where id_appro='".$_GET['Ap']."'";
	$reponse2= $DataBase->query($sql2);
	$rslt2= $reponse2->fetch();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire  d'annulation d'un Appro frigo.</title>
<style type="text/css">
label
{
	display:block;
	width:110px;
	float: left;
	}
</style>
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen" />
<script type="text/javascript" src="dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
</head>
 
<body>

<form action="CTRL/Ctrl_An_val_Appro_Frigo.php" method="post">

<fieldset style=" width:1060px;"><legend>Informations sur l'approvisionnement à annuler</legend>
<table >
<tr>
	<td><label for="codeappro"> Code * </label></td>
    <td><input type="text" id="codeappro" name="codeappro" readonly="readonly" style="width:150px; background-color:#ECECEC" value="<?php echo $rslt2['ID_APPRO']; ?>"/></td>
    <td><label for="date_appro"> Date de l'appro * </label> </td>
    <td><input type="text" id="date_appro" name="date_appro" style="width:200px; background-color:#ECECEC;" value="<?php echo dateFormatFrancais($rslt2['DATE_APPRO']); ?>" readonly="readonly"/> </td>
	<td><label for="observationappro"> Observation  </label></td>
    <td><input type="text" id="observationappro" name="observationappro" maxlength="" style="width:250px; background-color:#ECECEC;" value="<?php echo $rslt2['OBSERVATION']; ?>" readonly="readonly"/></td>
</tr>
<tr>
    <td colspan="6" align="right"><input type="submit" align="left" value="Annuler La Validation" id="Annuler" name="Annuler" style="background:#F00"/></td>
</tr>

</table>
<table id='tarticle' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="3"><h5>Sortie du Stock du Magasin/Boutique</h5></td>
            <td colspan="2"><h5>Entrée en Stock Frigo</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	<td align="center"><h5>Conditionnement </h5></td>
                <td  align="center"><h5>Libelle </h5></td>
                <td  align="center" ><h5>Qte </h5></td>
                <td  align="center" ><h5>Libelle </h5></td>
                <td  align="center" ><h5>Nbre de Bouteille </h5></td>
			</tr>
			
<?php
$couleur = "darkgray";
$i = 0;			
	$sql='SELECT  ARTICLE.ID_ARTICLE, ARTICLE.LIBELLE, ARTICLE.MARQUE,ARTICLE.QTESTOCK,ARTICLE.STOCKFRIGO, ARTICLE.NBREBTE as NBRE, ARTICLE_RECU_FRIGO.ID_ARTICLE,ARTICLE_RECU_FRIGO.QTERECU, ARTICLE_RECU_FRIGO.ID_APPRO, ARTICLE_RECU_FRIGO.NBREBTE as BTE FROM ARTICLE, ARTICLE_RECU_FRIGO WHERE ARTICLE.ID_ARTICLE=ARTICLE_RECU_FRIGO.ID_ARTICLE AND ARTICLE_RECU_FRIGO.ID_APPRO="'.$_GET['Ap'].'" ORDER BY ARTICLE_RECU_FRIGO.ID_ARTICLE ' ;
	$reponse= $DataBase->query($sql);
		while($rslt3= $reponse->fetch())
		{
			if ($i%2 == 0)
					$couleur = '#E0E0E0';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                        <td  align="center"> <?php echo $rslt3['MARQUE'].' '.$rslt3['NBRE']; ?> </td>
                        <td  align="center"> <?php echo $rslt3['LIBELLE'].' (Stock :'.$rslt3['QTESTOCK'].')'; ?> </td>
                        <td  align="center"> <?php echo $rslt3['QTERECU']; ?> </td>
                        <td  align="center"> <?php echo $rslt3['LIBELLE'].' (Stock :'.$rslt3['STOCKFRIGO'].')'; ?> </td>
                        <td  align="center"> <?php echo $rslt3['BTE']; ?> </td>
                     </tr>
                <?php
				$i++;
		 }
				?>
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
