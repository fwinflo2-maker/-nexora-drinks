<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur"))
{
 include('fpdf.php');
 include('Connexion.php');
 include('fonctions.php');
 $Debut=dateFormatAnglais($_GET["DateD"]);
 $Fin=dateFormatAnglais($_GET["DateF"]);

 //Select ds bd
 $pdf=new FPDF('L','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('RISTOURNES A PAYER');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('LISTING RISTOURNE A PAYER');
 $pdf->Ln(3);
 $pdf->SetFont('arial','B',14);
 $pdf->Write(70,'                                                                                    RISTOURNES CLIETNS');
 $pdf->Ln(1);
 $pdf->Write(70,'                                                                                    ____________________');
 $pdf->Ln(37);                                
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
  $pdf->Image('IMG\logo.jpg',20,10,250,30);
 $pdf->Cell(20,5,'CODE ',1,0,'C',1);
 $pdf->Cell(55,5,'CLIENT',1,0,'C',1);
 $pdf->Cell(35,5,'REGIME',1,0,'C',1);
 $pdf->Cell(15,5,'TAUX PSA',1,0,'C',1);
 $pdf->Cell(25,5,'CA TTC',1,0,'C',1);
 $pdf->Cell(20,5,'NBRE FACT.',1,0,'C',1);
 $pdf->Cell(20,5,'TOTAL COLIS',1,0,'C',1);
 $pdf->Cell(25,5,'MT RIST. HT',1,0,'C',1);
 $pdf->Cell(20,5,'TVA',1,0,'C',1);
 $pdf->Cell(20,5,'PSA',1,0,'C',1);
 $pdf->Cell(30,5,'MT RIST. TTC',1,1,'C',1);
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
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
 
 
  
  $pdf->Cell(20,7,$rslt2['ID_CLIENT'],1,0,'C');
  $pdf->Cell(55,7, substr($rslt2['NOM'],0,30),1,0,'L'); 
  $pdf->Cell(35,7, substr(utf8_decode($rslt2['ID_CATEGORIE']),0,20),1,0,'C');
  $pdf->Cell(15,7,$rslt2['TAUXRETFISCPRO'].' %',1,0,'C');
  $pdf->Cell(25,7,number_format($CAclt, 0, ',', ' '),1,0,'C');
  $pdf->Cell(20,7,number_format($nbrefactureclt, 0, ',', ' '),1,0,'C');
  $pdf->Cell(20,7,number_format($colisclt, 0, ',', ' '),1,0,'C');
  $pdf->Cell(25,7,number_format($ristourneclt, 0, ',', ' '),1,0,'C');
  $pdf->Cell(20,7,number_format($tvaristourneclt, 0, ',', ' '),1,0,'C');
  $pdf->Cell(20,7,number_format($psaristourneclt, 0, ',', ' '),1,0,'C');
  $pdf->Cell(30,7,number_format($ristournecltttc, 0, ',', ' '),1,1,'C');
  
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
  $pdf->SetFont('arial','B',8);
   $pdf->Cell(20,5,'TOTAUX  ',1,0,'C');
   $pdf->Cell(105,5,'NBRE DE CLIENT(S) : '.$nbreclt,1,0,'C');
   $pdf->Cell(25,5, number_format($totalCA, 0, ',', ' '),1,0,'C');
   $pdf->Cell(20,5,number_format($nbrefacture, 0, ',', ' '),1,0,'C');
   $pdf->Cell(20,5,number_format($totalcolis, 0, ',', ' '),1,0,'C');
   $pdf->Cell(25,5,number_format($totalristourne, 0, ',', ' '),1,0,'C');
   $pdf->Cell(20,5,number_format($totaltvaristourne, 0, ',', ' '),1,0,'C');
   $pdf->Cell(20,5,number_format($totalpsaristourne, 0, ',', ' '),1,0,'C');
   $pdf->Cell(30,5,number_format($totalristournettc, 0, ',', ' '),1,1,'C');
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
