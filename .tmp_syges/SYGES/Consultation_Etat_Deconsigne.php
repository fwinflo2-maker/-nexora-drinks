<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur"))
{
	include('Connexion.php');
	include('fonctions.php');
	$Debut=dateFormatAnglais($_GET['DateD']);
	$Fin=dateFormatAnglais($_GET['DateF']);

	$sql='SELECT C.ID_SORTIESTOCK, C.ID_EMBALLAGE, C.DATE_CONSIGNE, C.OBS_DECONSIGNE,C.QTE,C.DATE_DECONSIGNE, C.STATUT, C.PU, C.MONTANT, E.ID_EMBALLAGE, E.LIBELLE FROM CONSIGNE C, EMBALLAGE E WHERE C.ID_EMBALLAGE=E.ID_EMBALLAGE AND C.DATE_DECONSIGNE BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND C.STATUT="Deconsigne" ORDER BY C.ID_SORTIESTOCK';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultation de la Liste des Consignes</title>
</head>

<body>
<table id='liste' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="10"><h3>Etat des deconsignations </h3></td>
          </tr>
          <tr align="center" bgcolor="#CCCCCC">
          		<td colspan="10"><h5>Période Du : <?php echo dateFormatFrancais($Debut); ?> Au : <?php echo dateFormatFrancais($Fin); ?></h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center" ><h5>Date vente </h5> </td>
                <td align="left"><h5>Code </h5> </td>
                <td align="left" ><h5>Client </h5> </td>
                <td align="center"><h5>Date Consigne </h5> </td>
                <td align="center" ><h5>Emballage </h5> </td>
                <td align="center" ><h5>Qte </h5> </td>
				<td align="center" ><h5>Montant </h5></td>
                <td  align="center"><h5>Date Deconsigne</h5></td>
                <td  align="left"><h5>Observation </h5></td>
                <td align="left" ><h5>Statut </h5></td>
			</tr>
            
<?php
$MT=0;
$couleur = "darkgray";
$i = 0;
$nbre=0;
$reponse= $DataBase->query($sql);
while($rslt= $reponse->fetch())
{
 //ici on recupere la date de la vente et le client
	 $sql1 = 'SELECT ST.ID_SORTIESTOCK, ST.DATESORTIESTOCK, ST.ID_CLIENT, C.ID_CLIENT, C.NOM FROM SORTIE_STOCK ST, CLIENT C WHERE ST.ID_CLIENT=C.ID_CLIENT AND ST.ID_SORTIESTOCK="'.$rslt['ID_SORTIESTOCK'].'"';
	$reponse1= $DataBase->query($sql1);
		while($rslt2= $reponse1->fetch())
		{
			$datevente=$rslt2['DATESORTIESTOCK'];
    		$nom=$rslt2['NOM'];
		}

 
			if ($i%2 == 0)
					$couleur = '#CCCCCC';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                		<td  align="center"> <?php echo dateFormatFrancais($datevente); ?> </td>
                        <td  align="left"> <?php echo $rslt['ID_SORTIESTOCK']; ?> </td>
                        <td  align="left"> <?php echo $nom; ?> </td>
                        <td  align="center"> <?php echo dateFormatFrancais($rslt['DATE_CONSIGNE']); ?> </td>
                        <td  align="center"> <?php echo $rslt['LIBELLE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['QTE'];?> </td>
                        <td  align="center"> <?php echo $rslt['MONTANT'].' F'; ?> </td>
                        <td  align="center"> <?php echo dateFormatFrancais($rslt['DATE_DECONSIGNE']); ?> </td>
                        <td  align="left"> <?php echo $rslt['OBS_DECONSIGNE']; ?> </td>
                        <td  align="left"> <?php echo $rslt['STATUT']; ?> </td>
                     </tr>
                <?php
				$i++;
				$nbre++;
				$MT = ($MT + $rslt['MONTANT']);
		 }
?>
<tr>
	<td><a href="Etat_Deconsigne.php?debut=<?php echo $_GET["DateD"];?>&fin=<?php echo $_GET["DateF"];?>"/><input type="button" value="Imprimer" /></td></a>
	<td colspan="3"><h4>Nombre de deconsigne :  <?php echo $nbre; ?> </h4></td>
    <td align="center" colspan="7"><h4>Total Montant Deconsigne : <?php echo number_format($MT, 0, ',', ' ').' F'; ?> </h4></td>
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