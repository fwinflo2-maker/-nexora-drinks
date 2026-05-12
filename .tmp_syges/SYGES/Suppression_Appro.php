<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" || $_SESSION['habilitation']=="Caissier" || $_SESSION['habilitation']=="Comptable" || $_SESSION['habilitation']=="Magasinier"))
{
	include("Connexion.php");
	include("fonctions.php");
	$sql = " select *  from fournisseur  where id_fournisseur='".$_GET['Fs']."'";
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
<title>Formulaire  de suppression d'un Appro.</title>
<style type="text/css">
label
{
	display:block;
	width:110px;
	float: left;
	}
</style>
</head>
 
<body>

<form action="CTRL/Ctrl_Supp_Appro.php" method="post">

<fieldset style=" width:1050px;"><legend>Informations sur l'approvisionnement à supprimer</legend>
<table >
<tr>
	<td><label for="codeappro"> Code * </label></td>
    <td><input type="text" id="codeappro" name="codeappro" readonly="readonly" style="width:150px; background-color:#ECECEC" value="<?php echo $rslt2['ID_APPRO']; ?>"/></td>
	<td><label for="codefournisseur"> Fournisseur *  </label></td>
    <td><input type="text" id="codefournisseur" name="codefournisseur"  value="<?php echo $rslt['NOM']; ?>" readonly="readonly" style="width:150px; background:#ECECEC;"/></td>
</tr>
<tr>
    <td><label for="liquideht"> Liquide HT  </label></td>
    <td><input type="text" id="liquideht" name="liquideht"  value="<?php echo number_format($rslt2['LIQUIDEHT'], 0, ',', ' '); ?>" readonly="readonly" style="width:150px; background:#ECECEC;"/></td>
    <td><label for="date_appro"> Date de l'appro * </label> </td>
    <td><input type="text" id="date_appro" name="date_appro" style="width:150px; background-color:#ECECEC;" value="<?php echo dateFormatFrancais($rslt2['DATE_APPRO']); ?>" readonly="readonly"/> </td>
        <td><label for="nbrecolis"> Nbre de Colis *</label></td>
    <td><input type="text" id="nbrecolis" name="nbrecolis" style="width:80px; background-color:#ECECEC; text-align:center" value="<?php echo $rslt2['NBRECOLIS']; ?>" readonly="readonly"/></td>
	<td><label for="observationappro"> Observation  </label></td>
    <td><input type="text" id="observationappro" name="observationappro" maxlength="" style="width:150px; background-color:#ECECEC;" value="<?php echo $rslt2['OBSERVATION']; ?>" readonly="readonly"/></td>
</tr>
<tr>
    <td colspan="8" align="right"><input type="submit" align="left" value="Supprimer" id="Supprimer" name="Supprimer" style="background:#F00"/></td>
</tr>

</table>
</fieldset>

<table id='tarticle' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="4"><h5>Liste des Articles de l'approvisionnement</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	
            	<td align="center" width="20%"><h5>Code </h5></td>
                <td  align="center"><h5>Conditionnement </h5></td>
                <td  align="center" ><h5>Libelle </h5></td>
                <td  align="center" ><h5>Qte reçu </h5></td>

			</tr>
			
<?php
$couleur = "darkgray";
$i = 0;
$nbrear=0;			
	$sql='SELECT  ARTICLE.ID_ARTICLE, ARTICLE.LIBELLE, ARTICLE.MARQUE, ARTICLE_RECU.ID_ARTICLE,ARTICLE_RECU.QTERECU, ARTICLE_RECU.ID_APPRO FROM ARTICLE, ARTICLE_RECU WHERE ARTICLE.ID_ARTICLE=ARTICLE_RECU.ID_ARTICLE AND ARTICLE_RECU.ID_APPRO="'.$_GET['Ap'].'" ORDER BY ARTICLE_RECU.ID_ARTICLE ' ;
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
                        <td  align="center"> <?php echo $rslt3['LIBELLE']; ?> </td>
                        <td  align="center"> <?php echo $rslt3['QTERECU']; ?> </td>
                <?php
				$i++;
				$nbrear=$nbrear+$rslt3['QTERECU'];
		 }
				?>
<tr bgcolor="#E0E0E0"> 
    <td align="center" colspan="8"><h4>Nombre de colis :  <?php echo $nbrear; ?> </h4></td>
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
