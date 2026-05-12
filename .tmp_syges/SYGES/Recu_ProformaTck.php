<?php
session_start();
if (isset ($_SESSION['habilitation']))
{
 include('fpdf2.php');
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
 $pdf->SetAutoPageBreak(true);
 $pdf->AddPage();
 $pdf->SetTitle('Recu');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('Recu Vente');
 $pdf->SetFont('arial','B',32);
 $pdf->Write(1, utf8_decode('                 SODIBONO'));
 $pdf->Ln(15);
 $pdf->SetFont('arial','B',19);
 $pdf->Write(1, utf8_decode('    DISTRIBUTEUR DES BRASSERIES DU CAMEROUN'));
 $pdf->Ln(15);
 $pdf->SetFont('arial','B',20);
  $pdf->Write(1, utf8_decode('CONT. M030100015674K  RCCM: RC/NGA/2010/B/165'));
 $pdf->Ln(15);
 $pdf->Write(1, utf8_decode('           Tel : +237 699 49 95 91  /  677 68 58 84   '));
 $pdf->Ln(15);
 $pdf->Write(1, utf8_decode('                     MAGASIN : TOUBORO'));
 $pdf->Ln(13);
 $pdf->Write(1, utf8_decode('                                   *************'));
 $pdf->SetFont('arial','B',24);
 $pdf->Ln(10);
 $pdf->Write(25, utf8_decode('Proforma :'));
 $pdf->Write(25,strtoupper($_GET['Id']).'  '.dateFormatFrancais($Date).' '.$heure);
  $pdf->Ln(15); 
 $pdf->Write(25,'Operateur :  '.$_SESSION['login']);
 $pdf->Ln(13);
 $pdf->SetFont('arial','B',26);
 $pdf->Write(35,'Doit : ');
 $pdf->Write(35,$nom);
 $pdf->Ln(30);   
 $pdf->SetFont('Times','B',22);
 $pdf->Write(5,'N.I.U :');
 $pdf->SetFont('Times','B',22);
 $pdf->Write(5,$NIU);
 $pdf->SetFont('Times','B',22);
 $pdf->Write(5,' R.C: ');
 $pdf->SetFont('Times','B',22);
 $pdf->Write(5,$RC); 
 $pdf->Ln(15);        
 $pdf->SetFontSize(12);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFont('arial','B',24);
 $pdf->SetFillColor(255,255,255);
 $pdf->Cell(50,20,'Libelle',1,0,'L',1);
 $pdf->Cell(30,20,'QTE',1,0,'C',1);
 $pdf->Cell(25,20,'P.U',1,0,'C',1);
 $pdf->Cell(45,20,'P.T',1,0,'C',1);
 $pdf->Cell(30,20,'RIS.',1,0,'C',1);
 $pdf->Cell(40,20,'MT RIS.',1,1,'C',1);
 $pdf->SetFont('Arial','B',21);
 $pdf->SetTextColor(0,0,0);
  $MTR=0;
  $MT=0;
  $PU=0;
  $MTC=0;
  $ttcolis=0;
  $nbrecasier=0;
    $nbrepet=0;
  $ristournearticle=0;
  $tauxristournettc=0;
  //AFFichage des articles
  $sql2='SELECT A.ID_ARTICLE, A.LIBELLE, A.MARQUE, A.NBREBTE, A.TAUXRISTOURNE, AV.ID_ARTICLE, AV.QTESORTIE, AV.PRIXVENTE, AV.OBSERVATION, AV.ID_SORTIESTOCK FROM ARTICLE A, ARTICLEVENDU AV WHERE A.ID_ARTICLE=AV.ID_ARTICLE AND AV.ID_SORTIESTOCK="'.$_GET['Id'].'"';
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
  $pdf->Cell(50,20,$rslt2['LIBELLE'],1,0,'L',1);
  $pdf->Cell(30,20,$rslt2['QTESORTIE'],1,0,'C');
  $pdf->Cell(25,20,$PU,1,0,'C');
  $pdf->Cell(45,20,$rslt2['PRIXVENTE'],1,0,'C');
  $pdf->Cell(30,20,number_format($tauxristournettc, 0, ',', ' '),1,0,'C');
  $pdf->Cell(40,20,number_format(number_format($tauxristournettc, 0, ',', ' ')*$rslt2['QTESORTIE'], 0, ',', ' '),1,1,'C');
  }
  	//cacul nbre PET
	$nbrepet=$ttcolis-$nbrecasier;
   		//ON CALCULE LES FRAIS D'ENLEVEMENT
  $ttfraisenlevement=0;
  $ttfraisenlevement=($nbrecasier*$fraisenlevement_cassier)+($nbrepet*$fraisenlevement_pet); 
  
  $pdf->Cell(220,20,'MT ARTICLES(1) : '.number_format($MT, 0, ',', ' '),1,1,'C');
 $pdf->Cell(105,20,'TOTAL COLIS : '.number_format($ttcolis, 0, ',', ' '),1,0,'C');
 $pdf->Cell(115,20,'CASIER: '.$nbrecasier.'  /PET: '.$nbrepet,1,1,'C');
  $pdf->Cell(105,20,'TOTAL RIS. : '.number_format($MTR, 0, ',', ' '),1,0,'C');
  $pdf->Cell(115,20,'FRAIS ENLEV.(2) : '.number_format($ttfraisenlevement, 0, ',', ' '),1,1,'C');
 
 //affichage des consignes emballages
    $MTC=0;
	$ttcolisC=0;
	$pdf->Cell(220,20,'CONSIGNES',1,1,'C');
	
    $sql3='SELECT E.ID_EMBALLAGE, E.LIBELLE, E.MT_CONSIGNE, C.ID_EMBALLAGE, C.ID_SORTIESTOCK, C.QTE, C.MONTANT FROM EMBALLAGE E, CONSIGNE C WHERE E.ID_EMBALLAGE=C.ID_EMBALLAGE AND C.ID_SORTIESTOCK="'.$_GET['Id'].'"';
  $reponse3= $DataBase->query($sql3);
  while($rslt3= $reponse3->fetch())
 { 
  $ttcolisC=$ttcolisC+$rslt3['QTE'];
  $MTC=$MTC+$rslt3['MONTANT'];
  $pdf->Cell(105,20,$rslt3['LIBELLE'],1,0,'C');
  $pdf->Cell(34,20,$rslt3['QTE'],1,0,'C');
  $pdf->Cell(43,20,$rslt3['MT_CONSIGNE'],1,0,'C');
  $pdf->Cell(43,20,$rslt3['MONTANT'],1,1,'C');
  }
 $pdf->Cell(105,20,'TT EMB CONS: '.number_format($ttcolisC, 0, ',', ' '),1,0,'C');
 $pdf->Cell(115,20,'MT CONSIG.(3) : '.number_format($MTC, 0, ',', ' '),1,1,'C');
  
    //affichage des deconsignations emballages
    $MTD=0;
	$ttcolisD=0;
	$pdf->Cell(220,20,'DECONSIGNATION',1,1,'C');
	  
    $sql3='SELECT E.ID_EMBALLAGE, E.LIBELLE, E.MT_CONSIGNE, D.ID_EMBALLAGE, D.ID_SORTIESTOCK, D.QTE, D.MONTANT FROM EMBALLAGE E, RTREMBVTE D WHERE E.ID_EMBALLAGE=D.ID_EMBALLAGE AND D.ID_SORTIESTOCK="'.$_GET['Id'].'"';
  $reponse3= $DataBase->query($sql3);
  while($rslt3= $reponse3->fetch())
 {
  $MTD=$MTD+$rslt3['MONTANT'];
  $ttcolisD=$ttcolisD+$rslt3['QTE'];
  $pdf->Cell(105,20,$rslt3['LIBELLE'],1);
  $pdf->Cell(34,20,$rslt3['QTE'],1,0,'C');
  $pdf->Cell(43,20,$rslt3['MT_CONSIGNE'],1,0,'C');
  $pdf->Cell(43,20,$rslt3['MONTANT'],1,1,'C');
  }
 $pdf->Cell(105,20,'TT EMB DECONS: '.number_format($ttcolisD, 0, ',', ' '),1,0,'C');
 $pdf->Cell(115,20,'MT DECON.(4) : '.number_format($MTD, 0, ',', ' '),1,1,'C');

    //Ici on calcule le montant liquide nu la retenue fis pro et la tva
  $mtliquidenu=0;
  $mttva=0;
  $mtretfiscpro=0;

  $mtliquidenu=$MT*100/(100+$tva+$tauxretfiscpro);
  $mttva=$mtliquidenu*$tva/100;
  $mtretfiscpro=$mtliquidenu*$tauxretfiscpro/100;
  $MTTC=0;
  $MTTC=$MT+$MTC+$ttfraisenlevement-$MTD-$creditristourne;
  
  
  $pdf->Cell(220,20,'MONTANT ARTICLE HT : '.number_format($mtliquidenu, 0, ',', ' '),1,1,'C');
  $pdf->Cell(220,20,'TVA ('.$tva.'%) : '.number_format($mttva, 0, ',', ' '),1,1,'C');
  $pdf->Cell(220,20,'P.S.A ('.$tauxretfiscpro.'%) : '.number_format($mtretfiscpro, 0, ',', ' '),1,1,'C');
 $pdf->Cell(220,20,'SOUS-TOTAL (1)+(2)+(3)-(4) : '.number_format($MT+$MTC+$ttfraisenlevement-$MTD, 0, ',', ' '),1,1,'C');
 $pdf->Cell(220,20,'CREDIT RISTOURNE : '.number_format($creditristourne, 0, ',', ' '),1,1,'C'); 
  $pdf->SetFont('arial','B',32);
  $pdf->Cell(220,20,'NET A PAYER : '.number_format($MT+$MTC+$ttfraisenlevement-$MTD-$creditristourne, 0, ',', ' ').' FCFA',1,1,'C');
  $pdf->Ln(20);
  $pdf->SetFont('arial','I',30);
  $pdf->Write(10,' Vendeur                                 Client');
  $pdf->Ln(0);
  $pdf->Write(10,' _______                                 _____');
  $pdf->Ln(35);
  $pdf->SetFont('arial','I',20);
  $pdf->Write(20,'                          Merci pour votre fidelite.');

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
