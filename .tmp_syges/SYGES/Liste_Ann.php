<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur"))
{
 include('fpdf.php');
 include('Connexion.php');
 include('fonctions.php');
 $Debut=dateFormatAnglais($_GET["debut"]);
 $Fin=dateFormatAnglais($_GET["fin"]);
$couleur = "darkgray";
$i = 0;
$nbre=0;

			if($_GET['Ope']=='Toutes')
			{
				$sql1 = 'select distinct id_operation, date, heure, user, date_ann, detenteur, operation from mouvementar where operation like "AN_%" and date_ann between "'.$Debut.'" AND "'.$Fin.'" order by id_mouv';
			}
			if($_GET['Ope']=='Ventes')
			{
				$sql1 = 'select distinct id_operation, date, heure, user, date_ann, detenteur, operation from mouvementar where operation="AN_VAL_VENTE" and date_ann between "'.$Debut.'" AND "'.$Fin.'" order by id_mouv';
			}
			if	($_GET['Ope']=='Sorties Cessions')
			{
				$sql1 = 'select distinct id_operation, date, heure, user, date_ann, detenteur, operation from mouvementar where operation="AN_VAL_SORTIE_CESSION" and date_ann between "'.$Debut.'" AND "'.$Fin.'" order by id_mouv';
			}
			if	($_GET['Ope']=='Entrées Cessions')
			{
				$sql1 = 'select distinct id_operation, date, heure, user, date_ann, detenteur, operation from mouvementar where operation="AN_VAL_APPRO_CESSION" and date_ann between "'.$Debut.'" AND "'.$Fin.'" order by id_mouv';
			}
			if	($_GET['Ope']=='Approvisionnements')
			{
				$sql1 = 'select distinct id_operation, date, heure, user, date_ann, detenteur, operation from mouvementar where operation="AN_VAL_APPRO" and date_ann between "'.$Debut.'" AND "'.$Fin.'" order by id_mouv';
			}

 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('LISTE DES ANNULATIONS ');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('ANNULATIONS');
 $pdf->Ln(15);
 $pdf->SetFont('arial','B',16);
 $pdf->Write(50,'                                     LISTE DES ANNULATIONS');
 $pdf->Ln(1);
 $pdf->Write(50,'                                     _______________________');
 $pdf->Ln(35);                                
 $pdf->SetFont('arial','B',10);
 $pdf->Write(2,'Periode Du :  ');
 $pdf->Write(2,dateFormatFrancais($Debut));
 $pdf->Write(2,'   Au :   ');
 $pdf->Write(2,dateFormatFrancais($Fin));
 $pdf->Write(2,'   Type Operation :  ');
 $pdf->Write(2,$_GET['Ope']);
 $pdf->Write(2,'      Date :   ');
 $pdf->Write(2,date("d/m/y").' '.date('H:i'));
 $pdf->Ln(10);
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',0,10,180,30);
 $pdf->Cell(20,7,'DATE',1,0,'L',1);
 $pdf->Cell(15,7,'HEURE',1,0,'L',1);
 $pdf->Cell(25,7,'REFERENCE',1,0,'L',1);
 $pdf->Cell(60,7,'DETENTEUR',1,0,'C',1);
 $pdf->Cell(30,7,'OPERATEUR',1,0,'C',1);
 $pdf->Cell(40,7,'OPERATION',1,1,'L',1);
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
  
  $ttcolis=0;
  $nbrope=0;
  $reponse= $DataBase->query($sql1);
  while($rslt= $reponse->fetch())
 {
  $pdf->Cell(20,7,dateFormatFrancais($rslt['date_ann']),1,0,'C');
  $pdf->Cell(15,7,$rslt['heure'],1);
  $pdf->Cell(25,7,$rslt['id_operation'],1);
  $pdf->Cell(60,7,$rslt['detenteur'],1);
  $pdf->Cell(30,7,$rslt['user'],1,0,'C');
  $pdf->Cell(40,7,$rslt['operation'],1,1,'C');
  $nbre++;
  }
  //ecriture des totaux
   $pdf->SetFont('Arial','B',10);
  $pdf->Cell(190,7,'Nombre d\'operation. : '.number_format($nbre, 0, ',', ' '),1,0,'C');
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
