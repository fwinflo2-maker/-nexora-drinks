<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur"))
{
	include('Connexion.php');
	include('fonctions.php');
	$Debut=dateFormatAnglais($_GET['DateD']);
	$Fin=dateFormatAnglais($_GET['DateF']);

	$sql='SELECT R.ID_RTREMB, R.ID_APPRO, R.ID_EMBALLAGE, R.DATE_RTREMB,R.QTE,R.DATE_RTREMB, R.STATUT, R.PU, R.MONTANT, E.LIBELLE FROM RTREMBFSSR R, EMBALLAGE E WHERE R.ID_EMBALLAGE=E.ID_EMBALLAGE AND R.DATE_RTREMB BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND R.STATUT="OK" ORDER BY R.ID_RTREMB';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultation de la Liste des retours d'emballages fournisseur</title>
</head>

<body>
<table id='liste' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="9"><h3>Etat des retours d'emballages</h3></td>
          </tr>
          <tr align="center" bgcolor="#CCCCCC">
          		<td colspan="9"><h5>Période Du : <?php echo dateFormatFrancais($Debut); ?> Au : <?php echo dateFormatFrancais($Fin); ?></h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center"><h5>Code Retour</h5> </td>
                <td align="center" ><h5>Date Retour</h5> </td>
                <td align="center" ><h5>Code Appro. </h5> </td>
                <td align="center" ><h5>Date Appro</h5> </td>
                <td align="center"><h5>Emballage</h5> </td>
                <td align="center" ><h5>Qte </h5> </td>
                <td align="center" ><h5>PU </h5> </td>
                <td align="center" ><h5>Montant </h5> </td>
                <td align="center" ><h5>Statut </h5></td>
			</tr>
            
<?php
$MT=0;
$couleur = "darkgray";
$i = 0;
$nbre=0;
$reponse= $DataBase->query($sql);
while($rslt= $reponse->fetch())
{
 //ici on recupere la date de l'appro 
	$sql1 = 'SELECT DATE_APPRO FROM APPROVISIONNEMENT  WHERE ID_APPRO="'.$rslt['ID_APPRO'].'"';
	$reponse1= $DataBase->query($sql1);
		while($rslt2= $reponse1->fetch())
		{
			$date=$rslt2['DATE_APPRO'];
		}
 
			if ($i%2 == 0)
					$couleur = '#CCCCCC';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                		<td  align="center"> <?php echo $rslt['ID_RTREMB']; ?> </td>
                		<td  align="center"> <?php echo dateFormatFrancais($rslt['DATE_RTREMB']); ?> </td>
                        <td  align="center"> <?php echo $rslt['ID_APPRO']; ?> </td>
                        <td  align="center"> <?php echo dateFormatFrancais($date); ?> </td>
                        <td  align="center"> <?php echo$rslt['LIBELLE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['QTE'];?> </td>
                        <td  align="center"> <?php echo $rslt['PU'].' F'; ?> </td>
                        <td  align="center"> <?php echo $rslt['MONTANT'].' F'; ?> </td>
                        <td  align="center"> <?php echo $rslt['STATUT']; ?> </td>
                     </tr>
                <?php
				$i++;
				$nbre++;
				$MT = ($MT + $rslt['MONTANT']);
		 }
?>
<tr>
	<td><a href="Etat_RtrEmb.php?debut=<?php echo $_GET["DateD"];?>&fin=<?php echo $_GET["DateF"];?>"/><input type="button" value="Imprimer" /></td></a>
	<td colspan="3"><h4>Nombre :  <?php echo $nbre; ?> </h4></td>
    <td align="center" colspan="7"><h4> Montant Total : <?php echo number_format($MT, 0, ',', ' ').' F'; ?> </h4></td>
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