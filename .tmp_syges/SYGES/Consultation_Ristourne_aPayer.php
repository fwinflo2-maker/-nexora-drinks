<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur"))
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
          	<td colspan="5"><h3>Etat des Ristournes Clients</h3></td>
          </tr>
          <tr align="center" bgcolor="#CCCCCC">
          <td align="center"><a href="Etat_Ristourne_aPayer.php?DateD=<?php echo $_GET["DateD"];?>&DateF=<?php echo $_GET["DateF"];?>"/><input type="button" value="Imprimer" style="background:#FF6600" /></td></a>
                <td colspan="4"><h5>Période Du : <?php echo dateFormatFrancais($Debut); ?> Au : <?php echo dateFormatFrancais($Fin); ?></h5></td>
          </tr>
            
</table>


<!--ici on calcule les ristournes de base -->
<table id='Ristourne' border="1" width="100%" align="center">

       <tr bgcolor="#CCCCCC">
       		<td align="center"><h5>Code Client</h5> </td>
            <td align="center"><h5>Client</h5> </td>
            <td align="center"><h5>Categorie</h5> </td>
            <td align="center"><h5>Taux PSA</h5> </td>
            <td align="center"><h5>CA TTC</h5> </td>
            <td align="center"><h5>Nbre Facture(s)</h5> </td>
            <td align="center"><h5>Nbre Colis</h5> </td>
            <td align="center"><h5>Mt Ris. HT</h5> </td>
            <td align="center"><h5>TVA</h5> </td>
            <td align="center"><h5>PSA</h5> </td>
            <td align="center"><h5>Mt Ris. TTC</h5> </td>
	   </tr>
            
<?php


$totalcolis=0;
$totalristourne=0;
$nbreclt=0;
$nbrefacture=0;
$totaltvaristourne=0;
$totalpsaristourne=0;
$totalCA=0;
$totalristournettc=0;
//Ici on recupre la liste sans doublons des clients ayant achetes dans la periode 
 $sql2='SELECT DISTINCT  C.ID_CLIENT, C.NOM, C.ID_CATEGORIE, CAT.LIBELLE, CAT.TAUXRETFISCPRO, CAT.TAUXTVA FROM CLIENT C, SORTIE_STOCK ST, CATEGORIE CAT  WHERE C.ID_CLIENT=ST.ID_CLIENT AND C.ID_CATEGORIE=CAT.ID_CATEGORIE AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V" ORDER BY C.NOM';
