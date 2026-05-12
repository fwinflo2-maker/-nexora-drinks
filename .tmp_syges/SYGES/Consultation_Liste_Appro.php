<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"  || $_SESSION['habilitation']=="Comptable" || $_SESSION['habilitation']=="Magasinier"))
{
	include('Connexion.php');
	include('fonctions.php');
	$Debut=dateFormatAnglais($_GET['DateD']);
	$Fin=dateFormatAnglais($_GET['DateF']);
	if ($_GET['Stat']=='Mixte')
	{	
	$sql='SELECT A.ID_APPRO, A.DATE_APPRO, A.ID_FOURNISSEUR, A.OBSERVATION, A.STATUT,A.LIQUIDEHT,A.NBRECOLIS, F.ID_FOURNISSEUR, F.NOM FROM APPROVISIONNEMENT A, FOURNISSEUR F WHERE A.ID_FOURNISSEUR=F.ID_FOURNISSEUR AND A.DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY A.ID_APPRO' ;
	}
	else
		if ($_GET['Stat']=='N')
		{
		$sql='SELECT A.ID_APPRO, A.DATE_APPRO, A.ID_FOURNISSEUR, A.OBSERVATION, A.STATUT,A.LIQUIDEHT,A.NBRECOLIS, F.ID_FOURNISSEUR, F.NOM FROM APPROVISIONNEMENT A, FOURNISSEUR F WHERE A.STATUT ="N" AND A.ID_FOURNISSEUR=F.ID_FOURNISSEUR AND A.DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY A.ID_APPRO' ;
		}
		else
			{
			$sql='SELECT A.ID_APPRO, A.DATE_APPRO, A.ID_FOURNISSEUR, A.OBSERVATION, A.STATUT,A.LIQUIDEHT,A.NBRECOLIS, F.ID_FOURNISSEUR, F.NOM FROM APPROVISIONNEMENT A, FOURNISSEUR F WHERE A.STATUT ="V" AND A.ID_FOURNISSEUR=F.ID_FOURNISSEUR AND A.DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY A.ID_APPRO' ;
			}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultation de la Liste des Approvisionnements</title>
</head>

<body>
<table id='liste' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="7"><h3>Liste des Approvisionnements  </h3></td>
          </tr>
          <tr align="center" bgcolor="#CCCCCC">
                <td colspan="4"><h5>Période Du : <?php echo dateFormatFrancais($Debut); ?> Au : <?php echo dateFormatFrancais($Fin); ?></h5></td>
                <td colspan="3"><h5>Statut : <?php echo $_GET['Stat']; ?></h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center"><h5>Date d'appro </h5> </td>
                <td align="center"><h5>Code </h5> </td>
                <td align="center"><h5>Fournisseur </h5> </td>
                <td align="center"><h5>CA HT </h5> </td>
                <td align="center"><h5>Nbre Colis </h5> </td>
                <td  align="center"><h5>Statut </h5></td>
				<td align="left"><h5>Observation </h5></td>
			</tr>
            
<?php
$couleur = "darkgray";
$i = 0;
$nbre=0;
$ttca=0;
$ttcolis=0;
$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			$ttca=$ttca+$rslt['LIQUIDEHT'];
			$ttcolis=$ttcolis+$rslt['NBRECOLIS'];
			if ($i%2 == 0)
					$couleur = '#CCCCCC';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                		<td  align="center"> <?php echo dateFormatFrancais($rslt['DATE_APPRO']); ?> </td>
                        <td  align="center"> <?php echo $rslt['ID_APPRO']; ?> </td>
                        <td  align="center"> <?php echo $rslt['NOM']; ?> </td>
                        <td  align="center"> <?php echo $rslt['LIQUIDEHT']; ?> </td>
                        <td  align="center"> <?php echo $rslt['NBRECOLIS']; ?> </td>
                        <td  align="center"> <?php echo $rslt['STATUT']; ?> </td>
                        <td  align="left"> <?php echo $rslt['OBSERVATION']; ?> </td>
                     </tr>
                <?php
				$i++;
				$nbre++;
		 }
?>
<tr>
	<td><a href="Liste_Appro.php?debut=<?php echo $_GET["DateD"];?>&fin=<?php echo $_GET["DateF"];?>&Statut=<?php echo $_GET['Stat'];?>"/><input type="button" value="Imprimer" /></td></a>
	<td colspan="2" align="center"><h4>Nombre d'appro. :<?php echo $nbre; ?> </h4></td>
	<td colspan="2" align="center"><h4>CA HT :<?php echo number_format($ttca, 0, ',', ' ').' F'; ?> </h4></td>
    <td colspan="2" align="center"><h4>Nombre Colis :<?php echo number_format($ttcolis, 0, ',', ' '); ?> </h4></td>
</tr>
</table>
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