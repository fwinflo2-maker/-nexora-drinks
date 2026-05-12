<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" || $_SESSION['habilitation']=="Comptable" || $_SESSION['habilitation']=="CC"))
{
	include('Connexion.php');
	include('fonctions.php');
	$Debut=dateFormatAnglais($_GET['DateD']);
	$Fin=dateFormatAnglais($_GET['DateF']);
	$fraisenlevement=0;
	//On recuere le nom et le code du client 
	if ($_GET['Clt'] != 'TOUS')
	{
		$sql2='SELECT  ID_CLIENT, NOM, FRAISENLEVEMENT FROM CLIENT  WHERE ID_CLIENT="'.$_GET['Clt'].'" ' ;
		$reponse2= $DataBase->query($sql2);
			while($rslt2= $reponse2->fetch())
			{
				$codeC=$rslt2['ID_CLIENT'];
				$nomC=$rslt2['NOM'];
				//$fraisenlevement=$rslt2['FRAISENLEVEMENT'];
			}
	}
	else
	{
			$codeC='TOUS';
			$nomC='Tous les Clients';
	}
?>
<!DOCTYPE html >
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultation de la Liste des Achats pour un Clients</title>
</head>

<body>
<table id='liste' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="8"><h3>Listing des Achats client</h3></td>
          </tr>
          <tr>
                <td align="center" colspan="8"> Code  : <?php echo '('.$codeC.')  '.$nomC;?></td>
          </tr>
          <tr align="center" >
          			<td><a href="Liste_Vente_Client.php?debut=<?php echo $_GET["DateD"];?>&fin=<?php echo $_GET["DateF"];?>&Clt=<?php echo $_GET['Clt'];?>"/><input type="button" value="Imprimer" /></td></a>
          		<td colspan="6"><h5>Période : Du : <?php echo dateFormatFrancais($Debut); ?>  Au : <?php echo dateFormatFrancais($Fin); ?> </h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center" ><h5>Date Vente </h5> </td>
                <td align="center" ><h5>Code </h5> </td>
                <td align="center" ><h5>Mt Produit </h5> </td>
                <td align="center" ><h5>MT FACTURE </h5> </td>
                <td align="center" ><h5>Mt Ristourne TTC </h5> </td>
                <td align="center" ><h5>Credit Ristourne </h5> </td>
				<td align="center" ><h5>Client </h5></td>
			</tr>
            
<?php
$couleur = "darkgray";
$i = 0;
$nbre=0;
$MT=0;
$MTRIS=0;
$ttcolis=0;
$mtfraisenlevement=0;
$mtfacture=0;
$ttristourne=0;
if ($_GET['Clt'] != 'TOUS')
{
	$sql='SELECT ST.ID_SORTIESTOCK, ST.DATESORTIESTOCK, ST.MTFACTURE, ST.ID_CLIENT, ST.OBSERVATION, ST.CREDITRISTOURNE, ST.STATUT, C.ID_CLIENT, C.NOM, CAT.TAUXRETFISCPRO, CAT.TAUXTVA 	FROM SORTIE_STOCK ST, CLIENT C , CATEGORIE CAT WHERE C.ID_CATEGORIE=CAT.ID_CATEGORIE AND ST.ID_CLIENT=C.ID_CLIENT AND C.ID_CLIENT="'.$_GET['Clt'].'" AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V" ORDER BY ST.ID_SORTIESTOCK' ;
}
else
{
	$sql='SELECT ST.ID_SORTIESTOCK, ST.DATESORTIESTOCK, ST.MTFACTURE, ST.ID_CLIENT, ST.OBSERVATION, ST.CREDITRISTOURNE, ST.STATUT, C.ID_CLIENT, C.NOM, CAT.TAUXRETFISCPRO, CAT.TAUXTVA 	FROM SORTIE_STOCK ST, CLIENT C , CATEGORIE CAT WHERE C.ID_CATEGORIE=CAT.ID_CATEGORIE AND ST.ID_CLIENT=C.ID_CLIENT AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V" ORDER BY ST.ID_SORTIESTOCK' ;		
}
$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			     $tva=$rslt['TAUXTVA'];
				 $psa=$rslt['TAUXRETFISCPRO'];
				//Ici on recupere les articles de chacune des ventes puis on somme les prix et benef
				 $PRIXACHAT=0;
				 $PRIXREVIENT=0;
				 $PRIXVENTE=0;
				 $BENEF=0;
				 $ttcolisvte=0;
				 $mtristourne=0;
				 $tauxristournettc=0;
				 $sql1='SELECT AV.ID_SORTIESTOCK, AV.PRIXREVIENT, AV.PRIXVENTE, AV.QTESORTIE, A.TAUXRISTOURNE FROM  ARTICLEVENDU AV, ARTICLE A WHERE A.ID_ARTICLE=AV.ID_ARTICLE AND AV.ID_SORTIESTOCK= "'.$rslt['ID_SORTIESTOCK'].'" ' ;
				 $reponse1= $DataBase->query($sql1);
				  while($rslt1= $reponse1->fetch())
				 {
					 $PRIXREVIENT= ($PRIXREVIENT + $rslt1['PRIXREVIENT']);
					 $PRIXVENTE= ($PRIXVENTE + $rslt1['PRIXVENTE']);	
					 $BENEF= ($BENEF + ($rslt1['PRIXVENTE']-$rslt1['PRIXREVIENT']));
					 $ttcolisvte=$ttcolisvte+$rslt1['QTESORTIE'];
					 $tauxristournettc=((100+$rslt['TAUXTVA']+$rslt['TAUXRETFISCPRO'])/100)*$rslt1['TAUXRISTOURNE'];
					 $mtristourne=$mtristourne+($rslt1['QTESORTIE']*number_format($tauxristournettc, 0, ',', ' '));	 
				 } 
				if ($i%2 == 0)
					$couleur = "white";
				else
					$couleur = '#CCCCCC';
				echo "<tr bgcolor=$couleur>";
				?>
                		<td  align="center"> <?php echo dateFormatFrancais($rslt['DATESORTIESTOCK']); ?> </td>
                        <td  align="center"> <?php echo $rslt['ID_SORTIESTOCK']; ?> </td>
                        <td  align="center"> <?php echo number_format($PRIXVENTE, 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['MTFACTURE'], 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($mtristourne, 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['CREDITRISTOURNE'], 0, ',', ' '); ?> </td>
                        <td  align="left"> <?php echo $rslt['NOM']; ?> </td>
                     </tr>
                <?php
				$i++;
				$nbre++;
				$MT= $MT+$PRIXVENTE;
   				$MTRIS= $MTRIS+$rslt['CREDITRISTOURNE'];
				//$mtfraisenlevement=$mtfraisenlevement+($ttcolisvte*$fraisenlevement);
				$ttristourne=$ttristourne+$mtristourne;
				$mtfacture=$mtfacture+$rslt['MTFACTURE'];
		 }
?>
<tr>

     <td  align="center" colspan="2"> <h5>TOTAUX</h5></td>
     <td  align="center"> <h5><?php echo number_format($MT, 0, ',', ' '); ?></h5> </td>
     <td  align="center"> <h5><?php echo number_format($mtfacture, 0, ',', ' '); ?></h5> </td>
     <td  align="center"> <h5><?php echo number_format($ttristourne, 0, ',', ' '); ?></h5> </td>
     <td  align="center"> <h5><?php echo number_format($MTRIS, 0, ',', ' '); ?></h5> </td>
	<td colspan="6" align="center"><h5>Nbre facture(s) :<?php echo $nbre; ?></h5> </td>
</tr>
</table>
<table id='RECAP' border="1" width="100%" align="center">
          <tr align="center" >
          		<td colspan="6"><h5>RECAPITULATIF </h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">

                <td align="center" ><h5>FAMILLE </h5> </td>
                <td align="center" ><h5>TOTAL COLIS </h5> </td>
                <td align="center" ><h5> RISTOURNE HT</h5> </td>
                <td align="center" ><h5>TVA <?php echo $tva.' %'; ?></h5> </td>
                <td align="center" ><h5>PSA <?php echo $psa.' %'; ?></h5> </td>
                <td align="center" ><h5>MONTANT RISTOURNE TTC</h5> </td>
			</tr>
  <?php          
	   	 $ttqtefamille=0;
  		 $ttristournettc=0;
		 $tttvaristourne=0;
		 $ttpsaristourne=0;
		 $ttristourne=0;
if ($_GET['Clt'] != 'TOUS')
{
   $sql3='SELECT DISTINCT A.ID_FAMILLE FROM ARTICLE A, ARTICLEVENDU AV, SORTIE_STOCK ST, CLIENT C WHERE C.ID_CLIENT=ST.ID_CLIENT AND AV.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND AV.ID_ARTICLE=A.ID_ARTICLE AND C.ID_CLIENT= "'.$_GET['Clt'].'" AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V" ' ;
}
else
{
	   $sql3='SELECT DISTINCT A.ID_FAMILLE FROM ARTICLE A, ARTICLEVENDU AV, SORTIE_STOCK ST, CLIENT C WHERE C.ID_CLIENT=ST.ID_CLIENT AND AV.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND AV.ID_ARTICLE=A.ID_ARTICLE AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V" ' ;
}
   $reponse3= $DataBase->query($sql3);
   while($rslt3= $reponse3->fetch())
   {
	     $qtefamille=0;
  		 $ristournefamille=0;
		 $ristournefamillettc=0;
		 $tvaristournefamille=0;
		 $psaristournefamille=0;
		 $tauxristournettc=0;
   
		//On somme les quantites sorties pour chaque article de la famille
if ($_GET['Clt'] != 'TOUS')
{
     $sql4='SELECT AV.QTESORTIE, A.TAUXRISTOURNE FROM ARTICLE A, ARTICLEVENDU AV, SORTIE_STOCK ST, CLIENT C WHERE C.ID_CLIENT=ST.ID_CLIENT AND AV.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND AV.ID_ARTICLE=A.ID_ARTICLE AND C.ID_CLIENT= "'.$_GET['Clt'].'" AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V" AND A.ID_FAMILLE="'.$rslt3['ID_FAMILLE'].'"' ;
}
else
{
	     $sql4='SELECT AV.QTESORTIE, A.TAUXRISTOURNE FROM ARTICLE A, ARTICLEVENDU AV, SORTIE_STOCK ST, CLIENT C WHERE C.ID_CLIENT=ST.ID_CLIENT AND AV.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND AV.ID_ARTICLE=A.ID_ARTICLE  AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V" AND A.ID_FAMILLE="'.$rslt3['ID_FAMILLE'].'"' ;
}
	 $reponse4= $DataBase->query($sql4);
	  while($rslt4= $reponse4->fetch())
	 {
		//On somme les quantites sorties pour chaque article de la famille
		$qtefamille=$qtefamille+$rslt4['QTESORTIE'];
		$tauxristournettc=((100+$tva+$psa)/100)*$rslt4['TAUXRISTOURNE'];
		$ristournefamille= $ristournefamille+($rslt4['QTESORTIE']*$rslt4['TAUXRISTOURNE']);
		$ristournefamillettc=$ristournefamillettc+($rslt4['QTESORTIE']*number_format($tauxristournettc, 0, ',', ' '));
	 }
	 
	 //AFFICHAGE 
	 	 $tvaristournefamille=$ristournefamille*$tva/100;
		 $psaristournefamille=$ristournefamille*$psa/100;
	 ?>
        <tr>
             <td  align="center"> <?php echo $rslt3['ID_FAMILLE']; ?> </td>
             <td  align="center"> <?php echo number_format($qtefamille, 0, ',', ' '); ?> </td>
             <td  align="center"> <?php echo number_format($ristournefamille, 0, ',', ' '); ?> </td>
             <td  align="center"> <?php echo number_format($tvaristournefamille, 0, ',', ' '); ?> </td>
             <td  align="center"> <?php echo number_format($psaristournefamille, 0, ',', ' '); ?> </td>
             <td  align="center"> <?php echo number_format($ristournefamillettc, 0, ',', ' '); ?> </td>
        </tr>
     <?php
	  $ttqtefamille=$ttqtefamille+$qtefamille;
  	  $ttristourne=$ttristourne+$ristournefamille;
	  $tttvaristourne=$tttvaristourne+$tvaristournefamille;
	  $ttpsaristourne=$ttpsaristourne+$psaristournefamille;
	  $ttristournettc=$ttristournettc+$ristournefamillettc;
   }
   //TOTAUX
?>
        <tr>
             <td  align="center"> <h5><?php echo 'TOTAUX'; ?></h5> </td>
             <td  align="center"> <h5><?php echo number_format($ttqtefamille, 0, ',', ' '); ?></h5> </td>
             <td  align="center"> <h5><?php echo number_format($ttristourne, 0, ',', ' '); ?></h5> </td>
             <td  align="center"> <h5><?php echo number_format($tttvaristourne, 0, ',', ' '); ?></h5> </td>
             <td  align="center"> <h5><?php echo number_format($ttpsaristourne, 0, ',', ' '); ?></h5> </td>
             <td  align="center"> <h5><?php echo number_format($ttristournettc, 0, ',', ' '); ?></h5> </td>
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