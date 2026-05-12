<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="DGA" || $_SESSION['habilitation']=="Superviseur" || $_SESSION['habilitation']=="Comptable" ))
{
 include('fpdf.php');
 include('Connexion.php');
  $sql2 = " select *  from inventaire  where id_inv='".$_GET['Id']."'";
  $reponse2= $DataBase->query($sql2);
  $rslt2= $reponse2->fetch();
 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('INVENTAIRES  ');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('Stock');
 $pdf->SetFont('arial','B',10);
 $pdf->Write(50,'                                                                          INVENTAIRE GLOBAL');
 $pdf->Ln(0);
 $pdf->Write(50,'                                                                          ___________________');
 $pdf->Ln(17);
 $pdf->SetFont('arial','B',8);
 $pdf->Ln(2);
 $pdf->Write(25,' REFERENCE : '.$_GET['Id'].'             DATE :   '.$rslt2['DATE'].'   '.$rslt2['HEURE']   );
 $pdf->Ln(18);                                
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFont('Arial','B',7);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',5,5,180,22);

 $pdf->Cell(17,4,'LIBELLE',1,0,'L',1);
 $pdf->Cell(30,4,'CONDITION.',1,0,'L',1);
 $pdf->Cell(15,4,'QTE',1,0,'C',1);
 $pdf->Cell(17,4,'PU',1,0,'C',1);
 $pdf->Cell(25,4,'VALEUR LIQUIDE',1,0,'C',1);
 $pdf->Cell(30,4,'VALEUR EMBALLAGE',1,0,'C',1);
 $pdf->Cell(30,4,'FRAIS ENLEVEMENT',1,0,'C',1);
 $pdf->Cell(30,4,'VALEUR STOCK ',1,1,'C',1);
 
 $pdf->SetTextColor(0,0,0);
  
$nbre=0;
$totalcolis=0;
$montantliquide=0;
$montantemb=0;
$valeurstock=0;
$mtfraisenlement=0;
$montantfraisenlement=0;
$sql3 = "SELECT DISTINCT ID_FAMILLE FROM ARTICLE_INV WHERE ID_INV='".$_GET['Id']."'";
$reponse3= $DataBase->query($sql3);
while($rslt3= $reponse3->fetch())
{
	$pdf->SetFont('Arial','B',6);
	$pdf->Cell(194,4,'FAMILLE : '.$rslt3['ID_FAMILLE'],1,1,'C',1);
	$pdf->SetFont('Arial','',6);
	$sql = "SELECT * FROM ARTICLE_INV WHERE ID_INV='".$_GET['Id']."' AND ID_FAMILLE='".$rslt3['ID_FAMILLE']."' ORDER BY LIBELLE";
	$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			$mtmag=$rslt['PRIXVENTE']*$rslt['QTESTOCK'];
			if(($rslt['MARQUE']=="CASIER") || ($rslt['MARQUE']=="casier")|| ($rslt['MARQUE']=="CASIERS")|| ($rslt['MARQUE']=="casiers"))
			{
				$mtemb=$rslt2['PU_EMB_PLEIN']*$rslt['QTESTOCK'];
			}	
			else
			{
				$mtemb=0;
			}
			$mtfraisenlement=$rslt2['FRAISENLEVEMENT']*$rslt['QTESTOCK'];

		  $pdf->Cell(17,4,$rslt['LIBELLE'],1);
		  $pdf->Cell(30,4,$rslt['MARQUE'].' '.$rslt['NBREBTE'],1);
		  $pdf->Cell(15,4,number_format($rslt['QTESTOCK'], 0, ',', ' '),1,0,'C');
		  $pdf->Cell(17,4,number_format($rslt['PRIXVENTE'], 0, ',', ' '),1,0,'C');
		  $pdf->Cell(25,4,number_format($mtmag, 0, ',', ' '),1,0,'C');
		  $pdf->Cell(30,4,number_format($mtemb, 0, ',', ' '),1,0,'C');
		  $pdf->Cell(30,4,number_format($mtfraisenlement, 0, ',', ' '),1,0,'C');
		  $pdf->Cell(30,4,number_format($mtmag+$mtemb+$mtfraisenlement, 0, ',', ' '),1,1,'C');
		  
		  $nbre++;
		  $totalcolis=$totalcolis+$rslt['QTESTOCK'];
		  $montantliquide=$montantliquide+$mtmag;
		  $montantemb=$montantemb+$mtemb;
		  $montantfraisenlement=$montantfraisenlement+$mtfraisenlement;
		  $valeurstock=$montantliquide+$montantemb+$montantfraisenlement;
      }
}
  //totaux articles
  $pdf->SetFont('Arial','B',7);
  $pdf->Cell(47,5,' Nombre d\'articles : '.$nbre,1,0,'L');
  $pdf->Cell(15,5,$totalcolis,1,0,'C');
  $pdf->Cell(17,5,'//',1,0,'C');
  $pdf->Cell(25,5,number_format($montantliquide, 0, ',', ' '),1,0,'C');
  $pdf->Cell(30,5,number_format($montantemb, 0, ',', ' '),1,0,'C');
  $pdf->Cell(30,5,number_format($montantfraisenlement, 0, ',', ' '),1,0,'C');
  $pdf->Cell(30,5,number_format($valeurstock, 0, ',', ' '),1,1,'C');
  
  //emballages
 $pdf->Ln(10);
 $pdf->Cell(40,4,'',0,0,'C',1);
 $pdf->Cell(120,4,'EMBALLAGES',1,1,'C',1);
 
 $pdf->Cell(40,4,'',0,0,'C',1);
 $pdf->Cell(35,4,'LIBELLE',1,0,'L',1);
 $pdf->Cell(25,4,'QTE',1,0,'C',1);
 $pdf->Cell(25,4,'PU',1,0,'C',1);
 $pdf->Cell(35,4,'VALEUR',1,1,'C',1);
 $pdf->SetFont('Arial','',6);
