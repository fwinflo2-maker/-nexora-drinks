<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur"))
{
 include('fpdf.php');
 include('Connexion.php');
 include('fonctions.php');
 $Debut=dateFormatAnglais($_GET["debut"]);
 $Fin=dateFormatAnglais($_GET["fin"]);
	if ($_GET['Stat']=='Mixte')
	{	
	$sql='SELECT C.ID_APPRO, C.ID_EMBALLAGE, C.DATE_CONSIGNE, C.QTE, C.STATUT, C.PU, C.MONTANT, E.ID_EMBALLAGE, E.LIBELLE FROM CONSIGNEAPP C, EMBALLAGE E WHERE C.ID_EMBALLAGE=E.ID_EMBALLAGE AND C.DATE_CONSIGNE BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY C.ID_APPRO';
	}
	else
		if ($_GET['Stat']=='NV')
		{
			$sql='SELECT C.ID_APPRO, C.ID_EMBALLAGE, C.DATE_CONSIGNE, C.QTE, C.STATUT, C.PU, C.MONTANT, E.ID_EMBALLAGE, E.LIBELLE FROM CONSIGNEAPP C, EMBALLAGE E WHERE C.ID_EMBALLAGE=E.ID_EMBALLAGE AND C.DATE_CONSIGNE BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND C.STATUT="NV" ORDER BY C.ID_APPRO';
		}
		else
		{
			$sql='SELECT C.ID_APPRO, C.ID_EMBALLAGE, C.DATE_CONSIGNE, C.QTE, C.STATUT, C.PU, C.MONTANT, E.ID_EMBALLAGE, E.LIBELLE FROM CONSIGNEAPP C, EMBALLAGE E WHERE C.ID_EMBALLAGE=E.ID_EMBALLAGE AND C.DATE_CONSIGNE BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND C.STATUT!="NV" ORDER BY C.ID_APPRO';
		}
 //Select ds bd
 $pdf=new FPDF('L','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('ETAT DES CONSIGNES ');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('Vente');
 $pdf->Ln(3);
 $pdf->SetFont('arial','B',14);
 $pdf->Write(80,'                                                  ETAT DES CONSIGNES DES APPROS');
 $pdf->Ln(3);
 $pdf->Write(80,'                                                  ________________________________');
 $pdf->Ln(45);                                
 $pdf->SetFont('arial','B',10);
 $pdf->Write(2,'Periode de vente:   Du :  ');
 $pdf->Write(2,dateFormatFrancais($Debut));
 $pdf->Write(2,'   Au :   ');
 $pdf->Write(2,dateFormatFrancais($Fin));
 $pdf->Write(2,'              Statut :  ');
 $pdf->Write(2,$_GET['Stat']);
 $pdf->Write(2,'              Date :  ');
 $pdf->Write(2,date("d/m/y").' '.date('H:i'));
 $pdf->Ln(10);
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
  $pdf->SetFont('arial','B',9);
 $pdf->Image('IMG\logo.jpg',15,17,250,30);
 $pdf->Cell(25,7,'DATE APPRO',1,0,'L',1);
 $pdf->Cell(25,7,'CODE APPRO',1,0,'L',1);
 $pdf->Cell(40,7,'FOURNISSEUR',1,0,'C',1);
 $pdf->Cell(30,7,'DATE CONSIGNE',1,0,'L',1);
 $pdf->Cell(50,7,'EMBALLAGE',1,0,'L',1);
 $pdf->Cell(15,7,'QTE',1,0,'C',1);
 $pdf->Cell(25,7,'P.U.',1,0,'C',1);
 $pdf->Cell(30,7,'MONTANT',1,0,'C',1);
 $pdf->Cell(25,7,'STATUT',1,1,'L',1);
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
  
 $MT=0;
 $nbre=0;
 $reponse= $DataBase->query($sql);
 while($rslt= $reponse->fetch())
 {
 //ici on recupere la date de la vente et le client
	 $sql1 = 'SELECT A.ID_APPRO, A.DATE_APPRO, A.ID_FOURNISSEUR, F.ID_FOURNISSEUR, F.NOM FROM APPROVISIONNEMENT A, FOURNISSEUR F WHERE A.ID_FOURNISSEUR=F.ID_FOURNISSEUR AND A.ID_APPRO="'.$rslt['ID_APPRO'].'"';
	$reponse1= $DataBase->query($sql1);
		while($rslt2= $reponse1->fetch())
		{
			$date=$rslt2['DATE_APPRO'];
    		$nom=$rslt2['NOM'];
		}
	 //
	 
  $pdf->Cell(25,7,dateFormatFrancais($date),1,0,'L');
  $pdf->Cell(25,7,$rslt['ID_APPRO'],1,0,'C');
  $pdf->Cell(40,7,$nom,1);
  $pdf->Cell(30,7,dateFormatFrancais($rslt['DATE_CONSIGNE']),1,0,'C');
  $pdf->Cell(50,7,$rslt['LIBELLE'],1,0,'L');
  $pdf->Cell(15,7,$rslt['QTE'],1,0,'C');
  $pdf->Cell(25,7,$rslt['PU'].' FCFA',1,0,'C');
  $pdf->Cell(30,7,$rslt['MONTANT'].' FCFA',1,0,'C');
  $pdf->Cell(25,7,utf8_decode($rslt['STATUT']),1,1,'L');
  $nbre++;
  $MT = ($MT + $rslt['MONTANT']);
  }
  //Ecriture des totaux
 $pdf->SetFont('arial','B',10);
 $pdf->Cell(25,7,'Totaux :',1,0,'L',1);
 $pdf->Cell(25,7,'-//-',1,0,'L',1);
 $pdf->Cell(40,7,'Nbre de consigne : '.$nbre,1,0,'L',1);
 $pdf->Cell(30,7,'-//-',1,0,'C',1);
 $pdf->Cell(50,7,'-//-',1,0,'C',1);
 $pdf->Cell(15,7,'-//-',1,0,'C',1);
 $pdf->Cell(25,7,'-//-',1,0,'C',1);
 $pdf->Cell(30,7,number_format($MT, 0, ',', ' ').' FCFA',1,0,'L',1);
 $pdf->Cell(25,7,'-//-',1,1,'C',1);
  
 
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
