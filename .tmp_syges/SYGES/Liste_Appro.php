<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"  || $_SESSION['habilitation']=="Comptable" || $_SESSION['habilitation']=="Magasinier"))
{
 include('fpdf.php');
 include('Connexion.php');
 include('fonctions.php');
 $Debut=dateFormatAnglais($_GET["debut"]);
 $Fin=dateFormatAnglais($_GET["fin"]);
	if ($_GET['Statut']=='Mixte')
	{	
	$sql='SELECT A.ID_APPRO, A.DATE_APPRO, A.ID_FOURNISSEUR, A.OBSERVATION, A.STATUT,A.LIQUIDEHT,A.NBRECOLIS, F.ID_FOURNISSEUR, F.NOM FROM APPROVISIONNEMENT A, FOURNISSEUR F WHERE A.ID_FOURNISSEUR=F.ID_FOURNISSEUR AND A.DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY A.ID_APPRO' ;
	}
	else
		if ($_GET['Statut']=='N')
		{
		$sql='SELECT A.ID_APPRO, A.DATE_APPRO, A.ID_FOURNISSEUR, A.OBSERVATION, A.STATUT,A.LIQUIDEHT,A.NBRECOLIS, F.ID_FOURNISSEUR, F.NOM FROM APPROVISIONNEMENT A, FOURNISSEUR F WHERE A.STATUT ="N" AND A.ID_FOURNISSEUR=F.ID_FOURNISSEUR AND A.DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY A.ID_APPRO' ;
		}
		else
			{
			$sql='SELECT A.ID_APPRO, A.DATE_APPRO, A.ID_FOURNISSEUR, A.OBSERVATION, A.STATUT,A.LIQUIDEHT,A.NBRECOLIS, F.ID_FOURNISSEUR, F.NOM FROM APPROVISIONNEMENT A, FOURNISSEUR F WHERE A.STATUT ="V" AND A.ID_FOURNISSEUR=F.ID_FOURNISSEUR AND A.DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY A.ID_APPRO' ;
}
 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('LISTE DES APPROVISIONNEMENTS ');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('Approvisionnement');
 $pdf->Ln(15);
 $pdf->SetFont('arial','B',16);
 $pdf->Write(50,'                      LISTE DES APPROVISIONNEMENTS');
 $pdf->Ln(2);
 $pdf->Write(50,'                       _______________________________');
 $pdf->Ln(35);                                
 $pdf->SetFont('arial','B',10);
 $pdf->Write(2,'Periode Du :  ');
 $pdf->Write(2,dateFormatFrancais($Debut));
 $pdf->Write(2,'   Au :   ');
 $pdf->Write(2,dateFormatFrancais($Fin));
 $pdf->Write(2,'   Statut :  ');
 $pdf->Write(2,$_GET['Statut']);
 $pdf->Write(2,'      Date :   ');
 $pdf->Write(2,date("d/m/y").' '.date('H:i'));
 $pdf->Ln(10);
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',0,10,180,30);
 $pdf->Cell(20,7,'DATE',1,0,'L',1);
 $pdf->Cell(25,7,'CODE',1,0,'L',1);
 $pdf->Cell(45,7,'FOURNISSEUR',1,0,'L',1);
 $pdf->Cell(25,7,'CA HT',1,0,'C',1);
 $pdf->Cell(25,7,'NBRE COLIS',1,0,'C',1);
 $pdf->Cell(30,7,'OBSERVATION',1,0,'L',1);
 $pdf->Cell(10,7,'ST',1,1,'L',1);
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
  
  $ttca=0;
  $ttcolis=0;
  $nbreappro=0;
  $reponse= $DataBase->query($sql);
  while($rslt= $reponse->fetch())
 {
  $ttca=$ttca+$rslt['LIQUIDEHT'];
  $ttcolis=$ttcolis+$rslt['NBRECOLIS'];
  $pdf->Cell(20,7,dateFormatFrancais($rslt['DATE_APPRO']),1,0,'C');
  $pdf->Cell(25,7,$rslt['ID_APPRO'],1);
  $pdf->Cell(45,7,$rslt['NOM'],1);
  $pdf->Cell(25,7,number_format($rslt['LIQUIDEHT'], 0, ',', ' '),1,0,'C');
  $pdf->Cell(25,7,$rslt['NBRECOLIS'],1,0,'C');
  $pdf->Cell(30,7,utf8_decode($rslt['OBSERVATION']),1,0,'C');
  $pdf->Cell(10,7,utf8_decode($rslt['STATUT']),1,1,'L');
  $nbreappro++;
  }
  //ecriture des totaux
   $pdf->SetFont('Arial','B',10);
  $pdf->Cell(45,7,'Nbre Appro. : '.number_format($nbreappro, 0, ',', ' '),1,0,'C');
  $pdf->Cell(70,7,'CA HT : '.number_format($ttca, 0, ',', ' ').' F',1,0,'C');
  $pdf->Cell(65,7,'Nbre de Colis : '.number_format($ttcolis, 0, ',', ' '),1,0,'C');
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
