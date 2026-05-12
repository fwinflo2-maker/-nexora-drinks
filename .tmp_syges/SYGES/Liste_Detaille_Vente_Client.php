<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" || $_SESSION['habilitation']=="Comptable"  || $_SESSION['habilitation']=="CC"))
{
 include('fpdf.php');
 include('Connexion.php');
 include('fonctions.php');
 $Debut=dateFormatAnglais($_GET["debut"]);
 $Fin=dateFormatAnglais($_GET["fin"]);
	if ($_GET['Clt'] != 'TOUS')
	{
		$sql='SELECT V.DATESORTIESTOCK, V.ID_SORTIESTOCK, A.LIBELLE, A.NBREBTE, A.MARQUE, AV.ID_ARTICLE, AV.QTESORTIE, AV.PRIXREVIENT, AV.PRIXVENTE, V.ID_CLIENT, V.STATUT, C.NOM FROM SORTIE_STOCK V, ARTICLE A, ARTICLEVENDU AV, CLIENT C WHERE V.ID_CLIENT=C.ID_CLIENT AND  A.ID_ARTICLE=AV.ID_ARTICLE AND V.ID_SORTIESTOCK=AV.ID_SORTIESTOCK AND V.ID_CLIENT="'.$_GET['Clt'].'" AND V.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY V.ID_SORTIESTOCK' ;
	}
	else
	{
				$sql='SELECT V.DATESORTIESTOCK, V.ID_SORTIESTOCK, A.LIBELLE, A.NBREBTE, A.MARQUE, AV.ID_ARTICLE, AV.QTESORTIE, AV.PRIXREVIENT, AV.PRIXVENTE, V.ID_CLIENT, V.STATUT, C.NOM FROM SORTIE_STOCK V, ARTICLE A, ARTICLEVENDU AV, CLIENT C WHERE V.ID_CLIENT=C.ID_CLIENT AND  A.ID_ARTICLE=AV.ID_ARTICLE AND V.ID_SORTIESTOCK=AV.ID_SORTIESTOCK AND  V.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY V.ID_SORTIESTOCK' ;
	}


	if ($_GET['Clt'] != 'TOUS')
	{
			$nom=$rslt2['NOM'];
	}
	else
	{
			$nom='Tous les Clients';
	}
 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('ETAT DETAILLE DES ACHATS D\'UN CLIENT');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('Vente Client');
 $pdf->Ln(0);
 $pdf->SetFont('arial','B',16);
 $pdf->Write(70,'                     ETAT DETAILLE DES ACHATS  CLIENT (S)');
 $pdf->Ln(3);
 $pdf->Write(70,'                     _____________________________________');
 $pdf->Ln(45);                                
 $pdf->SetFont('arial','B',9);
 $pdf->Write(2,'Periode :   Du :  ');
 $pdf->Write(2,dateFormatFrancais($Debut));
 $pdf->Write(2,'   Au :   ');
 $pdf->Write(2,dateFormatFrancais($Fin));
 $pdf->Write(2,'               Date :  ');
 $pdf->Write(2,date("d/m/y").' '.date('H:i'));
 $pdf->Ln(5);
 $pdf->Write(2,'Code  :  ');
 $pdf->Write(2,$_GET['Clt']);
 $pdf->Write(2,'                     Client :  ');
 $pdf->Write(2,$nom);

 $pdf->Ln(7);
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFont('arial','B',8);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',5,15,180,30);
 $pdf->Cell(17,7,'DATE',1,0,'L',1);
 $pdf->Cell(20,7,'VENTE',1,0,'L',1);
 $pdf->Cell(20,7,'ARTICLE',1,0,'L',1);
 $pdf->Cell(15,7,'QTE',1,0,'C',1);
 $pdf->Cell(23,7,'PRIX REVIENT',1,0,'L',1);
 $pdf->Cell(20,7,'PRIX VENTE',1,0,'L',1);
 $pdf->Cell(24,7,'MARGE BRUTE',1,0,'L',1);
 $pdf->Cell(45,7,'CLIENT',1,1,'L',1);
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
  
$TTPV=0;
$TTPR=0;
$NbeArt=0;
$marge=0;
 $reponse= $DataBase->query($sql);
  while($rslt= $reponse->fetch())
 {
	 
    $pdf->Cell(17,7,dateFormatFrancais($rslt['DATESORTIESTOCK']),1,0,'L');
    $pdf->Cell(20,7,$rslt['ID_SORTIESTOCK'],1);
    $pdf->Cell(20,7,$rslt['LIBELLE'],1,0,'L');
    $pdf->Cell(15,7,$rslt['QTESORTIE'],1,0,'C');
	$pdf->Cell(23,7,number_format($rslt['PRIXREVIENT'], 0, ',', ' '),1,0,'C');
	$pdf->Cell(20,7,number_format($rslt['PRIXVENTE'], 0, ',', ' '),1,0,'C');
    $pdf->Cell(24,7,number_format($rslt['PRIXVENTE']-$rslt['PRIXREVIENT'], 0, ',', ' '),1,0,'C');
	$pdf->Cell(45,7,utf8_decode(substr($rslt['NOM'],0,30)),1,1,'L');

	$TTPV = ($TTPV + $rslt['PRIXVENTE']);
	$NbeArt = ($NbeArt+$rslt['QTESORTIE']);
	$marge=$marge+($rslt['PRIXVENTE']-$rslt['PRIXREVIENT']);
	$TTPR = ($TTPR + $rslt['PRIXREVIENT']);
  }
  //Ecriture des totaux
 $pdf->SetFont('arial','B',8);
 $pdf->Cell(17,7,'Totaux :',1,0,'L',1);
 $pdf->Cell(20,7,'//',1,0,'C',1);
 $pdf->Cell(20,7,'//',1,0,'C',1);
 $pdf->Cell(15,7,number_format($NbeArt, 0, ',', ' '),1,0,'C',1);
 $pdf->Cell(23,7,number_format($TTPR, 0, ',', ' '),1,0,'C',1);
 $pdf->Cell(20,7,number_format($TTPV, 0, ',', ' '),1,0,'C',1);
$pdf->Cell(24,7,number_format($marge, 0, ',', ' '),1,0,'C',1);
$pdf->Cell(45,7,'//',1,0,'C',1);
  
 
  //nro de page
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