$couleur = "darkgray";
$mtstock=0;
$Valeurstockemb=0;

		  $pdf->Cell(40,4,'',0,0,'C',1);
		  $pdf->Cell(35,4,'PALETTE BOIS(S)',1,0,'L');
		  $pdf->Cell(25,4,$rslt2['PALETTEBOIS'],1,0,'C');
		  $pdf->Cell(25,4,number_format($rslt2['PU_PALETTEBOIS'], 0, ',', ' '),1,0,'C');
		  $pdf->Cell(35,4,number_format($rslt2['PALETTEBOIS']*$rslt2['PU_PALETTEBOIS'], 0, ',', ' '),1,1,'C');
		  
		  $pdf->Cell(40,4,'',0,0,'C',1);
		  $pdf->Cell(35,4,'PALETTE PLASTIQUE(S)',1,0,'L');
		  $pdf->Cell(25,4,$rslt2['PALETTEPLASTIQUE'],1,0,'C');
		  $pdf->Cell(25,4,number_format($rslt2['PU_PALETTEPLASTIQUE'], 0, ',', ' '),1,0,'C');
		  $pdf->Cell(35,4,number_format($rslt2['PALETTEPLASTIQUE']*$rslt2['PU_PALETTEPLASTIQUE'], 0, ',', ' '),1,1,'C');
		  $pdf->Cell(40,4,'',0,0,'C',1);
		  $pdf->Cell(35,4,'EMBALLAGES PLEINS',1,0,'L');
		  $pdf->Cell(25,4,$rslt2['EMB_PLEIN'],1,0,'C');
		  $pdf->Cell(25,4,number_format($rslt2['PU_EMB_PLEIN'], 0, ',', ' '),1,0,'C');
		  $pdf->Cell(35,4,number_format($rslt2['EMB_PLEIN']*$rslt2['PU_EMB_PLEIN'], 0, ',', ' '),1,1,'C');
		  
		  $pdf->Cell(40,4,'',0,0,'C',1);
		  $pdf->Cell(35,4,'EMBALLAGES VIDES',1,0,'L');
		  $pdf->Cell(25,4,$rslt2['EMB_VIDE'],1,0,'C');
		  $pdf->Cell(25,4,number_format($rslt2['PU_EMB_VIDE'], 0, ',', ' '),1,0,'C');
		  $pdf->Cell(35,4,number_format($rslt2['EMB_VIDE']*$rslt2['PU_EMB_VIDE'], 0, ',', ' '),1,1,'C');

  
		$Valeurstockemb=($rslt2['PALETTEBOIS']*$rslt2['PU_PALETTEBOIS'])+($rslt2['PALETTEPLASTIQUE']*$rslt2['PU_PALETTEPLASTIQUE'])+($rslt2['EMB_PLEIN']*$rslt2['PU_EMB_PLEIN'])+$rslt2['EMB_VIDE']*($rslt2['PU_EMB_VIDE']);

  //totaux emballages
  $pdf->SetFont('Arial','B',7);

  $pdf->Cell(40,4,'',0,0,'C',1);
  $pdf->Cell(35,4,'Totaux : ',1,0,'C');
 
  $pdf->Cell(25,4,number_format($rslt2['PALETTEBOIS']+$rslt2['PALETTEPLASTIQUE']+$rslt2['EMB_PLEIN']+$rslt2['EMB_VIDE'], 0, ',', ' '),1,0,'C');
  $pdf->Cell(25,4, '//',1,0,'C');
  $pdf->Cell(35,4,number_format($Valeurstockemb, 0, ',', ' ').' F',1,1,'C');
    //SITUATION GLOBALE
	
	$pdf->Ln(5);
$TotalGlobal=$valeurstock+$rslt2['SOLDECAISSE']+$rslt2['SOLDESABC']+$rslt2['SOLDEOM']+$rslt2['SOLDEMOMO']+$rslt2['CREDITCLIENT']+$rslt2['CREDITEMBALLAGE']+$rslt2['SOLDEBANQUE']+$rslt2['AUTRECREDIT']+$Valeurstockemb-$rslt2['CREDITBRASSERIES']-$rslt2['CREDITBANQUE']-$rslt2['RISTOURNESCLIENTS']-$rslt2['AUTRESDEBIT'];
	
