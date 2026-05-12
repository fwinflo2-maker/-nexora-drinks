<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Comptable"))
{
	include('Connexion.php');
	include('fonctions.php');
	$Debut=dateFormatAnglais($_GET['DateD']);
	$Fin=dateFormatAnglais($_GET['DateF']);
	
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Etat des Ristournes</title>
</head>

<body>
<!--ici on calcule le CA HT les prelements (psa et epargne) ainsi que le CA TTC -->
<table id='chp' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="5"><h3>CA CLIENTS</h3></td>
          </tr>
          <tr align="center" bgcolor="#CCCCCC">
          <td align="center"><a href="Etat_CA_Client.php?DateD=<?php echo $_GET["DateD"];?>&DateF=<?php echo $_GET["DateF"];?>"/><input type="button" value="Imprimer" style="background:#FF6600" /></td></a>
                <td colspan="4"><h5>Période Du : <?php echo dateFormatFrancais($Debut); ?> Au : <?php echo dateFormatFrancais($Fin); ?></h5></td>
          </tr>
            
</table>


<!--ici on calcule les ristournes de base -->
<table id='Ristourne' border="1" width="100%" align="center">

       <tr bgcolor="#CCCCCC">
       		<td align="center"><h5>Code Client</h5> </td>
            <td align="center"><h5>Client</h5> </td>
            <td align="center"><h5>Taux PSA</h5> </td>
            <td align="center"><h5>Nbre Facture(s)</h5> </td>
            <td align="center"><h5>Nbre Colis</h5> </td>
            <td align="center"><h5>Montant HT</h5> </td>
            <td align="center"><h5>TVA</h5> </td>
            <td align="center"><h5>PSA</h5> </td>
            <td align="center"><h5>Montant TTC</h5> </td>
	   </tr>
            
<?php


$totalcolis=0;
$nbreclt=0;
$nbrefacture=0;
$totalCA=0;
$totalCAHT=0;
$totalPSA=0;
$totalTVA=0;
//Ici on recupre la liste sans doublons des clients ayant achetes dans la periode 
 $sql2='SELECT DISTINCT  C.ID_CLIENT, C.NOM, C.ID_CATEGORIE, CAT.LIBELLE, CAT.TAUXRETFISCPRO, CAT.TAUXTVA FROM CLIENT C, SORTIE_STOCK ST, CATEGORIE CAT  WHERE C.ID_CLIENT=ST.ID_CLIENT AND C.ID_CATEGORIE=CAT.ID_CATEGORIE AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V" ORDER BY C.NOM';
$reponse2= $DataBase->query($sql2);
  while($rslt2= $reponse2->fetch())
  {
	  $colisclt=0;
	  $CA_HT=0;
	  $CA_TTC=0;
	  $TVA_CLT=0;
	  $PSA_CLT=0;
	  $nbrefactureclt=0;
	  //Ici on recupere les Achats du client de la periode

	$sql3 = 'SELECT  ST.ID_SORTIESTOCK FROM CLIENT C, SORTIE_STOCK ST  WHERE C.ID_CLIENT=ST.ID_CLIENT AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V" AND ST.ID_CLIENT="'.$rslt2['ID_CLIENT'].'"';
	$reponse3= $DataBase->query($sql3);
		while($rslt3= $reponse3->fetch())
		{
				$colisvte=0;
				$CAvte=0;
				 $sql4='SELECT AV.ID_SORTIESTOCK, AV.QTESORTIE,  AV.PRIXVENTE, A.TAUXRISTOURNE FROM  ARTICLEVENDU AV, ARTICLE A WHERE A.ID_ARTICLE=AV.ID_ARTICLE AND AV.ID_SORTIESTOCK= "'.$rslt3['ID_SORTIESTOCK'].'" ' ;
				 $reponse4= $DataBase->query($sql4);
				  while($rslt4= $reponse4->fetch())
				 {
					$colisvte=$colisvte+$rslt4['QTESORTIE'];
					$CAvte=$CAvte+$rslt4['PRIXVENTE'];
				 } 
				 
              	$colisclt=$colisclt+$colisvte;
	  			$nbrefactureclt++;
				$CA_TTC=$CA_TTC+$CAvte;
	   }
//affichage les clients
				  $CA_HT=$CA_TTC*100/(100+$rslt2['TAUXTVA']+$rslt2['TAUXRETFISCPRO']);
				  $TVA_CLT=$CA_HT*$rslt2['TAUXTVA']/100;
				  $PSA_CLT=$CA_HT*$rslt2['TAUXRETFISCPRO']/100;
?>
              <tr>
             	  <td  align="center"> <?php echo $rslt2['ID_CLIENT']; ?> </td>
                  <td  align="center"> <?php echo $rslt2['NOM']; ?> </td> 
                  <td  align="center"> <?php echo $rslt2['TAUXRETFISCPRO'].' %'; ?> </td> 
                  <td  align="center"> <?php echo number_format($nbrefactureclt, 0, ',', ' '); ?></td> 
                  <td  align="center"> <?php echo number_format($colisclt, 0, ',', ' '); ?></td> 
                  <td  align="center"> <?php echo number_format($CA_HT, 0, ',', ' '); ?></td> 
                  <td  align="center"> <?php echo number_format($TVA_CLT, 0, ',', ' '); ?></td> 
                  <td  align="center"> <?php echo number_format($PSA_CLT, 0, ',', ' '); ?></td> 
                  <td  align="center"> <?php echo number_format($CA_TTC, 0, ',', ' '); ?></td> 
              </tr>
<!--TOTAUX -->
<?php
$nbrefacture=$nbrefacture+$nbrefactureclt;
$totalcolis=$totalcolis+$colisclt;
$nbreclt++;
$totalCA=$totalCA+$CA_TTC;
$totalCAHT=$totalCAHT+$CA_HT;
$totalPSA=$totalPSA+$PSA_CLT;
$totalTVA=$totalTVA+$TVA_CLT;
}
//affichage les totaux
?>
              <tr>
                  <td  align="center" colspan=""><h5> <?php echo 'TOTAUX '?> </h5></td>
             	  <td  align="center" colspan="2"><h5> <?php echo 'NBRE DE CLIENT(S) : '.$nbreclt; ?> </h5></td>
                  <td  align="center"> <h5><?php echo number_format($nbrefacture, 0, ',', ' '); ?></h5></td> 
                  <td  align="center"> <h5><?php echo number_format($totalcolis, 0, ',', ' '); ?></h5></td> 
                  <td  align="center"> <h5><?php echo number_format($totalCAHT, 0, ',', ' '); ?></h5></td> 
                  <td  align="center"> <h5><?php echo number_format($totalTVA, 0, ',', ' '); ?></h5></td> 
                  <td  align="center"> <h5><?php echo number_format($totalPSA, 0, ',', ' '); ?></h5></td> 
                  <td  align="center"> <h5><?php echo number_format($totalCA, 0, ',', ' '); ?></h5></td> 
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