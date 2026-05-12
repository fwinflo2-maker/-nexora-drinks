<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur"))
{
	include('Connexion.php');
	include('fonctions.php');
	include('fpdf.php');
	$Debut=dateFormatAnglais($_GET['DateD']);
	$Fin=dateFormatAnglais($_GET['DateF']);
	//On recuere les infos clients 
	$tauxristournesht=0;
    $tauxpsaristournes=0;
	$tva=0;
	$tauxretfiscpro=0;
	$sql2='SELECT  C.ID_CLIENT, C.EMAIL, C.NOM, C.TAUXRISTOURNEHT, C.PSARISTOURNES, CAT.TAUXRETFISCPRO, CAT.TAUXTVA FROM CLIENT C, CATEGORIE CAT WHERE C.ID_CATEGORIE=CAT.ID_CATEGORIE AND  C.ID_CLIENT="'.$_GET['Clt'].'" ' ;
    $reponse2= $DataBase->query($sql2);
		while($rslt2= $reponse2->fetch())
		{
			$codeC=$rslt2['ID_CLIENT'];
			$nomC=$rslt2['NOM'];
			$emailC=$rslt2['EMAIL'];
			$tauxristournesht=$rslt2['TAUXRISTOURNEHT'];
			$tauxretfiscpro=$rslt2['TAUXRETFISCPRO'];
			$tva=$rslt2['TAUXTVA'];
			$tauxpsaristournes=$rslt2['PSARISTOURNES'];
		}
	
//on recupere les parametres

$tauxpsa=0;
$bonuscasse=0;
$depotgarantie=0;
$retfiscpro=0;
$tauxremisesht=0;
$tauxristournesht=0;
$tauxpsaristournes=0;

 $sql='SELECT  * FROM PARAMETRE ' ;
 $reponse= $DataBase->query($sql);
 while($rslt= $reponse->fetch())
		{
			
			$tauxpsaremise=$rslt['PSAREMISE'];
			$tauxepargne=$rslt['TAUXEPARGNE'];
			$tauxpsa=$rslt['PSA'];
			$tauxremisesht=$rslt['TAUXREMISESHT'];
		}
 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('ETAT DES RISTOURNES CLT');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('RISTOURNES');
 $pdf->Ln(0);
 $pdf->SetFont('times','B',12);
 $pdf->Write(65,'                                                  AVOIR PARTICIPATION RISTOURNES CLIENT');
 $pdf->Ln(1);
 $pdf->Write(65,'                                                   _____________________________________________');
 $pdf->Ln(40);                                
 $pdf->SetFont('times','B',10);
 $pdf->Write(2,'                                                Client :  '.'('.$codeC.')  '.$nomC.'    '.$emailC);
 $pdf->Ln(5);
 $pdf->Write(2,'                                                Periode Du :  ');
 $pdf->Write(2,dateFormatFrancais($Debut));
 $pdf->Write(2,'   Au :   ');
 $pdf->Write(2,dateFormatFrancais($Fin));
 $pdf->Write(2,'      Date :   ');
 $pdf->Write(2,date("d/m/y").' '.date('H:i'));
 $pdf->Ln(5);
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',10,5,180,30);
 
 //ici on calcule le CA HT  ainsi que le CA TTC
 $pdf->SetFillColor(200,200,200);
 $pdf->SetFont('times','B',10);
 $pdf->Cell(180,5,'CHIFFRE D\'AFFAIRE',1,1,'C',1);
 $pdf->Cell(60,5,'CA HT',1,0,'C');
 $pdf->Cell(55,5,'TVA('.$tva.'%)',1,0,'C');
 $pdf->Cell(65,5,'CA TTC',1,1,'C');
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
  
$caht=0;
$cattc=0;
$caepargne=0;
$capsa=0;
$catva=0;

  //ici on recupere le total des achats boutiques du client
$sql10='SELECT * FROM SORTIE_STOCK WHERE DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ID_CLIENT="'.$codeC.'" AND STATUT="V"' ;
$reponse10= $DataBase->query($sql10);
while($rslt10= $reponse10->fetch())
{
	//Ici on recupere les articles de chacune des ventes puis on somme les prix 
	 $sql11='SELECT * FROM  ARTICLEVENDU WHERE ID_SORTIESTOCK= "'.$rslt10['ID_SORTIESTOCK'].'" ' ;
	 $reponse11= $DataBase->query($sql11);
  	while($rslt11= $reponse11->fetch())
 	{
	 	$cattc= ($cattc + $rslt11['PRIXVENTE']);	 
 	}
}
			//$capsa=$caht*$tauxpsa/100;
			//$caepargne=$caht*$tauxepargne/100;
			$caht=($cattc*100)/($tva+100); 
			$catva=$caht*$tva/100; 
 
  $pdf->Cell(60,5,number_format($caht, 0, ',', ' '),1,0,'C');
  $pdf->Cell(55,5,number_format($catva, 0, ',', ' '),1,0,'C');
  $pdf->Cell(65,5,number_format($cattc, 0, ',', ' '),1,1,'C');
  
   //ici on calcule les ristournes de bases 
 $pdf->SetFont('times','B',8);
 $pdf->Cell(180,5,'ACHATS ET RISTOURNES CORRESPONDANTS',1,1,'C',1);
 $pdf->Cell(75,5,'LIBELLE',1,0,'L');
 $pdf->Cell(35,5,'QTE',1,0,'C');
 $pdf->Cell(45,5,'TAUX AU COLIS',1,0,'C');
 $pdf->Cell(25,5,'VALEUR ',1,1,'C');
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
  
//parametres calcul ristourne pour un article
$valeurR=0;
$qteR=0;
//parametres calcul pour tous les articles
$TTvaleurttcS=0;
$TTqteS=0;
$ttqtesortie=0;

//Ici on recupre la liste sans doublons des articles livres dans la periode 
$sql2='SELECT DISTINCT  AV.ID_ARTICLE FROM ARTICLEVENDU AV, SORTIE_STOCK ST  WHERE AV.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND ST.ID_CLIENT="'.$codeC.'" AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V" ORDER BY AV.ID_ARTICLE  ';
$reponse2= $DataBase->query($sql2);
while($rslt2= $reponse2->fetch())
{
	//Ici on recupere les quantites de la meme article puis on somme
	$qtesortie=0;
	$tauxristournettc=0;
	$sql3 = 'SELECT  AV.ID_ARTICLE, AV.QTESORTIE, AV.ID_SORTIESTOCK, ST.ID_SORTIESTOCK, ST.DATESORTIESTOCK FROM ARTICLEVENDU AV, SORTIE_STOCK ST WHERE AV.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND ST.ID_CLIENT="'.$codeC.'" AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND AV.ID_ARTICLE="'.$rslt2['ID_ARTICLE'].'" AND ST.STATUT="V"';
	$reponse3= $DataBase->query($sql3);
	while($rslt3= $reponse3->fetch())
	{
		$qtesortie=$qtesortie+$rslt3['QTESORTIE'];
	}
	//ici on recupere le libelle et le taux ristourne de l'article
	$sql4 = 'SELECT ID_ARTICLE, LIBELLE, TAUXRISTOURNE FROM ARTICLE WHERE ID_ARTICLE="'.$rslt2['ID_ARTICLE'].'"';
	$reponse4= $DataBase->query($sql4);
	while($rslt4= $reponse4->fetch())
	{
    	$libelle=$rslt4['LIBELLE'];
		$tauxristournear=$rslt4['TAUXRISTOURNE'];
	}
	//calcul des valeurs pr l'article
		//on met la TVA et le retenu fisc pro a zero
		//$tva=0;
		//$tauxretfiscpro=0;
		
	    $tauxristournettc=((100+$tva+$tauxretfiscpro)/100)*$tauxristournear;
		
		$valeurS=number_format($tauxristournettc, 0, ',', ' ')*$qtesortie;
		
		$tvaS=$valeurS*$tva/100;
	//calcul des valeurs totales
		$TTvaleurttcS=$TTvaleurttcS+$valeurS;
		$ttqtesortie=$ttqtesortie+$qtesortie;
		
	  $pdf->Cell(75,5,$libelle,1,0,'L');
	  $pdf->Cell(35,5,number_format($qtesortie, 0, ',', ' '),1,0,'C');
	  $pdf->Cell(45,5,number_format($tauxristournettc, 0, ',', ' '),1,0,'C');
	  $pdf->Cell(25,5,number_format($valeurS, 0, ',', ' '),1,1,'C');

	}
//affichage les ristournes (pr tous les articles)
 	  $pdf->SetFont('times','B',9);
	  $pdf->Cell(75,5,'Totaux :',1,0,'C');
	  $pdf->Cell(35,5,number_format($ttqtesortie, 0, ',', ' '),1,0,'C');
	  $pdf->Cell(45,5,'//',1,0,'C');
	  $pdf->Cell(25,5,number_format($TTvaleurttcS, 0, ',', ' '),1,1,'C');

	  
 //ici on calcule la participation ristourne 
 $pdf->SetFont('times','B',8);
 $pdf->Cell(180,5,'PARTICIPATION  RISTOURNE (QUANTITE * TAUX)',1,1,'C',1);
 $pdf->Cell(45,5,'VALEUR ',1,0,'C');
 $pdf->Cell(45,5,'TVA('.$tva.'%)',1,0,'C');
 $pdf->Cell(45,5,'RETENU FISC('.$tauxretfiscpro.'%)',1,0,'C');
 $pdf->Cell(45,5,'VALEUR TTC',1,1,'C');

 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
 
$valeurristourneht=0;
$tvaristourne=0;
$retfiscpro=0;


			$valeurristourneht=(100*$TTvaleurttcS)/(100+$tauxretfiscpro+$tva);
			$tvaristourne=($valeurristourneht*$tva)/100;
			$retfiscpro=($valeurristourneht*$tauxretfiscpro)/100;
 
 $pdf->Cell(45,5,number_format($valeurristourneht, 0, ',', ' '),1,0,'C');
 $pdf->Cell(45,5,number_format($tvaristourne, 0, ',', ' '),1,0,'C');
 $pdf->Cell(45,5,number_format($retfiscpro, 0, ',', ' '),1,0,'C');
 $pdf->Cell(45,5,number_format($TTvaleurttcS, 0, ',', ' '),1,1,'C');
 
    //Rappel de la Retenu TVA
 $pdf->SetFont('times','B',8);
 //$pdf->Cell(180,5,'RAPPEL DE LA RETENUE',1,1,'C',1);
 //$pdf->Cell(90,5,' RETENUE TVA',1,0,'C');
 //$pdf->Cell(90,5,'- '.number_format($tvaristourne, 0, ',', ' '),1,1,'C');

 
 $valeurhtTR=0;
 $tvaTR=0;
 $retfiscproTR=0;
 $valeurttcTR=0;

  //Total Ristourne Hors TVA 
	$ttristourneshorstva=0;
	$ttristourneshorstva=$TTvaleurttcS-$tvaristourne;
  
 $pdf->SetFont('times','B',8);
 //$pdf->Cell(180,5,'TOTAL RISTOURNE HORS TVA',1,1,'C',1);
 //$pdf->Cell(90,5,'TOTAL RISTOURNE HORS TVA',1,0,'C');
 //$pdf->Cell(90,5,number_format($ttristourneshorstva, 0, ',', ' '),1,1,'C');
 
  //RETROCESSION PSA
  $valeurristournestaux=0;
  $valeurhtPSA=0;
  $valeurhtPSA=$caht*$tauxpsaristournes/100;
  $valeurristournestaux=$valeurristourneht*$tauxristournesht/100;
  $valeurepargneachat=$valeurhtPSA-$valeurristournestaux;
 $pdf->SetFont('times','B',8);
 $pdf->Cell(180,5,'RETROCESSION PSA CLIENTS',1,1,'C',1);
 $pdf->SetFont('times','B',7);
 $pdf->Cell(25,5,'CA HT',1,0,'C',1);
 $pdf->Cell(30,5,'TAUX PSA RISTOU. HT',1,0,'C',1);
 $pdf->Cell(30,5,'PRELEV. SUR CA',1,0,'C',1);
 $pdf->Cell(30,5,'TOTAL RISTOU. HT',1,0,'C',1);
 $pdf->Cell(25,5,'-'.$tauxristournesht.'  % RISTOU. HT',1,0,'C',1);
 $pdf->Cell(40,5,'REMBOURS. EPARGNE ACHAT',1,1,'C',1);

 $pdf->Cell(25,5,number_format($caht, 0, ',', ' '),1,0,'C');
 $pdf->Cell(30,5,number_format($tauxpsaristournes, 0, ',', ' ').'%',1,0,'C');
 $pdf->Cell(30,5,number_format($valeurhtPSA, 0, ',', ' '),1,0,'C');
 $pdf->Cell(30,5,number_format($valeurristourneht, 0, ',', ' '),1,0,'C');
 $pdf->Cell(25,5,number_format($valeurristournestaux, 0, ',', ' '),1,0,'C');
 $pdf->Cell(40,5,number_format($valeurepargneachat, 0, ',', ' '),1,1,'C');
 
 
 //Retenu Diverses
$retenuefrigo=0;
$retenueDA=0;
$retenueCGA=0;

$retenuefrigo=$_GET['Retfr'];
$retenueDA=$_GET['RetDA'];
$retenueCGA=$_GET['RetCGA'];
$ttretenues=$retenuefrigo+$retenueDA+$retenueCGA;  
 
 $pdf->SetFont('times','B',8);
 $pdf->Cell(180,5,'RETENUES DIVERS',1,1,'C',1);
 $pdf->Cell(90,5,'RETENUES ',1,0,'C',1);
 $pdf->Cell(90,5,'MONTANT',1,1,'C',1);
  
 $pdf->Cell(90,5,'RETENUE FRIGO ',1,0,'L');
 $pdf->Cell(90,5,number_format($retenuefrigo, 0, ',', ' '),1,1,'C');
 $pdf->Cell(90,5,'RETENUE DROIT D\'AUTEUR ',1,0,'L');
 $pdf->Cell(90,5,number_format($retenueDA, 0, ',', ' '),1,1,'C');
 $pdf->Cell(90,5,'RETENUE CGA A LA SOURCE ',1,0,'L');	
 $pdf->Cell(90,5,number_format($retenueCGA, 0, ',', ' '),1,1,'C'); 
	 
	 
//Regularisations Diverses
	$reguristourne=0;
	$regpsaencours=0;
	$regpsaanterieur=0;
	$regpsaanterieur=0;
	$regDA=0;
	$regEntFrigo=0;
	$regCGA=0;
	
	$reguristourne=$_GET['RegR'];
	$regpsaencours=$_GET['RegPSAEC'];
	$regpsaanterieur=$_GET['RegPSAAnt'];
	$regDA=$_GET['RegDA'];
	$regEntFrigo=$_GET['RegEntfr'];
	$regCGA=$_GET['RegCGA'];
	
	$ttregularisations=$reguristourne+$regpsaencours+$regpsaanterieur+$regDA+$regEntFrigo+$regCGA;
	$pdf->SetFont('times','B',8);
   $pdf->Cell(180,5,'REGULARISATIONS DIVERSES',1,1,'C',1);
   $pdf->Cell(90,5,'LIBELLE',1,0,'C');
   $pdf->Cell(90,5,'MONTANT',1,1,'C');
	
   $pdf->Cell(90,5,'REGULARISATION RISTOURNES ',1,0,'L');
   $pdf->Cell(90,5,number_format($reguristourne, 0, ',', ' '),1,1,'C');
   $pdf->Cell(90,5,'REGULARISATION PSA EXERCICE ENCOURS ',1,0,'L');
   $pdf->Cell(90,5,number_format($regpsaencours, 0, ',', ' '),1,1,'C');
   $pdf->Cell(90,5,'REGULARISATION PSA EXERCICE ANTERIEUR ',1,0,'L');	
   $pdf->Cell(90,5,number_format($regpsaanterieur, 0, ',', ' '),1,1,'C');
   $pdf->Cell(90,5,'REGULARISATION DROIT D\'AUTEUR ',1,0,'L');
   $pdf->Cell(90,5,number_format($regDA, 0, ',', ' '),1,1,'C');
   $pdf->Cell(90,5,'REGULARISATION ENTRETIEN FRIGO ',1,0,'L');
   $pdf->Cell(90,5,number_format($regEntFrigo, 0, ',', ' '),1,1,'C');
   $pdf->Cell(90,5,'REGULARISATION CGA ',1,0,'L');	
   $pdf->Cell(90,5,number_format($regCGA, 0, ',', ' '),1,1,'C');
	
    //ici on affiche Total Ristourne Nettes à Payer
	$ttristournesnettes=0;
	$ttristournesnettes=$TTvaleurttcS+$valeurepargneachat-$ttretenues+$ttregularisations;
	
 $pdf->SetFont('times','B',10);
 $pdf->Cell(90,5,'TOTAL RISTOURNES NETTES A PAYER',1,0,'C',1);
 $pdf->SetFont('times','B',16);
 $pdf->Cell(90,5,number_format($ttristournesnettes, 0, ',', ' '),1,1,'C',1);
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
