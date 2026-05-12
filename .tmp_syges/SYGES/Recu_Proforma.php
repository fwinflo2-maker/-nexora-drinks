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
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(true,10);
 $pdf->AddPage();
 $pdf->SetTitle('Recu');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('Recu Vente');
 $pdf->Ln(25);
 $pdf->SetFont('Times','B',14);
  $pdf->SetFontSize(7);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(200,200,200);
 $pdf->Cell(195,5,'PROFORMA',1,1,'C',1);
 $pdf->Ln(0);
 $pdf->SetFont('Times','B',10);
 $pdf->Write(10, utf8_decode('     N°        :     '));
 $pdf->SetFont('Times','',10);
 $pdf->Write(10,strtoupper($_GET['Id']));
  $pdf->SetFont('Times','B',10);
 $pdf->Write(10,'       DATE :     ');
 $pdf->SetFont('Times','',10);
 $pdf->Write(10,dateFormatFrancais($Date).' '.$heure);
 $pdf->SetFont('Times','B',10);
  $pdf->Write(10,'        Operateur : ');
  $pdf->SetFont('Times','',10);
  $pdf->Write(10,$USER);
 $pdf->Ln(1);
 $pdf->SetFont('Times','B',10);
 $pdf->Write(20,'     DOIT  :     ');
 $pdf->SetFont('Times','',10);
 $pdf->Write(20,$nom);
 $pdf->SetFont('Times','B',10);
  $pdf->Write(20,'                    TEL    :     ');
 $pdf->SetFont('Times','',10);
 $pdf->Write(20,$NUMTEL);
 $pdf->Ln(3);  
 $pdf->SetFont('Times','B',10);
 $pdf->Write(25,'     N.I.U   :     ');
 $pdf->SetFont('Times','',10);
 $pdf->Write(25,$NIU);
 $pdf->SetFont('Times','B',10);
 $pdf->Write(25,'                                                             R.C     :     ');
 $pdf->SetFont('Times','',10);
 $pdf->Write(25,$RC);
 $pdf->SetFont('Times','B',10);
 $pdf->Ln(16);                                                
 $pdf->SetFontSize(10);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(200,200,200);
 $pdf->SetFont('Times','B',8);
 $pdf->Image('IMG\logo.jpg',10,5,180,25);
 $pdf->Cell(10,5, utf8_decode('N°'),1,0,'L',1);
 $pdf->Cell(80,5,'DESIGNATION',1,0,'L',1);
 $pdf->Cell(15,5,'QTE',1,0,'C',1);
 $pdf->Cell(20,5,'P.U',1,0,'C',1);
 $pdf->Cell(20,5,'P.T (FCFA)',1,0,'C',1);
 $pdf->Cell(25,5,'TAUX RISTOU.',1,0,'C',1);
 $pdf->Cell(25,5,'MT RISTOU.',1,1,'C',1);
 $pdf->SetFont('Times','',8);
 $pdf->SetTextColor(0,0,0);
  
  $MTR=0;
  $MT=0;
  $PU=0;
  $ttcolis=0;
  $nbrecasier=0;
  $nbrepet=0;
  $ristournearticle=0;
  $tauxristournettc=0;
  $i=1;
  $sql2='SELECT A.ID_ARTICLE, A.LIBELLE, A.MARQUE,A.NBREBTE, A.TAUXRISTOURNE, AV.ID_ARTICLE, AV.QTESORTIE, AV.PRIXVENTE, AV.OBSERVATION, AV.ID_SORTIESTOCK FROM ARTICLE A, ARTICLEVENDU AV WHERE A.ID_ARTICLE=AV.ID_ARTICLE AND AV.ID_SORTIESTOCK="'.$_GET['Id'].'"';
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
	  //calcul ristourne
	  $tauxristournettc=((100+$tva+$tauxretfiscpro)/100)*$rslt2['TAUXRISTOURNE'];
	  $MTR=$MTR+ number_format($tauxristournettc, 0, ',', ' ')*$rslt2['QTESORTIE'];
	  
	  $MT=$MT+$rslt2['PRIXVENTE'];
	  $PU=$rslt2['PRIXVENTE']/$rslt2['QTESORTIE'];
	  $pdf->Cell(10,5,$i,1,0,'L',1); 
	  $pdf->Cell(80,5,$rslt2['LIBELLE'],1,0,'L');
	  $pdf->Cell(15,5,$rslt2['QTESORTIE'],1,0,'C');
	  $pdf->Cell(20,5,number_format($PU, 0, ',', ' '),1,0,'C');
	  $pdf->Cell(20,5,number_format($rslt2['PRIXVENTE'], 0, ',', ' '),1,0,'C');
	  $pdf->Cell(25,5,number_format($tauxristournettc, 0, ',', ' '),1,0,'C');
	  $pdf->Cell(25,5,number_format(number_format($tauxristournettc, 0, ',', ' ')*$rslt2['QTESORTIE'], 0, ',', ' '),1,1,'C');
	  $i++;
  }
	//cacul nbre PET
	$nbrepet=$ttcolis-$nbrecasier;
    $pdf->SetFont('Times','B',8);
  	$pdf->Cell(40,5,' TOTAL COLIS  : '.number_format($ttcolis, 0, ',', ' '),1,0,'C');
	$pdf->Cell(30,5,' CASIERS  : '.number_format($nbrecasier, 0, ',', ' '),1,0,'C');
	$pdf->Cell(20,5,' PET  : '.number_format($nbrepet, 0, ',', ' '),1,0,'C');
	$pdf->Cell(55,5,'MONTANT ARTICLES (1) : '.number_format($MT, 0, ',', ' ').' F',1,0,'C');
	$pdf->Cell(50,5,'MONTANT RISTOURNES : '.number_format($MTR, 0, ',', ' ').'F',1,1,'C');
		//ON CALCULE LES FRAIS D'ENLEVEMENT
  $pdf->SetFont('Times','B',8);
  $ttfraisenlevement=0;
  $ttfraisenlevement=($nbrecasier*$fraisenlevement_cassier)+($nbrepet*$fraisenlevement_pet); 
  $pdf->Cell(105,5,' PU ENLEVEMENT  : CASSIER= '.number_format($fraisenlevement_cassier, 0, ',', ' ').' F/PET='.number_format($fraisenlevement_pet.' F', 0, ',', ' '),1,0,'C');
  $pdf->Cell(90,5,'MONTANT FRAIS ENLEVEMENT (2): '.number_format($ttfraisenlevement, 0, ',', ' ').'F',1,1,'C');
	  //affichage des consignes emballages
    $MTC=0;
	$ttcolisC=0;
    $sql3='SELECT E.ID_EMBALLAGE, E.LIBELLE, E.MT_CONSIGNE, C.ID_EMBALLAGE, C.ID_SORTIESTOCK, C.QTE, C.MONTANT FROM EMBALLAGE E, CONSIGNE C WHERE E.ID_EMBALLAGE=C.ID_EMBALLAGE AND C.ID_SORTIESTOCK="'.$_GET['Id'].'"';
  $reponse3= $DataBase->query($sql3);
  while($rslt3= $reponse3->fetch())
 {
  $ttcolisC=$ttcolisC+$rslt3['QTE'];
  $MTC=$MTC+$rslt3['MONTANT'];
  $pdf->SetFont('Times','B',8);
  $pdf->Cell(25,5,'CONSIGNE',1,0,'L');
  $pdf->SetFont('Times','',9);
  $pdf->Cell(50,5,$rslt3['LIBELLE'],1);
  $pdf->Cell(15,5,$rslt3['QTE'],1,0,'C');
  $pdf->Cell(20,5,$rslt3['MT_CONSIGNE'],1,0,'C');
  $pdf->Cell(20,5,number_format($rslt3['MONTANT'], 0, ',', ' '),1,0,'C');
  $pdf->Cell(50,5,'//',1,1,'C');
  }
  $pdf->SetFont('Times','B',8);
  $pdf->Cell(105,5,' TOTAL EMBALLAGES CONSIGNES  : '.number_format($ttcolisC, 0, ',', ' '),1,0,'C');
  $pdf->Cell(90,5,'MONTANT CONSIGNES (3): '.number_format($MTC, 0, ',', ' ').'F',1,1,'C');
  	  //affichage des deconsignations emballages
    $MTD=0;
	$ttcolisD=0;
    $sql3='SELECT E.ID_EMBALLAGE, E.LIBELLE, E.MT_CONSIGNE, D.ID_EMBALLAGE, D.ID_SORTIESTOCK, D.QTE, D.MONTANT FROM EMBALLAGE E, RTREMBVTE D WHERE E.ID_EMBALLAGE=D.ID_EMBALLAGE AND D.ID_SORTIESTOCK="'.$_GET['Id'].'"';
  $reponse3= $DataBase->query($sql3);
  while($rslt3= $reponse3->fetch())
 {
  $ttcolisD=$ttcolisD+$rslt3['QTE'];
  $MTD=$MTD+$rslt3['MONTANT'];
  $pdf->SetFont('Times','B',8);
  $pdf->Cell(30,5,'DECONSIGNATION',1,0,'L');
  $pdf->SetFont('Times','',9);
  $pdf->Cell(45,5,$rslt3['LIBELLE'],1);
  $pdf->Cell(15,5,$rslt3['QTE'],1,0,'C');
  $pdf->Cell(20,5,$rslt3['MT_CONSIGNE'],1,0,'C');
  $pdf->Cell(20,5,number_format($rslt3['MONTANT'], 0, ',', ' '),1,0,'C');
  $pdf->Cell(50,5,'//',1,1,'C');
  }
  $pdf->SetFont('Times','B',8);
  $pdf->Cell(105,5,' TOTAL EMBALLAGES DECONSIGNATIONS  : '.number_format($ttcolisD, 0, ',', ' '),1,0,'C');
  $pdf->Cell(90,5,'MONTANT DECONSIGNATIONS (4): '.number_format($MTD, 0, ',', ' ').'F',1,1,'C');
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
    $pdf->SetFont('Times','B',10);
  $pdf->Cell(60,5,'MONTANT ARTICLES HT  ',1,0,'C');
  $pdf->Cell(35,5,number_format($mtliquidenu, 0, ',', ' ').' F',1,0,'C');
  $pdf->Cell(60,5,'MONTANT ARTICLES TTC',1,0,'C');
  $pdf->Cell(40,5,number_format($MT, 0, ',', ' ').' F',1,1,'C');
  $pdf->Cell(60,5,'TVA ('.$tva.'%)',1,0,'C');
  $pdf->Cell(35,5,number_format($mttva, 0, ',', ' ').' F',1,0,'C');
  $pdf->Cell(60,5,'RET. FISC. PRO. ('.$tauxretfiscpro.'%)',1,0,'C');
  $pdf->Cell(40,5,number_format($mtretfiscpro, 0, ',', ' ').' F',1,1,'C');

  $pdf->Cell(60,5,'SOUS TOTAL (1)+(2)+(3)-(4)',1,0,'C');
  $pdf->Cell(35,5,number_format($MT+$MTC+$ttfraisenlevement-$MTD, 0, ',', ' ').' F',1,0,'C');
  $pdf->Cell(60,5,'CREDIT RISTOURNE',1,0,'C');
  $pdf->Cell(40,5,number_format($creditristourne, 0, ',', ' ').' F',1,1,'C');
//  $pdf->Cell(60,5,'',0,0,'C');
//  $pdf->Cell(65,5,'TOTAL EMBALLAGES  : ',1,0,'C');
//  $pdf->Cell(55,5,number_format($MTC, 0, ',', ' ').' F',1,1,'C');
  $pdf->Ln(2);
  $pdf->Cell(195,5,'MONTANT TTC : '.number_format($MTTC, 0, ',', ' ').' FCFA',1,1,'C');
  $pdf->Cell(195,5,utf8_decode('Arrête la présente facture à la somme de : ').asLetters($MTTC).' Francs.',1,1,'L');
  $pdf->SetFont('Times','B',10);
  $pdf->Write(10,'  LE CLIENT                                                                                                                                                      LE VENDEUR ');
        
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
