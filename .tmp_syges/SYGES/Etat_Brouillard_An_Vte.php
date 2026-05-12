<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"  || $_SESSION['habilitation']=="Comptable"))
{
 include('fpdf.php');
 include('Connexion.php');
 include('fonctions.php');
 
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
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('BROUILLARD DES VENTES');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('BROUILLARD DES VENTES');
 $pdf->Ln(10);
 $pdf->SetFont('Times','B',14);
 //$pdf->Write(7,'                                                                                               KAMI SARL');
 $pdf->Ln(25);
 $pdf->Write(10,'                                                               BROUILLARD DES VENTES EXERCICE ');;
 $pdf->Write(10,$exercice);
 $pdf->Ln(15);                                
 $pdf->SetFontSize(8);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',20,10,250,30);
 $pdf->SetFont('Times','B',9);
 $pdf->Cell(17,7,utf8_decode('N° Ordre'),1,0,'L',1);
 $pdf->Cell(20,7,'Mois',1,0,'C',1);
 $pdf->Cell(28,7,'TVA Liduide Nu',1,0,'L',1);
 $pdf->Cell(15,7,'PSA',1,0,'C',1);
 $pdf->Cell(20,7,'Liquide HT',1,0,'C',1);
 $pdf->Cell(28,7,'CA Correspondant',1,0,'C',1);
 $pdf->Cell(25,7,utf8_decode('I.B. TVA').$tva.'%',1,0,'C',1);
 $pdf->Cell(28,7,utf8_decode('I.B. Acompte ').$tac.'%',1,0,'C',1);
 $pdf->Cell(25,7,utf8_decode('I.N. TVA ').$tva.'%',1,0,'C',1);
 $pdf->Cell(28,7,utf8_decode('I.N. Acompte ').$tac.'%',1,0,'C',1);
  $pdf->Cell(25,7,utf8_decode('Precompte ').$prec.'%',1,0,'C',1);
 $pdf->Cell(25,7, utf8_decode('Montant à Verser'),1,1,'C',1); 
 $pdf->SetFont('Times','',9);
 $pdf->SetTextColor(0,0,0);

//Annuel
$TTliquideht=0;
$TTpsa=0;
$TTtvaliquidenu=0;
$TTca=0;
$TTtvaib=0;
$TTacompteib=0;
$TTtvain=0;
$TTacomptein=0;
$TTprecompte=0;
//Janvier
$TTliquideht1=0;
$TTpsa1=0;
$TTtvaliquidenu1=0;
$TTca1=0;
$TTtvaib1=0;
$TTacompteib1=0;
$TTtvain1=0;
$TTacomptein1=0;
$TTprecompte1=0;
//Fevrier
$TTliquideht2=0;
$TTpsa2=0;
$TTtvaliquidenu2=0;
$TTca2=0;
$TTtvaib2=0;
$TTacompteib2=0;
$TTtvain2=0;
$TTacomptein2=0;
$TTprecompte2=0;
//MARS
$TTliquideht3=0;
$TTpsa3=0;
$TTtvaliquidenu3=0;
$TTca3=0;
$TTtvaib3=0;
$TTacompteib3=0;
$TTtvain3=0;
$TTacomptein3=0;
$TTprecompte3=0;
//AVRIL
$TTliquideht4=0;
$TTpsa4=0;
$TTtvaliquidenu4=0;
$TTca4=0;
$TTtvaib4=0;
$TTacompteib4=0;
$TTtvain4=0;
$TTacomptein4=0;
$TTprecompte4=0;
//MAI
$TTliquideht5=0;
$TTpsa5=0;
$TTtvaliquidenu5=0;
$TTca5=0;
$TTtvaib5=0;
$TTacompteib5=0;
$TTtvain5=0;
$TTacomptein5=0;
$TTprecompte5=0;
//JUIN
$TTliquideht6=0;
$TTpsa6=0;
$TTtvaliquidenu6=0;
$TTca6=0;
$TTtvaib6=0;
$TTacompteib6=0;
$TTtvain6=0;
$TTacomptein6=0;
$TTprecompte6=0;
//JUILLET
$TTliquideht7=0;
$TTpsa7=0;
$TTtvaliquidenu7=0;
$TTca7=0;
$TTtvaib7=0;
$TTacompteib7=0;
$TTtvain7=0;
$TTacomptein7=0;
$TTprecompte7=0;
//AOUT
$TTliquideht8=0;
$TTpsa8=0;
$TTtvaliquidenu8=0;
$TTca8=0;
$TTtvaib8=0;
$TTacompteib8=0;
$TTtvain8=0;
$TTacomptein8=0;
$TTprecompte8=0;
//SEPTEMBRE
$TTliquideht9=0;
$TTpsa9=0;
$TTtvaliquidenu9=0;
$TTca9=0;
$TTtvaib9=0;
$TTacompteib9=0;
$TTtvain9=0;
$TTacomptein9=0;
$TTprecompte9=0;
//OCTOBRE
$TTliquideht10=0;
$TTpsa10=0;
$TTtvaliquidenu10=0;
$TTca10=0;
$TTtvaib10=0;
$TTacompteib10=0;
$TTtvain10=0;
$TTacomptein10=0;
$TTprecompte10=0;
//NOVEMBRE
$TTliquideht11=0;
$TTpsa11=0;
$TTtvaliquidenu11=0;
$TTca11=0;
$TTtvaib11=0;
$TTacompteib11=0;
$TTtvain11=0;
$TTacomptein11=0;
$TTprecompte11=0;
//DECEMBRE
$TTliquideht12=0;
$TTpsa12=0;
$TTtvaliquidenu12=0;
$TTca12=0;
$TTtvaib12=0;
$TTacompteib12=0;
$TTtvain12=0;
$TTacomptein12=0;
$TTprecompte12=0;

$sql='SELECT ID_APPRO, LIQUIDEHT,DATE_APPRO FROM APPROVISIONNEMENT  WHERE STATUT="V" ORDER BY DATE_APPRO' ;
$reponse= $DataBase->query($sql);
while($rslt= $reponse->fetch())
{
	if (substr($rslt['DATE_APPRO'],0,4) == $exercice)
	{
			$liquideht=0;
			$psa=0;
			$tvaliquidenu=0;
			$ca=0;
			$tvaib=0;
			$acompteib=0;
			$tvain=0;
			$acomptein=0;
			$precompte=0;

			$liquideht=$rslt['LIQUIDEHT'];
			$tvaliquidenu=$tva*$liquideht/100;
			$psa=$tauxpsa*$liquideht/100;
			$ca=$liquideht+($tca*$liquideht/100);
			$tvaib=$tva*$ca/100;
			$acompteib=$tac*$ca/100;
			$tvain=$tvaib-$tvaliquidenu;
			$acomptein=$acompteib-$psa;
			$precompte=$ca*$prec/100;
			
			if (substr($rslt['DATE_APPRO'],5,2) == '01')
			{
				$TTliquideht1=$TTliquideht1+$liquideht;
				$TTpsa1=$TTpsa1+$psa;
				$TTtvaliquidenu1=$TTtvaliquidenu1+$tvaliquidenu;
				$TTca1=$TTca1+$ca;
				$TTtvaib1=$TTtvaib1+$tvaib;
				$TTacompteib1=$TTacompteib1+$acompteib;
				$TTtvain1=$TTtvain1+$tvain;
				$TTacomptein1=$TTacomptein1+$acomptein;
				$TTprecompte1=$TTprecompte1+$precompte;
			}
			if (substr($rslt['DATE_APPRO'],5,2) == '02')
			{
				$TTliquideht2=$TTliquideht2+$liquideht;
				$TTpsa2=$TTpsa2+$psa;
				$TTtvaliquidenu2=$TTtvaliquidenu2+$tvaliquidenu;
				$TTca2=$TTca2+$ca;
				$TTtvaib2=$TTtvaib2+$tvaib;
				$TTacompteib2=$TTacompteib2+$acompteib;
				$TTtvain2=$TTtvain2+$tvain;
				$TTacomptein2=$TTacomptein2+$acomptein;
				$TTprecompte2=$TTprecompte2+$precompte;
			}
			if (substr($rslt['DATE_APPRO'],5,2) == '03')
			{
				$TTliquideht3=$TTliquideht3+$liquideht;
				$TTpsa3=$TTpsa3+$psa;
				$TTtvaliquidenu3=$TTtvaliquidenu3+$tvaliquidenu;
				$TTca3=$TTca3+$ca;
				$TTtvaib3=$TTtvaib3+$tvaib;
				$TTacompteib3=$TTacompteib3+$acompteib;
				$TTtvain3=$TTtvain3+$tvain;
				$TTacomptein3=$TTacomptein3+$acomptein;
				$TTprecompte3=$TTprecompte3+$precompte;
			}
			if (substr($rslt['DATE_APPRO'],5,2) == '04')
			{
				$TTliquideht4=$TTliquideht4+$liquideht;
				$TTpsa4=$TTpsa4+$psa;
				$TTtvaliquidenu4=$TTtvaliquidenu4+$tvaliquidenu;
				$TTca4=$TTca4+$ca;
				$TTtvaib4=$TTtvaib4+$tvaib;
				$TTacompteib4=$TTacompteib4+$acompteib;
				$TTtvain4=$TTtvain4+$tvain;
				$TTacomptein4=$TTacomptein4+$acomptein;
				$TTprecompte4=$TTprecompte4+$precompte;
			}
			if (substr($rslt['DATE_APPRO'],5,2) == '05')
			{
				$TTliquideht5=$TTliquideht5+$liquideht;
				$TTpsa5=$TTpsa5+$psa;
				$TTtvaliquidenu5=$TTtvaliquidenu5+$tvaliquidenu;
				$TTca5=$TTca5+$ca;
				$TTtvaib5=$TTtvaib5+$tvaib;
				$TTacompteib5=$TTacompteib5+$acompteib;
				$TTtvain5=$TTtvain5+$tvain;
				$TTacomptein5=$TTacomptein5+$acomptein;
				$TTprecompte5=$TTprecompte5+$precompte;
			}
			if (substr($rslt['DATE_APPRO'],5,2) == '06')
			{
				$TTliquideht6=$TTliquideht6+$liquideht;
				$TTpsa6=$TTpsa6+$psa;
				$TTtvaliquidenu6=$TTtvaliquidenu6+$tvaliquidenu;
				$TTca6=$TTca6+$ca;
				$TTtvaib6=$TTtvaib6+$tvaib;
				$TTacompteib6=$TTacompteib6+$acompteib;
				$TTtvain6=$TTtvain6+$tvain;
				$TTacomptein6=$TTacomptein6+$acomptein;
				$TTprecompte6=$TTprecompte6+$precompte;
			}
			if (substr($rslt['DATE_APPRO'],5,2) == '07')
			{
				$TTliquideht7=$TTliquideht7+$liquideht;
				$TTpsa7=$TTpsa7+$psa;
				$TTtvaliquidenu7=$TTtvaliquidenu7+$tvaliquidenu;
				$TTca7=$TTca7+$ca;
				$TTtvaib7=$TTtvaib7+$tvaib;
				$TTacompteib7=$TTacompteib7+$acompteib;
				$TTtvain7=$TTtvain7+$tvain;
				$TTacomptein7=$TTacomptein7+$acomptein;
				$TTprecompte7=$TTprecompte7+$precompte;
			}
			if (substr($rslt['DATE_APPRO'],5,2) == '08')
			{
				$TTliquideht8=$TTliquideht8+$liquideht;
				$TTpsa8=$TTpsa8+$psa;
				$TTtvaliquidenu8=$TTtvaliquidenu8+$tvaliquidenu;
				$TTca8=$TTca8+$ca;
				$TTtvaib8=$TTtvaib8+$tvaib;
				$TTacompteib8=$TTacompteib8+$acompteib;
				$TTtvain8=$TTtvain8+$tvain;
				$TTacomptein8=$TTacomptein8+$acomptein;
				$TTprecompte8=$TTprecompte8+$precompte;
			}
			if (substr($rslt['DATE_APPRO'],5,2) == '09')
			{
				$TTliquideht9=$TTliquideht9+$liquideht;
				$TTpsa9=$TTpsa9+$psa;
				$TTtvaliquidenu9=$TTtvaliquidenu9+$tvaliquidenu;
				$TTca9=$TTca9+$ca;
				$TTtvaib9=$TTtvaib9+$tvaib;
				$TTacompteib9=$TTacompteib9+$acompteib;
				$TTtvain9=$TTtvain9+$tvain;
				$TTacomptein9=$TTacomptein9+$acomptein;
				$TTprecompte9=$TTprecompte9+$precompte;
			}
			if (substr($rslt['DATE_APPRO'],5,2) == '10')
			{
				$TTliquideht10=$TTliquideht10+$liquideht;
				$TTpsa10=$TTpsa10+$psa;
				$TTtvaliquidenu10=$TTtvaliquidenu10+$tvaliquidenu;
				$TTca10=$TTca10+$ca;
				$TTtvaib10=$TTtvaib10+$tvaib;
				$TTacompteib10=$TTacompteib10+$acompteib;
				$TTtvain10=$TTtvain10+$tvain;
				$TTacomptein10=$TTacomptein10+$acomptein;
				$TTprecompte10=$TTprecompte10+$precompte;
			}
			if (substr($rslt['DATE_APPRO'],5,2) == '11')
			{
				$TTliquideht11=$TTliquideht11+$liquideht;
				$TTpsa11=$TTpsa11+$psa;
				$TTtvaliquidenu11=$TTtvaliquidenu11+$tvaliquidenu;
				$TTca11=$TTca11+$ca;
				$TTtvaib11=$TTtvaib11+$tvaib;
				$TTacompteib11=$TTacompteib11+$acompteib;
				$TTtvain11=$TTtvain11+$tvain;
				$TTacomptein11=$TTacomptein11+$acomptein;
				$TTprecompte11=$TTprecompte11+$precompte;
			}
			if (substr($rslt['DATE_APPRO'],5,2) == '12')
			{
				$TTliquideht12=$TTliquideht12+$liquideht;
				$TTpsa12=$TTpsa12+$psa;
				$TTtvaliquidenu12=$TTtvaliquidenu12+$tvaliquidenu;
				$TTca12=$TTca12+$ca;
				$TTtvaib12=$TTtvaib12+$tvaib;
				$TTacompteib12=$TTacompteib12+$acompteib;
				$TTtvain12=$TTtvain12+$tvain;
				$TTacomptein12=$TTacomptein12+$acomptein;
				$TTprecompte12=$TTprecompte12+$precompte;
			}
		 
		}
  }
	 		//affichage
			//Janvier
		  $pdf->Cell(17,7,'1',1,0,'C');
		  $pdf->Cell(20,7,'Janvier',1,0,'C');
		  $pdf->Cell(28,7,number_format($TTtvaliquidenu1, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(15,7,number_format($TTpsa1, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(20,7,number_format($TTliquideht1, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTca1, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvaib1, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTacompteib1, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvain1, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTacomptein1, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTprecompte1, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvain1+$TTacomptein1+$TTprecompte1, 0, ',', ' '),1,1,'R');
		  //Fevrier
		  $pdf->Cell(17,7,'2',1,0,'C');
		  $pdf->Cell(20,7,'Fevrier',1,0,'C');
		  $pdf->Cell(28,7,number_format($TTtvaliquidenu2, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(15,7,number_format($TTpsa2, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(20,7,number_format($TTliquideht2, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTca2, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvaib2, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTacompteib2, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvain2, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTacomptein2, 0, ',', ' '),1,0,'R');
		   $pdf->Cell(25,7,number_format($TTprecompte2, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvain2+$TTacomptein2+$TTprecompte2, 0, ',', ' '),1,1,'R');
		  //Mars
		  $pdf->Cell(17,7,'3',1,0,'C');
		  $pdf->Cell(20,7,'Mars',1,0,'C');
		  $pdf->Cell(28,7,number_format($TTtvaliquidenu3, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(15,7,number_format($TTpsa3, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(20,7,number_format($TTliquideht3, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTca3, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvaib3, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTacompteib3, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvain3, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTacomptein3, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTprecompte3, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvain3+$TTacomptein3+$TTprecompte3, 0, ',', ' '),1,1,'R');
		  //Avril
		  $pdf->Cell(17,7,'4',1,0,'C');
		  $pdf->Cell(20,7,'Avril',1,0,'C');
		  $pdf->Cell(28,7,number_format($TTtvaliquidenu4, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(15,7,number_format($TTpsa4, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(20,7,number_format($TTliquideht4, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTca4, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvaib4, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTacompteib4, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvain4, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTacomptein4, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTprecompte4, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvain4+$TTacomptein4+$TTprecompte4, 0, ',', ' '),1,1,'R');
		  //Mai
		  $pdf->Cell(17,7,'5',1,0,'C');
		  $pdf->Cell(20,7,'Mai',1,0,'C');
		  $pdf->Cell(28,7,number_format($TTtvaliquidenu5, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(15,7,number_format($TTpsa5, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(20,7,number_format($TTliquideht5, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTca5, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvaib5, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTacompteib5, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvain5, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTacomptein5, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTprecompte5, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvain5+$TTacomptein5+$TTprecompte5, 0, ',', ' '),1,1,'R');
		  //Juin
		  $pdf->Cell(17,7,'6',1,0,'C');
		  $pdf->Cell(20,7,'Juin',1,0,'C');
		  $pdf->Cell(28,7,number_format($TTtvaliquidenu6, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(15,7,number_format($TTpsa6, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(20,7,number_format($TTliquideht6, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTca6, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvaib6, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTacompteib6, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvain6, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTacomptein6, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTprecompte6, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvain6+$TTacomptein6+$TTprecompte6, 0, ',', ' '),1,1,'R');
		  //Juille
		  $pdf->Cell(17,7,'7',1,0,'C');
		  $pdf->Cell(20,7,'Juillet',1,0,'C');
		  $pdf->Cell(28,7,number_format($TTtvaliquidenu7, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(15,7,number_format($TTpsa7, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(20,7,number_format($TTliquideht7, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTca7, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvaib7, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTacompteib7, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvain7, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTacomptein7, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTprecompte7, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvain7+$TTacomptein7+$TTprecompte7, 0, ',', ' '),1,1,'R');
		  //Aout
		  $pdf->Cell(17,7,'8',1,0,'C');
		  $pdf->Cell(20,7,'Aout',1,0,'C');
		  $pdf->Cell(28,7,number_format($TTtvaliquidenu8, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(15,7,number_format($TTpsa8, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(20,7,number_format($TTliquideht8, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTca8, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvaib8, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTacompteib8, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvain8, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTacomptein8, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTprecompte8, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvain8+$TTacomptein8+$TTprecompte8, 0, ',', ' '),1,1,'R');
		  //Septembre
		  $pdf->Cell(17,7,'9',1,0,'C');
		  $pdf->Cell(20,7,'Septembre',1,0,'C');
		  $pdf->Cell(28,7,number_format($TTtvaliquidenu9, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(15,7,number_format($TTpsa9, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(20,7,number_format($TTliquideht9, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTca9, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvaib9, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTacompteib9, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvain9, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTacomptein9, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTprecompte9, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvain9+$TTacomptein9+$TTprecompte9, 0, ',', ' '),1,1,'R');
		  //Octobre
		  $pdf->Cell(17,7,'10',1,0,'C');
		  $pdf->Cell(20,7,'Octobre',1,0,'C');
		  $pdf->Cell(28,7,number_format($TTtvaliquidenu10, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(15,7,number_format($TTpsa10, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(20,7,number_format($TTliquideht10, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTca10, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvaib10, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTacompteib10, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvain10, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTacomptein10, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTprecompte10, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvain10+$TTacomptein10+$TTprecompte10, 0, ',', ' '),1,1,'R');
		  //Novembre
		  $pdf->Cell(17,7,'11',1,0,'C');
		  $pdf->Cell(20,7,'Novembre',1,0,'C');
		  $pdf->Cell(28,7,number_format($TTtvaliquidenu11, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(15,7,number_format($TTpsa11, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(20,7,number_format($TTliquideht11, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTca11, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvaib11, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTacompteib11, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvain11, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTacomptein11, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTprecompte11, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvain11+$TTacomptein11+$TTprecompte11, 0, ',', ' '),1,1,'R');
		  //Decembre
		  $pdf->Cell(17,7,'12',1,0,'C');
		  $pdf->Cell(20,7,'Decembre',1,0,'C');
		  $pdf->Cell(28,7,number_format($TTtvaliquidenu12, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(15,7,number_format($TTpsa12, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(20,7,number_format($TTliquideht12, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTca12, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvaib12, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTacompteib12, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvain12, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(28,7,number_format($TTacomptein12, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTprecompte12, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(25,7,number_format($TTtvain12+$TTacomptein12+$TTprecompte12, 0, ',', ' '),1,1,'R');
		  
//calcul annuel
		 		$TTliquideht=$TTliquideht1+$TTliquideht2+$TTliquideht3+$TTliquideht4+$TTliquideht5+$TTliquideht6+$TTliquideht7+$TTliquideht8+$TTliquideht9+$TTliquideht10+$TTliquideht11+$TTliquideht12;
				$TTpsa=$TTpsa1+$TTpsa2+$TTpsa3+$TTpsa4+$TTpsa5+$TTpsa6+$TTpsa7+$TTpsa8+$TTpsa9+$TTpsa10+$TTpsa11+$TTpsa12;
				$TTtvaliquidenu=$TTtvaliquidenu1+$TTtvaliquidenu2+$TTtvaliquidenu3+$TTtvaliquidenu4+$TTtvaliquidenu5+$TTtvaliquidenu6+$TTtvaliquidenu7+$TTtvaliquidenu8+$TTtvaliquidenu9+$TTtvaliquidenu10+$TTtvaliquidenu11+$TTtvaliquidenu12;
				$TTca=$TTca1+$TTca2+$TTca3+$TTca4+$TTca5+$TTca6+$TTca7+$TTca8+$TTca9+$TTca10+$TTca11+$TTca12;
				$TTtvaib=$TTtvaib1+$TTtvaib2+$TTtvaib3+$TTtvaib4+$TTtvaib5+$TTtvaib6+$TTtvaib7+$TTtvaib8+$TTtvaib9+$TTtvaib10+$TTtvaib11+$TTtvaib12;
				$TTacompteib=$TTacompteib1+$TTacompteib2+$TTacompteib3+$TTacompteib4+$TTacompteib5+$TTacompteib6+$TTacompteib7+$TTacompteib8+$TTacompteib9+$TTacompteib10+$TTacompteib11+$TTacompteib12;
				$TTtvain=$TTtvain1+$TTtvain2+$TTtvain3+$TTtvain4+$TTtvain5+$TTtvain6+$TTtvain7+$TTtvain8+$TTtvain9+$TTtvain10+$TTtvain11+$TTtvain12;
				$TTacomptein=$TTacomptein1+$TTacomptein2+$TTacomptein3+$TTacomptein4+$TTacomptein5+$TTacomptein6+$TTacomptein7+$TTacomptein8+$TTacomptein9+$TTacomptein10+$TTacomptein11+$TTacomptein12;
				$TTprecompte=$TTprecompte1+$TTprecompte2+$TTprecompte3+$TTprecompte4+$TTprecompte5+$TTprecompte6+$TTprecompte7+$TTprecompte8+$TTprecompte9+$TTprecompte10+$TTprecompte11+$TTprecompte12;
  
  //Ecriture des totaux
$pdf->SetFont('Times','B',9);
$pdf->Cell(37,7,'Totaux',1,0,'C');
$pdf->Cell(28,7,number_format($TTtvaliquidenu, 0, ',', ' '),1,0,'R');
$pdf->Cell(15,7,number_format($TTpsa, 0, ',', ' '),1,0,'R');
$pdf->Cell(20,7,number_format($TTliquideht, 0, ',', ' '),1,0,'R');
$pdf->Cell(28,7,number_format($TTca, 0, ',', ' '),1,0,'R');
$pdf->Cell(25,7,number_format($TTtvaib, 0, ',', ' '),1,0,'R');
$pdf->Cell(28,7,number_format($TTacompteib, 0, ',', ' '),1,0,'R');
$pdf->Cell(25,7,number_format($TTtvain, 0, ',', ' '),1,0,'R');
$pdf->Cell(28,7,number_format($TTacomptein, 0, ',', ' '),1,0,'R');
$pdf->Cell(25,7,number_format($TTprecompte, 0, ',', ' '),1,0,'R');
$pdf->Cell(25,7,number_format($TTtvain+$TTacomptein+$TTprecompte, 0, ',', ' '),1,1,'R');
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
