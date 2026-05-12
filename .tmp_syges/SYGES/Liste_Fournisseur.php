<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
{
 include('fpdf.php');
 include('Connexion.php');
 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(true);
 $pdf->AddPage();
 $pdf->SetTitle('Liste des Fournisseurs ');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('Fournisseurs');
 $pdf->Ln(10);
 $pdf->SetFont('arial','B',12);
 $pdf->Write(65,'                                              LISTE DES FOURNISSEURS');
 $pdf->Ln(3);
 $pdf->Write(65,'                                              ___________________________');
 $pdf->Ln(5);
 $pdf->SetFont('arial','',9);
 $pdf->Write(65,'                                                                                                                                              Date :   ');
 $pdf->Write(65,date("d/m/Y").' '.date('H:i'));
 $pdf->Ln(40);                                
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',0,15,180,30);
 $pdf->SetFont('Arial','B',9);
 $pdf->Cell(25,7,'CODE',1,0,'L',1);
 $pdf->Cell(75,7,'NOM ET PRENOM',1,0,'L',1);
 $pdf->Cell(30,7,utf8_decode('N° Tel'),1,0,'L',1);
 $pdf->Cell(35,7,utf8_decode('E-MAIL'),1,0,'L',1);
 $pdf->Cell(20,7,'STATUT',1,1,'L',1);
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
  
  $nbrefssr=0;
  $sql='SELECT * FROM FOURNISSEUR ';
  $reponse= $DataBase->query($sql);
  while($rslt= $reponse->fetch())
 {
  $pdf->Cell(25,7,$rslt['ID_FOURNISSEUR'],1,0,'L');
  $pdf->Cell(75,7,$rslt['NOM'],1);
  $pdf->Cell(30,7,$rslt['NUMTEL'],1);
  $pdf->Cell(35,7,$rslt['EMAIL'],1);
  $pdf->Cell(20,7,utf8_decode($rslt['STATUT']),1,1,'C');
  $nbrefssr++;
  }
  //nro de page
   $pdf->Write(8,'                                                                                                                                                                    Nombre de fournisseur :  ');
 $pdf->Write(8,$nbrefssr);
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
