<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"  || $_SESSION['habilitation']=="Comptable"))
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
	 $pdf->SetTitle('ETAT DES ENTREES EN STOCKS EMBALLAGES');
	 $pdf->SetAuthor('SYGES');
	 $pdf->SetSubject('ENTREE STOCK EMB');
	 $pdf->Ln(5);
	 $pdf->SetFont('arial','B',12);
	 $pdf->Write(80,'                             ETAT DES ENTREES EN STOCK EMBALLAGES');
	 $pdf->Ln(3);
	 $pdf->Write(80,'                             _________________________________________');
	 $pdf->Ln(45);                                
	 $pdf->SetFont('arial','B',10);
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
	 // APPRO EMBALLAGES
	 $pdf->Cell(180,7,'APPROVISIONNEMENTS EMBALLAGES',1,1,'C',1);
	 $pdf->Cell(60,7,'Date',1,0,'C',1);
	 $pdf->Cell(60,7,'Approvisionnement',1,0,'C',1);
	 $pdf->Cell(60,7,'QUANTITE',1,1,'C',1);
	 $pdf->SetFont('Arial','',9);
	 $pdf->SetTextColor(0,0,0);
  
	//Ici on recupere les quantites de la meme article puis on somme
	$nbre=0;
	$qterecu=0;
	$nbre=0;
	$sql1 = 'SELECT  ER.ID_EMBALLAGE, ER.QTERECU, AE.ID_APPRO, AE.DATE_APPRO FROM EMBALLAGE_RECU ER, APPROEMB AE WHERE ER.ID_APPRO=AE.ID_APPRO AND AE.DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ER.ID_EMBALLAGE="'.$_GET['Emb'].'" AND AE.STATUT="V"';
	$reponse1= $DataBase->query($sql1);
		while($rslt1= $reponse1->fetch())
		{
			$qterecu=$qterecu+$rslt1['QTERECU'];
			
			$pdf->Cell(60,7,dateFormatFrancais($rslt1['DATE_APPRO']),1,0,'C');
		  	$pdf->Cell(60,7,$rslt1['ID_APPRO'],1,0,'C');
		  	$pdf->Cell(60,7, $rslt1['QTERECU'],1,1,'C');
			$nbre++;
		}
		
	  $pdf->SetFont('Arial','B',9);
	  $pdf->Cell(90,7,'Nombre Emballage(s) : '.$nbre,1,0,'C');
	  $pdf->Cell(90,7, utf8_decode('Total Colis : ').$qterecu,1,1,'C');
  
  
  // CONSIGNES APPRO
	 $pdf->Cell(180,7,' CONSIGNES APPROVISIONNEMENTS',1,1,'C',1);
	 $pdf->Cell(20,7,' DATE',1,0,'C',1);
	 $pdf->Cell(50,7,' APPROVISIONNEMENT',1,0,'C',1);
	 $pdf->Cell(80,7,' FOURNISSEUR',1,0,'L',1);
	 $pdf->Cell(30,7,' QUANTITE',1,1,'C',1);
	 $pdf->SetFont('Arial','',9);
	 $pdf->SetTextColor(0,0,0);

//Ici on recupere les quantites du meme emballage puis on somme
	$qte2=0;
	$nbre=0;
	$sql1 = 'SELECT  CA.ID_EMBALLAGE, CA.QTE, CA.ID_APPRO, A.ID_APPRO, A.DATE_APPRO FROM CONSIGNEAPP CA, APPROVISIONNEMENT A WHERE CA.ID_APPRO=A.ID_APPRO AND A.DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND CA.ID_EMBALLAGE="'.$_GET['Emb'].'" AND A.STATUT="V"';
	$reponse1= $DataBase->query($sql1);
	while($rslt1= $reponse1->fetch())
		{
			$qte2=$qte2+$rslt1['QTE'];
			
			//ici on recupere le nom du fournisseur
			$sql2 = 'SELECT  F.NOM FROM FOURNISSEUR F, APPROVISIONNEMENT A WHERE A.ID_FOURNISSEUR=F.ID_FOURNISSEUR AND A.ID_APPRO="'.$rslt1['ID_APPRO'].'" ';
			$reponse2= $DataBase->query($sql2);
			while($rslt2= $reponse2->fetch())
			{
				$nomfssr=$rslt2['NOM'];
			}
			$pdf->Cell(20,7,dateFormatFrancais($rslt1['DATE_APPRO']),1,0,'C');
		  	$pdf->Cell(50,7,$rslt1['ID_APPRO'],1,0,'C');
			$pdf->Cell(80,7,$nomfssr,1,0,'L');
		  	$pdf->Cell(30,7,$rslt1['QTE'],1,1,'C');
			$nbre++;
		}
		
		  $pdf->SetFont('Arial','B',9);
		  $pdf->Cell(90,7,'Nombre Emballage(s) : '.$nbre,1,0,'C');
		  $pdf->Cell(90,7, utf8_decode('Total Colis : ').$qte2,1,1,'C');
  
   // DECONSIGNATIONS CLIENTS
	 $pdf->Cell(180,7,'DECONSIGNATIONS CLIENTS',1,1,'C',1);
	 $pdf->Cell(20,7,' DATE',1,0,'L',1);
	 $pdf->Cell(50,7,' VENTE',1,0,'C',1);
	 $pdf->Cell(80,7,' CLIENT',1,0,'L',1);
	 $pdf->Cell(30,7,' QUANTITE',1,1,'C',1);
	 $pdf->SetFont('Arial','',9);
	 $pdf->SetTextColor(0,0,0);
	 
	$nbre=0;
	$qte3=0;
			//Ici on recupere les quantites du meme emballage puis on somme
			$sql1 = 'SELECT  ID_EMBALLAGE, QTE, ID_SORTIESTOCK, DATE_DECONSIGNE FROM CONSIGNE  WHERE DATE_DECONSIGNE BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ID_EMBALLAGE="'.$_GET['Emb'].'" AND STATUT="Deconsigne"';
			$reponse1= $DataBase->query($sql1);
			while($rslt1= $reponse1->fetch())
			{
				$qte3=$qte3+$rslt1['QTE'];
			
				//ici on recupere le nom du client
				$sql2 = 'SELECT  C.NOM FROM CLIENT C, SORTIE_STOCK ST WHERE C.ID_CLIENT=ST.ID_CLIENT AND ST.ID_SORTIESTOCK="'.$rslt1['ID_SORTIESTOCK'].'"';
				$reponse2= $DataBase->query($sql2);
				while($rslt2= $reponse2->fetch())
				{
    				$nomclt=$rslt2['NOM'];
				}
				$pdf->Cell(20,7,dateFormatFrancais($rslt1['DATE_DECONSIGNE']),1);
		  		$pdf->Cell(50,7,$rslt1['ID_SORTIESTOCK'],1,0,'C');
				$pdf->Cell(80,7,$nomclt,1,0,'L');
		  		$pdf->Cell(30,7,$rslt1['QTE'],1,1,'C');
				$nbre++;
			}
		  	$pdf->SetFont('Arial','b',8);
		  	$pdf->Cell(90,7,'Nombre Emballage(s) : '.$nbre,1,0,'C');
		  	$pdf->Cell(90,7, utf8_decode('Total Colis : ').$qte3,1,1,'C');
		  
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
