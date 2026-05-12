<?php
session_start();
if (isset ($_SESSION['habilitation']))
{
 include('fpdf.php');
 include('fonctions.php');
 include('Connexion.php');
 //ici on recupere les infos sur le client de la vente 
 $fraisenlevement=0;
 $sql='SELECT C.ID_CLIENT, C.NOM, C.FRAISENLEVEMENT,C.FRAISENLEVEMENT_PET, ST.ID_CLIENT, ST.ID_SORTIESTOCK,ST.CREDITRISTOURNE, ST.DATESORTIESTOCK FROM CLIENT C, SORTIE_STOCK ST WHERE C.ID_CLIENT=ST.ID_CLIENT AND ST.ID_SORTIESTOCK="'.$_GET['Id'].'" ';
 $reponse= $DataBase->query($sql);
 while($rslt= $reponse->fetch())
 {
  $nom=$rslt['NOM'];
  $Clt=$rslt['ID_CLIENT'];
  $Date=$rslt['DATESORTIESTOCK'];
  $fraisenlevement_cassier=$rslt['FRAISENLEVEMENT'];
  $fraisenlevement_pet=$rslt['FRAISENLEVEMENT_PET'];
   $creditristourne=$rslt['CREDITRISTOURNE'];
 }
 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(true);
 $pdf->AddPage();
 $pdf->SetTitle('Recu');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('Recu Vente');
 $pdf->Ln(0);
 $pdf->SetFont('arial','B',10);
 $pdf->Write(10, utf8_decode('                                                                               SODIBONO '));
 $pdf->SetFont('arial','B',9);
 $pdf->Ln(3);
 $pdf->Write(15, utf8_decode('DISTRIBUTEUR SABC  NIU. M030100015674K  RCCM: RC/NGA/2010/B/165 /Tel: 699 49 95 91/677 68 58 84 /MAG : TOUBORO'));
 $pdf->Ln(1);
  $pdf->Write(15, utf8_decode('                                                                                     ________________ '));
 $pdf->Ln(5);
 $pdf->SetFont('arial','B',8);
 $pdf->Write(20, utf8_decode('     FACTURE : '));
  $pdf->SetFont('arial','',8);
 $pdf->Write(20,$_GET['Id'].'       Date :'.dateFormatFrancais($Date).' '.date('H:i').'                      Operateur :    '.$_SESSION['login']);
 $pdf->Ln(1);
 $pdf->SetFont('arial','B',8);
 $pdf->Write(25,'     DOIT : ');
  $pdf->SetFont('arial','',8);
 $pdf->Write(25,$nom.' ('.$Clt.')');
 $pdf->Ln(15);                                                  
 $pdf->SetFontSize(8);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->Cell(60,5,'LIBELLE',1,0,'L',1);
 $pdf->Cell(40,5,'CONDITIONNEMENT',1,0,'L',1);
 $pdf->Cell(10,5,'QTE',1,0,'L',1);
 $pdf->Cell(20,5,'P.U',1,0,'L',1);
 $pdf->Cell(20,5,'P.T',1,0,'C',1);
 $pdf->Cell(30,5,'OBSERVATION',1,1,'L',1);
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
  
  $MT=0;
  $ttcolis=0;
  $nbrecasier=0;
  $nbrepet=0;
  $PU=0;
  $sql2='SELECT A.ID_ARTICLE, A.LIBELLE, A.MARQUE, A.NBREBTE, AV.ID_ARTICLE, AV.QTESORTIE, AV.PRIXVENTE, AV.OBSERVATION, AV.ID_SORTIESTOCK FROM ARTICLE A, ARTICLEVENDU AV WHERE A.ID_ARTICLE=AV.ID_ARTICLE AND AV.ID_SORTIESTOCK="'.$_GET['Id'].'"';
  $reponse2= $DataBase->query($sql2);
  while($rslt2= $reponse2->fetch())
 {
  		//on compte les colis
	  $ttcolis=$ttcolis+$rslt2['QTESORTIE'];
	  //on compte les casiers
	  if(($rslt2['MARQUE']=="CASIER") || ($rslt2['MARQUE']=="casier")|| ($rslt2['MARQUE']=="CASIERS")|| ($rslt2['MARQUE']=="casiers"))
	  {
		  $nbrecasier=$nbrecasier+$rslt2['QTESORTIE'];
	  }
  $MT=$MT+$rslt2['PRIXVENTE'];
  $PU=$rslt2['PRIXVENTE']/$rslt2['QTESORTIE'];
  $pdf->Cell(60,5,$rslt2['LIBELLE'],1,0,'L');
  $pdf->Cell(40,5,$rslt2['MARQUE'].' '.$rslt2['NBREBTE'],1);
  $pdf->Cell(10,5,$rslt2['QTESORTIE'],1,0,'C');
  $pdf->Cell(20,5,$PU,1,0,'C');
  $pdf->Cell(20,5,number_format($rslt2['PRIXVENTE'], 0, ',', ' '),1,0,'C');
  $pdf->Cell(30,5,$rslt2['OBSERVATION'],1,1,'L');
  }
  $pdf->SetFont('Arial','B',8);
  $pdf->Cell(50,5,' TOTAL COLIS  : '.number_format($ttcolis, 0, ',', ' '),1,0,'C');
  $pdf->Cell(50,5,' TOTAL CASIERS  : '.number_format($nbrecasier, 0, ',', ' '),1,0,'C');
  $pdf->Cell(80,5,'MONTANT ARTICLES (1) :  '.number_format($MT, 0, ',', ' ').' F',1,1,'C');
		//ON CALCULE LES FRAIS D'ENLEVEMENT
  $pdf->SetFont('Times','B',8);
  	//cacul nbre PET
	$nbrepet=$ttcolis-$nbrecasier;
   		//ON CALCULE LES FRAIS D'ENLEVEMENT
  $ttfraisenlevement=0;
  $ttfraisenlevement=($nbrecasier*$fraisenlevement_cassier)+($nbrepet*$fraisenlevement_pet);
  $pdf->Cell(100,5,' PU ENLEVEMENT  : CASSIER= '.number_format($fraisenlevement_cassier, 0, ',', '').' F/PET='.number_format($fraisenlevement_pet.' F', 0, ',', ' '),1,0,'C');
  $pdf->Cell(80,5,'MONTANT FRAIS ENLEVEMENT (2): '.number_format($ttfraisenlevement, 0, ',', ' ').'F',1,1,'C');
  //affichage des consignes emballages
    $pdf->SetFont('Arial','',8);
	$MTC=0;
    $ttcolisC=0;
    $sql3='SELECT E.ID_EMBALLAGE, E.LIBELLE, E.MT_CONSIGNE, C.ID_EMBALLAGE, C.ID_SORTIESTOCK, C.QTE, C.MONTANT FROM EMBALLAGE E, CONSIGNE C WHERE E.ID_EMBALLAGE=C.ID_EMBALLAGE AND C.ID_SORTIESTOCK="'.$_GET['Id'].'"';
  $reponse3= $DataBase->query($sql3);
  while($rslt3= $reponse3->fetch())
 {
  $ttcolisC=$ttcolisC+$rslt3['QTE'];

  $MTC=$MTC+$rslt3['MONTANT'];
  $pdf->Cell(30,5,'CONSIGNE',1,0,'L');
  $pdf->Cell(55,5,$rslt3['LIBELLE'],1);
  $pdf->Cell(15,5,$rslt3['QTE'],1,0,'C');
  $pdf->Cell(30,5,$rslt3['MT_CONSIGNE'].' F',1,0,'C');
  $pdf->Cell(50,5,number_format($rslt3['MONTANT'], 0, ',', ' ').' F',1,1,'C');
  }
  $pdf->SetFont('Arial','B',8);
  $pdf->Cell(100,5,'TOTAL EMBALLAGES CONSINES : '.number_format($ttcolisC, 0, ',', ' '),1,0,'C');
  $pdf->Cell(80,5,'MONTANT CONSIGNES (3) :  '.number_format($MTC, 0, ',', ' ').' F',1,1,'C');
  
  //affichage des deconsignations emballages
  
    $MTD=0;
	$ttcolisD=0;
    $sql3='SELECT E.ID_EMBALLAGE, E.LIBELLE, E.MT_CONSIGNE, D.ID_EMBALLAGE, D.ID_SORTIESTOCK, D.QTE, D.MONTANT FROM EMBALLAGE E, RTREMBVTE D WHERE E.ID_EMBALLAGE=D.ID_EMBALLAGE AND D.ID_SORTIESTOCK="'.$_GET['Id'].'"';
  $reponse3= $DataBase->query($sql3);
  while($rslt3= $reponse3->fetch())
 {
	 
  $MTD=$MTD+$rslt3['MONTANT'];
  $ttcolisD=$ttcolisD+$rslt3['QTE'];
  $pdf->SetFont('Arial','',8);
  $pdf->Cell(30,5,'DECONSIGNATION',1,0,'L');
  $pdf->Cell(55,5,$rslt3['LIBELLE'],1);
  $pdf->Cell(15,5,$rslt3['QTE'],1,0,'C');
  $pdf->Cell(30,5,$rslt3['MT_CONSIGNE'].' F',1,0,'C');
  $pdf->Cell(50,5,number_format($rslt3['MONTANT'], 0, ',', ' ').' F',1,1,'C');
  }
  $pdf->SetFont('Arial','B',8);
  $pdf->Cell(100,5,'TOTAL EMBALLAGES DECONSIGNES : '.number_format($ttcolisD, 0, ',', ' '),1,0,'C');
  $pdf->Cell(80,5,'MONTANT DECONSIGNATIONS (4) :  '.number_format($MTD, 0, ',', ' ').' F',1,1,'C');
  
  $pdf->Cell(100,5,'',0,0,'C');
  $pdf->Cell(80,5,'SOUS-TOTAL (1)+(2)+(3)-(4)  :  '.number_format($MT+$MTC+$ttfraisenlevement-$MTD, 0, ',', ' ').' F',1,1,'C');
  
  $pdf->Cell(100,5,'',0,0,'C');
  $pdf->Cell(80,5,'CREDIT RISTOURNE  :  '.number_format($creditristourne, 0, ',', ' ').' F',1,1,'C');
  
  
  $pdf->SetFont('arial','B',12);
  $pdf->Write(5,'                       Montant total : '.number_format($MT+$MTC+$ttfraisenlevement-$MTD-$creditristourne, 0, ',', ' ').' Franc CFA');
  $pdf->Ln(0);
  $pdf->SetFont('arial','',8);
  $pdf->Write(15,'  Signature du Client                                                                                                                                                             Signature du Vendeur ');
  $pdf->Ln(8);
  $pdf->SetFont('arial','BI',8);
  $pdf->Write(10,'                                                                                          Merci pour votre fidelite.');

$pdf->Write(10,'------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------');
 $pdf->Ln(7);
 $pdf->SetFont('arial','B',10);
 $pdf->Write(10, utf8_decode('                                                                               SODIBONO '));
 $pdf->SetFont('arial','B',9);
 $pdf->Ln(3);
 $pdf->Write(15, utf8_decode('CONCESSIONNAIRE SABC  NIU. M030100015674K  RCCM: RC/NGA/2010/B/165 /Tel: 699 49 95 91/677 68 58 84 /MAG : TOUBORO'));
 $pdf->Ln(1);
  $pdf->Write(15, utf8_decode('                                                                                     ________________ '));
 $pdf->Ln(5);
 $pdf->SetFont('arial','B',8);
 $pdf->Write(20, utf8_decode('     FACTURE : '));
  $pdf->SetFont('arial','',8);
 $pdf->Write(20,$_GET['Id'].'       Date :'.dateFormatFrancais($Date).' '.date('H:i').'                      Operateur :    '.$_SESSION['login']);
 $pdf->Ln(1);
 $pdf->SetFont('arial','B',8);
 $pdf->Write(25,'     DOIT : ');
  $pdf->SetFont('arial','',8);
 $pdf->Write(25,$nom.' ('.$Clt.')');
 $pdf->Ln(15);                                                  
 $pdf->SetFontSize(8);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->Cell(60,5,'LIBELLE',1,0,'L',1);
 $pdf->Cell(40,5,'CONDITIONNEMENT',1,0,'L',1);
 $pdf->Cell(10,5,'QTE',1,0,'L',1);
 $pdf->Cell(20,5,'P.U',1,0,'L',1);
 $pdf->Cell(20,5,'P.T',1,0,'C',1);
 $pdf->Cell(30,5,'OBSERVATION',1,1,'L',1);
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
  
    $MT=0;
    $ttcolis=0;
  $nbrecasier=0;
  $nbrepet=0;
  $PU=0;
  $sql2='SELECT A.ID_ARTICLE, A.LIBELLE, A.MARQUE, A.NBREBTE, AV.ID_ARTICLE, AV.QTESORTIE, AV.PRIXVENTE, AV.OBSERVATION, AV.ID_SORTIESTOCK FROM ARTICLE A, ARTICLEVENDU AV WHERE A.ID_ARTICLE=AV.ID_ARTICLE AND AV.ID_SORTIESTOCK="'.$_GET['Id'].'"';
  $reponse2= $DataBase->query($sql2);
  while($rslt2= $reponse2->fetch())
 {
	//on compte les colis
  $ttcolis=$ttcolis+$rslt2['QTESORTIE'];
  //on compte les casiers
  if(($rslt2['MARQUE']=="CASIER") || ($rslt2['MARQUE']=="casier")|| ($rslt2['MARQUE']=="CASIERS")|| ($rslt2['MARQUE']=="casiers"))
  {
	  $nbrecasier=$nbrecasier+$rslt2['QTESORTIE'];
  }
  $MT=$MT+$rslt2['PRIXVENTE'];
  $PU=$rslt2['PRIXVENTE']/$rslt2['QTESORTIE'];
  $pdf->Cell(60,5,$rslt2['LIBELLE'],1,0,'L');
  $pdf->Cell(40,5,$rslt2['MARQUE'].' '.$rslt2['NBREBTE'],1);
  $pdf->Cell(10,5,$rslt2['QTESORTIE'],1,0,'C');
  $pdf->Cell(20,5,$PU,1,0,'C');
  $pdf->Cell(20,5,number_format($rslt2['PRIXVENTE'], 0, ',', ' '),1,0,'C');
  $pdf->Cell(30,5,$rslt2['OBSERVATION'],1,1,'L');
  }
  $pdf->SetFont('Arial','B',8);
  $pdf->Cell(50,5,' TOTAL COLIS  : '.number_format($ttcolis, 0, ',', ' '),1,0,'C');
  $pdf->Cell(50,5,' TOTAL CASIERS  : '.number_format($nbrecasier, 0, ',', ' '),1,0,'C');
  $pdf->Cell(80,5,'MONTANT ARTICLES (1) :  '.number_format($MT, 0, ',', ' ').' F',1,1,'C');
		//ON CALCULE LES FRAIS D'ENLEVEMENT
  $pdf->SetFont('Times','B',8);
  	//cacul nbre PET
	$nbrepet=$ttcolis-$nbrecasier;
   		//ON CALCULE LES FRAIS D'ENLEVEMENT
  $ttfraisenlevement=0;
  $ttfraisenlevement=($nbrecasier*$fraisenlevement_cassier)+($nbrepet*$fraisenlevement_pet);
  $pdf->Cell(100,5,' PU ENLEVEMENT  : CASSIER= '.number_format($fraisenlevement_cassier, 0, ',', '').' F/PET='.number_format($fraisenlevement_pet, 0, ',', ' ').' F',1,0,'C');
  $pdf->Cell(80,5,'MONTANT FRAIS ENLEVEMENT (2): '.number_format($ttfraisenlevement, 0, ',', ' ').'F',1,1,'C');
  //affichage des consignes emballages
    $pdf->SetFont('Arial','',8);
	$MTC=0;
    $ttcolisC=0;
    $sql3='SELECT E.ID_EMBALLAGE, E.LIBELLE, E.MT_CONSIGNE, C.ID_EMBALLAGE, C.ID_SORTIESTOCK, C.QTE, C.MONTANT FROM EMBALLAGE E, CONSIGNE C WHERE E.ID_EMBALLAGE=C.ID_EMBALLAGE AND C.ID_SORTIESTOCK="'.$_GET['Id'].'"';
  $reponse3= $DataBase->query($sql3);
  while($rslt3= $reponse3->fetch())
 {
  $ttcolisC=$ttcolisC+$rslt3['QTE'];
  $MTC=$MTC+$rslt3['MONTANT'];
  $pdf->Cell(30,5,'CONSIGNE',1,0,'L');
  $pdf->Cell(55,5,$rslt3['LIBELLE'],1);
  $pdf->Cell(15,5,$rslt3['QTE'],1,0,'C');
  $pdf->Cell(30,5,$rslt3['MT_CONSIGNE'].' F',1,0,'C');
  $pdf->Cell(50,5,number_format($rslt3['MONTANT'], 0, ',', ' ').' F',1,1,'C');
  }
  $pdf->SetFont('Arial','B',8);
  $pdf->Cell(100,5,'TOTAL EMBALLAGES CONSINES : '.number_format($ttcolisC, 0, ',', ' '),1,0,'C');
  $pdf->Cell(80,5,'MONTANT CONSIGNES (3) :  '.number_format($MTC, 0, ',', ' ').' F',1,1,'C');
  
  //affichage des deconsignations emballages
  
    $MTD=0;
	$ttcolisD=0;
    $sql3='SELECT E.ID_EMBALLAGE, E.LIBELLE, E.MT_CONSIGNE, D.ID_EMBALLAGE, D.ID_SORTIESTOCK, D.QTE, D.MONTANT FROM EMBALLAGE E, RTREMBVTE D WHERE E.ID_EMBALLAGE=D.ID_EMBALLAGE AND D.ID_SORTIESTOCK="'.$_GET['Id'].'"';
  $reponse3= $DataBase->query($sql3);
  while($rslt3= $reponse3->fetch())
 {
	 
  $MTD=$MTD+$rslt3['MONTANT'];
  $ttcolisD=$ttcolisD+$rslt3['QTE'];
  $pdf->SetFont('Arial','',8);
  $pdf->Cell(30,5,'DECONSIGNATION',1,0,'L');
  $pdf->Cell(55,5,$rslt3['LIBELLE'],1);
  $pdf->Cell(15,5,$rslt3['QTE'],1,0,'C');
  $pdf->Cell(30,5,$rslt3['MT_CONSIGNE'].' F',1,0,'C');
  $pdf->Cell(50,5,number_format($rslt3['MONTANT'], 0, ',', ' ').' F',1,1,'C');
  }
  $pdf->SetFont('Arial','B',8);
  $pdf->Cell(100,5,'TOTAL EMBALLAGES DECONSIGNES : '.number_format($ttcolisD, 0, ',', ' '),1,0,'C');
  $pdf->Cell(80,5,'MONTANT DECONSIGNATIONS (4) :  '.number_format($MTD, 0, ',', ' ').' F',1,1,'C');
  
  $pdf->Cell(100,5,'',0,0,'C');
  $pdf->Cell(80,5,'SOUS-TOTAL (1)+(2)+(3)-(4)  :  '.number_format($MT+$MTC+$ttfraisenlevement-$MTD, 0, ',', ' ').' F',1,1,'C');
  
  $pdf->Cell(100,5,'',0,0,'C');
  $pdf->Cell(80,5,'CREDIT RISTOURNE  :  '.number_format($creditristourne, 0, ',', ' ').' F',1,1,'C');
  
  $pdf->SetFont('arial','B',12);
  $pdf->Write(10,'                       Montant total : '.number_format($MT+$MTC+$ttfraisenlevement-$MTD-$creditristourne, 0, ',', ' ').' Franc CFA');
  $pdf->Ln(5);
  $pdf->SetFont('arial','',8);
  $pdf->Write(10,'  Signature du Client                                                                                                                                                                   Signature du Vendeur ');

  $pdf->Ln(3);
  $pdf->SetFont('arial','BI',8);
  $pdf->Write(10,'                                                                                                   Merci pour votre fidelite.');

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
