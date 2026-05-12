<?php
session_start();
if (isset ($_SESSION['habilitation']) && (($_SESSION['habilitation']=="Administrateur")|| ($_SESSION['habilitation']=="Gerant")))
{
 include('fpdf.php');
 include('Connexion.php');
 include('fonctions.php');
 $Debut=dateFormatAnglais($_GET["debut"]);
 $Fin=dateFormatAnglais($_GET["fin"]);
 
$sql='SELECT R.ID_RTREMB, R.ID_APPRO, R.ID_EMBALLAGE, R.DATE_RTREMB,R.QTE,R.DATE_RTREMB, R.STATUT, R.PU, R.MONTANT, E.LIBELLE FROM RTREMBFSSR R, EMBALLAGE E WHERE R.ID_EMBALLAGE=E.ID_EMBALLAGE AND R.DATE_RTREMB BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND R.STATUT="OK" ORDER BY R.ID_RTREMB';

 //Select ds bd
 $pdf=new FPDF('L','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('ETAT DES RTR EMB ');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('RTR EMB');
 $pdf->Ln(0);
 $pdf->SetFont('arial','B',12);
 $pdf->Write(80,'                                                      ETAT DES RETOURS D\'EMBALLAGES AU FOURNISSEUR');
 $pdf->Ln(3);
 $pdf->Write(80,'                                                      __________________________________________________');
 $pdf->Ln(45);                                
 $pdf->SetFont('arial','B',10);
 $pdf->Write(2,'Periode :   Du :  ');
 $pdf->Write(2,dateFormatFrancais($Debut));
 $pdf->Write(2,'   Au :   ');
 $pdf->Write(2,dateFormatFrancais($Fin));
 $pdf->Write(2,'              Date :  ');
 $pdf->Write(2,date("d/m/Y").'    '.date('H:i'));
 $pdf->Ln(10);
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->SetFont('arial','B',9);
 $pdf->Image('IMG\logo.jpg',15,17,250,30);
 $pdf->Cell(25,7,'CODE RETOUR',1,0,'C',1);
 $pdf->Cell(25,7,'DATE RETOUR',1,0,'L',1);
 $pdf->Cell(25,7,'CODE APPRO.',1,0,'L',1);
 $pdf->Cell(25,7,'DATE APPRO',1,0,'L',1);
 $pdf->Cell(50,7,'EMBALLAGE',1,0,'L',1);
 $pdf->Cell(15,7,'QTE',1,0,'C',1);
 $pdf->Cell(25,7,'PU',1,0,'C',1);
 $pdf->Cell(35,7,'MONTANT',1,0,'C',1);
 $pdf->Cell(20,7,'STATUT',1,1,'L',1);
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
  
 $MT=0;
 $QTE = 0;
 $PU = 0;
 $nbre=0;
 $reponse= $DataBase->query($sql);
 while($rslt= $reponse->fetch())
 {
 //ici on recupere la date de l'appro 
	$sql1 = 'SELECT DATE_APPRO FROM APPROVISIONNEMENT  WHERE ID_APPRO="'.$rslt['ID_APPRO'].'"';
	$reponse1= $DataBase->query($sql1);
		while($rslt2= $reponse1->fetch())
		{
			$date=$rslt2['DATE_APPRO'];
		}
	 //
	 
  $pdf->Cell(25,7,$rslt['ID_RTREMB'],1,0,'C');
  $pdf->Cell(25,7,dateFormatFrancais($rslt['DATE_RTREMB']),1,0,'C');
  $pdf->Cell(25,7,$rslt['ID_APPRO'],1);
  $pdf->Cell(25,7,dateFormatFrancais($date),1,0,'C');
  $pdf->Cell(50,7,$rslt['LIBELLE'],1,0,'L');
  $pdf->Cell(15,7,$rslt['QTE'],1,0,'C');
  $pdf->Cell(25,7,$rslt['PU'].' F',1,0,'C');
  $pdf->Cell(35,7,$rslt['MONTANT'].' F',1,0,'C');
  $pdf->Cell(20,7,utf8_decode($rslt['STATUT']),1,1,'C');
  $nbre++;
  $MT = ($MT + $rslt['MONTANT']);
  $QTE = ($QTE + $rslt['QTE']);
  $PU = ($PU + $rslt['PU']);
  }
  //Ecriture des totaux
 $pdf->SetFont('arial','B',10);
 $pdf->Cell(50,7,'Nombre : '.$nbre,1,0,'C',1);
 $pdf->Cell(100,7,'Totaux :',1,0,'C',1);
 $pdf->Cell(15,7,number_format($QTE, 0, ',', ' '),1,0,'C',1);
 $pdf->Cell(25,7,number_format($PU, 0, ',', ' ').' F',1,0,'C',1);
 $pdf->Cell(35,7,number_format($MT, 0, ',', ' ').' F',1,0,'C',1);
 $pdf->Cell(20,7,'-//-',1,1,'C',1);
  
 
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
