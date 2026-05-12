<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" || $_SESSION['habilitation']=="Caissier" ))
{
 include('fpdf.php');
 include('Connexion.php');
 //Select ds bd
 $pdf=new FPDF('L','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('ETAT DES STOCKS EMBALLAGES');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('Stock');
 $pdf->Ln(3);
 $pdf->SetFont('arial','B',16);
 $pdf->Write(80,'                                                          ETAT DES EMBALLAGES');
 $pdf->Ln(3);
 $pdf->Write(80,'                                                          ______________________');
 $pdf->SetFont('arial','',10); 
 $pdf->Write(30,'                                                                                                                                                                                                                                                                                                    Date :   ');
 $pdf->Write(30,date("d/m/y").' '.date('H:i'));
 $pdf->Ln(20);                                
 $pdf->SetFontSize(9);
 $pdf->SetFont('Arial','B',9);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',20,15,250,30);
 $pdf->Cell(30,7,'CODE',1,0,'L',1);
 $pdf->Cell(90,7,'LIBELLE',1,0,'L',1);
 $pdf->Cell(40,7,'FRAIS CONSIGNE',1,0,'C',1);
 $pdf->Cell(30,7,'QTE TOTAL',1,0,'C',1);
 $pdf->Cell(30,7,'QTE STOCK',1,0,'C',1);
 $pdf->Cell(30,7,'QTE CONSIGNE',1,1,'C',1);
 $pdf->SetFont('Arial','',9);
 $pdf->SetTextColor(0,0,0);
  
 $nbre=0;
 $Qtet=0;
 $Qtetst=0;
 $Qtecg=0;
  $sql='SELECT * FROM EMBALLAGE ';
  $reponse= $DataBase->query($sql);
  while($rslt= $reponse->fetch())
 {
  $pdf->Cell(30,7,$rslt['ID_EMBALLAGE'],1,0,'L');
  $pdf->Cell(90,7,$rslt['LIBELLE'],1);
  $pdf->Cell(40,7,$rslt['MT_CONSIGNE'].' FCFA',1,0,'C');
  $pdf->Cell(30,7,$rslt['QTE'],1,0,'C');
  $pdf->Cell(30,7,$rslt['QTESTOCK'],1,0,'C');
  $pdf->Cell(30,7,($rslt['QTE']-$rslt['QTESTOCK']),1,1,'C');

  $nbre++;
  $Qtet=$Qtet+$rslt['QTE'];
  $Qtetst=$Qtetst+$rslt['QTESTOCK'];
  $Qtecg=$Qtecg+($rslt['QTE']-$rslt['QTESTOCK']);
  }
  $pdf->SetFont('Arial','B',10);
  $pdf->Cell(30,7,'Totaux',1,0,'L');
  $pdf->Cell(90,7,'Nombre Emballage : '.$nbre,1);
  $pdf->Cell(40,7,'//',1,0,'C');
  $pdf->Cell(30,7,$Qtet,1,0,'C');
  $pdf->Cell(30,7,$Qtetst,1,0,'C');
  $pdf->Cell(30,7,($Qtet-$Qtetst),1,1,'C');
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
