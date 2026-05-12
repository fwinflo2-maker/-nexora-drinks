<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"  || $_SESSION['habilitation']=="Comptable"))
{
 include('fpdf.php');
 include('Connexion.php');
 include('fonctions.php');
 $Debut=dateFormatAnglais($_GET["debut"]);
 $Fin=dateFormatAnglais($_GET["fin"]);
 
 //on recupere les parametres
$tva=0;
$tauxpsa=0;
$exercice=0;
$tva=0;
$tca=0;
$tac=0;

 $sql2='SELECT  * FROM PARAMETRE ' ;
 $reponse2= $DataBase->query($sql2);
 while($rslt2= $reponse2->fetch())
		{
			
			$tva=$rslt2['TVA'];
			$tauxpsa=$rslt2['PSA'];
			$exercice=$rslt2['EXERCICE'];
			$tca=$rslt2['TAUXCACORRESPONDANT'];
			$tac=$rslt2['TAUXACOMPTEIB'];
			$prec=$rslt2['PRECOMPTE'];
		}

 //Select ds bd
 $pdf=new FPDF('L','mm','A4');
 $pdf->SetAutoPageBreak(true,10);
 $pdf->AddPage();
 $pdf->SetTitle('BROUILLARD DES VENTES');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('BROUILLARD DES VENTES');
 $pdf->Ln(0);
 $pdf->SetFont('Times','B',11);
 $pdf->Write(10, utf8_decode('SODIBONO '));
 $pdf->SetFont('arial','B',11);
 $pdf->Ln(5);
 $pdf->Write(15, utf8_decode('CONCESSIONNAIRE SABC  NIU. M030100015674K  RCCM: RC/NGA/2010/B/165 / Tel : 699 49 95 91  /  677 88 29 46    /MAG : TOUBORO'));
 $pdf->Ln(5);
 $pdf->SetFont('arial','B',12);
 $pdf->Ln(8);
 $pdf->Write(10,'                                                                  BROUILLARD DES VENTES    ');
 $pdf->Write(10,'Du :  ');
 $pdf->Write(10,dateFormatFrancais($Debut));
 $pdf->Write(10,'   Au :   ');
 $pdf->Write(10,dateFormatFrancais($Fin));
 $pdf->Ln(10);                                
 $pdf->SetFontSize(8);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 //$pdf->Image('IMG\logo.jpg',20,10,250,30);
 $pdf->SetFont('Times','B',8);
 $pdf->Cell(17,5,utf8_decode('N° Ordre'),1,0,'L',1);
 $pdf->Cell(25,5,'Facture',1,0,'C',1);
 $pdf->Cell(22,5,'TVA Liduide Nu',1,0,'L',1);
 $pdf->Cell(15,5,'PSA',1,0,'C',1);
 $pdf->Cell(20,5,'Liquide HT',1,0,'C',1);
 $pdf->Cell(28,5,'CA Correspondant',1,0,'C',1);
 $pdf->Cell(25,5,utf8_decode('I.B. TVA').$tva.'%',1,0,'C',1);
 $pdf->Cell(28,5,utf8_decode('I.B. Acompte ').$tac.'%',1,0,'C',1);
 $pdf->Cell(25,5,utf8_decode('I.N. TVA ').$tva.'%',1,0,'C',1);
 $pdf->Cell(28,5,utf8_decode('I.N. Acompte ').$tac.'%',1,0,'C',1);
 $pdf->Cell(25,5,utf8_decode('Precompte ').$prec.'%',1,0,'C',1);
 $pdf->Cell(25,5, utf8_decode('Montant à Verser'),1,1,'C',1); 
 $pdf->SetFont('Times','',9);
 $pdf->SetTextColor(0,0,0);

$n=1;
$nbre=0;
$liquideht=0;
$psa=0;
$tvaliquidenu=0;
$ca=0;
$tvaib=0;
$acompteib=0;
$tvain=0;
$precompte=0;
$acomptein=0;
$TTimpot=0;
$TTliquideht=0;
$TTpsa=0;
$TTtvaliquidenu=0;
$TTca=0;
$TTtvaib=0;
$TTacompteib=0;
$TTtvain=0;
$TTacomptein=0;
$TTprecompte=0;
$sql='SELECT ID_APPRO, LIQUIDEHT FROM APPROVISIONNEMENT  WHERE DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND STATUT="V" ORDER BY DATE_APPRO' ;
$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			$liquideht=$rslt['LIQUIDEHT'];
			$tvaliquidenu=$tva*$liquideht/100;
			$psa=$tauxpsa*$liquideht/100;
			$ca=$liquideht+($tca*$liquideht/100);
			$tvaib=$tva*$ca/100;
			$acompteib=$tac*$ca/100;
			$tvain=$tvaib-$tvaliquidenu;
			$acomptein=$acompteib-$psa;
			$precompte=$ca*$prec/100;
	 
		  $pdf->Cell(17,5,$n,1,0,'C');
		  $pdf->Cell(25,5,$rslt['ID_APPRO'],1,0,'C');
		  $pdf->Cell(22,5,number_format($tvaliquidenu, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(15,5,number_format($psa, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(20,5,number_format($liquideht, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,5,number_format($ca, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,5,number_format($tvaib, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,5,number_format($acompteib, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,5,number_format($tvain, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,5,number_format($acomptein, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,5,number_format($precompte, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,5,number_format($tvain+$acomptein+$precompte, 0, ',', ' '),1,1,'R');
		  
		  $n++;
				
		  $TTliquideht=$TTliquideht+$liquideht;
		  $TTpsa=$TTpsa+$psa;
		  $TTtvaliquidenu=$TTtvaliquidenu+$tvaliquidenu;
		  $TTca=$TTca+$ca;
		  $TTtvaib=$TTtvaib+$tvaib;
		  $TTacompteib=$TTacompteib+$acompteib;
		  $TTtvain=$TTtvain+$tvain;
		  $TTprecompte=$TTprecompte+$precompte;
		  $TTacomptein=$TTacomptein+$acomptein;
  }
  //Ecriture des totaux
$pdf->SetFont('Times','B',8);
$pdf->Cell(42,5,'Totaux',1,0,'C');
$pdf->Cell(22,5,number_format($TTtvaliquidenu, 0, ',', ' '),1,0,'R');
$pdf->Cell(15,5,number_format($TTpsa, 0, ',', ' '),1,0,'R');
$pdf->Cell(20,5,number_format($TTliquideht, 0, ',', ' '),1,0,'R');
$pdf->Cell(28,5,number_format($TTca, 0, ',', ' '),1,0,'R');
$pdf->Cell(25,5,number_format($TTtvaib, 0, ',', ' '),1,0,'R');
$pdf->Cell(28,5,number_format($TTacompteib, 0, ',', ' '),1,0,'R');
$pdf->Cell(25,5,number_format($TTtvain, 0, ',', ' '),1,0,'R');
$pdf->Cell(28,5,number_format($TTacomptein, 0, ',', ' '),1,0,'R');
$pdf->Cell(25,5,number_format($TTprecompte, 0, ',', ' '),1,0,'R');
$pdf->Cell(25,5,number_format($TTtvain+$TTacomptein+$TTprecompte, 0, ',', ' '),1,1,'R');
$pdf->Ln(3);
$pdf->Write(7,'                                                                                                                                                                                                                                                                                            Le Directeur');
$pdf->Ln(1);
$pdf->Write(7,'                                                                                                                                                                                                                                                                                            ___________');
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
