<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"  || $_SESSION['habilitation']=="Comptable"))
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
 $pdf->SetTitle('Brouillard');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('Brouillard');
 $pdf->Ln(15);
 $pdf->SetFont('arial','B',16);
 $pdf->Write(55,'                                     BROUILLARD DE CAISSE');
 $pdf->Ln(1);
 $pdf->Write(55,'                                     ______________________');
 $pdf->Ln(35);                                 
 $pdf->SetFont('arial','B',10);
 $pdf->Write(2,'Periode :   Du :  ');
 $pdf->Write(2,dateFormatFrancais($Debut));
 $pdf->Write(2,'   Au :   ');
 $pdf->Write(2,dateFormatFrancais($Fin));
  $pdf->Write(2,'                    Date :  ');
 $pdf->Write(2,date("d/m/Y").' '.date('H:i'));
 $pdf->Ln(10);
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',15,15,180,30);
 $pdf->Cell(20,7,'DATE',1,0,'L',1);
 $pdf->Cell(80,7,'LIBELLE',1,0,'L',1);
 $pdf->Cell(25,7,'RECETTE',1,0,'C',1);
 $pdf->Cell(25,7,'DEPENSE',1,0,'C',1);
 $pdf->Cell(25,7,'SOLDE',1,1,'C',1);
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
 
 //ici on reporte le solde initial
 $sldcourant=$_GET['Sld'];
 $pdf->Cell(20,7,dateFormatFrancais($Debut),1,0,'L',1);
 $pdf->Cell(80,7,'Report Solde Initial / Veille',1,0,'L',1);
 $pdf->Cell(25,7,number_format($_GET['Sld'], 0, ',', ' ').' F',1,0,'C',1);
 $pdf->Cell(25,7,'',1,0,'C',1);
 $pdf->Cell(25,7,number_format($sldcourant, 0, ',', ' ').' F',1,1,'C',1);
 //ici on recupere tous les apports
 $mta=0;
 $sql='SELECT * FROM APPORT WHERE DATE_APPORT BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND STATUT="V" ORDER BY DATE_APPORT' ;
 $reponse= $DataBase->query($sql);
  while($rslt= $reponse->fetch())
 {
  $sldcourant=$sldcourant+$rslt['MONTANT'];
  $mta=$mta+$rslt['MONTANT'];	 
  $pdf->Cell(20,7,dateFormatFrancais($rslt['DATE_APPORT']),1,0,'L');
  $pdf->Cell(80,7,$rslt['LIBELLE'],1);
  $pdf->Cell(25,7,number_format($rslt['MONTANT'], 0, ',', ' ').' F',1,0,'C');
  $pdf->Cell(25,7,' ',1,0,'C');
  $pdf->Cell(25,7,number_format($sldcourant, 0, ',', ' ').' F',1,1,'C');
  }
  //ici on recupere le total des ventes boutiques
$mtvte=0;
$fraisenlevement=0;
$sql2='SELECT * FROM SORTIE_STOCK WHERE DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND STATUT="V"' ;
$reponse2= $DataBase->query($sql2);
while($rslt2= $reponse2->fetch())
{
	//Ici on recupere les articles de chacune des ventes puis on somme les prix 
	 $sql3='SELECT * FROM  ARTICLEVENDU WHERE ID_SORTIESTOCK= "'.$rslt2['ID_SORTIESTOCK'].'" ' ;
	 $reponse3= $DataBase->query($sql3);
  	while($rslt3= $reponse3->fetch())
 	{
	 	$mtvte= ($mtvte + $rslt3['PRIXVENTE']);	 
 	}
	$fraisenlevement = ($fraisenlevement + $rslt2['FRAISENLEVEMENT']);
}
//on reporte le montant des ventes boutiques
  $sldcourant=$sldcourant+$mtvte;
  $pdf->Cell(20,7,utf8_decode('Période'),1,0,'L');
  $pdf->Cell(80,7,'Vente Boutique/Magasin Du : '.dateFormatFrancais($Debut).'  Au : '.dateFormatFrancais($Fin),1);
  $pdf->Cell(25,7,number_format($mtvte, 0, ',', ' ').' F',1,0,'C');
  $pdf->Cell(25,7,' ',1,0,'C');
  $pdf->Cell(25,7,number_format($sldcourant, 0, ',', ' ').' F',1,1,'C');	
