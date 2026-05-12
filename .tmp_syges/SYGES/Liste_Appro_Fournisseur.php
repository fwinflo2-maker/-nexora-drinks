<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
{
 include('fpdf.php');
 include('Connexion.php');
 include('fonctions.php');
 $Debut=dateFormatAnglais($_GET["debut"]);
 $Fin=dateFormatAnglais($_GET["fin"]);

$sql='SELECT A.ID_APPRO, A.DATE_APPRO, A.ID_FOURNISSEUR, A.OBSERVATION, A.STATUT, F.ID_FOURNISSEUR, F.NOM FROM APPROVISIONNEMENT A, FOURNISSEUR F WHERE A.ID_FOURNISSEUR=F.ID_FOURNISSEUR AND A.DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY A.ID_APPRO AND F.ID_FOURNISSEUR="'.$_GET["Fssr"].'"' ;
	//ici on recupere le nom du fournisseur
$sql2='SELECT NOM FROM FOURNISSEUR WHERE ID_FOURNISSEUR="'.$_GET["Fssr"].'"' ;
$reponse2= $DataBase->query($sql2);
  while($rslt2= $reponse2->fetch())
 {
  $nomF=$rslt2['NOM'];

  }
 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('LISTE DES APPROVISIONNEMENTS D\'UN FOURNISSEUR');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('Approvisionnement d\'un fournisseur');
 $pdf->Ln(5);
 $pdf->SetFont('arial','B',16);
 $pdf->Write(80,'                      LISTE DES APPROVISIONNEMENTS');
 $pdf->Ln(5);
 $pdf->Write(80,'                                 ____________________');
 $pdf->Ln(45);                                
 $pdf->SetFont('arial','B',10);
 $pdf->Ln(5);
 $pdf->Write(2,'Code du Fournisseur                :  ');
 $pdf->Write(2,$_GET["Fssr"]);
 $pdf->Ln(5);
 $pdf->Write(2,'Nom du Fournisseur                 :  ');
 $pdf->Write(2,$nomF);
 $pdf->Ln(5);
 $pdf->Write(2,'Periode d\'approvisionnement:   Du :  ');
 $pdf->Write(2,dateFormatFrancais($Debut));
 $pdf->Write(2,'   Au :   ');
 $pdf->Write(2,dateFormatFrancais($Fin));
 $pdf->Write(2,'     Date :   ');
 $pdf->Write(2,date("d/m/y").' '.date('H:i'));
 $pdf->Ln(10);
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',0,15,180,30);
 $pdf->SetFont('Arial','B',9);
 $pdf->Cell(30,7,'DATE D\'APPRO.',1,0,'L',1);
 $pdf->Cell(20,7,'CODE',1,0,'C',1);
 $pdf->Cell(20,7,'STATUT',1,0,'L',1);
 $pdf->Cell(90,7,'OBSERVATION',1,1,'L',1);
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
  
 $nbreappro=0;
  $reponse= $DataBase->query($sql);
  while($rslt= $reponse->fetch())
 {
  $pdf->Cell(30,7,dateFormatFrancais($rslt['DATE_APPRO']),1,0,'C');
  $pdf->Cell(20,7,$rslt['ID_APPRO'],1,0,'C');
  $pdf->Cell(20,7,utf8_decode($rslt['STATUT']),1,0,'C');
  $pdf->Cell(90,7,utf8_decode($rslt['OBSERVATION']),1,1,'L');
  $nbreappro++;
  }
  //nro de page
   $pdf->Write(14,'                                                                                                                                  Nombre d\'approvisionnement :  ');
  $pdf->Write(14,$nbreappro);
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
