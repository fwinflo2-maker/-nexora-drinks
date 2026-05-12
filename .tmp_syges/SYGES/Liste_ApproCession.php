<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
{
 include('fpdf.php');
 include('Connexion.php');
 include('fonctions.php');
 $Debut=dateFormatAnglais($_GET["debut"]);
 $Fin=dateFormatAnglais($_GET["fin"]);
	if ($_GET['Statut']=='Mixte')
	{	
	$sql='SELECT A.ID_APPRO, A.DATE_APPRO, A.LOGIN, A.OBSERVATION, A.STATUT, U.LOGIN, U.NOM FROM APPROCESSION A, USER U WHERE A.LOGIN=U.LOGIN AND A.DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY A.ID_APPRO' ;
	}
	else
		if ($_GET['Statut']=='N')
		{
		$sql='SELECT A.ID_APPRO, A.DATE_APPRO, A.LOGIN, A.OBSERVATION, A.STATUT, U.LOGIN, U.NOM FROM APPROCESSION A, USER U WHERE A.LOGIN=U.LOGIN AND A.DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'"  AND A.STATUT ="N" ORDER BY A.ID_APPRO' ;
		}
		else
			{
			$sql='SELECT A.ID_APPRO, A.DATE_APPRO, A.LOGIN, A.OBSERVATION, A.STATUT, U.LOGIN, U.NOM FROM APPROCESSION A, USER U WHERE A.LOGIN=U.LOGIN AND A.DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'"  AND A.STATUT ="V" ORDER BY A.ID_APPRO' ;
			}
 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('LISTE DES APPRO. CESSION ');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('Approvisionnement');
 $pdf->Ln(10);
 $pdf->SetFont('arial','B',12);
 $pdf->Write(65,'                                                LISTE DES APPRO. CESSION');
 $pdf->Ln(3);
 $pdf->Write(65,'                                                _________________________');
 $pdf->Ln(40);                                
 $pdf->SetFont('arial','B',10);
 $pdf->Write(2,'Periode  Du :  ');
 $pdf->Write(2,dateFormatFrancais($Debut));
 $pdf->Write(2,'   Au :   ');
 $pdf->Write(2,dateFormatFrancais($Fin));
 $pdf->Write(2,'   Statut :  ');
 $pdf->Write(2,$_GET['Statut']);
 $pdf->Write(2,'      Date :   ');
 $pdf->Write(2,date("d/m/y").' '.date('H:i'));
 $pdf->Ln(10);
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',10,15,180,30);
 $pdf->Cell(30,7,'DATE .',1,0,'L',1);
 $pdf->Cell(20,7,'CODE',1,0,'L',1);
 $pdf->Cell(40,7,'UTILISATEUR',1,0,'L',1);
 $pdf->Cell(20,7,'STATUT',1,0,'L',1);
 $pdf->Cell(70,7,'OBSERVATION',1,1,'L',1);
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
  
 $nbreappro=0;
  $reponse= $DataBase->query($sql);
  while($rslt= $reponse->fetch())
 {
  $pdf->Cell(30,7,dateFormatFrancais($rslt['DATE_APPRO']),1,0,'C');
  $pdf->Cell(20,7,$rslt['ID_APPRO'],1);
  $pdf->Cell(40,7,$rslt['NOM'],1);
  $pdf->Cell(20,7,utf8_decode($rslt['STATUT']),1,0,'C');
  $pdf->Cell(70,7,utf8_decode($rslt['OBSERVATION']),1,1,'L');
  $nbreappro++;
  }
  //nro de page
  $pdf->SetFont('Arial','B',9);
   $pdf->Write(5,'                                                                                                                                                                                                                                                                           Nombre de Cession :  ');
  $pdf->Write(5,$nbreappro);
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
