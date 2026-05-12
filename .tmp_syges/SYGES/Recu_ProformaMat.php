<?php
session_start();
if (isset ($_SESSION['habilitation']))
{
 include('fpdf.php');
 include('fonctions.php');
 include('Connexion.php');
 //ici on recupere les infos sur le client de la vente 
   $fraisenlevement=0;
 $sql='SELECT C.ID_CLIENT,C.NIU,C.RC, C.NOM, C.FRAISENLEVEMENT,C.FRAISENLEVEMENT_PET, C.NUMTEL, C.ID_CATEGORIE, ST.ID_CLIENT, ST.ID_SORTIESTOCK, ST.CREDITRISTOURNE, ST.DATESORTIESTOCK, ST.HEURESORTIESTOCK, ST.LOGIN, ST.TAUXTVA, ST.TAUXRETFISCPRO FROM CLIENT C, SORTIE_STOCK ST WHERE C.ID_CLIENT=ST.ID_CLIENT AND ST.ID_SORTIESTOCK="'.$_GET['Id'].'" ';
 $reponse= $DataBase->query($sql);
 while($rslt= $reponse->fetch())
 {
  $nom=$rslt['NOM'];
  $Clt=$rslt['ID_CLIENT'];
  $Date=$rslt['DATESORTIESTOCK'];
  $Heure=$rslt['HEURESORTIESTOCK'];
  $NIU=$rslt['NIU'];
  $RC=$rslt['RC'];
  $USER=$rslt['LOGIN'];
  $NUMTEL=$rslt['NUMTEL'];
  $heure=$rslt['HEURESORTIESTOCK'];
  $fraisenlevement_cassier=$rslt['FRAISENLEVEMENT'];
  $fraisenlevement_pet=$rslt['FRAISENLEVEMENT_PET'];
  $creditristourne=$rslt['CREDITRISTOURNE'];
  $categorie=$rslt['ID_CATEGORIE'];
  $tauxretfiscpro=$rslt['TAUXRETFISCPRO'];
  $tva=$rslt['TAUXTVA'];
 }
 //Select ds bd
 $pdf=new FPDF('L','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('Recu');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('Recu Vente');
  $pdf->Ln(0);
 $pdf->SetFont('arial','B',16);
 $pdf->Write(10, utf8_decode('                                                                         SODIBONO '));
 $pdf->SetFont('arial','B',12);
 $pdf->Ln(5);
 $pdf->Write(15, utf8_decode('  DISTRIBUTEUR SABC  NIU. M030100015674K  RCCM: RC/NGA/2010/B/165 / Tel : 699 49 95 91  /  677 68 58 84    /MAG : TOUBORO'));
 $pdf->Ln(2);
  $pdf->Write(15, utf8_decode('                                                                                            ____________________ '));
 $pdf->Ln(5);
 $pdf->SetFont('arial','B',14);
 $pdf->Write(30, utf8_decode('     PROFORMA : '));
 $pdf->SetFont('arial','B',14);
 $pdf->Write(30,strtoupper($_GET['Id']).'       Date : '.dateFormatFrancais($Date).' '.$Heure.'                      Operateur :    '.$_SESSION['login']);
 $pdf->Ln(1);
 $pdf->SetFont('arial','B',14);
 $pdf->Write(45,'     DOIT : ');
 $pdf->SetFont('arial','B',14);
 $pdf->Write(45,$nom.' ('.$Clt.')');
 $pdf->Ln(27);        
 $pdf->SetFontSize(12);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFont('arial','B',12);
 $pdf->SetFillColor(255,255,255);
 $pdf->Cell(50,7,'CONDITIONNEMENT',1,0,'L',1);
 $pdf->Cell(95,7,'LIBELLE',1,0,'L',1);
 $pdf->Cell(25,7,'QTE',1,0,'L',1);
 $pdf->Cell(35,7,'P.U',1,0,'C',1);
 $pdf->Cell(40,7,'P.T',1,1,'C',1);
 $pdf->SetFont('Arial','B',12);
 $pdf->SetTextColor(0,0,0);
  
  $MT=0;
  $PU=0;
  $MTC=0;
  $ttcolis=0;
  $nbrecasier=0;
  $nbrepet=0;
  //AFFichage des articles
  $sql2='SELECT A.ID_ARTICLE, A.LIBELLE, A.MARQUE, A.NBREBTE, AV.ID_ARTICLE, AV.QTESORTIE, AV.PRIXVENTE, AV.OBSERVATION, AV.ID_SORTIESTOCK FROM ARTICLE A, ARTICLEVENDU AV WHERE A.ID_ARTICLE=AV.ID_ARTICLE AND AV.ID_SORTIESTOCK="'.$_GET['Id'].'"';
  $reponse2= $DataBase->query($sql2);
  while($rslt2= $reponse2->fetch())
 {
	//On compte les casiers 
    $ttcolis=$ttcolis+$rslt2['QTESORTIE'];
	//on compte les casiers
	if(($rslt2['MARQUE']=="CASIER") || ($rslt2['MARQUE']=="casier")|| ($rslt2['MARQUE']=="CASIERS")|| ($rslt2['MARQUE']=="casiers"))
	{
		$nbrecasier=$nbrecasier+$rslt2['QTESORTIE'];
	}
  $MT=$MT+$rslt2['PRIXVENTE'];
  $PU=$rslt2['PRIXVENTE']/$rslt2['QTESORTIE'];
  
  $pdf->Cell(50,7,$rslt2['MARQUE'].' '.$rslt2['NBREBTE'],1,0,'L');
  $pdf->Cell(95,7,$rslt2['LIBELLE'],1);
  $pdf->Cell(25,7,$rslt2['QTESORTIE'],1,0,'C');
  $pdf->Cell(35,7,$PU,1,0,'C');
  $pdf->Cell(40,7,$rslt2['PRIXVENTE'],1,1,'C');
  }
  $pdf->Cell(50,7,'TOTAL COLIS : '.number_format($ttcolis, 0, ',', ' '),1,0,'L');
  $pdf->Cell(70,7,'TOTAL CASIERS : '.number_format($nbrecasier, 0, ',', ' '),1,0,'L');
  $pdf->Cell(125,7,'MONTANT ARTICLES (1): '.number_format($MT, 0, ',', ' ').' F',1,1,'C');
  	//cacul nbre PET
	$nbrepet=$ttcolis-$nbrecasier;
   		//ON CALCULE LES FRAIS D'ENLEVEMENT
  $ttfraisenlevement=0;
  $ttfraisenlevement=($nbrecasier*$fraisenlevement_cassier)+($nbrepet*$fraisenlevement_pet);
  $pdf->Cell(120,7,' PU ENLEVEMENT  : CASIER= '.number_format($fraisenlevement_cassier, 0, ',', '').' F/PET='.number_format($fraisenlevement_pet.' F', 0, ',', ' '),1,0,'C');
  $pdf->Cell(125,7,'MONTANT FRAIS ENLEVEMENT (2): '.number_format($ttfraisenlevement, 0, ',', ' ').'F',1,1,'C');
  //affichage des consignes emballages
    $MTC=0;
	$ttcolisC=0;
    $sql3='SELECT E.ID_EMBALLAGE, E.LIBELLE, E.MT_CONSIGNE, C.ID_EMBALLAGE, C.ID_SORTIESTOCK, C.QTE, C.MONTANT FROM EMBALLAGE E, CONSIGNE C WHERE E.ID_EMBALLAGE=C.ID_EMBALLAGE AND C.ID_SORTIESTOCK="'.$_GET['Id'].'"';
  $reponse3= $DataBase->query($sql3);
  while($rslt3= $reponse3->fetch())
 {
  $ttcolisC=$ttcolisC+$rslt3['QTE'];
  $MTC=$MTC+$rslt3['MONTANT'];
  $pdf->Cell(50,7,'CONSIGNE',1,0,'L');
  $pdf->Cell(95,7,$rslt3['LIBELLE'],1);
  $pdf->Cell(25,7,$rslt3['QTE'],1,0,'C');
  $pdf->Cell(35,7,$rslt3['MT_CONSIGNE'],1,0,'C');
  $pdf->Cell(40,7,$rslt3['MONTANT'],1,1,'C');
  }
  $pdf->Cell(120,7,'TOTAL EMBALLAGES CONSIGNES : '.number_format($ttcolisC, 0, ',', ' '),1,0,'C');
  $pdf->Cell(125,7,'MONTANT CONSIGNES (3) : '.number_format($MTC, 0, ',', ' ').' FCFA',1,1,'C');
  
    //affichage des deconsignations emballages
    $MTD=0;
	$ttcolisD=0;
    $sql3='SELECT E.ID_EMBALLAGE, E.LIBELLE, E.MT_CONSIGNE, D.ID_EMBALLAGE, D.ID_SORTIESTOCK, D.QTE, D.MONTANT FROM EMBALLAGE E, RTREMBVTE D WHERE E.ID_EMBALLAGE=D.ID_EMBALLAGE AND D.ID_SORTIESTOCK="'.$_GET['Id'].'"';
  $reponse3= $DataBase->query($sql3);
  while($rslt3= $reponse3->fetch())
 {
  $ttcolisD=$ttcolisD+$rslt3['QTE'];
  $MTD=$MTD+$rslt3['MONTANT'];
  $pdf->Cell(50,7,'DECONSIGNATION',1,0,'L');
  $pdf->Cell(95,7,$rslt3['LIBELLE'],1);
  $pdf->Cell(25,7,$rslt3['QTE'],1,0,'C');
  $pdf->Cell(35,7,$rslt3['MT_CONSIGNE'],1,0,'C');
  $pdf->Cell(40,7,$rslt3['MONTANT'],1,1,'C');
  }
  $pdf->Cell(120,7,'TOTAL EMBALLAGES DECONSIGNATIONS : '.number_format($ttcolisD, 0, ',', ' '),1,0,'C');
  $pdf->Cell(125,7,'MONTANT DECONSIGNATIONS (4) : '.number_format($MTD, 0, ',', ' ').' FCFA',1,1,'C');
  
      //Ici on calcule le montant liquide nu la retenue fis pro et la tva
  $mtliquidenu=0;
  $mttva=0;
  $mtretfiscpro=0;

  $mtliquidenu=$MT*100/(100+$tva+$tauxretfiscpro);
  $mttva=$mtliquidenu*$tva/100;
  $mtretfiscpro=$mtliquidenu*$tauxretfiscpro/100;
  $MTTC=0;
  $MTTC=$MT+$MTC+$ttfraisenlevement-$MTD-$creditristourne;
  
  $pdf->Ln(2);
   // $pdf->SetFont('Times','B',10);
  $pdf->Cell(120,7,'MONTANT ARTICLES HT : '.number_format($mtliquidenu, 0, ',', ' ').' F',1,0,'C');
  $pdf->Cell(125,7,'MONTANT ARTICLES TTC : '.number_format($mtliquidenu, 0, ',', ' ').' F',1,1,'C');
  $pdf->Cell(120,7,'TVA ('.$tva.'%) : '.number_format($mttva, 0, ',', ' ').' F',1,0,'C');
  $pdf->Cell(125,7,'RET. FISC. PRO. ('.$tauxretfiscpro.'%) : '.number_format($mtretfiscpro, 0, ',', ' ').' F',1,1,'C');

  $pdf->Cell(120,7,'SOUS TOTAL (1)+(2)+(3)-(4) : '.number_format($MT+$MTC+$ttfraisenlevement-$MTD, 0, ',', ' ').' F',1,0,'C');
  $pdf->Cell(125,7,'CREDIT RISTOURNE : '.number_format($creditristourne, 0, ',', ' ').' F',1,1,'C');
  
  $pdf->Ln(2);
  $pdf->SetFont('Times','B',14);
  $pdf->Cell(245,7,'MONTANT TTC : '.number_format($MTTC, 0, ',', ' ').' FCFA',1,1,'C');
  $pdf->SetFont('Times','B',12);
  $pdf->Cell(245,7,utf8_decode('Arrête la présente facture à la somme de : ').asLetters($MTTC).' Francs.',1,1,'L');

  $pdf->Ln(2);
  $pdf->SetFont('arial','B',12);
  $pdf->Write(20,'             Signature du Client                                                                                                               Signature du Vendeur ');
    $pdf->Ln(20);
  $pdf->SetFont('arial','BI',12);
  $pdf->Write(20,'                                                                                                   Merci pour votre fidelite.');

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
