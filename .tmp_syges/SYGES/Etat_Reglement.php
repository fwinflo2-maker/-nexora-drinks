<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
{
 include('fpdf.php');
 include('Connexion.php');
 include('fonctions.php');
$Debut=dateFormatAnglais($_GET['DateD']);
$Fin=dateFormatAnglais($_GET['DateF']);
if ($_GET['Statut']=='Mixte')
	{	
	$sql='SELECT R.ID_REGLEMENT, R.DATEAVANCE, R.ID_SORTIESTOCK, R.STATUT, R.MONTANT, R.MTAVANCE, R.MTRESTANT, R.USER, ST.DATESORTIESTOCK, C.NOM FROM CLIENT C, SORTIE_STOCK ST, REGLEMENT R WHERE C.ID_CLIENT=ST.ID_CLIENT AND ST.ID_SORTIESTOCK=R.ID_SORTIESTOCK AND R.DATEAVANCE BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY R.ID_REGLEMENT' ;
	}
	else
		if ($_GET['Statut']=='Avance')
		{
		$sql='SELECT R.ID_REGLEMENT, R.DATEAVANCE, R.ID_SORTIESTOCK, R.STATUT, R.MONTANT, R.MTAVANCE, R.MTRESTANT, R.USER, ST.DATESORTIESTOCK, C.NOM FROM CLIENT C, SORTIE_STOCK ST, REGLEMENT R WHERE C.ID_CLIENT=ST.ID_CLIENT AND ST.ID_SORTIESTOCK=R.ID_SORTIESTOCK AND R.STATUT="Avance" AND R.DATEAVANCE BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY R.ID_REGLEMENT' ;
		}
		else
		{
		$sql='SELECT R.ID_REGLEMENT, R.DATEAVANCE, R.ID_SORTIESTOCK, R.STATUT, R.MONTANT, R.MTAVANCE, R.MTRESTANT, R.USER, ST.DATESORTIESTOCK, C.NOM FROM CLIENT C, SORTIE_STOCK ST, REGLEMENT R WHERE C.ID_CLIENT=ST.ID_CLIENT AND ST.ID_SORTIESTOCK=R.ID_SORTIESTOCK AND R.STATUT="Paye" AND R.DATEAVANCE BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY R.ID_REGLEMENT' ;
		}
 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('ETAT DES REGLEMENTS ');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('Reglement');
 $pdf->Ln(5);
 $pdf->SetFont('arial','B',12);
 $pdf->Write(80,'                                                 ETAT DES REGLEMENTS');
 $pdf->Ln(3);
 $pdf->Write(80,'                                                 ______________________');
 $pdf->Ln(45);                                
 $pdf->SetFont('arial','B',10);
 $pdf->Write(2,'Periode :  ');
 $pdf->Write(2,dateFormatFrancais($Debut));
 $pdf->Write(2,'   Au :   ');
 $pdf->Write(2,dateFormatFrancais($Fin));
 $pdf->Write(2,'              Statut :  ');
 $pdf->Write(2,$_GET['Statut']);
 $pdf->Write(2,'              Date :  ');
 $pdf->Write(2,date("d/m/Y").' '.date('H:i'));
 $pdf->Ln(10);
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->SetFont('arial','B',9);
 $pdf->Image('IMG\logo.jpg',10,17,180,30);
 $pdf->Cell(20,7,'DATE',1,0,'C',1);
 $pdf->Cell(25,7,'FACTURE',1,0,'C',1);
 $pdf->Cell(45,7,'CLIENT',1,0,'L',1);
 $pdf->Cell(20,7,'MONTANT ',1,0,'C',1);
 $pdf->Cell(20,7,'AVANCE',1,0,'C',1);
 $pdf->Cell(20,7,'RESTE',1,0,'C',1);
 $pdf->Cell(15,7,'STATUT',1,0,'C',1);
 $pdf->Cell(30,7,'UTILISATEUR',1,1,'C',1);
 $pdf->SetFont('Arial','',9);
 $pdf->SetTextColor(0,0,0);
  
 $montanttt=0;
 $avancett=0;
 $restett=0;
 $nbre=0;
 $reponse= $DataBase->query($sql);
  while($rslt= $reponse->fetch())
 {
	  $pdf->Cell(20,7,dateFormatFrancais($rslt['DATEAVANCE']),1,0,'C');
	  $pdf->Cell(25,7,$rslt['ID_SORTIESTOCK'],1,0,'C');
	  $pdf->Cell(45,7,$rslt['NOM'],1);
	  $pdf->Cell(20,7,number_format($rslt['MONTANT'], 0, ',', ' '),1,0,'C');
	  $pdf->Cell(20,7,number_format($rslt['MTAVANCE'], 0, ',', ' '),1,0,'C');
	  $pdf->Cell(20,7,number_format($rslt['MTRESTANT'], 0, ',', ' '),1,0,'C');
	  $pdf->Cell(15,7,utf8_decode($rslt['STATUT']),1,0,'C');
	  $pdf->Cell(30,7,utf8_decode($rslt['USER']),1,1,'C');
	  $nbre++;
	  $montanttt=$montanttt+$rslt['MONTANT'];
	  $avancett=$avancett+$rslt['MTAVANCE'];
	  $restett=$restett+$rslt['MTRESTANT'];
  }
  //Ecriture des totaux
 $pdf->SetFont('arial','B',9);
 $pdf->Cell(90,7,'Nombre de Reglement(s) : '.$nbre,1,0,'C',1);
 $pdf->Cell(20,7,number_format($montanttt, 0, ',', ' '),1,0,'C',1);
 $pdf->Cell(20,7,number_format($avancett, 0, ',', ' '),1,0,'C',1);
 $pdf->Cell(20,7,number_format( $restett, 0, ',', ' '),1,0,'C',1);
 $pdf->Cell(45,7,'//',1,1,'C',1);
 $pdf->Ln(3);
 $pdf->Write(25,'                                                                                                                                                     LA DIRECTION ');
  $pdf->Ln(1);
  $pdf->Write(25,'                                                                                                                                                      ____________');
 
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