//on reporte le montant des frais d'enlevement
  $sldcourant=$sldcourant+$fraisenlevement;
  $pdf->Cell(20,7,utf8_decode('Période'),1,0,'L');
  $pdf->Cell(80,7,'Frais Enlevement Du : '.dateFormatFrancais($Debut).'  Au : '.dateFormatFrancais($Fin),1);
  $pdf->Cell(25,7,number_format($fraisenlevement, 0, ',', ' ').' F',1,0,'C');
  $pdf->Cell(25,7,' ',1,0,'C');
  $pdf->Cell(25,7,number_format($sldcourant, 0, ',', ' ').' F',1,1,'C');
//ici on recupere le total des ventes frigo
$mtvtf=0;
$sql4='SELECT * FROM SORTIE_STOCK_FRIGO WHERE DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND STATUT="V"' ;
$reponse4= $DataBase->query($sql4);
while($rslt4= $reponse4->fetch())
{
	//Ici on recupere les articles de chacune des ventes frigo puis on somme les prix 
	 $sql5='SELECT * FROM  ARTICLEVENDU_FRIGO WHERE ID_SORTIESTOCK= "'.$rslt4['ID_SORTIESTOCK'].'" ' ;
	 $reponse5= $DataBase->query($sql5);
  	while($rslt5= $reponse5->fetch())
 	{
	 	$mtvtf= ($mtvtf + $rslt5['PRIXVENTE']);	 
 	}
}	
//on reporte le montant des ventes frigo
  $sldcourant=$sldcourant+$mtvtf;
  $pdf->Cell(20,7,utf8_decode('Période'),1,0,'L');
  $pdf->Cell(80,7,'Vente Frigo Du : '.dateFormatFrancais($Debut).'  Au : '.dateFormatFrancais($Fin),1);
  $pdf->Cell(25,7,number_format($mtvtf, 0, ',', ' ').' F',1,0,'C');
  $pdf->Cell(25,7,' ',1,0,'C');
  $pdf->Cell(25,7,number_format($sldcourant, 0, ',', ' ').' F',1,1,'C');
//ici on recupere les consignes clients
$mtcc=0;
$sql6='SELECT C.ID_SORTIESTOCK, C.DATE_CONSIGNE, C.MONTANT, C.ID_EMBALLAGE,C.QTE, E.ID_EMBALLAGE, E.LIBELLE FROM CONSIGNE C, EMBALLAGE E WHERE C.ID_EMBALLAGE=E.ID_EMBALLAGE AND C.DATE_CONSIGNE BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND C.STATUT!="NV" ORDER BY C.DATE_CONSIGNE' ;
$reponse6= $DataBase->query($sql6);
while($rslt6= $reponse6->fetch())
	{
		$sldcourant=$sldcourant+$rslt6['MONTANT'];
		$mtcc=$mtcc+$rslt6['MONTANT'];			
		$pdf->Cell(20,7,dateFormatFrancais($rslt6['DATE_CONSIGNE']),1,0,'L');
		$pdf->Cell(80,7,'Consigne : '.$rslt6['ID_SORTIESTOCK'].'/'.$rslt6['LIBELLE'].' ('.$rslt6['QTE'].')',1);
		$pdf->Cell(25,7,number_format($rslt6['MONTANT'], 0, ',', ' ').' F',1,0,'C');
		$pdf->Cell(25,7,' ',1,0,'C');
		$pdf->Cell(25,7,number_format($sldcourant, 0, ',', ' ').' F',1,1,'C');
               
	 }
