<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
{
	include('Connexion.php');
	include('fonctions.php');	
	include('fpdf.php');
	 $exercice=0;
	 $sql2='SELECT * FROM PARAMETRE ' ;
	 $reponse2= $DataBase->query($sql2);
	 while($rslt2= $reponse2->fetch())
		{
			$exercice=$rslt2['EXERCICE'];
			$objanv=$rslt2['OBJANV'];
			$obfevr=$rslt2['OBFEVR'];
			$obmars=$rslt2['OBMARS'];
			$obavril=$rslt2['OBAVRI'];
			$obmai=$rslt2['OBMAI'];
			$objuin=$rslt2['OBJUIN'];
			$objuil=$rslt2['OBJUIL'];
			$obaout=$rslt2['OBAOUT'];
			$obsept=$rslt2['OBSEPT'];
			$obocto=$rslt2['OBOCTO'];
			$obnove=$rslt2['OBNOVE'];
			$obdece=$rslt2['OBDECE'];
			$obannu=$rslt2['OBANNU'];
		}
 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('OBJECTIFS');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('EVALUATION OBJECTIFS');
 $pdf->Ln(5);
 $pdf->SetFont('Times','B',12);
 //$pdf->Write(7,'                                                                ETS ALI & FRERES');
 $pdf->Ln(30);
 $pdf->Write(10,'                                        EVALUATION DES OBJECTIFS EXERCICE : ');
 $pdf->Write(10,$exercice);
 $pdf->Ln(15);                                
 $pdf->SetFontSize(10);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',10,10,180,30);
 $pdf->SetFont('Times','B',10);
 $pdf->Cell(17,9,utf8_decode('N° Ordre'),1,0,'L',1);
 $pdf->Cell(25,9,'Mois',1,0,'C',1);
 $pdf->Cell(25,9,'NBRE APPRO',1,0,'L',1);
 $pdf->Cell(30,9,'NBRE DE COLIS',1,0,'C',1);
 $pdf->Cell(40,9,'OBJECTIF MENSUEL',1,0,'C',1);
 $pdf->Cell(45,9,'TAUX DE REALISATION',1,1,'C',1); 
 $pdf->SetFont('Times','',12);
 $pdf->SetTextColor(0,0,0);


//Annuel
$TTNbreAppro=0;
$TTNbreColis=0;
//JANV
$NbreAppro1=0;
$NbreColis1=0;
//FEV
$NbreAppro2=0;
$NbreColis2=0;
//MARS
$NbreAppro3=0;
$NbreColis3=0;
//AVRIL
$NbreAppro4=0;
$NbreColis4=0;
//MAI
$NbreAppro5=0;
$NbreColis5=0;
//JUIN
$NbreAppro6=0;
$NbreColis6=0;
//JUILLET
$NbreAppro7=0;
$NbreColis7=0;
//AOUT
$NbreAppro8=0;
$NbreColis8=0;
//SEPT
$NbreAppro9=0;
$NbreColis9=0;
//OCT
$NbreAppro10=0;
$NbreColis10=0;
//NOV
$NbreAppro11=0;
$NbreColis11=0;
//DEC
$NbreAppro12=0;
$NbreColis12=0;

$sql='SELECT ID_APPRO, LIQUIDEHT,DATE_APPRO,NBRECOLIS FROM APPROVISIONNEMENT  WHERE STATUT="V" ORDER BY DATE_APPRO' ;
$reponse= $DataBase->query($sql);
while($rslt= $reponse->fetch())
{
	if (substr($rslt['DATE_APPRO'],0,4) == $exercice)
	{
		switch(substr($rslt['DATE_APPRO'],5,2))
		{
			case '01':
			{
				$NbreAppro1++;
				$NbreColis1=$NbreColis1+$rslt['NBRECOLIS'];
				break;
			}
			case '02':
			{
				$NbreAppro2++;
				$NbreColis2=$NbreColis2+$rslt['NBRECOLIS'];
				break;
			}
			case '3':
			{
				$NbreAppro3++;
				$NbreColis3=$NbreColis3+$rslt['NBRECOLIS'];
				break;
			}
			case '04':
			{
				$NbreAppro4++;
				$NbreColis4=$NbreColis4+$rslt['NBRECOLIS'];
				break;
			}	
			case '05':
			{
				$NbreAppro5++;
				$NbreColis5=$NbreColis5+$rslt['NBRECOLIS'];
				break;
			}
			case '06':
			{
				$NbreAppro6++;
				$NbreColis6=$NbreColis6+$rslt['NBRECOLIS'];
				break;
			}
			case '07':
			{
				$NbreAppro7++;
				$NbreColis7=$NbreColis7+$rslt['NBRECOLIS'];
				break;
			}
			case '08':
			{
				$NbreAppro8++;
				$NbreColis8=$NbreColis8+$rslt['NBRECOLIS'];
				break;
			}
			case '09':
			{
				$NbreAppro9++;
				$NbreColis9=$NbreColis9+$rslt['NBRECOLIS'];
				break;
			}
			case '10':
			{
				$NbreAppro10++;
				$NbreColis10=$NbreColis10+$rslt['NBRECOLIS'];
				break;
			}
			case '11':
			{
				$NbreAppro11++;
				$NbreColis11=$NbreColis11+$rslt['NBRECOLIS'];
				break;
			}
			case '12':
			{
				$NbreAppro12++;
				$NbreColis12=$NbreColis12+$rslt['NBRECOLIS'];
				break;
			}					
		}
	}
  }
	 		//affichage
			//Janvier
		  $pdf->Cell(17,9,'1',1,0,'C');
		  $pdf->Cell(25,9,'Janvier',1,0,'C');
		  $pdf->Cell(25,9,number_format($NbreAppro1, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(30,9,number_format($NbreColis1, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(40,9,number_format($objanv, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(45,9,number_format(($NbreColis1/$objanv)*100, 2, ',', ' ').'%',1,1,'R');
		  //Fevrier
		  $pdf->Cell(17,9,'2',1,0,'C');
		  $pdf->Cell(25,9,'Fevrier',1,0,'C');
		  $pdf->Cell(25,9,number_format($NbreAppro2, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(30,9,number_format($NbreColis2, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(40,9,number_format($obfevr, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(45,9,number_format(($NbreColis2/$obfevr)*100, 2, ',', ' ').'%',1,1,'R');
		  //Mars
		  $pdf->Cell(17,9,'3',1,0,'C');
		  $pdf->Cell(25,9,'Mars',1,0,'C');
		  $pdf->Cell(25,9,number_format($NbreAppro3, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(30,9,number_format($NbreColis3, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(40,9,number_format($obmars, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(45,9,number_format(($NbreColis3/$obmars)*100, 2, ',', ' ').'%',1,1,'R');
		  //Avril
		  $pdf->Cell(17,9,'4',1,0,'C');
		  $pdf->Cell(25,9,'Avril',1,0,'C');
		  $pdf->Cell(25,9,number_format($NbreAppro4, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(30,9,number_format($NbreColis4, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(40,9,number_format($obavril, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(45,9,number_format(($NbreColis4/$obavril)*100, 2, ',', ' ').'%',1,1,'R');
		  //Mai
		  $pdf->Cell(17,9,'5',1,0,'C');
		  $pdf->Cell(25,9,'Mai',1,0,'C');
		  $pdf->Cell(25,9,number_format($NbreAppro5, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(30,9,number_format($NbreColis5, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(40,9,number_format($obmai, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(45,9,number_format(($NbreColis5/$obmai)*100, 2, ',', ' ').'%',1,1,'R');
		  //Juin
		  $pdf->Cell(17,9,'6',1,0,'C');
		  $pdf->Cell(25,9,'Juin',1,0,'C');
		  $pdf->Cell(25,9,number_format($NbreAppro6, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(30,9,number_format($NbreColis6, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(40,9,number_format($objuin, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(45,9,number_format(($NbreColis6/$objuin)*100, 2, ',', ' ').'%',1,1,'R');
		  //Juillet
		  $pdf->Cell(17,9,'7',1,0,'C');
		  $pdf->Cell(25,9,'Juillet',1,0,'C');
		  $pdf->Cell(25,9,number_format($NbreAppro7, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(30,9,number_format($NbreColis7, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(40,9,number_format($objuil, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(45,9,number_format(($NbreColis7/$objuil)*100, 2, ',', ' ').'%',1,1,'R');
		  //Aout
		  $pdf->Cell(17,9,'8',1,0,'C');
		  $pdf->Cell(25,9,'Aout',1,0,'C');
		  $pdf->Cell(25,9,number_format($NbreAppro8, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(30,9,number_format($NbreColis8, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(40,9,number_format($obaout, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(45,9,number_format(($NbreColis8/$obaout)*100, 2, ',', ' ').'%',1,1,'R');
		  //Septembre
		  $pdf->Cell(17,9,'9',1,0,'C');
		  $pdf->Cell(25,9,'Septembre',1,0,'C');
		  $pdf->Cell(25,9,number_format($NbreAppro9, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(30,9,number_format($NbreColis9, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(40,9,number_format($obsept, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(45,9,number_format(($NbreColis9/$obsept)*100, 2, ',', ' ').'%',1,1,'R');
		  //Octobre
		  $pdf->Cell(17,9,'10',1,0,'C');
		  $pdf->Cell(25,9,'Octobre',1,0,'C');
		  $pdf->Cell(25,9,number_format($NbreAppro10, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(30,9,number_format($NbreColis10, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(40,9,number_format($obocto, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(45,9,number_format(($NbreColis10/$obocto)*100, 2, ',', ' ').'%',1,1,'R');
		  //Novembre
		  $pdf->Cell(17,9,'11',1,0,'C');
		  $pdf->Cell(25,9,'Novembre',1,0,'C');
		  $pdf->Cell(25,9,number_format($NbreAppro11, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(30,9,number_format($NbreColis11, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(40,9,number_format($obnove, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(45,9,number_format(($NbreColis11/$obnove)*100, 2, ',', ' ').'%',1,1,'R');
		  //Decembre
		  $pdf->Cell(17,9,'12',1,0,'C');
		  $pdf->Cell(25,9,'Decembre',1,0,'C');
		  $pdf->Cell(25,9,number_format($NbreAppro12, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(30,9,number_format($NbreColis12, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(40,9,number_format($obdece, 0, ',', ' '),1,0,'R');
		  $pdf->Cell(45,9,number_format(($NbreColis12/$obdece)*100, 2, ',', ' ').'%',1,1,'R');
		  
//calcul annuel
	$TTNbreAppro=$NbreAppro1+$NbreAppro2+$NbreAppro3+$NbreAppro4+$NbreAppro5+$NbreAppro6+$NbreAppro7+$NbreAppro8+$NbreAppro9+$NbreAppro10+$NbreAppro11+$NbreAppro12;
		$TTNbreColis=$NbreColis1+$NbreColis2+$NbreColis3+$NbreColis4+$NbreColis5+$NbreColis6+$NbreColis7+$NbreColis8+$NbreColis9+$NbreColis10+$NbreColis11+$NbreColis12;
  
  //Ecriture des totaux
$pdf->SetFont('Times','B',12);
$pdf->Cell(42,9,'Totaux',1,0,'C');
$pdf->Cell(25,9,number_format($TTNbreAppro, 0, ',', ' '),1,0,'R');
$pdf->Cell(30,9,number_format($TTNbreColis, 0, ',', ' '),1,0,'R');
$pdf->Cell(40,9,'Annuel : '.number_format($obannu, 0, ',', ' '),1,0,'R');
$pdf->Cell(45,9,number_format(($TTNbreColis/$obannu)*100, 2, ',', ' '),1,0,'R').'%';
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
