<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"  || $_SESSION['habilitation']=="Comptable"))
{
 include('fpdf.php');
 include('Connexion.php');
 include('fonctions.php');
	$Debut=dateFormatAnglais($_GET['DateD']);
	$Fin=dateFormatAnglais($_GET['DateF']);
	$ttva=0;
	$tpsa=0;
	$sql4='SELECT * FROM PARAMETRE';
	$reponse4= $DataBase->query($sql4);
	while($rslt4= $reponse4->fetch())
	{
			$ttva= $rslt4['TVA'];
			$tpsa=$rslt4['TAUXRETFISCPRO'];
	}
 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('ETAT DES PRELEVEMENTS SUR RISTOURNES CLIENTS');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('PSA');
 $pdf->Ln(0);
 $pdf->SetFont('arial','B',10);
 $pdf->Write(60,'                                                               ETAT DES PRELEVEMENTS SUR RISTOURNES CLIENTS');
 $pdf->Ln(1);
 $pdf->Write(60,'                                                               _________________________________________________');
 $pdf->Ln(35);                                
 $pdf->SetFont('arial','B',8);
 $pdf->Ln(5);
 $pdf->Write(2,'Periode     Du :  ');
 $pdf->Write(2,dateFormatFrancais($Debut));
 $pdf->Write(2,'   Au :   ');
 $pdf->Write(2,dateFormatFrancais($Fin));
 $pdf->Write(2,'                     Date :   ');
 $pdf->Write(2,date("d/m/y").' '.date('H:i'));
 $pdf->Ln(7);
 $pdf->SetFontSize(7);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',15,5,180,30);
 $pdf->Cell(10,5,utf8_decode('N°'),1,0,'C',1); 
 $pdf->Cell(20,5,'DATE',1,0,'C',1);
 $pdf->Cell(20,5,'FACTURE',1,0,'C',1);
 $pdf->Cell(50,5,'CLIENT',1,0,'C',1);
 $pdf->Cell(25,5,'MT RISTOURNE HT',1,0,'C',1);
 $pdf->Cell(25,5,'TVA '.$ttva.'%',1,0,'C',1);
 $pdf->Cell(25,5,'PSA '.$tpsa.'%',1,0,'C',1);
 $pdf->Cell(25,5,'MT RISTOURNE TTC',1,1,'C',1);
 $pdf->SetFont('Arial','',7);
 $pdf->SetTextColor(0,0,0);

$i = 1;
$nbre=0;
$TTC=0;
$TVA=0;
$PSA=0;
$MT=0;
$MHT=0;
$sql='SELECT ST.ID_SORTIESTOCK, ST.DATESORTIESTOCK, C.NOM, ST.CREDITRISTOURNE, C.NOM FROM CLIENT C, SORTIE_STOCK ST WHERE C.ID_CLIENT=ST.ID_CLIENT AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V" AND ST.CREDITRISTOURNE!=0 ORDER BY ST.DATESORTIESTOCK';
$reponse= $DataBase->query($sql);
while($rslt= $reponse->fetch())
{
		  //Ici on calcule le montant de la retenue fis pro et la tva
		  $mttva=0;
		  $mtpsa=0;
		  $mtttc=0;
		  $mtht=$rslt['CREDITRISTOURNE'];
		  $mttva=$mtht*$ttva/100;
		  $mtpsa=$mtht*$tpsa/100;
		  $mtttc=$mtht+$mttva+$mtpsa;
				  
		  
  $pdf->Cell(10,5,$i,1,0,'C');		  
  $pdf->Cell(20,5,dateFormatFrancais($rslt['DATESORTIESTOCK']),1,0,'C');
  $pdf->Cell(20,5,$rslt['ID_SORTIESTOCK'],1,0,'C');
  $pdf->Cell(50,5,utf8_decode($rslt['NOM']),1,0,'C');
  $pdf->Cell(25,5,number_format($mtht, 0, ',', ' '),1,0,'C');
  $pdf->Cell(25,5,number_format($mttva, 0, ',', ' '),1,0,'C');
  $pdf->Cell(25,5,number_format($mtpsa, 0, ',', ' '),1,0,'C');
  $pdf->Cell(25,5,number_format($mtttc, 0, ',', ' '),1,1,'C');

  $i++;
  $nbre++;
  $MHT= $MHT+$mtht;
  $MT= $MT+$mtttc;
  $TVA= $TVA+$mttva;
  $PSA= $PSA+$mtpsa;
  }
  //nro de page
  $pdf->SetFont('arial','B',8);
   $pdf->Cell(100,5,'TOTAUX  ',1,0,'C');
   $pdf->Cell(25,5,number_format($MHT, 0, ',', ' '),1,0,'C');
   $pdf->Cell(25,5,number_format($TVA, 0, ',', ' '),1,0,'C');
   $pdf->Cell(25,5,number_format($PSA, 0, ',', ' '),1,0,'C');
   $pdf->Cell(25,5,number_format($MT, 0, ',', ' '),1,1,'C');
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
