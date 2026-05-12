<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
{
 include('fpdf.php');
 include('Connexion.php');
 include('fonctions.php');

	$sql1 = "SELECT * FROM SAUV_STOCK WHERE ID_SAUV='".$_GET['Id']."' ";
	$reponse1= $DataBase->query($sql1);
		while($rslt1= $reponse1->fetch())
		{
			$date=dateFormatFrancais($rslt1['DATE_SAUV']);
			$heure=$rslt1['HEURE_SAUV'];
		}
 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('ETAT DES STOCKS SAUV');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('STOCK');
 $pdf->Ln(3);
 $pdf->SetFont('arial','B',16);
 $pdf->Write(70,'                                   ETAT DES STOCKS');
 $pdf->Ln(3);
 $pdf->Write(70,'                                    ________________');
 $pdf->Ln(45);                                
 $pdf->SetFont('arial','B',10);
 $pdf->Write(2,'                                                   Date :   '.$date.'    Heure :  '.$heure);
 $pdf->Ln(10);
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',0,17,180,30);
 $pdf->Cell(20,7,'CODE',1,0,'L',1);
 $pdf->Cell(40,7,'CONDITIONNEMENT',1,0,'L',1);
 $pdf->Cell(50,7,'LIBELLE',1,0,'L',1);
 $pdf->Cell(20,7,'MAGASIN',1,0,'C',1);
 $pdf->Cell(20,7,'FRIGO',1,0,'C',1);
 $pdf->Cell(20,7,'STATUT',1,1,'C',1);
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
  
$sql = "SELECT * FROM ARTICLE_SAUV WHERE ID_SAUV='".$_GET['Id']."' ";
$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{

			  $pdf->Cell(20,7,$rslt['ID_ARTICLE'],1);
			  $pdf->Cell(40,7,$rslt['MARQUE'].' '.$rslt['NBREBTE'],1,0,'L');
			  $pdf->Cell(50,7,$rslt['LIBELLE'],1);
			  $pdf->Cell(20,7,$rslt['QTESTOCK'],1,0,'C');
			  $pdf->Cell(20,7,$rslt['STOCKFRIGO'],1,0,'C');
			  $pdf->Cell(20,7,$rslt['STATUT'],1,1,'C');
  }
   
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
