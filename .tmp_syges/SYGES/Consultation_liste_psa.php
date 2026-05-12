<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"  || $_SESSION['habilitation']=="Comptable"))
{
	include('Connexion.php');
	include('fonctions.php');
	$Debut=dateFormatAnglais($_GET['DateD']);
	$Fin=dateFormatAnglais($_GET['DateF']);
	//On recuere le taux de la categorie
	$sql2='SELECT  ID_CATEGORIE, TAUXTVA, LIBELLE, TAUXRETFISCPRO FROM CATEGORIE  WHERE ID_CATEGORIE="'.$_GET['Cat'].'" ' ;
    $reponse2= $DataBase->query($sql2);
		while($rslt2= $reponse2->fetch())
		{
			$codeC=$rslt2['ID_CATEGORIE'];
			$libelle=$rslt2['LIBELLE'];
			$tauxtva=$rslt2['TAUXTVA'];
			$tauxretfiscpro=$rslt2['TAUXRETFISCPRO'];
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultation de la Liste des Achats pour un Clients</title>
</head>

<body>
<table id='liste' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="8"><h4>ETAT DES PRELEVEMENTS SUR ACHATS CLIENTS </h4></td>
          </tr>
          <tr>
          		<td><a href="Etat_PSA.php?DateD=<?php echo $_GET["DateD"];?>&DateF=<?php echo $_GET["DateF"];?>&Cat=<?php echo $_GET['Cat'];?>"/><input type="button" value="Imprimer" /></a></td>
                <td align="center" colspan="6"><h4> Categorie/Regime : <?php echo $libelle;?></h4></td>
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
                <td align="center" ><h5>Mt Produits TTC </h5> </td>
                <td align="center" ><h5>Mt Produits HT </h5> </td>
                <td align="center" ><h5>TVA (<?php echo $tauxtva; ?>%)</h5> </td>
                <td align="center" ><h5>PSA (<?php echo $tauxretfiscpro; ?>%)</h5> </td>
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
$sql='SELECT ST.ID_SORTIESTOCK, ST.DATESORTIESTOCK, C.ID_CATEGORIE, C.NOM FROM SORTIE_STOCK ST, CLIENT C WHERE C.ID_CLIENT=ST.ID_CLIENT AND C.ID_CATEGORIE="'.$codeC.'" AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V" ORDER BY ST.ID_SORTIESTOCK' ;
$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
				//Ici on recupere les articles de chacune des ventes puis on somme les prix et benef
				 $mtttc=0;
				 $sql1='SELECT AV.ID_SORTIESTOCK, AV.PRIXREVIENT, AV.PRIXVENTE, AV.QTESORTIE, A.TAUXRISTOURNE FROM  ARTICLEVENDU AV, ARTICLE A WHERE A.ID_ARTICLE=AV.ID_ARTICLE AND AV.ID_SORTIESTOCK= "'.$rslt['ID_SORTIESTOCK'].'" ' ;
				 $reponse1= $DataBase->query($sql1);
				  while($rslt1= $reponse1->fetch())
				 {
					 $mtttc= ($mtttc + $rslt1['PRIXVENTE']);	
				 }
				 
					     //Ici on calcule le montant de la retenue fis pro et la tva
				  $mtht=0;
				  $mttva=0;
				  $mtpsa=0;
				  $mtht=$mtttc*100/(100+$tauxtva+$tauxretfiscpro);
				  $mttva=$mtht*$tauxtva/100;
				  $mtpsa=$mtht*$tauxretfiscpro/100;
				 
				  
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
                        <td  align="center"> <?php echo number_format($mtttc, 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($mtht, 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($mttva, 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($mtpsa, 0, ',', ' '); ?> </td>
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
	<td><a href="Etat_PSA.php?DateD=<?php echo $_GET["DateD"];?>&DateF=<?php echo $_GET["DateF"];?>&Cat=<?php echo $_GET['Cat'];?>"/><input type="button" value="Imprimer" /></a></td>
     <td colspan="3" align="center"> <h5>TOTAUX</h5></td>
     <td  align="center"> <h5><?php echo number_format($MT, 0, ',', ' '); ?></h5> </td>
     <td  align="center"> <h5><?php echo number_format($MHT, 0, ',', ' '); ?></h5> </td>
     <td  align="center"> <h5><?php echo number_format($TVA, 0, ',', ' '); ?></h5> </td>
     <td  align="center"> <h5><?php echo number_format($PSA, 0, ',', ' '); ?></h5> </td>
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