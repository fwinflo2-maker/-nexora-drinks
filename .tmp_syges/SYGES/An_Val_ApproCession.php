<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur"))
{
	include("Connexion.php");
	include("fonctions.php");
	$sql2 = " select *  from approcession  where id_appro='".$_GET['Ap']."'";
	$reponse2= $DataBase->query($sql2);
	$rslt2= $reponse2->fetch();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire  de dévalidation d'un Appro. Cession</title>
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

<form action="CTRL/Ctrl_An_Val_ApproCession.php" method="post">

<fieldset style=" width:1050px;"><legend>Informations sur l'appro cession à dévalider</legend>
<table >
<tr>
	<td><label for="codeappro"> Code * </label></td>
    <td><input type="text" id="codeappro" name="codeappro" readonly="readonly" style="width:150px; background-color:#ECECEC" value="<?php echo $rslt2['ID_APPRO']; ?>"/></td>
    <td><label for="date_appro"> Date de l'appro * </label> </td>
    <td><input type="text" id="date_appro" name="date_appro" style="width:200px; background:#ECECEC;" value="<?php echo dateFormatFrancais($rslt2['DATE_APPRO']); ?>" readonly="readonly"/> </td>
	<td><label for="observationappro"> Observation  </label></td>
    <td><input type="text" id="observationappro" name="observationappro" maxlength="" style="width:200px; background:#ECECEC;" value="<?php echo $rslt2['OBSERVATION']; ?>" readonly="readonly"/></td>
</tr>
<tr>
    <td colspan="6" align="right"><input type="submit" align="left" value="Annuler Validation" id="Annuler" name="Annuler"/></td>
</tr>

</table>
</fieldset>

<table id='tarticle' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="4"><h5>Liste des Articles de l'appro cession</h5></td>
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
			
	$sql='SELECT  ARTICLE.ID_ARTICLE, ARTICLE.LIBELLE, ARTICLE.MARQUE,ARTICLE.QTESTOCK, ARTICLE_RECU_CESSION.ID_ARTICLE,ARTICLE_RECU_CESSION.QTERECU, ARTICLE_RECU_CESSION.ID_APPRO FROM ARTICLE, ARTICLE_RECU_CESSION WHERE ARTICLE.ID_ARTICLE=ARTICLE_RECU_CESSION.ID_ARTICLE AND ARTICLE_RECU_CESSION.ID_APPRO="'.$_GET['Ap'].'" ORDER BY ARTICLE_RECU_CESSION.ID_ARTICLE ' ;
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
                        <td  align="center"> <?php echo $rslt3['LIBELLE'].' (Stock :'.$rslt3['QTESTOCK'].')'; ?> </td>
                        <td  align="center"> <?php echo $rslt3['QTERECU']; ?> </td>
                <?php
				$i++;
		 }
				?>
</table>
<table id='temballage' border="0" width="100%" align="center">
          <tr align="center" >
          	<td colspan="3"><h5>Liste des Emballages de la Cession</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	<td align="center" width="20%"><h5>Code </h5></td>
                <td  align="center"><h5>Libelle </h5></td>
                <td  align="center" ><h5>Quantite </h5></td>
			</tr>
<?php
$color = "darkgray";
$i = 0;
$NBRECC=0;			
$sql='SELECT  E.ID_EMBALLAGE, E.LIBELLE, CC.ID_CONSIGNE, E.ID_EMBALLAGE ,CC.ID_APPRO ,CC.QTE FROM EMBALLAGE E, CONSIGNECESSION CC WHERE CC.ID_EMBALLAGE =E.ID_EMBALLAGE AND CC.ID_APPRO="'.$_GET['Ap'].'" ORDER BY CC.ID_EMBALLAGE ' ;
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
