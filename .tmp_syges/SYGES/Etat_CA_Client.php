<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Comptable"))
{
 include('fpdf.php');
 include('Connexion.php');
 include('fonctions.php');
 $Debut=dateFormatAnglais($_GET["DateD"]);
 $Fin=dateFormatAnglais($_GET["DateF"]);

 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('CA CLTS');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('CA');
 $pdf->Ln(3);
 $pdf->SetFont('arial','B',12);
 $pdf->Write(80,'                                                                               CA CLIETNS');
 $pdf->Ln(1);
 $pdf->Write(80,'                                                                               ___________');
 $pdf->Ln(42);                                
 $pdf->SetFont('arial','B',10);
 $pdf->Ln(5);
 $pdf->Write(2,'Periode     Du :  ');
 $pdf->Write(2,dateFormatFrancais($Debut));
 $pdf->Write(2,'   Au :   ');
 $pdf->Write(2,dateFormatFrancais($Fin));
 $pdf->Write(2,'                     Date :   ');
 $pdf->Write(2,date("d/m/y").' '.date('H:i'));
 $pdf->Ln(5);
 $pdf->SetFontSize(8);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',15,15,180,30);
 $pdf->Cell(20,5,'CODE ',1,0,'C',1);
 $pdf->Cell(60,5,'CLIENT',1,0,'C',1);
 $pdf->Cell(20,5,'TAUX PSA',1,0,'C',1);
 $pdf->Cell(25,5,'CA HT',1,0,'C',1);
 $pdf->Cell(20,5,'TVA',1,0,'C',1);
 $pdf->Cell(20,5,'PSA',1,0,'C',1);
 $pdf->Cell(30,5,'CA TTC',1,1,'C',1);
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
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
  
  $pdf->Cell(20,7,$rslt2['ID_CLIENT'],1,0,'C');
  $pdf->Cell(60,7, substr($rslt2['NOM'],0,30),1,0,'L'); 
  $pdf->Cell(20,7,$rslt2['TAUXRETFISCPRO'].' %',1,0,'C');
  $pdf->Cell(25,7,number_format($CA_HT, 0, ',', ' '),1,0,'C');
  $pdf->Cell(20,7,number_format($TVA_CLT, 0, ',', ' '),1,0,'C');
  $pdf->Cell(20,7,number_format($PSA_CLT, 0, ',', ' '),1,0,'C');
  $pdf->Cell(30,7,number_format($CA_TTC, 0, ',', ' '),1,1,'C');
  
//TOTAUX
$nbrefacture=$nbrefacture+$nbrefactureclt;
$totalcolis=$totalcolis+$colisclt;
$nbreclt++;
$totalCA=$totalCA+$CA_TTC;
$totalCAHT=$totalCAHT+$CA_HT;
$totalPSA=$totalPSA+$PSA_CLT;
$totalTVA=$totalTVA+$TVA_CLT;
  }
  //affichage les totaux
  $pdf->SetFont('arial','B',8);
   $pdf->Cell(20,5,'TOTAUX  ',1,0,'C');
   $pdf->Cell(80,5,'NBRE DE CLIENT(S) : '.$nbreclt,1,0,'C');
   $pdf->Cell(25,5, number_format($totalCAHT, 0, ',', ' '),1,0,'C');
   $pdf->Cell(20,5,number_format($totalTVA, 0, ',', ' '),1,0,'C');
   $pdf->Cell(20,5,number_format($totalPSA, 0, ',', ' '),1,0,'C');
   $pdf->Cell(30,5,number_format($totalCA, 0, ',', ' '),1,1,'C');
  $pdf->SetFontSize(8);
  $pdf->Output(); 
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
