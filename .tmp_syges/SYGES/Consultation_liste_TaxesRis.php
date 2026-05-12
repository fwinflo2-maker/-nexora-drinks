<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur"  || $_SESSION['habilitation']=="Comptable"))
{
	include('Connexion.php');
	include('fonctions.php');
	$Debut=dateFormatAnglais($_GET['DateD']);
	$Fin=dateFormatAnglais($_GET['DateF']);
	
	$ttva=0;
	$tpsa=0;
	$sql4='SELECT * FROM PARAMETRE';
	$reponse4= $DataBase->query($sql4);
	while($rslt4= $reponse4->fetch())
	{
			$ttva= $rslt4['TVA'];
			$tpsa=$rslt4['TAUXRETFISCPRO'];
	}
?>
<!DOCTYPE html PUBLIC>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultation de la Liste des Achats pour un Clients</title>
</head>

<body>
<table id='liste' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="8"><h4>ETAT DES PRELEVEMENTS SUR RISTOURNES CLIENTS </h4></td>
          </tr>
          <tr>
          		<td><a href="Etat_TaxesRis.php?DateD=<?php echo $_GET["DateD"];?>&DateF=<?php echo $_GET["DateF"];?>"/><input type="button" value="Imprimer" /></a></td>
                <td></td>
          </tr>
          <tr align="center" >
          		<td colspan="8"><h5>Période : Du : <?php echo dateFormatFrancais($Debut); ?>  Au : <?php echo dateFormatFrancais($Fin); ?> </h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center" ><h5>N° </h5> </td>
                <td align="center" ><h5>Date  </h5> </td>
                <td align="center" ><h5>N° Facture </h5> </td>
                <td align="center" ><h5>Client</h5> </td>
                <td align="center" ><h5>Mt Ristourne HT </h5> </td>
                <td align="center" ><h5>TVA (<?php echo $ttva; ?>%)</h5> </td>
                <td align="center" ><h5>PSA (<?php echo $tpsa; ?>%)</h5> </td>
                <td align="center" ><h5>Mt Ristourne TTC </h5> </td>
			</tr>
            
<?php
$couleur = "darkgray";
$i = 1;
$nbre=0;
$TTC=0;
$TVA=0;
$PSA=0;
$MT=0;
$MHT=0;
$sql='SELECT ST.ID_SORTIESTOCK, ST.DATESORTIESTOCK, C.NOM, ST.CREDITRISTOURNE, C.NOM FROM CLIENT C, SORTIE_STOCK ST WHERE C.ID_CLIENT=ST.ID_CLIENT AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V" AND ST.CREDITRISTOURNE!=0 ORDER BY ST.DATESORTIESTOCK';
$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
					     //Ici on calcule le montant de la retenue fis pro et la tva
				  $mttva=0;
				  $mtpsa=0;
				  $mtttc=0;
				  $mtht=$rslt['CREDITRISTOURNE'];
				  $mttva=$mtht*$ttva/100;
				  $mtpsa=$mtht*$tpsa/100;
				  $mtttc=$mtht+$mttva+$mtpsa;
				  
				 
				  
				if ($i%2 != 0)
					$couleur = "white";
				else
					$couleur = '#CCCCCC';
				echo "<tr bgcolor=$couleur>";
				?>
                        <td  align="center"> <?php echo $i; ?> </td>
                		<td  align="center"> <?php echo dateFormatFrancais($rslt['DATESORTIESTOCK']); ?> </td>
                        <td  align="center"> <?php echo $rslt['ID_SORTIESTOCK']; ?> </td>
                        <td  align="center"> <?php echo $rslt['NOM']; ?> </td>
                        <td  align="center"> <?php echo number_format($mtht, 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($mttva, 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($mtpsa, 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($mtttc, 0, ',', ' '); ?> </td>
                     </tr>
                <?php
				$i++;
				$nbre++;
				$MT= $MT+$mtttc;
				$MHT= $MHT+$mtht;
				$TVA= $TVA+$mttva;
				$PSA= $PSA+$mtpsa;
		 }
?>
<tr>
	<td><a href="Etat_TaxesRis.php?DateD=<?php echo $_GET["DateD"];?>&DateF=<?php echo $_GET["DateF"];?>"/><input type="button" value="Imprimer" /></a></td>
     <td colspan="3" align="center"> <h5>TOTAUX</h5></td>
     <td  align="center"> <h5><?php echo number_format($MHT, 0, ',', ' '); ?></h5> </td>
     <td  align="center"> <h5><?php echo number_format($TVA, 0, ',', ' '); ?></h5> </td>
     <td  align="center"> <h5><?php echo number_format($PSA, 0, ',', ' '); ?></h5> </td>
     <td  align="center"> <h5><?php echo number_format($MT, 0, ',', ' '); ?></h5> </td>
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