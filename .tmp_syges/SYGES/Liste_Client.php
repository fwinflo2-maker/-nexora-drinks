<?php
session_start();
if (isset ($_SESSION['habilitation']))
{
 include('fpdf.php');
 include('Connexion.php');
 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('Liste des clients ');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('Clients');
 $pdf->Ln(5);
 $pdf->SetFont('arial','B',12);
 $pdf->Write(60,'                                                                   LISTE DES CLIENTS');
 $pdf->Ln(1);
 $pdf->Write(60,'                                                                  __________________');
 $pdf->Ln(5);
 $pdf->SetFont('arial','',9);
 $pdf->Write(60,'                                                                                              Date :   ');
 $pdf->Write(60,date("d/m/y").' '.date('H:i'));
 $pdf->Ln(35);                                
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',10,5,180,30);
 $pdf->SetFont('Arial','B',9);
 $pdf->Cell(20,7,'CODE',1,0,'L',1);
 $pdf->Cell(75,7,'NOM ET PRENOM',1,0,'L',1);
 $pdf->Cell(20,7,utf8_decode('N° Tel'),1,0,'L',1);
 $pdf->Cell(30,7,utf8_decode('NIU'),1,0,'L',1);
  $pdf->Cell(30,7,utf8_decode('REGIME FISC'),1,0,'L',1);
 $pdf->Cell(20,7,'STATUT',1,1,'L',1);
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
  
  $nbreclient=0;
  $sql='SELECT * FROM CLIENT ';
  $reponse= $DataBase->query($sql);
  while($rslt= $reponse->fetch())
 {
  $pdf->Cell(20,7,$rslt['ID_CLIENT'],1,0,'L');
  $pdf->Cell(75,7,$rslt['NOM'],1);
  $pdf->Cell(20,7,$rslt['NUMTEL'],1);
  $pdf->Cell(30,7,$rslt['NIU'],1);
  $pdf->Cell(30,7,$rslt['ID_CATEGORIE'],1);
  $pdf->Cell(20,7,utf8_decode($rslt['STATUT']),1,1,'C');
  $nbreclient++;
  }
  //nro de page
   $pdf->Write(8,'                                                                                                                                                                                         Nombre de client :  ');
  $pdf->Write(8,$nbreclient);
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
