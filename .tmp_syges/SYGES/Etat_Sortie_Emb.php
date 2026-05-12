<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
{
	 include('fpdf.php');
	 include('Connexion.php');
	 include('fonctions.php');
	 $Debut=dateFormatAnglais($_GET["debut"]);
	 $Fin=dateFormatAnglais($_GET["fin"]);
	
	 //Select ds bd
	 $pdf=new FPDF('P','mm','A4');
	 $pdf->SetAutoPageBreak(true,15);
	 $pdf->AddPage();
	 $pdf->SetTitle('ETAT DES SORTIES DE STOCKS EMBALLAGES');
	 $pdf->SetAuthor('SYGES');
	 $pdf->SetSubject('SORTIE STOCK EMB');
	 $pdf->Ln(5);
	 $pdf->SetFont('arial','B',12);
	 $pdf->Write(80,'                             ETAT DES SORTIES DE STOCK EMBALLAGES');
	 $pdf->Ln(3);
	 $pdf->Write(80,'                             _________________________________________');
	 $pdf->Ln(45);                                
	 $pdf->SetFont('arial','B',10);
	 $pdf->Write(2,'                                     Periode :   Du :  ');
	 $pdf->Write(2,dateFormatFrancais($Debut));
	 $pdf->Write(2,'   Au :   ');
	 $pdf->Write(2,dateFormatFrancais($Fin));
	 $pdf->Write(2,'       Date :   ');
	 $pdf->Write(2,date("d/m/y").' '.date('H:i'));
	 $pdf->Ln(10);
	 $pdf->SetFontSize(9);
	 $pdf->SetTextColor(0,0,0);
	 $pdf->SetFillColor(255,255,255);
	 $pdf->Image('IMG\logo.jpg',10,17,180,30);
	 // Deconsignation Approvisionnements
	 $pdf->Cell(180,7,'DECONSIGNATIONS  APPROVISIONNEMENTS',1,1,'C',1);
	 $pdf->Cell(30,7,'CODE',1,0,'L',1);
	 $pdf->Cell(120,7,'LIBELLE',1,0,'L',1);
	 $pdf->Cell(30,7,'QUANTITE',1,1,'C',1);
	 $pdf->SetFont('Arial','',8);
	 $pdf->SetTextColor(0,0,0);
	 
     $nbre=0;
	 $colis=0;
	 //Ici on recupre la liste sans doublons des emballages RTR AU FSSR dans la periode 
	 $sql='SELECT DISTINCT  ID_EMBALLAGE FROM RTREMBFSSR  WHERE DATE_RTREMB BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND STATUT="OK" ';
	 $reponse= $DataBase->query($sql);
	while($rslt= $reponse->fetch())
		{
			//Ici on recupere les quantites du meme emballage puis on somme
			$qte=0;
			$sql1 = 'SELECT  QTE FROM RTREMBFSSR WHERE DATE_RTREMB BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ID_EMBALLAGE="'.$rslt['ID_EMBALLAGE'].'" AND STATUT="OK"';
			$reponse1= $DataBase->query($sql1);
			while($rslt1= $reponse1->fetch())
			{
				$qte=$qte+$rslt1['QTE'];
			}
			//ici on recupere le libelle de l'emballage
			$sql2 = 'SELECT  LIBELLE FROM EMBALLAGE WHERE ID_EMBALLAGE="'.$rslt['ID_EMBALLAGE'].'"';
			$reponse2= $DataBase->query($sql2);
			while($rslt2= $reponse2->fetch())
			{
    			$libelle=$rslt2['LIBELLE'];
			}
			$pdf->Cell(30,7,$rslt['ID_EMBALLAGE'],1);
		  	$pdf->Cell(120,7,$libelle,1);
		  	$pdf->Cell(30,7,$qte,1,1,'C');
			$nbre++;
			$colis=$colis+$qte;
		}
		
	  $pdf->SetFont('Arial','b',8);
	  $pdf->Cell(90,7,'Nombre Emballage(s) : '.$nbre,1,0,'C');
	  $pdf->Cell(90,7, utf8_decode('Total Colis : ').$colis,1,1,'C');
  
  
  // CONSIGNES VENTES
	 $pdf->Cell(180,7,'CONSIGNES VENTES',1,1,'C',1);
	 $pdf->Cell(30,7,'CODE',1,0,'L',1);
	 $pdf->Cell(120,7,'LIBELLE',1,0,'L',1);
	 $pdf->Cell(30,7,'QUANTITE',1,1,'C',1);
	 $pdf->SetFont('Arial','',8);
	 $pdf->SetTextColor(0,0,0);
	 
     $nbre=0;
	 $colis=0;
	//Ici on recupre la liste sans doublons des emballages consignees dans la periode 
    $sql='SELECT DISTINCT  ID_EMBALLAGE FROM CONSIGNE WHERE DATE_CONSIGNE BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND STATUT="Consigne"';
    $reponse= $DataBase->query($sql);
	while($rslt= $reponse->fetch())
		{
			//Ici on recupere les quantites du meme emballage puis on somme
			$qte=0;
			$sql1 = 'SELECT  ID_EMBALLAGE, QTE FROM CONSIGNE C WHERE DATE_CONSIGNE BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ID_EMBALLAGE="'.$rslt['ID_EMBALLAGE'].'" AND STATUT="Consigne"';
			$reponse1= $DataBase->query($sql1);
			while($rslt1= $reponse1->fetch())
			{
				$qte=$qte+$rslt1['QTE'];
			}
			//ici on recupere le libelle de l'emballage
			$sql2 = 'SELECT  LIBELLE FROM EMBALLAGE WHERE ID_EMBALLAGE="'.$rslt['ID_EMBALLAGE'].'"';
			$reponse2= $DataBase->query($sql2);
			while($rslt2= $reponse2->fetch())
			{
    			$libelle=$rslt2['LIBELLE'];
			}
			$pdf->Cell(30,7,$rslt['ID_EMBALLAGE'],1);
		  	$pdf->Cell(120,7,$libelle,1);
		  	$pdf->Cell(30,7,$qte,1,1,'C');
			$nbre++;
			$colis=$colis+$qte;
		}
		
		  $pdf->SetFont('Arial','b',8);
		  $pdf->Cell(90,7,'Nombre Emballage(s) : '.$nbre,1,0,'C');
		  $pdf->Cell(90,7, utf8_decode('Total Colis : ').$colis,1,1,'C');
		  
		  $pdf->Ln(7);                                
		  $pdf->SetFont('arial','B',8);
		  $pdf->Write(2,'                                                                                                                                                 LE RESPONSABLE');
		  $pdf->Ln(1);
$pdf->Write(2,'                                                                                                                                                 ________________');
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