$reponse2= $DataBase->query($sql2);
  while($rslt2= $reponse2->fetch())
  {
	  $colisclt=0;
	  $nbrefactureclt=0;
	  $ristourneclt=0;
	  $ristournecltttc=0;
	  $ristournecltttc=0;
	  $tvaristourneclt=0;
	  $psaristourneclt=0;
	  $CAclt=0;
	  //Ici on recupere les Achats du client de la periode

	$sql3 = 'SELECT  ST.ID_SORTIESTOCK FROM CLIENT C, SORTIE_STOCK ST  WHERE C.ID_CLIENT=ST.ID_CLIENT AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V" AND ST.ID_CLIENT="'.$rslt2['ID_CLIENT'].'"';
	$reponse3= $DataBase->query($sql3);
		while($rslt3= $reponse3->fetch())
		{
				$ristournevte=0;
				$ristournevtettc=0;
				$tauxristounettc=0;
				$colisvte=0;
				$CAvte=0;
				 $sql4='SELECT AV.ID_SORTIESTOCK, AV.QTESORTIE,  AV.PRIXVENTE, A.TAUXRISTOURNE FROM  ARTICLEVENDU AV, ARTICLE A WHERE A.ID_ARTICLE=AV.ID_ARTICLE AND AV.ID_SORTIESTOCK= "'.$rslt3['ID_SORTIESTOCK'].'" ' ;
				 $reponse4= $DataBase->query($sql4);
				  while($rslt4= $reponse4->fetch())
				 {
					$tauxristournettc=((100+$rslt2['TAUXTVA']+$rslt2['TAUXRETFISCPRO'])/100)*$rslt4['TAUXRISTOURNE']; 
					$ristournevtettc=$ristournevtettc+$rslt4['QTESORTIE']*number_format($tauxristournettc, 0, ',', ' ');
					$ristournevte=$ristournevte+($rslt4['QTESORTIE']*$rslt4['TAUXRISTOURNE']);
					
					$colisvte=$colisvte+$rslt4['QTESORTIE'];
					$CAvte=$CAvte+$rslt4['PRIXVENTE'];
				 } 
				 
              	$colisclt=$colisclt+$colisvte;
	  			$nbrefactureclt++;
	  			$ristourneclt=$ristourneclt+$ristournevte;
				$ristournecltttc=$ristournecltttc+$ristournevtettc;
				$CAclt=$CAclt+$CAvte;
	   }
//affichage les clients
				  $tvaristourneclt=$ristourneclt*$rslt2['TAUXTVA']/100;
	 		      $psaristourneclt=$ristourneclt*$rslt2['TAUXRETFISCPRO']/100;  
?>
              <tr>
             	  <td  align="center"> <?php echo $rslt2['ID_CLIENT']; ?> </td>
                  <td  align="center"> <?php echo $rslt2['NOM']; ?> </td> 
                  <td  align="center"> <?php echo $rslt2['ID_CATEGORIE']; ?> </td> 
                  <td  align="center"> <?php echo $rslt2['TAUXRETFISCPRO'].' %'; ?> </td> 
                  <td  align="center"> <?php echo number_format($CAclt, 0, ',', ' '); ?> </td> 
                  <td  align="center"> <?php echo number_format($nbrefactureclt, 0, ',', ' '); ?></td> 
                  <td  align="center"> <?php echo number_format($colisclt, 0, ',', ' '); ?></td> 
                  <td  align="center"> <?php echo number_format($ristourneclt, 0, ',', ' '); ?></td> 
                  <td  align="center"> <?php echo number_format($tvaristourneclt, 0, ',', ' '); ?></td> 
                  <td  align="center"> <?php echo number_format($psaristourneclt, 0, ',', ' '); ?></td> 
                  <td  align="center"> <?php echo number_format($ristournecltttc, 0, ',', ' '); ?></td> 
              </tr>
<!--Ristourne globale -->
<?php
$nbrefacture=$nbrefacture+$nbrefactureclt;
$totalcolis=$totalcolis+$colisclt;
$totalristourne=$totalristourne+$ristourneclt;
$nbreclt++;
$totaltvaristourne=$totaltvaristourne+$tvaristourneclt;
$totalpsaristourne=$totalpsaristourne+$psaristourneclt;
$totalCA=$totalCA+$CAclt;
$totalristournettc=$totalristournettc+$ristournecltttc;
}
//affichage les totaux
?>
              <tr>
                  <td  align="center" colspan=""><h5> <?php echo 'TOTAUX '?> </h5></td>
             	  <td  align="center" colspan=""><h5> <?php echo 'NBRE DE CLIENT(S) : '.$nbreclt; ?> </h5></td>
                  <td  align="center"> <?php echo '//'; ?> </td>
                  <td  align="center"> <?php echo '//'; ?> </td>
                  <td  align="center">  <h5><?php echo number_format($totalCA, 0, ',', ' '); ?> </h5></td>
                  <td  align="center"> <h5><?php echo number_format($nbrefacture, 0, ',', ' '); ?></h5></td> 
                  <td  align="center"> <h5><?php echo number_format($totalcolis, 0, ',', ' '); ?></h5></td> 
                  <td  align="center"> <h5><?php echo number_format($totalristourne, 0, ',', ' '); ?></h5></td> 
                  <td  align="center"> <h5><?php echo number_format($totaltvaristourne, 0, ',', ' '); ?></h5></td> 
                  <td  align="center"> <h5><?php echo number_format($totalpsaristourne, 0, ',', ' '); ?></h5></td> 
                  <td  align="center"> <h5><?php echo number_format($totalristournettc, 0, ',', ' '); ?></h5></td> 
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