//ici on recupere les deconsignes fssr
$mtre=0;
$sql7='SELECT R.ID_APPRO, R.ID_EMBALLAGE,R.QTE,R.DATE_RTREMB, R.MONTANT, E.LIBELLE FROM RTREMBFSSR R, EMBALLAGE E WHERE R.ID_EMBALLAGE=E.ID_EMBALLAGE AND R.DATE_RTREMB BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND R.STATUT="OK" ORDER BY R.DATE_RTREMB';
$reponse7= $DataBase->query($sql7);
while($rslt7= $reponse7->fetch())
		{
			$sldcourant=$sldcourant+$rslt7['MONTANT'];
			$mtre=$mtre+$rslt7['MONTANT'];
            $pdf->Cell(20,7,dateFormatFrancais($rslt7['DATE_RTREMB']),1,0,'L');
            $pdf->Cell(80,7,'Deconsignation(s) Fournisseur(s) : '.$rslt7['ID_APPRO'].'/'.$rslt7['LIBELLE'].' ('.$rslt7['QTE'].')',1);
            $pdf->Cell(25,7,number_format($rslt7['MONTANT'], 0, ',', ' ').' F',1,0,'C');
			$pdf->Cell(25,7,' ',1,0,'C');
			$pdf->Cell(25,7,number_format($sldcourant, 0, ',', ' ').' F',1,1,'C');
		 }
//ici on recupere les deconsignes clt
$mtdc=0;
$sql9='SELECT C.ID_SORTIESTOCK, C.ID_EMBALLAGE,C.QTE,C.DATE_DECONSIGNE, C.MONTANT, E.ID_EMBALLAGE, E.LIBELLE FROM CONSIGNE C, EMBALLAGE E WHERE C.ID_EMBALLAGE=E.ID_EMBALLAGE AND C.DATE_DECONSIGNE BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND C.STATUT="Deconsigne" ORDER BY C.DATE_DECONSIGNE';
$reponse9= $DataBase->query($sql9);
while($rslt9= $reponse9->fetch())
		{
			$sldcourant=$sldcourant-$rslt9['MONTANT'];
			$mtdc=$mtdc+$rslt9['MONTANT'];
            $pdf->Cell(20,7,dateFormatFrancais($rslt9['DATE_DECONSIGNE']),1,0,'L');
            $pdf->Cell(80,7,'Deconsignation Clt : '.$rslt9['ID_SORTIESTOCK'].'/'.$rslt9['LIBELLE'].' ('.$rslt9['QTE'].')',1);
			$pdf->Cell(25,7,' ',1,0,'C');
            $pdf->Cell(25,7,number_format($rslt9['MONTANT'], 0, ',', ' ').' F',1,0,'C');
			$pdf->Cell(25,7,number_format($sldcourant, 0, ',', ' ').' F',1,1,'C');
		 }
//ici on recupere les deconsignes VTE
$mtrevte=0;
$sql11='SELECT R.ID_SORTIESTOCK, R.ID_EMBALLAGE,R.QTE,R.DATE_RTREMB, R.MONTANT, E.LIBELLE FROM RTREMBVTE R, EMBALLAGE E WHERE R.ID_EMBALLAGE=E.ID_EMBALLAGE AND R.DATE_RTREMB BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND R.STATUT="V" ORDER BY R.DATE_RTREMB';
$reponse11= $DataBase->query($sql11);
while($rslt11= $reponse11->fetch())
		{
			$sldcourant=$sldcourant-$rslt11['MONTANT'];
			$mtrevte=$mtrevte+$rslt11['MONTANT'];
            $pdf->Cell(20,7,dateFormatFrancais($rslt11['DATE_RTREMB']),1,0,'L');
            $pdf->Cell(80,7,'Deconsignation Vente : '.$rslt11['ID_SORTIESTOCK'].'/'.$rslt11['LIBELLE'].' ('.$rslt11['QTE'].')',1);
			$pdf->Cell(25,7,' ',1,0,'C');
            $pdf->Cell(25,7,number_format($rslt11['MONTANT'], 0, ',', ' ').' F',1,0,'C');
			$pdf->Cell(25,7,number_format($sldcourant, 0, ',', ' ').' F',1,1,'C');
		 }
