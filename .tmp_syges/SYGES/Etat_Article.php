<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" || $_SESSION['habilitation']=="OPS" || $_SESSION['habilitation']=="Comptable"|| $_SESSION['habilitation']=="Magasinier" ))
{
 include('fpdf.php');
 include('Connexion.php');
 //Select ds bd
 $pdf=new FPDF('L','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('ETAT DES STOCKS ');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('Stock');
 $pdf->Ln(3);
 $pdf->SetFont('arial','B',16);
 $pdf->Write(80,'                                                                                 ETAT DES ARTICLES');
 $pdf->Ln(3);
 $pdf->Write(80,'                                                                                 ___________________');
  $pdf->SetFont('arial','',10); 
 $pdf->Write(30,'                                                                                                                                                                                                                                                                                                    Date :   ');
 $pdf->Write(30,date("d/m/y").' '.date('H:i'));
 $pdf->Ln(20);                                
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',20,15,250,30);
 $pdf->Cell(30,7,'CODE',1,0,'C',1);
  $pdf->Cell(50,7,'CONDITIONNEMENT',1,0,'L',1);
 $pdf->Cell(70,7,'LIBELLE',1,0,'L',1);
 $pdf->Cell(30,7,'PRIX VENTE',1,0,'C',1);
 $pdf->Cell(30,7,'PRIX DETAIL',1,0,'C',1);
 $pdf->Cell(40,7,'FAMILLE',1,0,'C',1);
 $pdf->Cell(20,7,'STATUT',1,1,'L',1);
 $pdf->SetFont('Arial','',9);
 $pdf->SetTextColor(0,0,0);
  
  $nbrearticle=0;
  $sql='SELECT * FROM ARTICLE ORDER BY LIBELLE';
  $reponse= $DataBase->query($sql);
  while($rslt= $reponse->fetch())
 {
  $pdf->Cell(30,7,$rslt['ID_ARTICLE'],1,0,'C');
  $pdf->Cell(50,7,$rslt['MARQUE'].' '.$rslt['NBREBTE'],1);
  $pdf->Cell(70,7,$rslt['LIBELLE'],1);
  $pdf->Cell(30,7,$rslt['PRIXVENTE'].' FCFA',1,0,'C');
  $pdf->Cell(30,7,$rslt['PRIXDETAIL'].' FCFA',1,0,'C');
  $pdf->Cell(40,7,utf8_decode($rslt['ID_FAMILLE']),1,0,'C');
  $pdf->Cell(20,7,utf8_decode($rslt['STATUT']),1,1,'C');
  $nbrearticle++;
  }
  //nro de page

   $pdf->Write(14,'                                                                                                                                                                                                                                     Nombre d\'article :  ');
 $pdf->Write(14,$nbrearticle);
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
