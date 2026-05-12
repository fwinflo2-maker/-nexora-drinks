<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" )||($_SESSION['habilitation']=="Comptable"))
{
 include('fpdf.php');
 include('Connexion.php');
 include('fonctions.php');
 $Debut=dateFormatAnglais($_GET["debut"]);
 $Fin=dateFormatAnglais($_GET["fin"]);
	$mt=0;
$sql='SELECT C.ID_CHARGE, TC.LIBELLE, TC.ID_TYPECHARGE, C.ID_TYPECHARGE, C.DESCRIPTION, C.MONTANT, C.DATE_CHARGE, C.STATUT FROM CHARGE C, TYPE_CHARGE TC WHERE C.ID_TYPECHARGE=TC.ID_TYPECHARGE AND TC.ID_TYPECHARGE="'.$_GET['Chg'].'" AND C.DATE_CHARGE BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY C.ID_CHARGE' ;

	//on recupere le libelle charge
	$sql2='SELECT LIBELLE FROM TYPE_CHARGE WHERE ID_TYPECHARGE="'.$_GET['Chg'].'"';
	$reponse2= $DataBase->query($sql2);
		while($rslt2= $reponse2->fetch())
		{
		$libelle=$rslt2['LIBELLE'];
	    }
 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('ETAT DES CHARGES ');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('CHARGE');
 $pdf->Ln(3);
 $pdf->SetFont('arial','B',14);
 $pdf->Write(80,'                                                        ETAT DES CHARGES');
 $pdf->Ln(1);
 $pdf->Write(80,'                                                        __________________');
 $pdf->Ln(45);                                 
 $pdf->SetFont('arial','B',10);
 $pdf->Write(2,'Periode :   Du :  ');
 $pdf->Write(2,dateFormatFrancais($Debut));
 $pdf->Write(2,'   Au :   ');
 $pdf->Write(2,dateFormatFrancais($Fin));
 $pdf->Ln(5);
 $pdf->Write(2,'Type Charge :  ');
 $pdf->Write(2,$libelle);
  $pdf->Write(2,'            Date :  ');
 $pdf->Write(2,date("d/m/y").' '.date('H:i'));
 $pdf->Ln(10);
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',15,15,180,30);
 $pdf->Cell(20,7,'DATE.',1,0,'L',1);
 $pdf->Cell(20,7,'CODE',1,0,'L',1);
 $pdf->Cell(55,7,'LIBELLE',1,0,'L',1);
 $pdf->Cell(60,7,'DESCRIPTION',1,0,'L',1);
 $pdf->Cell(25,7,'MONTANT',1,1,'C',1);
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
 
 $mt=0; 
 $nbrecharge=0;
 $reponse= $DataBase->query($sql);
  while($rslt= $reponse->fetch())
 {
  $pdf->Cell(20,7,dateFormatFrancais($rslt['DATE_CHARGE']),1,0,'C');
  $pdf->Cell(20,7,$rslt['ID_CHARGE'],1);
  $pdf->Cell(55,7,$rslt['LIBELLE'],1);
  $pdf->Cell(60,7,$rslt['DESCRIPTION'],1);
  $pdf->Cell(25,7,$rslt['MONTANT'].' F',1,1,'C');
  $nbrecharge++;
  $mt=($mt+$rslt['MONTANT']);
  }
  //ecriture des totaux
   $pdf->SetFont('arial','B',9);
  $pdf->Cell(20,7,'Totaux',1,0,'C');
  $pdf->Cell(75,7,'Nbre de Charge : '.$nbrecharge,1,0,'C');
  $pdf->Cell(60,7,'//',1);
  $pdf->Cell(25,7,number_format($mt, 0, ',', ' ').' F',1,1,'C');
  //nro de page
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