//ici on recupere les consignes fssr
$mtcf=0;
$sql8='SELECT C.ID_APPRO, C.ID_EMBALLAGE, C.DATE_CONSIGNE, C.QTE, C.MONTANT, E.ID_EMBALLAGE, E.LIBELLE FROM CONSIGNEAPP C, EMBALLAGE E WHERE C.ID_EMBALLAGE=E.ID_EMBALLAGE AND C.DATE_CONSIGNE BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND C.STATUT!="NV" ORDER BY C.DATE_CONSIGNE';
$reponse8= $DataBase->query($sql8);
while($rslt8= $reponse8->fetch())
		{
			$sldcourant=$sldcourant-$rslt8['MONTANT'];
			$mtcf=$mtcf+$rslt8['MONTANT'];			
			$pdf->Cell(20,7,dateFormatFrancais($rslt8['DATE_CONSIGNE']),1,0,'L');
			$pdf->Cell(80,7,'Consigne : '.$rslt8['ID_APPRO'].'/'.$rslt8['LIBELLE'].' ('.$rslt8['QTE'].')',1);
			$pdf->Cell(25,7,' ',1,0,'C');
			$pdf->Cell(25,7,number_format($rslt8['MONTANT'], 0, ',', ' ').' F',1,0,'C');
			$pdf->Cell(25,7,number_format($sldcourant, 0, ',', ' ').' F',1,1,'C');
		 }
//ici on recupere les charges
$mtchg=0;
$sql10='SELECT C.ID_CHARGE, C.ID_TYPECHARGE, C.DESCRIPTION, C.MONTANT, C.DATE_CHARGE, TC.LIBELLE, TC.ID_TYPECHARGE FROM CHARGE C, TYPE_CHARGE TC WHERE C.ID_TYPECHARGE=TC.ID_TYPECHARGE  AND C.STATUT="V" AND C.DATE_CHARGE BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY C.DATE_CHARGE' ;
$reponse10= $DataBase->query($sql10);
while($rslt10= $reponse10->fetch())
		{
			$sldcourant=$sldcourant-$rslt10['MONTANT'];
			$mtchg=$mtchg+$rslt10['MONTANT'];
			$pdf->Cell(20,7,dateFormatFrancais($rslt10['DATE_CHARGE']),1,0,'L');
			$pdf->Cell(80,7,$rslt10['LIBELLE'].'/'.$rslt10['DESCRIPTION'],1);
			$pdf->Cell(25,7,' ',1,0,'C');
			$pdf->Cell(25,7,number_format($rslt10['MONTANT'], 0, ',', ' ').' F',1,0,'C');
			$pdf->Cell(25,7,number_format($sldcourant, 0, ',', ' ').' F',1,1,'C');
		 }
  //ecriture des totaux
  $pdf->SetFont('arial','B',10);
  $pdf->Cell(100,7,'Sous-Totaux : ',1,0,'C');
  $pdf->Cell(25,7,number_format($_GET['Sld']+$mta+$mtvte+$mtvtf+$mtcc+$mtre+$fraisenlevement, 0, ',', ' ').' F',1,0,'C');
  $pdf->Cell(25,7,number_format($mtdc+$mtcf+$mtchg+$mtrevte, 0, ',', ' ').' F',1,0,'C');
  $pdf->Cell(25,7,number_format($sldcourant, 0, ',', ' ').' F',1,1,'C');
  //ecriture solde final
  $pdf->SetFont('arial','B',14);
  $pdf->Cell(175,7,'SOLDE FINAL : '.number_format($sldcourant, 0, ',', ' ').' FCFA',1,0,'C');
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
