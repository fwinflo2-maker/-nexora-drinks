<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" || $_SESSION['habilitation']=="Comptable" ))
{
	 include('fpdf.php');
	 include('Connexion.php');
	 include('fonctions.php');
	 $Debut=dateFormatAnglais($_GET["debut"]);
	 $Fin=dateFormatAnglais($_GET["fin"]);
		//ici on recupere le libelle de l'emballage
		$sql3 = 'SELECT  LIBELLE FROM EMBALLAGE WHERE ID_EMBALLAGE="'.$_GET['Emb'].'"';
		$reponse3= $DataBase->query($sql3);
		while($rslt3= $reponse3->fetch())
		{
    		$libelle=$rslt3['LIBELLE'];
		}
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
	 $pdf->SetFont('arial','B',9);
	 $pdf->Write(2,'                                     Emballage :  '.$libelle);
	 $pdf->Ln(7);
	 $pdf->Write(2,'                                     Periode :   Du :  '.dateFormatFrancais($Debut).'   Au :   '.dateFormatFrancais($Fin));
	 $pdf->Ln(7);
	 $pdf->Write(2,'                                     Date :   '.date("d/m/y").' '.date('H:i'));
	 $pdf->Ln(7);
	 $pdf->SetFontSize(9);
	 $pdf->SetTextColor(0,0,0);
	 $pdf->SetFillColor(255,255,255);
	 $pdf->Image('IMG\logo.jpg',10,17,180,30);
	 // Deconsignation Approvisionnements
	 $pdf->Cell(180,7,' DECONSIGNATIONS  APPROVISIONNEMENTS',1,1,'C',1);
	 $pdf->Cell(30,7,' DATE',1,0,'L',1);
	 $pdf->Cell(40,7,' APPROVISIONNEMENT',1,0,'C',1);
	 $pdf->Cell(80,7,' FOURNISSEUR',1,0,'L',1);
	 $pdf->Cell(30,7,' QUANTITE',1,1,'C',1);
	 $pdf->SetFont('Arial','',9);
	 $pdf->SetTextColor(0,0,0);
	 
	$nbre=0;
	$qte=0;

			//Ici on recupere les quantites du meme emballage puis on somme
			$qterecu=0;
			$sql1 = 'SELECT  QTE, ID_APPRO,DATE_RTREMB  FROM RTREMBFSSR WHERE DATE_RTREMB BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ID_EMBALLAGE="'.$_GET['Emb'].'" AND STATUT="OK"';
			$reponse1= $DataBase->query($sql1);
			while($rslt1= $reponse1->fetch())
			{
				$qte=$qte+$rslt1['QTE'];
				//ici on recupere le nom du fournisseur
				$sql2 = 'SELECT  F.NOM FROM FOURNISSEUR F, APPROVISIONNEMENT A WHERE A.ID_FOURNISSEUR=F.ID_FOURNISSEUR AND A.ID_APPRO="'.$rslt1['ID_APPRO'].'" ';
				$reponse2= $DataBase->query($sql2);
				while($rslt2= $reponse2->fetch())
				{
					$nomfssr=$rslt2['NOM'];
				}
				$pdf->Cell(30,7,dateFormatFrancais($rslt1['DATE_RTREMB']),1);
				$pdf->Cell(40,7,$rslt1['ID_APPRO'],1);
				$pdf->Cell(80,7,$nomfssr,1);
				$pdf->Cell(30,7,$rslt1['QTE'],1,1,'C');
				$nbre++;
		    }
		
	  $pdf->SetFont('Arial','B',9);
	  $pdf->Cell(90,7,'Nombre Emballage(s) : '.$nbre,1,0,'C');
	  $pdf->Cell(90,7, utf8_decode('Total Colis : ').$qte,1,1,'C');
  
  
  // CONSIGNES VENTES
  	$pdf->SetFont('arial','B',9);
	 $pdf->Cell(180,7,'CONSIGNES VENTES',1,1,'C',1);
	 $pdf->Cell(30,7,'DATE',1,0,'L',1);
	 $pdf->Cell(40,7,'VENTE',1,0,'C',1);
	 $pdf->Cell(80,7,'CLIENT',1,0,'C',1);
	 $pdf->Cell(30,7,'QUANTITE',1,1,'C',1);
	 $pdf->SetFont('Arial','',9);
	 $pdf->SetTextColor(0,0,0);
	 
	$colis=0;
	$qte2=0;
	//Ici on recupere les quantites du meme emballage puis on somme
	$qte=0;
	$sql1 = 'SELECT  ID_EMBALLAGE, QTE, DATE_CONSIGNE, ID_SORTIESTOCK FROM CONSIGNE C WHERE DATE_CONSIGNE BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ID_EMBALLAGE="'.$_GET['Emb'].'" AND STATUT!="NV"';
	$reponse1= $DataBase->query($sql1);
	while($rslt1= $reponse1->fetch())
		{
			$qte2=$qte2+$rslt1['QTE'];
			
			//ici on recupere le nom du client
			$sql2 = 'SELECT  C.NOM FROM CLIENT C, SORTIE_STOCK ST WHERE C.ID_CLIENT=ST.ID_CLIENT AND ST.ID_SORTIESTOCK="'.$rslt1['ID_SORTIESTOCK'].'"';
			$reponse2= $DataBase->query($sql2);
			while($rslt2= $reponse2->fetch())
			{
    			$nomclt=$rslt2['NOM'];
			}

			$pdf->Cell(30,7,dateFormatFrancais($rslt1['DATE_CONSIGNE']),1);
		  	$pdf->Cell(40,7,$rslt1['ID_SORTIESTOCK'],1);
			$pdf->Cell(80,7,$nomclt,1);
		  	$pdf->Cell(30,7,$rslt1['QTE'],1,1,'C');
			$nbre++;
		}
		
		  $pdf->SetFont('Arial','b',8);
		  $pdf->Cell(90,7,'Nombre Emballage(s) : '.$nbre,1,0,'C');
		  $pdf->Cell(90,7, utf8_decode('Total Colis : ').$qte2,1,1,'C');
		  
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
