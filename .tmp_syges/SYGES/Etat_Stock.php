<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" || $_SESSION['habilitation']=="OPS" || $_SESSION['habilitation']=="Comptable" || $_SESSION['habilitation']=="Magasinier"))
{
 include('fpdf.php');
 include('Connexion.php');
 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(true,20);
 $pdf->AddPage();
 $pdf->SetTitle('ETAT DES STOCKS ');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('Stock');
 $pdf->Ln(0);
 $pdf->SetFont('arial','B',12);
 $pdf->Write(65,'                                                                ETAT DES STOCKS');
 $pdf->Ln(1);
 $pdf->Write(65,'                                                                _________________');
 $pdf->SetFont('arial','',10);
 $pdf->Write(25,'                                                                                                                                                              Date :   ');
 $pdf->Write(25,date("d/m/Y").' '.date('H:i'));
  $pdf->Ln(4);
 $pdf->SetFont('arial','B',9);
 $pdf->Write(20,'FAMILLE : '.$_GET['famille']);
 $pdf->Ln(13);                                
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFont('Arial','B',8);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',10,5,180,30);

 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
$nbre=0;
$colismag=0;
$colisfr=0;
$cassier=0;
if ($_GET['famille']=='TOUTES')
{
	$sql = "SELECT DISTINCT ID_FAMILLE FROM ARTICLE";
}
else
{
	$sql = "SELECT DISTINCT ID_FAMILLE FROM ARTICLE  WHERE ID_FAMILLE='".$_GET['famille']."'";
}
$reponse= $DataBase->query($sql);
while($rslt= $reponse->fetch())
 {
 $pdf->SetFont('Arial','B',8);	 
 $pdf->Cell(195,4,'FAMILLE : '.$rslt['ID_FAMILLE'],1,1,'C',1);	
  
   $pdf->Cell(20,4,'CODE',1,0,'L',1);
   $pdf->Cell(25,4,'CONDITION.',1,0,'L',1);
   $pdf->Cell(45,4,'LIBELLE',1,0,'L',1);
   $pdf->Cell(40,4,'MAGASIN',1,0,'C',1);
   $pdf->Cell(40,4,'FRIGO(BTE)',1,0,'C',1);
   $pdf->Cell(25,4,'STATUT',1,1,'C',1);
   $pdf->SetFont('arial','',8);
	$sscasier=0;
	$ssnbre=0;
	$sscolis=0;
	$sscolismag=0;
	$sscolisfr=0;
	$sql2 = "SELECT * FROM ARTICLE WHERE ID_FAMILLE='".$rslt['ID_FAMILLE']."' AND STATUT='Actif' ORDER BY ID_FAMILLE";
	$reponse2= $DataBase->query($sql2);
	while($rslt2= $reponse2->fetch())
		{
		  $pdf->Cell(20,5,$rslt2['ID_ARTICLE'],1,0,'L');
		  $pdf->Cell(25,5,$rslt2['MARQUE'].' ('.$rslt2['NBREBTE'].')',1);
		  $pdf->Cell(45,5,$rslt2['LIBELLE'],1);
		  $pdf->Cell(40,5,utf8_decode($rslt2['QTESTOCK']),1,0,'C');
		  $pdf->Cell(40,5,utf8_decode($rslt2['STOCKFRIGO']),1,0,'C');
		  $pdf->Cell(25,5,utf8_decode($rslt2['STATUT']),1,1,'C');

		  $ssnbre++;
		  $sscolismag=$sscolismag+$rslt2['QTESTOCK'];
		  $sscolisfr=$sscolisfr+$rslt2['STOCKFRIGO'];
		  //on compte les casiers
			if(($rslt2['MARQUE']=="CASIER") || ($rslt2['MARQUE']=="casier")|| ($rslt2['MARQUE']=="CASIERS")|| ($rslt2['MARQUE']=="casiers"))
			{
				$sscasier=$sscasier+$rslt2['QTESTOCK'];
			}
		}
		  //ECRITURE DES SOUS TOTAUX
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(45,5,'FAMILLE : '.$rslt['ID_FAMILLE'],1,0);
		$pdf->Cell(45,5,'Article(s) : '.$ssnbre,1,0);
		$pdf->Cell(40,5,'Colis au Magasin : '.$sscolismag,1,0);
		$pdf->Cell(40,5,'Bouteille(s) au Frigo : '.$sscolisfr,1,0);
		$pdf->Cell(25,5,'Casier(s) : '.$sscasier,1,1);
		
		$nbre=$nbre+$ssnbre;
		$colismag=$colismag+$sscolismag;
		$colisfr=$colisfr+$sscolisfr;
		$cassier=$cassier+$sscasier;
  }
  //ECRITURE DES TAOTAUX
    $pdf->SetFont('Arial','B',9);
  $pdf->Cell(195,5,'TOTAUX : ',1,1,'C');
  $pdf->Cell(45,5,' Nombre d\'Article(s) : '.$nbre,1,0);
  $pdf->Cell(45,5,'Colis au Magasin : '.$colismag,1,0);
  $pdf->Cell(55,5,'Bouteille(s) au Frigo : '.$colisfr,1,0);
  $pdf->Cell(50,5,'Nombre de Casier(s) : '.$cassier,1,1);
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
