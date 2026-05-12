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
 $pdf->SetTitle('Liste des utilisateurs ');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('Utilisateurs');
 $pdf->Ln(10);
 $pdf->SetFont('arial','B',12);
 $pdf->Write(65,'                                                    LISTE DES UTILISATEURS');
 $pdf->Ln(3);
 $pdf->Write(65,'                                                    ________________________');
 $pdf->Ln(1);
 $pdf->SetFont('arial','',9);
 $pdf->Write(75,'                                                                                                                                             Date :   ');
 $pdf->Write(75,date("d/m/Y").' '.date('H:i'));
 $pdf->Ln(40);                                
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',15,15,180,30);
 $pdf->SetFont('arial','B',9);
 $pdf->Cell(40,7,'LOGIN',1,0,'L',1);
 $pdf->Cell(75,7,'NOM ET PRENOM',1,0,'L',1);
 $pdf->Cell(40,7,'HABILITATION',1,0,'L',1);
 $pdf->Cell(20,7,'STATUT',1,1,'C',1);
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
  
  $nbreuser=0;
  $sql='SELECT * FROM USER ';
  $reponse= $DataBase->query($sql);
  while($rslt= $reponse->fetch())
 {
  $pdf->Cell(40,7,$rslt['LOGIN'],1,0,'L');
  $pdf->Cell(75,7,$rslt['NOM'].'  '.$rslt['PRENOM'],1,0,'L');
  $pdf->Cell(40,7,$rslt['HABILITATION'],1);
  $pdf->Cell(20,7,utf8_decode($rslt['STATUT']),1,1,'C');
  $nbreuser++;
  }
  //nro de page
   $pdf->Write(5,'                                                                                                                                                                                                                                     Nombre d\'utilisateur :  ');
  $pdf->Write(5,$nbreuser);
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
