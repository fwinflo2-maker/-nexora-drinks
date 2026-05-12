<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur"))
{
 include('fpdf.php');
 include('Connexion.php');
 //Select ds bd
 $pdf=new FPDF('L','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('EVALUATION DES STOCKS ');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('Stock');
 $pdf->SetFont('arial','B',16);
 $pdf->Write(65,'                                                        EVALUATION CHIFFREE DES STOCKS');
 $pdf->Ln(1);
 $pdf->Write(65,'                                                        ________________________________');
 $pdf->Ln(25);
 $pdf->SetFont('arial','',10);
 $pdf->Ln(2);
 $pdf->Write(25,'                                                                                                                   Date :   ');
 $pdf->Write(25,date("d/m/Y").' '.date('H:i'));
 $pdf->Ln(18);                                
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFont('Arial','B',9);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',20,5,250,30);
 
 $pdf->Cell(110,7,'ARTICLE',1,0,'C',1);
 $pdf->Cell(60,7,'MAGASIN',1,0,'C',1);
 $pdf->Cell(60,7,'FRIGO',1,0,'C',1);
 $pdf->Cell(35,7,'',1,1,'C',1);
 
 $pdf->Cell(30,7,'CODE',1,0,'L',1);
 $pdf->Cell(30,7,'CONDITION.',1,0,'L',1);
 $pdf->Cell(50,7,'LIBELLE',1,0,'L',1);
 $pdf->Cell(15,7,'QTE',1,0,'C',1);
 $pdf->Cell(15,7,'PR',1,0,'C',1);
 $pdf->Cell(30,7,'MONTANT',1,0,'C',1);
 $pdf->Cell(15,7,'QTE',1,0,'C',1);
 $pdf->Cell(15,7,'PR',1,0,'C',1);
 $pdf->Cell(30,7,'MONTANT',1,0,'C',1);
 $pdf->Cell(35,7,'MONTANT TOTAL',1,1,'C',1);
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
  
 $totalcolis=0;
 $totalprcolis=0;
 $totalprbtle=0;
 $totalbtle=0;
 $motantcolis=0;
 $motantbtle=0;
 $montanttotal=0;
  $nbrearticle=0;
  $sql = "SELECT * FROM ARTICLE ORDER BY ID_FAMILLE";
  $reponse= $DataBase->query($sql);
  while($rslt= $reponse->fetch())
 {
	 
  $prfrigo=number_format($rslt['PRIXREVIENT']/$rslt['NBREBTE'], 0, ',', ' ');
  $mtmag=$rslt['PRIXREVIENT']*$rslt['QTESTOCK'];
  $mtfr=$prfrigo*$rslt['STOCKFRIGO'];
  
  
  $pdf->Cell(30,7,$rslt['ID_ARTICLE'],1,0,'L');
  $pdf->Cell(30,7,$rslt['MARQUE'].' '.$rslt['NBREBTE'],1);
  $pdf->Cell(50,7,$rslt['LIBELLE'],1);
  $pdf->Cell(15,7,$rslt['QTESTOCK'],1,0,'C');
  $pdf->Cell(15,7,$rslt['PRIXREVIENT'].' F',1,0,'C');
  $pdf->Cell(30,7,number_format($mtmag, 0, ',', ' ').' F',1,0,'C');
  $pdf->Cell(15,7,$rslt['STOCKFRIGO'],1,0,'C');
  $pdf->Cell(15,7,$prfrigo.' F',1,0,'C');
  $pdf->Cell(30,7,number_format($mtfr, 0, ',', ' ').' F',1,0,'C');
  $pdf->Cell(35,7,number_format($mtfr+$mtmag, 0, ',', ' ').' F',1,1,'C');
  
  $nbrearticle++;
  $totalcolis=$totalcolis+$rslt['QTESTOCK'];
  $totalprcolis=$totalprcolis+$rslt['PRIXREVIENT'];
  $motantcolis=$motantcolis+$mtmag;
  $totalbtle=$totalbtle+$rslt['STOCKFRIGO'];
  $totalprbtle=$totalprbtle+$prfrigo;
  $motantbtle=$motantbtle+$mtfr;
  $montanttotal=$montanttotal+$mtmag+$mtfr;
  }
  //totaux articles
  $pdf->SetFont('Arial','B',9);
  $pdf->Cell(60,7,' Nombre d\'articles : '.$nbrearticle,1,0,'L');
  $pdf->Cell(50,7,'Totaux : ',1,0,'L');
  $pdf->Cell(15,7,$totalcolis,1,0,'C');
  $pdf->Cell(15,7,number_format($totalprcolis, 0, ',', ' ').' F',1,0,'C');
  $pdf->Cell(30,7,number_format($motantcolis, 0, ',', ' ').' F',1,0,'C');
  $pdf->Cell(15,7,$totalbtle,1,0,'C');
  $pdf->Cell(15,7,number_format($totalprbtle, 0, ',', ' ').' F',1,0,'C');
  $pdf->Cell(30,7,number_format($motantbtle, 0, ',', ' ').' F',1,0,'C');
  $pdf->SetFont('Arial','B',12);
  $pdf->Cell(35,7,number_format($montanttotal, 0, ',', ' ').' F',1,1,'C');
  
  //emballages
 $pdf->Ln(10);
 $pdf->Cell(105,7,'EMBALLAGES',1,0,'C',1);
 $pdf->Cell(80,7,'STOCK TOTAL',1,0,'C',1);
 $pdf->Cell(80,7,'STOCK DISPONIBLE',1,1,'C',1);
 
 $pdf->Cell(30,7,'CODE',1,0,'L',1);
 $pdf->Cell(75,7,'LIBELLE',1,0,'L',1);
 $pdf->Cell(25,7,'QTE',1,0,'C',1);
 $pdf->Cell(25,7,'PU',1,0,'C',1);
 $pdf->Cell(30,7,'MONTANT',1,0,'C',1);
 $pdf->Cell(25,7,'QTE',1,0,'C',1);
 $pdf->Cell(25,7,'PU',1,0,'C',1);
 $pdf->Cell(30,7,'MONTANT',1,1,'C',1);
 $pdf->SetFont('Arial','',8);
$nbre2=0;
$mtstock=0;
$mtdispo=0;
$ttmtstock=0;
$ttmtdispo=0;
$totalembdispo=0;
$totalembstock=0;

$sql = "SELECT * FROM EMBALLAGE";
$reponse= $DataBase->query($sql);
$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			$mtstock=$rslt['MT_CONSIGNE']*$rslt['QTE'];
			$mtdispo=$rslt['MT_CONSIGNE']*$rslt['QTESTOCK'];
  

		  $pdf->Cell(30,7,$rslt['ID_EMBALLAGE'],1,0,'L');
		  $pdf->Cell(75,7,$rslt['LIBELLE'],1);
		  $pdf->Cell(25,7,$rslt['QTE'],1,0,'C');
		  $pdf->Cell(25,7,$rslt['MT_CONSIGNE'].' F',1,0,'C');
		  $pdf->Cell(30,7,number_format($mtstock, 0, ',', ' ').' F',1,0,'C');
		  $pdf->Cell(25,7,$rslt['QTESTOCK'],1,0,'C');
		  $pdf->Cell(25,7, $rslt['MT_CONSIGNE'].' F',1,0,'C');
		  $pdf->Cell(30,7,number_format($mtdispo, 0, ',', ' ').' F',1,1,'C');
  
		 $nbre2++;
		 $ttmtstock=$ttmtstock+$mtstock;
		 $ttmtdispo=$ttmtdispo+$mtdispo;
		 $totalembdispo=$totalembdispo+$rslt['QTESTOCK'];
		 $totalembstock=$totalembstock+$rslt['QTE'];
	}
  //totaux emballages
  $pdf->SetFont('Arial','B',9);
  $pdf->Cell(55,7,' Nombre d\'emballages : '.$nbre2,1,0,'L');
  $pdf->Cell(50,7,'Totaux : ',1,0,'C');
  $pdf->Cell(25,7,$totalembstock,1,0,'C');
  $pdf->Cell(25,7,'//',1,0,'C');
  $pdf->Cell(30,7,number_format( $ttmtstock, 0, ',', ' ').' F',1,0,'C');
  $pdf->Cell(25,7, $totalembdispo,1,0,'C');
  $pdf->Cell(25,7,'//',1,0,'C');
  $pdf->SetFont('Arial','B',12);
  $pdf->Cell(30,7,number_format($ttmtdispo, 0, ',', ' ').' F',1,1,'C');
    //totaux articles + emballages
	$pdf->SetFont('Arial','B',14);
  $pdf->Cell(165,7,'MONTANT TOTAL DES STOCKS ',1,0,'C');
  $pdf->Cell(100,7,number_format($ttmtdispo+$montanttotal, 0, ',', ' ').' F',1,1,'C');
  
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