$pdf->Cell(40,4,'',0,0,'C',1);	
 $pdf->Cell(120,4,'SITUATION GLOBALE',1,1,'C',1);
 $pdf->SetFont('Arial','B',7);
 $pdf->Cell(40,4,'',0,0,'C',1);
 $pdf->Cell(60,4,'LIBELLE',1,0,'L',1);
 $pdf->Cell(60,4,'MONTANT',1,1,'C',1);
  $pdf->SetFont('Arial','',7);
$pdf->Cell(40,4,'',0,0,'C',1); 
$pdf->Cell(60,4,'VALEUR STOCK',1,0,'L');
$pdf->Cell(60,4,number_format($valeurstock, 0, ',', ' '),1,1,'C');
 
 $pdf->SetFont('Arial','B',7);
 $pdf->Cell(40,4,'',0,0,'C',1);	
 $pdf->Cell(120,4,'CREDIT',1,1,'L',1);


$pdf->SetFont('Arial','',7);
$pdf->Cell(40,4,'',0,0,'C',1);
$pdf->Cell(60,4,'SOLDE CAISSE',1,0,'L');
$pdf->Cell(60,4,number_format($rslt2['SOLDECAISSE'], 0, ',', ' '),1,1,'C');

$pdf->Cell(40,4,'',0,0,'C',1);
$pdf->Cell(60,4,'SOLDE BRASSERIES',1,0,'L');
$pdf->Cell(60,4,number_format($rslt2['SOLDESABC'], 0, ',', ' '),1,1,'C');

$pdf->Cell(40,4,'',0,0,'C',1);
$pdf->Cell(60,4,'SOLDE OM',1,0,'L');
$pdf->Cell(60,4,number_format($rslt2['SOLDEOM'], 0, ',', ' '),1,1,'C');

$pdf->Cell(40,4,'',0,0,'C',1);
$pdf->Cell(60,4,'SOLDE MOMO',1,0,'L');
$pdf->Cell(60,4,number_format($rslt2['SOLDEMOMO'], 0, ',', ' '),1,1,'C');

$pdf->Cell(40,4,'',0,0,'C',1);
$pdf->Cell(60,4,'CREDIT CLIENT',1,0,'L');
$pdf->Cell(60,4,number_format($rslt2['CREDITCLIENT'], 0, ',', ' '),1,1,'C');

$pdf->Cell(40,4,'',0,0,'C',1);
$pdf->Cell(60,4,'CREDIT EMBALLAGE',1,0,'L');
$pdf->Cell(60,4,number_format($rslt2['CREDITEMBALLAGE'], 0, ',', ' '),1,1,'C');

$pdf->Cell(40,4,'',0,0,'C',1);
$pdf->Cell(60,4,'SOLDE BANQUE',1,0,'L');
$pdf->Cell(60,4,number_format($rslt2['SOLDEBANQUE'], 0, ',', ' '),1,1,'C');

$pdf->Cell(40,4,'',0,0,'C',1);
$pdf->Cell(60,4,'AUTRE CREDIT',1,0,'L');
$pdf->Cell(60,4,number_format($rslt2['AUTRECREDIT'], 0, ',', ' '),1,1,'C');

 $pdf->SetFont('Arial','B',7);
 $pdf->Cell(40,4,'',0,0,'C',1);	
 $pdf->Cell(120,4,'DEBIT',1,1,'L',1);


$pdf->SetFont('Arial','',7);
$pdf->Cell(40,4,'',0,0,'C',1);
$pdf->Cell(60,4,'CREDIT BRASSERIE',1,0,'L');
$pdf->Cell(60,4,number_format($rslt2['CREDITBRASSERIES'], 0, ',', ' '),1,1,'C');

$pdf->Cell(40,4,'',0,0,'C',1);
$pdf->Cell(60,4,'CREDIT BANQUE',1,0,'L');
$pdf->Cell(60,4,number_format($rslt2['CREDITBANQUE'], 0, ',', ' '),1,1,'C');

$pdf->Cell(40,4,'',0,0,'C',1);
$pdf->Cell(60,4,'RISTOURNES CLIENTS',1,0,'L');
$pdf->Cell(60,4,number_format($rslt2['RISTOURNESCLIENTS'], 0, ',', ' '),1,1,'C');

$pdf->Cell(40,4,'',0,0,'C',1);
$pdf->Cell(60,4,'AUTRE DEBIT',1,0,'L');
$pdf->Cell(60,4,number_format($rslt2['AUTRESDEBIT'], 0, ',', ' '),1,1,'C');

$pdf->SetFont('Times','B',8);
$pdf->Cell(40,4,'',0,0,'C',1);
$pdf->Cell(60,5,'TOTAL GLOBAL',1,0,'L');
$pdf->Cell(60,5,number_format($TotalGlobal, 0, ',', ' '),1,1,'C');

  $pdf->SetFont('Times','B',6);
  $pdf->Write(20,'LE DIRECTEUR                                          LE CAISSIER                                                             LE COMPTABLE                                               LE MAGASINIER                                      LE MANDATAIRE');
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
