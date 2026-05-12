<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" )||($_SESSION['habilitation']=="Caissier")||($_SESSION['habilitation']=="Comptable"))
{
 include('fpdf.php');
 include('Connexion.php');
 include('fonctions.php');
 $Debut=dateFormatAnglais($_GET["debut"]);
 $Fin=dateFormatAnglais($_GET["fin"]);
	if ($_GET['Statut']=='Mixte')
	{	
	
	$sql='SELECT C.ID_CHARGE, TC.LIBELLE, TC.ID_TYPECHARGE, C.ID_TYPECHARGE, C.DESCRIPTION, C.MONTANT, C.DATE_CHARGE, C.STATUT FROM CHARGE C, TYPE_CHARGE TC WHERE C.ID_TYPECHARGE=TC.ID_TYPECHARGE AND C.DATE_CHARGE BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY C.ID_CHARGE' ;
	
	}
	else
		if ($_GET['Statut']=='N')
		{
			$sql='SELECT C.ID_CHARGE, TC.LIBELLE, TC.ID_TYPECHARGE, C.ID_TYPECHARGE, C.DESCRIPTION, C.MONTANT, C.DATE_CHARGE , C.STATUT FROM CHARGE C, TYPE_CHARGE TC WHERE C.ID_TYPECHARGE=TC.ID_TYPECHARGE  AND C.STATUT="N" AND C.DATE_CHARGE BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY C.ID_CHARGE' ;
		}
		else
			{
			$sql='SELECT C.ID_CHARGE, TC.LIBELLE, TC.ID_TYPECHARGE, C.ID_TYPECHARGE, C.DESCRIPTION, C.MONTANT, C.DATE_CHARGE , C.STATUT FROM CHARGE C, TYPE_CHARGE TC WHERE C.ID_TYPECHARGE=TC.ID_TYPECHARGE  AND C.STATUT="V" AND C.DATE_CHARGE BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY C.ID_CHARGE' ;
			}
 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('ETAT DES CHARGES ');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('CHARGE');
 $pdf->Ln(0);
 $pdf->SetFont('arial','B',12);
 $pdf->Write(75,'                                                                 ETAT DES CHARGES');
 $pdf->Ln(1);
 $pdf->Write(75,'                                                                 __________________');
 $pdf->Ln(45);                                
 $pdf->SetFont('arial','B',10);
 $pdf->Write(2,'Periode :   Du :  ');
 $pdf->Write(2,dateFormatFrancais($Debut));
 $pdf->Write(2,'   Au :   ');
 $pdf->Write(2,dateFormatFrancais($Fin));
 $pdf->Write(2,'              Statut :  ');
 $pdf->Write(2,$_GET['Statut']);
  $pdf->Write(2,'                    Date :  ');
 $pdf->Write(2,date("d/m/y").' '.date('H:i'));
 $pdf->Ln(10);
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',15,10,180,30);
 $pdf->Cell(20,7,'DATE',1,0,'L',1);
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
  $pdf->Cell(25,7,number_format($rslt['MONTANT'], 0, ',', ' ').' F',1,1,'C');
  $nbrecharge++;
  $mt=($mt+$rslt['MONTANT']);
  }
  //ecriture des totaux
   $pdf->SetFont('arial','B',9);
  $pdf->Cell(40,7,'Nbre de Charge : '.$nbrecharge,1,0,'C');
  $pdf->SetFont('Arial','B',12);
  $pdf->Cell(140,7,'Montant Total : '.number_format($mt, 0, ',', ' ').' FCFA',1,1,'R');
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
