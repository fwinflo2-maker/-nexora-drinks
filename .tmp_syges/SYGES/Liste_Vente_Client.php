<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" || $_SESSION['habilitation']=="Comptable"  || $_SESSION['habilitation']=="CC"))
{
 include('fpdf.php');
 include('Connexion.php');
 include('fonctions.php');
 $Debut=dateFormatAnglais($_GET["debut"]);
 $Fin=dateFormatAnglais($_GET["fin"]);
 $mtfacture=0;

//ici on recupere le nom du client
$sql2='SELECT  ID_CLIENT, NOM, FRAISENLEVEMENT FROM CLIENT  WHERE ID_CLIENT="'.$_GET['Clt'].'" ' ;
$reponse2= $DataBase->query($sql2);
	if ($_GET['Clt'] != 'TOUS')
	{
		$sql2='SELECT  ID_CLIENT, NOM, FRAISENLEVEMENT FROM CLIENT  WHERE ID_CLIENT="'.$_GET['Clt'].'" ' ;
		$reponse2= $DataBase->query($sql2);
			while($rslt2= $reponse2->fetch())
			{
				$codeC=$rslt2['ID_CLIENT'];
				$nomC=$rslt2['NOM'];
				//$fraisenlevement=$rslt2['FRAISENLEVEMENT'];
			}
	}
	else
	{
			$codeC='TOUS';
			$nomC='Tous les Clients';
	}
 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('LISTE DES VENTES D\'UN CLIENT');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('Ventes d\'un client');
 $pdf->Ln(2);
 $pdf->SetFont('arial','B',12);
 $pdf->Write(65,'                                                                 LISTING DES ACHATS CLIENT(S)');
 $pdf->Ln(1);
 $pdf->Write(65,'                                                                 ___________________________');
 $pdf->Ln(38);                                
 $pdf->SetFont('arial','B',9);
 $pdf->Ln(1);
 $pdf->Write(2,'Code Client                :  ');
 $pdf->Write(2,$_GET["Clt"]);
 $pdf->Ln(5);
 $pdf->Write(2,'Client (s)                 :  ');
 $pdf->Write(2,$nomC);
 $pdf->Ln(5);
 $pdf->Write(2,'Periode     Du :  ');
 $pdf->Write(2,dateFormatFrancais($Debut));
 $pdf->Write(2,'   Au :   ');
 $pdf->Write(2,dateFormatFrancais($Fin));
 $pdf->Write(2,'                     Date :   ');
 $pdf->Write(2,date("d/m/y").' '.date('H:i'));
 $pdf->Ln(7);
 $pdf->SetFontSize(7);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',12,5,180,30);
 $pdf->Cell(17,5,'DATE',1,0,'C',1);
 $pdf->Cell(22,5,'CODE',1,0,'C',1);
 $pdf->Cell(22,5,'CA TTC',1,0,'C',1);
 $pdf->Cell(25,5,'MT FACTURE',1,0,'C',1);
 $pdf->Cell(27,5,'RISTOURNE TTC',1,0,'C',1);
 $pdf->Cell(30,5,'CREDIT RISTOURNE',1,0,'C',1);
 $pdf->Cell(55,5,'CLIENT',1,1,'L',1);
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
  $MT=0;
  $MTRIS=0;
$ttcolis=0;
$mtfraisenlevement=0;
$ttristourne=0;
 $nbre=0;
if ($_GET['Clt'] != 'TOUS')
{
	$sql='SELECT ST.ID_SORTIESTOCK, ST.DATESORTIESTOCK, ST.MTFACTURE, ST.ID_CLIENT, ST.OBSERVATION, ST.CREDITRISTOURNE, ST.STATUT, C.ID_CLIENT, C.NOM, CAT.TAUXRETFISCPRO, CAT.TAUXTVA 	FROM SORTIE_STOCK ST, CLIENT C , CATEGORIE CAT WHERE C.ID_CATEGORIE=CAT.ID_CATEGORIE AND ST.ID_CLIENT=C.ID_CLIENT AND C.ID_CLIENT="'.$_GET['Clt'].'" AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V" ORDER BY ST.ID_SORTIESTOCK' ;
}
else
{
	$sql='SELECT ST.ID_SORTIESTOCK, ST.DATESORTIESTOCK, ST.MTFACTURE, ST.ID_CLIENT, ST.OBSERVATION, ST.CREDITRISTOURNE, ST.STATUT, C.ID_CLIENT, C.NOM, CAT.TAUXRETFISCPRO, CAT.TAUXTVA 	FROM SORTIE_STOCK ST, CLIENT C , CATEGORIE CAT WHERE C.ID_CATEGORIE=CAT.ID_CATEGORIE AND ST.ID_CLIENT=C.ID_CLIENT AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V" ORDER BY ST.ID_SORTIESTOCK' ;		
}
  $reponse= $DataBase->query($sql);
  while($rslt= $reponse->fetch())
 {
	  $tva=$rslt['TAUXTVA'];
	 $psa=$rslt['TAUXRETFISCPRO'];
	 //Ici on recupere les articles de chacune des ventes puis on somme les prix et benef
 $PRIXACHAT=0;
 $PRIXREVIENT=0;
 $PRIXVENTE=0;
 $BENEF=0;
 $ttcolisvte=0;
 $mtristourne=0;
 $tauxristournettc=0;

$sql1='SELECT AV.ID_SORTIESTOCK, AV.PRIXREVIENT, AV.PRIXVENTE, AV.QTESORTIE, A.TAUXRISTOURNE FROM  ARTICLEVENDU AV, ARTICLE A WHERE A.ID_ARTICLE=AV.ID_ARTICLE AND AV.ID_SORTIESTOCK= "'.$rslt['ID_SORTIESTOCK'].'" ' ;
 $reponse1= $DataBase->query($sql1);
  while($rslt1= $reponse1->fetch())
 {
	 $PRIXREVIENT= ($PRIXREVIENT + $rslt1['PRIXREVIENT']);
	 $PRIXVENTE= ($PRIXVENTE + $rslt1['PRIXVENTE']);	
	 $BENEF= ($BENEF + ($rslt1['PRIXVENTE']-$rslt1['PRIXREVIENT']));
	 $ttcolisvte=$ttcolisvte+$rslt1['QTESORTIE'];
	 $tauxristournettc=((100+$rslt['TAUXTVA']+$rslt['TAUXRETFISCPRO'])/100)*$rslt1['TAUXRISTOURNE'];
	 $mtristourne=$mtristourne+($rslt1['QTESORTIE']*number_format($tauxristournettc, 0, ',', ' '));	 
 } 
  $pdf->Cell(17,5,dateFormatFrancais($rslt['DATESORTIESTOCK']),1,0,'C');
  $pdf->Cell(22,5,$rslt['ID_SORTIESTOCK'],1,0,'C');
  $pdf->Cell(22,5,number_format($PRIXVENTE, 0, ',', ' '),1,0,'C');
  $pdf->Cell(25,5,number_format($rslt['MTFACTURE'], 0, ',', ' '),1,0,'C');
  $pdf->Cell(27,5,number_format($mtristourne, 0, ',', ' '),1,0,'C');
  $pdf->Cell(30,5,number_format($rslt['CREDITRISTOURNE'], 0, ',', ' '),1,0,'C');
  $pdf->Cell(55,5,utf8_decode(substr($rslt['NOM'],0,30)),1,1,'L'); 
  $nbre++;
   $MT= $MT+$PRIXVENTE;
   $MTRIS= $MTRIS+$rslt['CREDITRISTOURNE'];
  //$mtfraisenlevement=$mtfraisenlevement+($ttcolisvte*$fraisenlevement);
  $ttristourne=$ttristourne+$mtristourne;
  $mtfacture=$mtfacture+$rslt['MTFACTURE'];
  }
  //totaux
  $pdf->SetFont('arial','B',9);
   $pdf->Cell(39,5,'TOTAUX  ',1,0,'C');
   $pdf->Cell(22,5,number_format($MT, 0, ',', ' '),1,0,'C');
   $pdf->Cell(25,5,number_format($mtfacture, 0, ',', ' '),1,0,'C');
   $pdf->Cell(27,5,number_format($ttristourne, 0, ',', ' '),1,0,'C');
   $pdf->Cell(30,5,number_format($MTRIS, 0, ',', ' '),1,0,'C');
   $pdf->Cell(55,5,'Nombre de facture(s) : '.$nbre,1,1,'L');
   
   //recapitulatif par famille d'article
   $pdf->Ln(10);
   //on recupere la liste sans doublons des familles d'article
   

   	 //famille
	   $pdf->SetFont('arial','B',9);
	   $pdf->Cell(200,5,'RACAPITULATIF  ',1,1,'C');
	   $pdf->Cell(50,5,'FAMILLE',1,0,'C');
	   $pdf->Cell(30,5,'TOTAL COLIS',1,0,'C');
	   $pdf->Cell(30,5,'RISTOURNE HT',1,0,'C');
	   $pdf->Cell(30,5,'TVA',1,0,'C');
	   $pdf->Cell(30,5,'PSA',1,0,'C');
	   $pdf->Cell(30,5,'RISTOURNE TTC',1,1,'C');
	   
	   	 $ttqtefamille=0;
  		 $ttristournettc=0;
		 $tttvaristourne=0;
		 $ttpsaristourne=0;
		 $ttristourne=0;
		 
if ($_GET['Clt'] != 'TOUS')
{
   $sql3='SELECT DISTINCT A.ID_FAMILLE FROM ARTICLE A, ARTICLEVENDU AV, SORTIE_STOCK ST, CLIENT C WHERE C.ID_CLIENT=ST.ID_CLIENT AND AV.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND AV.ID_ARTICLE=A.ID_ARTICLE AND C.ID_CLIENT= "'.$_GET['Clt'].'" AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V" ' ;
}
else
{
	   $sql3='SELECT DISTINCT A.ID_FAMILLE FROM ARTICLE A, ARTICLEVENDU AV, SORTIE_STOCK ST, CLIENT C WHERE C.ID_CLIENT=ST.ID_CLIENT AND AV.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND AV.ID_ARTICLE=A.ID_ARTICLE AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V" ' ;
}
   $reponse3= $DataBase->query($sql3);
   while($rslt3= $reponse3->fetch())
   {
	     $qtefamille=0;
  		 $ristournefamille=0;
		 $ristournefamillettc=0;
		 $tvaristournefamille=0;
		 $psaristournefamille=0;
   
		//On somme les quantites sorties pour chaque article de la famille
if ($_GET['Clt'] != 'TOUS')
{
     $sql4='SELECT AV.QTESORTIE, A.TAUXRISTOURNE FROM ARTICLE A, ARTICLEVENDU AV, SORTIE_STOCK ST, CLIENT C WHERE C.ID_CLIENT=ST.ID_CLIENT AND AV.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND AV.ID_ARTICLE=A.ID_ARTICLE AND C.ID_CLIENT= "'.$_GET['Clt'].'" AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V" AND A.ID_FAMILLE="'.$rslt3['ID_FAMILLE'].'"' ;
}
else
{
	     $sql4='SELECT AV.QTESORTIE, A.TAUXRISTOURNE FROM ARTICLE A, ARTICLEVENDU AV, SORTIE_STOCK ST, CLIENT C WHERE C.ID_CLIENT=ST.ID_CLIENT AND AV.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND AV.ID_ARTICLE=A.ID_ARTICLE  AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V" AND A.ID_FAMILLE="'.$rslt3['ID_FAMILLE'].'"' ;
}
	 $reponse4= $DataBase->query($sql4);
	  while($rslt4= $reponse4->fetch())
	 {
		//On somme les quantites sorties pour chaque article de la famille
		//On somme les quantites sorties pour chaque article de la famille
		$qtefamille=$qtefamille+$rslt4['QTESORTIE'];
		$tauxristournettc=((100+$tva+$psa)/100)*$rslt4['TAUXRISTOURNE'];
		$ristournefamille= $ristournefamille+($rslt4['QTESORTIE']*$rslt4['TAUXRISTOURNE']);
		$ristournefamillettc=$ristournefamillettc+($rslt4['QTESORTIE']*number_format($tauxristournettc, 0, ',', ' '));
	 }
	 	 $tvaristournefamille=$ristournefamille*$tva/100;
		 $psaristournefamille=$ristournefamille*$psa/100;
		  //$ristournefamillettc=$ristournefamille+$tvaristournefamille+ $psaristournefamille;
		  
		$pdf->SetFont('arial','',9); 
	   $pdf->Cell(50,5,$rslt3['ID_FAMILLE'],1,0,'C');
	   $pdf->Cell(30,5,number_format($qtefamille, 0, ',', ' '),1,0,'C');
	   $pdf->Cell(30,5,number_format($ristournefamille, 0, ',', ' '),1,0,'C');
	   $pdf->Cell(30,5,number_format($tvaristournefamille, 0, ',', ' '),1,0,'C');
	   $pdf->Cell(30,5,number_format($psaristournefamille, 0, ',', ' '),1,0,'C');
	   $pdf->Cell(30,5,number_format($ristournefamillettc, 0, ',', ' '),1,1,'C');
	  $ttqtefamille=$ttqtefamille+$qtefamille;
  	  $ttristourne=$ttristourne+$ristournefamille;
	  $tttvaristourne=$tttvaristourne+$tvaristournefamille;
	  $ttpsaristourne=$ttpsaristourne+$psaristournefamille;
	  $ttristournettc=$ttristournettc+$ristournefamillettc;
 }
   	  $pdf->SetFont('arial','B',9); 
	   $pdf->Cell(50,5,'TOTAUX',1,0,'C');
	   $pdf->Cell(30,5,number_format($ttqtefamille, 0, ',', ' '),1,0,'C');
	   $pdf->Cell(30,5,number_format($ttristourne, 0, ',', ' '),1,0,'C');
	   $pdf->Cell(30,5,number_format($tttvaristourne, 0, ',', ' '),1,0,'C');
	   $pdf->Cell(30,5,number_format($ttpsaristourne, 0, ',', ' '),1,0,'C');
	   $pdf->Cell(30,5,number_format($ttristournettc, 0, ',', ' '),1,1,'C');

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
