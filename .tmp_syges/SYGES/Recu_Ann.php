<?php
session_start();
if (isset ($_SESSION['habilitation']))
{
 include('fpdf.php');
 //include('fonctions.php');
 include('Connexion.php');
 //ici on recupere les infos sur l'anns
	include("Connexion.php");
	include("fonctions.php");
	$sql2 = " select *  from mouvementar  where id_operation='".$_GET['Vte']."' and heure='".$_GET['Hre']."'";
	$reponse2= $DataBase->query($sql2);
	$rslt2= $reponse2->fetch();

 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(true,10);
 $pdf->AddPage();
 $pdf->SetTitle('Recu');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('Recu');
 $pdf->Ln(25);
 $pdf->SetFont('Times','B',14);
  $pdf->SetFontSize(7);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(200,200,200);
 $pdf->Cell(195,5,'ANNULATION',1,1,'C',1);
 $pdf->Ln(0);
 $pdf->SetFont('Times','B',10);
 $pdf->Write(10, utf8_decode('     Opération    N°   :     '));
 $pdf->SetFont('Times','',10);
 $pdf->Write(10,strtoupper($_GET['Vte']));
  $pdf->SetFont('Times','B',10);
 $pdf->Write(10,'       Du :     ');
 $pdf->SetFont('Times','',10);
 $pdf->Write(10,dateFormatFrancais($rslt2['DATE']));
 $pdf->Ln(5);
 $pdf->SetFont('Times','B',10);
  $pdf->Write(10,utf8_decode('     Supprimè Par :     '));
  $pdf->SetFont('Times','',10);
  $pdf->Write(10,$rslt2['USER']);
  $pdf->SetFont('Times','B',10);
  $pdf->Write(10,utf8_decode('     Le :     '));
  $pdf->SetFont('Times','',10);
  $pdf->Write(10,dateFormatFrancais($rslt2['DATE_ANN']));
  $pdf->SetFont('Times','B',10);
  $pdf->Write(10,utf8_decode('    A :     '));
  $pdf->SetFont('Times','',10);
  $pdf->Write(10,$_GET['Hre']);
 $pdf->Ln(1);
 $pdf->SetFont('Times','B',10);
 $pdf->Write(20,'     Detenteur  :     ');
 $pdf->SetFont('Times','',10);
 $pdf->Write(20,$rslt2['DETENTEUR']);
 $pdf->SetFont('Times','B',10);
  $pdf->Write(20,'                  Observation    :     ');
 $pdf->SetFont('Times','',10);
 $pdf->Write(20,$rslt2['OPERATION']);
 $pdf->Ln(3);  
 $pdf->SetFont('Times','B',10);
 $pdf->Ln(16);                                                
 $pdf->SetFontSize(10);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(200,200,200);
 $pdf->SetFont('Times','B',8);
 $pdf->Image('IMG\logo.jpg',10,5,180,25);
 $pdf->Cell(10,5, utf8_decode('N°'),1,0,'L',1);
 $pdf->Cell(30,5,'CODE',1,0,'L',1);
 $pdf->Cell(50,5,'LIBELLE',1,0,'L',1);
  $pdf->Cell(40,5,'CONDITIONNEMENT',1,0,'L',1);
 $pdf->Cell(20,5,'QTE',1,0,'C',1);
 $pdf->Cell(20,5,'ST. AVANT',1,0,'C',1);
 $pdf->Cell(20,5,'ST. FINAL',1,1,'C',1);
 $pdf->SetFont('Times','',8);
 $pdf->SetTextColor(0,0,0);
  
$MT=0;
$i=0;	
$ttcolis=0;
$nbrecasier=0;
$sql2='SELECT  A.ID_ARTICLE,A.LIBELLE, A.MARQUE, A.NBREBTE, M.QTE, M.SI, M.SF FROM  MOUVEMENTAR M, ARTICLE A WHERE A.ID_ARTICLE=M.ID_ARTICLE AND M.ID_OPERATION ="'.$_GET['Vte'].'" AND M.HEURE="'.$_GET['Hre'].'" AND  M.OPERATION LIKE "AN%"' ;
  $reponse2= $DataBase->query($sql2);
  while($rslt2= $reponse2->fetch())
 {
		//on compte les colis
	  $ttcolis=$ttcolis+$rslt2['QTE'];
	  //on compte les casiers
	  if(($rslt2['MARQUE']=="CASIER") || ($rslt2['MARQUE']=="casier")|| ($rslt2['MARQUE']=="CASIERS")|| ($rslt2['MARQUE']=="casiers"))
	  {
		  $nbrecasier=$nbrecasier+$rslt2['QTE'];
	  }
	  $pdf->Cell(10,5,$i,1,0,'L',1); 
	  $pdf->Cell(30,5,$rslt2['ID_ARTICLE'],1,0,'L');
	  $pdf->Cell(50,5,$rslt2['LIBELLE'],1,0,'L');
	  $pdf->Cell(40,5,$rslt2['MARQUE'].' '.$rslt2['NBREBTE'],1,0,'L');
	  $pdf->Cell(20,5,number_format($rslt2['QTE'], 0, ',', ' '),1,0,'C');
	  $pdf->Cell(20,5,number_format($rslt2['SI'], 0, ',', ' '),1,0,'C');
	  $pdf->Cell(20,5,number_format($rslt2['SF'], 0, ',', ' '),1,1,'C',1);
	  $i++;
  }

    $pdf->SetFont('Times','B',8);
  	$pdf->Cell(90,5,' TOTAL COLIS  : '.number_format($ttcolis, 0, ',', ' '),1,0,'C');
	$pdf->Cell(100,5,' TOTAL CASIERS  : '.number_format($nbrecasier, 0, ',', ' '),1,0,'C');

        
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
