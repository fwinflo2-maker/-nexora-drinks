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
	
	$sql='SELECT * FROM APPORT WHERE DATE_APPORT BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY DATE_APPORT' ;
	
	}
	else
		if ($_GET['Statut']=='N')
		{
			$sql='SELECT * FROM APPORT WHERE DATE_APPORT BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND STATUT="N" ORDER BY DATE_APPORT' ;
		}
		else
			{
				$sql='SELECT * FROM APPORT WHERE DATE_APPORT BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND STATUT="V" ORDER BY DATE_APPORT' ;
			}
 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('ETAT DES APPORTS ');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('APPORT');
 $pdf->Ln(5);
 $pdf->SetFont('arial','B',16);
 $pdf->Write(75,'                               ETAT DES APPORTS FINANCIERS');
 $pdf->Ln(1);
 $pdf->Write(75,'                               ______________________________');
 $pdf->Ln(45);                                
 $pdf->SetFont('arial','B',10);
 $pdf->Write(2,'Periode :   Du :  ');
 $pdf->Write(2,dateFormatFrancais($Debut));
 $pdf->Write(2,'   Au :   ');
 $pdf->Write(2,dateFormatFrancais($Fin));
 $pdf->Write(2,'              Statut :  ');
 $pdf->Write(2,$_GET['Statut']);
  $pdf->Write(2,'                    Date :  ');
 $pdf->Write(2,date("d/m/y").' '.date('H:i'));
 $pdf->Ln(10);
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',15,15,180,30);
 $pdf->Cell(20,7,'DATE.',1,0,'L',1);
 $pdf->Cell(25,7,'CODE',1,0,'L',1);
 $pdf->Cell(90,7,'LIBELLE',1,0,'L',1);
 $pdf->Cell(30,7,'MONTANT',1,0,'C',1);
 $pdf->Cell(15,7,'STATUT',1,1,'C',1);
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
 
 $mt=0; 
 $nbre=0;
 $reponse= $DataBase->query($sql);
  while($rslt= $reponse->fetch())
 {
  $pdf->Cell(20,7,dateFormatFrancais($rslt['DATE_APPORT']),1,0,'C');
  $pdf->Cell(25,7,$rslt['ID_APPORT'],1);
  $pdf->Cell(90,7,$rslt['LIBELLE'],1);
  $pdf->Cell(30,7,number_format($rslt['MONTANT'], 0, ',', ' ').' F',1,0,'C');
  $pdf->Cell(15,7,utf8_decode($rslt['STATUT']),1,1,'C');
  $nbre++;
  $mt=($mt+$rslt['MONTANT']);
  }
  //ecriture des totaux
   $pdf->SetFont('arial','B',9);
  $pdf->Cell(20,7,'Totaux',1,0,'C');
  $pdf->Cell(25,7,'Nombre : '.$nbre,1);
  $pdf->Cell(90,7,'Montant Total des Mouvements de Fonds : ',1,0,'R');
  $pdf->Cell(45,7,number_format($mt, 0, ',', ' ').' F',1,1,'C');
  //nro de page
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
