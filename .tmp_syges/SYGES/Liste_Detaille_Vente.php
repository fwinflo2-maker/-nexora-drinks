<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
{
 include('fpdf.php');
 include('Connexion.php');
 include('fonctions.php');
 $Debut=dateFormatAnglais($_GET["debut"]);
 $Fin=dateFormatAnglais($_GET["fin"]);
	if ($_GET['Statut']=='Mixte')
	{	
	$sql='SELECT V.DATESORTIESTOCK, V.ID_SORTIESTOCK, A.LIBELLE,A.NBREBTE, A.MARQUE, AV.ID_ARTICLE, AV.QTESORTIE, AV.PRIXREVIENT, AV.PRIXVENTE FROM SORTIE_STOCK V, ARTICLE A, ARTICLEVENDU AV WHERE  A.ID_ARTICLE=AV.ID_ARTICLE AND V.ID_SORTIESTOCK=AV.ID_SORTIESTOCK AND V.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY V.ID_SORTIESTOCK' ;
	}
	else
		if ($_GET['Statut']=='N')
		{
	$sql='SELECT V.DATESORTIESTOCK, V.ID_SORTIESTOCK, A.LIBELLE,A.NBREBTE, A.MARQUE, AV.ID_ARTICLE, AV.QTESORTIE, AV.PRIXREVIENT, AV.PRIXVENTE FROM SORTIE_STOCK V, ARTICLE A, ARTICLEVENDU AV WHERE  A.ID_ARTICLE=AV.ID_ARTICLE AND V.STATUT="N" AND V.ID_SORTIESTOCK=AV.ID_SORTIESTOCK AND V.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY V.ID_SORTIESTOCK' ;
		}
		else
		{
	$sql='SELECT V.DATESORTIESTOCK, V.ID_SORTIESTOCK, A.LIBELLE, A.NBREBTE, A.MARQUE, AV.ID_ARTICLE, AV.QTESORTIE, AV.PRIXREVIENT, AV.PRIXVENTE FROM SORTIE_STOCK V, ARTICLE A, ARTICLEVENDU AV WHERE  A.ID_ARTICLE=AV.ID_ARTICLE AND V.STATUT="V" AND V.ID_SORTIESTOCK=AV.ID_SORTIESTOCK AND V.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY V.ID_SORTIESTOCK' ;
		}
 //Select ds bd
 $pdf=new FPDF('L','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('ETAT DETAILLE DES VENTES ');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('Vente');
 $pdf->Ln(10);
 $pdf->SetFont('arial','B',12);
 $pdf->Write(70,'                                                                       ETAT DETAILLE DES VENTES');
 $pdf->Ln(3);
 $pdf->Write(70,'                                                                       _________________________');
 $pdf->Ln(45);                                
 $pdf->SetFont('arial','B',9);
 $pdf->Write(2,'Periode de vente:   Du :  ');
 $pdf->Write(2,dateFormatFrancais($Debut));
 $pdf->Write(2,'   Au :   ');
 $pdf->Write(2,dateFormatFrancais($Fin));
 $pdf->Write(2,'              Statut :  ');
 $pdf->Write(2,$_GET['Statut']);
 $pdf->Write(2,'              Date :  ');
 $pdf->Write(2,date("d/m/y").' '.date('H:i'));
 $pdf->Ln(10);
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFont('arial','B',10);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',0,17,250,30);
 $pdf->Cell(25,7,'DATE',1,0,'L',1);
 $pdf->Cell(20,7,'VENTE',1,0,'L',1);
 $pdf->Cell(50,7,'CONDITIONNEMENT',1,0,'L',1);
 $pdf->Cell(50,7,'LIBELLE',1,0,'L',1);
 $pdf->Cell(20,7,'QTE',1,0,'C',1);
 $pdf->Cell(30,7,'PRIX REVIENT ',1,0,'L',1);
 $pdf->Cell(30,7,'PRIX VENTE',1,0,'L',1);
 $pdf->Cell(30,7,'BENEFICE',1,1,'L',1);
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
  
$TTPR=0;
$TTPV=0;
$TTB=0;
$NbeArt=0;;
 $reponse= $DataBase->query($sql);
  while($rslt= $reponse->fetch())
 {
	 $Benef=$rslt['PRIXVENTE']-$rslt['PRIXREVIENT'];
	 
    $pdf->Cell(25,7,dateFormatFrancais($rslt['DATESORTIESTOCK']),1,0,'L');
    $pdf->Cell(20,7,$rslt['ID_SORTIESTOCK'],1);
    $pdf->Cell(50,7,$rslt['MARQUE'].' '.$rslt['NBREBTE'],1);
    $pdf->Cell(50,7,$rslt['LIBELLE'],1,0,'L');
    $pdf->Cell(20,7,$rslt['QTESORTIE'],1,0,'C');
    $pdf->Cell(30,7,$rslt['PRIXREVIENT'].' FCFA',1,0,'C');
	$pdf->Cell(30,7,$rslt['PRIXVENTE'].' FCFA',1,0,'C');
    $pdf->Cell(30,7,$Benef.' FCFA',1,1,'C');

	$TTPR = ($TTPR + $rslt['PRIXREVIENT']);
	$TTPV = ($TTPV + $rslt['PRIXVENTE']);
	$TTB = ($TTB + $Benef);
	$NbeArt = ($NbeArt+$rslt['QTESORTIE']);
  }
  //Ecriture des totaux
 $pdf->SetFont('arial','B',10);
 $pdf->Cell(25,7,'Totaux :',1,0,'L',1);
 $pdf->Cell(20,7,'//',1,0,'C',1);
 $pdf->Cell(50,7,'//',1,0,'C',1);
 $pdf->Cell(50,7,'//',1,0,'C',1);
 $pdf->Cell(20,7,$NbeArt,1,0,'C',1);
 $pdf->Cell(30,7,$TTPR.' FCFA',1,0,'C',1);
 $pdf->Cell(30,7,$TTPV.' FCFA',1,0,'C',1);
 $pdf->Cell(30,7,$TTB.' FCFA',1,1,'C',1);
  
 
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
