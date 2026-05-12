<?php
session_start();
if (isset ($_SESSION['habilitation']))
{
 include('fpdf.php');
 include('fonctions.php');
 include('Connexion.php');
 //ici on recupere les infos sur le client de la vente 
 $fraisenlevement=0;
 $sql='SELECT C.ID_CLIENT,C.NIU,C.RC, C.NOM, C.FRAISENLEVEMENT, C.NUMTEL, C.ID_CATEGORIE, ST.ID_CLIENT, ST.ID_SORTIESTOCK, ST.CREDITRISTOURNE, ST.DATESORTIESTOCK, ST.HEURESORTIESTOCK, ST.LOGIN, ST.TAUXTVA, ST.TAUXRETFISCPRO FROM CLIENT C, SORTIE_STOCK ST WHERE C.ID_CLIENT=ST.ID_CLIENT AND ST.ID_SORTIESTOCK="'.$_GET['Id'].'" ';
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
  $fraisenlevement=$rslt['FRAISENLEVEMENT'];
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
 $pdf->SetFont('arial','B',10);
 $pdf->Write(1, utf8_decode('                 SODIBONO'));
 $pdf->Ln(15);
 $pdf->SetFont('arial','B',19);
 $pdf->Write(1, utf8_decode('CONCESSIONNAIRE DES BRASSERIES DU CAMEROUN'));
 $pdf->Ln(15);
 $pdf->SetFont('arial','B',20);
  $pdf->Write(1, utf8_decode('CONT. M030100015674K  RCCM: RC/NGA/2010/B/165'));
 $pdf->Ln(15);
 $pdf->Write(1, utf8_decode('           Tel : +237 699 49 95 91  /  677 68 58 84   '));
 $pdf->Ln(15);
 $pdf->Write(1, utf8_decode('                     MAGASIN : TOUBORO'));
 $pdf->Ln(13);
 $pdf->Write(1, utf8_decode('                                   *************'));
 $pdf->SetFont('arial','B',8);
 $pdf->Ln(2);
 $pdf->Write(3, utf8_decode('Proforma : '));
 $pdf->Write(3,strtoupper($_GET['Id']).'  '.dateFormatFrancais($Date).' '.$heure);
  $pdf->Ln(3); 
 $pdf->Write(5,'Operateur :  '.$_SESSION['login']);
 $pdf->Ln(5);
 $pdf->SetFont('arial','B',8);
 $pdf->Write(5,'Doit : ');
 $pdf->Write(5,$nom);
 $pdf->Ln(5);        
 $pdf->SetFontSize(12);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFont('arial','B',8);
 $pdf->SetFillColor(255,255,255);
 $pdf->Cell(14,7,'LIBELLE',1,0,'L',1);
 $pdf->Cell(8,7,'QTE',1,0,'C',1);
 $pdf->Cell(8,7,'P.U',1,0,'C',1);
 $pdf->Cell(12,7,'P.T',1,0,'C',1);
 $pdf->Cell(10,7,'RIS.',1,0,'C',1);
 $pdf->Cell(13,7,'MT RIS.',1,1,'C',1);
 $pdf->SetFont('Arial','B',7);
 $pdf->SetTextColor(0,0,0);
  $MTR=0;
  $MT=0;
  $PU=0;
  $MTC=0;
  $ttcolis=0;
  $nbrecasier=0;
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
  $pdf->Cell(14,7,$rslt2['LIBELLE'],1,0,'L',1);
  $pdf->Cell(8,7,$rslt2['QTESORTIE'],1,0,'C');
  $pdf->Cell(8,7,$PU,1,0,'C');
  $pdf->Cell(12,7,$rslt2['PRIXVENTE'],1,0,'C');
  $pdf->Cell(10,7,number_format($tauxristournettc, 0, ',', ' '),1,0,'C');
  $pdf->Cell(13,7,number_format(number_format($tauxristournettc, 0, ',', ' ')*$rslt2['QTESORTIE'], 0, ',', ' '),1,1,'C');
  }
   		//ON CALCULE LES FRAIS D'ENLEVEMENT
  $ttfraisenlevement=0;
  $ttfraisenlevement=$fraisenlevement*$ttcolis;
  
  $pdf->Cell(65,7,'MT ARTICLES(1) : '.number_format($MT, 0, ',', ' '),1,1,'C');
 $pdf->Cell(30,7,'TOTAL COLIS : '.number_format($ttcolis, 0, ',', ' '),1,0,'C');
 $pdf->Cell(35,7,' TOTAL CASIERS  : '.number_format($nbrecasier, 0, ',', ' '),1,1,'C');
  $pdf->Cell(30,7,'TOTAL RIS. : '.number_format($MTR, 0, ',', ' '),1,0,'C');
  $pdf->Cell(35,7,'FRAIS ENLEV.(2) : '.number_format($ttfraisenlevement, 0, ',', ' '),1,1,'C');
 
 //affichage des consignes emballages
    $MTC=0;
	$ttcolisC=0;
	$pdf->Cell(65,7,'CONSIGNES',1,1,'C');
	
    $sql3='SELECT E.ID_EMBALLAGE, E.LIBELLE, E.MT_CONSIGNE, C.ID_EMBALLAGE, C.ID_SORTIESTOCK, C.QTE, C.MONTANT FROM EMBALLAGE E, CONSIGNE C WHERE E.ID_EMBALLAGE=C.ID_EMBALLAGE AND C.ID_SORTIESTOCK="'.$_GET['Id'].'"';
  $reponse3= $DataBase->query($sql3);
  while($rslt3= $reponse3->fetch())
 { 
  $ttcolisC=$ttcolisC+$rslt3['QTE'];
  $MTC=$MTC+$rslt3['MONTANT'];
  $pdf->Cell(20,7,$rslt3['LIBELLE'],1,0,'C');
  $pdf->Cell(15,7,$rslt3['QTE'],1,0,'C');
  $pdf->Cell(15,7,$rslt3['MT_CONSIGNE'],1,0,'C');
  $pdf->Cell(15,7,$rslt3['MONTANT'],1,1,'C');
  }
 $pdf->Cell(30,7,'TT EMB CONS: '.number_format($ttcolisC, 0, ',', ' '),1,0,'C');
 $pdf->Cell(35,7,'MT CONSIG.(3) : '.number_format($MTC, 0, ',', ' '),1,1,'C');
  
    //affichage des deconsignations emballages
    $MTD=0;
	$ttcolisD=0;
	$pdf->Cell(65,7,'DECONSIGNATION',1,1,'C');
	  
    $sql3='SELECT E.ID_EMBALLAGE, E.LIBELLE, E.MT_CONSIGNE, D.ID_EMBALLAGE, D.ID_SORTIESTOCK, D.QTE, D.MONTANT FROM EMBALLAGE E, RTREMBVTE D WHERE E.ID_EMBALLAGE=D.ID_EMBALLAGE AND D.ID_SORTIESTOCK="'.$_GET['Id'].'"';
  $reponse3= $DataBase->query($sql3);
  while($rslt3= $reponse3->fetch())
 {
  $MTD=$MTD+$rslt3['MONTANT'];
  $ttcolisD=$ttcolisD+$rslt3['QTE'];
  $pdf->Cell(20,7,$rslt3['LIBELLE'],1);
  $pdf->Cell(15,7,$rslt3['QTE'],1,0,'C');
  $pdf->Cell(15,7,$rslt3['MT_CONSIGNE'],1,0,'C');
  $pdf->Cell(15,7,$rslt3['MONTANT'],1,1,'C');
  }
 $pdf->Cell(30,7,'TT EMB DECONS: '.number_format($ttcolisD, 0, ',', ' '),1,0,'C');
 $pdf->Cell(35,7,'MT DECON.(4) : '.number_format($MTD, 0, ',', ' '),1,1,'C');

    //Ici on calcule le montant liquide nu la retenue fis pro et la tva
  $mtliquidenu=0;
  $mttva=0;
  $mtretfiscpro=0;

  $mtliquidenu=$MT*100/(100+$tva+$tauxretfiscpro);
  $mttva=$mtliquidenu*$tva/100;
  $mtretfiscpro=$mtliquidenu*$tauxretfiscpro/100;
  $MTTC=0;
  $MTTC=$MT+$MTC+$ttfraisenlevement-$MTD-$creditristourne;
  
  
  $pdf->Cell(65,7,'MONTANT ARTICLE HT : '.number_format($mtliquidenu, 0, ',', ' '),1,1,'C');
  $pdf->Cell(65,7,'TVA ('.$tva.'%) : '.number_format($mttva, 0, ',', ' '),1,1,'C');
  $pdf->Cell(65,7,'P.S.A ('.$tauxretfiscpro.'%) : '.number_format($mtretfiscpro, 0, ',', ' '),1,1,'C');
 $pdf->Cell(65,7,'SOUS-TOTAL (1)+(2)+(3)-(4) : '.number_format($MT+$MTC+$ttfraisenlevement-$MTD, 0, ',', ' '),1,1,'C');
 $pdf->Cell(65,7,'CREDIT RISTOURNE : '.number_format($creditristourne, 0, ',', ' '),1,1,'C'); 
  $pdf->SetFont('arial','B',10);
  $pdf->Cell(65,7,'NET A PAYER : '.number_format($MT+$MTC+$ttfraisenlevement-$MTD-$creditristourne, 0, ',', ' ').' FCFA',1,1,'C');
  $pdf->SetFont('arial','',10);
  $pdf->Write(10,' Vendeur                                       Client');
  $pdf->Ln(1);
  $pdf->Write(10,' _______                                       _____');
  $pdf->Ln(15);
  $pdf->SetFont('arial','I',10);
  $pdf->Write(20,'             Merci pour votre fidelite.');

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
