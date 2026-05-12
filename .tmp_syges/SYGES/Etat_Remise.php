<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
{
 include('fpdf.php');
 include('Connexion.php');
 include('fonctions.php');
 $Debut=dateFormatAnglais($_GET["debut"]);
 $Fin=dateFormatAnglais($_GET["fin"]);
 //on recupere les parametres
$tva=0;
$tauxpsa=0;
$bonuscasse=0;
$depotgarantie=0;
$retfiscpro=0;
$tauxremisesht=0;

 $sql='SELECT  * FROM PARAMETRE ' ;
 $reponse= $DataBase->query($sql);
 while($rslt= $reponse->fetch())
		{
			
			$tva=$rslt['TVA'];
			$tauxpsaremise=$rslt['PSAREMISE'];
			$bonuscasse=$rslt['BONUSCASSE'];
			$depotgarantie=$rslt['DEPOTGARANTIE'];
			$tauxepargne=$rslt['TAUXEPARGNE'];
			$tauxpsa=$rslt['PSA'];
			$retfiscpro=$rslt['TAUXRETFISCPRO'];
			$tauxremisesht=$rslt['TAUXREMISESHT'];
		}

 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('ETAT DES REMISES ');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('REMISES');
 $pdf->Ln(0);
 $pdf->SetFont('times','B',12);
 $pdf->Write(59,'                                                           AVOIR DE REMISES SUR ACHAT');
 $pdf->Ln(1);
 $pdf->Write(59,'                                                            _______________________________');
 $pdf->Ln(35);                                
 $pdf->SetFont('times','B',10);
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
 
 //ici on calcule le CA HT les prelements (psa et epargne) ainsi que le CA TTC
 $pdf->SetFillColor(200,200,200);
 $pdf->SetFont('times','B',10);
 $pdf->Cell(180,5,'CHIFFRE D\'AFFAIRE',1,1,'C',1);
 $pdf->Cell(35,5,'CA HT',1,0,'C');
 $pdf->Cell(35,5,'PSA('.$tauxpsa.'%)',1,0,'C');
 $pdf->Cell(35,5,'EPARGNE('.$tauxepargne.'%)',1,0,'C');
 $pdf->Cell(35,5,'TVA('.$tva.'%)',1,0,'C');
 $pdf->Cell(40,5,'CA TTC',1,1,'C');
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
  
$caht=0;
$caepargne=0;
$capsa=0;
$catva=0;

$sql1='SELECT LIQUIDEHT FROM APPROVISIONNEMENT  WHERE DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'"' ;
$reponse1= $DataBase->query($sql1);
while($rslt1= $reponse1->fetch())
		{
			$caht=$caht+$rslt1['LIQUIDEHT'];
	    }
			$capsa=$caht*$tauxpsa/100;
			$caepargne=$caht*$tauxepargne/100;
			$catva=$caht*$tva/100; 
			$cattc=$caht+$capsa+$caepargne+$catva;
 
  $pdf->Cell(35,5,number_format($caht, 0, ',', ' '),1,0,'C');
  $pdf->Cell(35,5,number_format($capsa, 0, ',', ' '),1,0,'C');
  $pdf->Cell(35,5,number_format($caepargne, 0, ',', ' '),1,0,'C');
  $pdf->Cell(35,5,number_format($catva, 0, ',', ' '),1,0,'C');
  $pdf->Cell(40,5,number_format($cattc, 0, ',', ' '),1,1,'C');
  
   //ici on calcule les remises de bases 
 $pdf->SetFont('times','B',8);
 $pdf->Cell(180,5,'REMISES DE BASE',1,1,'C',1);
 $pdf->Cell(55,5,'LIBELLE',1,0,'L');
 $pdf->Cell(15,5,'QTE',1,0,'C');
 $pdf->Cell(20,5,'TAUX HT',1,0,'C');
 $pdf->Cell(20,5,'VALEUR HT',1,0,'C');
 $pdf->Cell(20,5,'MT TVA',1,0,'C');
 $pdf->Cell(25,5,'RET. FISC. PRO.',1,0,'C');
 $pdf->Cell(25,5,'VALEUR TTC',1,1,'C');
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
  
//parametres calcul remise pour un article
$valeurhtRB=0;
$tvaRB=0;
$retfiscproRB=0;
$valeurttcRB=0;
$qteRB=0;
//parametres calcul pour tous les articles
$TTvaleurhtRB=0;
$TTtvaRB=0;
$TTretfiscproRB=0;
$TTvaleurttcRB=0;
$TTqteRB=0;
$TTqterecu=0;

//Ici on recupre la liste sans doublons des articles livres dans la periode 
$sql2='SELECT DISTINCT  AR.ID_ARTICLE FROM ARTICLE_RECU AR, APPROVISIONNEMENT A  WHERE AR.ID_APPRO=A.ID_APPRO AND A.DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND A.STATUT="V" ORDER BY AR.ID_ARTICLE  ';
$reponse2= $DataBase->query($sql2);
while($rslt2= $reponse2->fetch())
{
	//Ici on recupere les quantites de la meme article puis on somme
	$qterecu=0;
	$sql3 = 'SELECT  AR.ID_ARTICLE, AR.QTERECU, AR.ID_APPRO, A.ID_APPRO, A.DATE_APPRO FROM ARTICLE_RECU AR, APPROVISIONNEMENT A WHERE AR.ID_APPRO=A.ID_APPRO AND A.DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND AR.ID_ARTICLE="'.$rslt2['ID_ARTICLE'].'" AND A.STATUT="V"';
	$reponse3= $DataBase->query($sql3);
	while($rslt3= $reponse3->fetch())
	{
		$qterecu=$qterecu+$rslt3['QTERECU'];
	}
	//ici on recupere le libelle et le taux remise de l'article
	$sql4 = 'SELECT ID_ARTICLE, LIBELLE, TAUXREMISE FROM ARTICLE WHERE ID_ARTICLE="'.$rslt2['ID_ARTICLE'].'"';
	$reponse4= $DataBase->query($sql4);
	while($rslt4= $reponse4->fetch())
	{
    	$libelle=$rslt4['LIBELLE'];
		$tauxremisear=$rslt4['TAUXREMISE'];
	}
	//calcul des valeurs pr l'article
		$valeurhtRB=$qterecu*$tauxremisear;
		$tvaRB=$valeurhtRB*$tva/100;
		$retfiscproRB=$valeurhtRB*$retfiscpro/100;
		$valeurttcRB=$valeurhtRB+$tvaRB+$retfiscproRB;
	//calcul des valeurs totales
		$TTvaleurhtRB=$TTvaleurhtRB+$valeurhtRB;
		$TTtvaRB=$TTtvaRB+$tvaRB;
		$TTretfiscproRB=$TTretfiscproRB+$retfiscproRB;
		$TTvaleurttcRB=$TTvaleurttcRB+$valeurttcRB;
		$TTqterecu=$TTqterecu+$qterecu;
 
	  $pdf->Cell(55,5,$libelle,1,0,'L');
	  $pdf->Cell(15,5,number_format($qterecu, 0, ',', ' '),1,0,'C');
	  $pdf->Cell(20,5,$tauxremisear,1,0,'C');
	  $pdf->Cell(20,5,number_format($valeurhtRB, 0, ',', ' '),1,0,'C');
	  $pdf->Cell(20,5,number_format($tvaRB, 0, ',', ' '),1,0,'C');
	  $pdf->Cell(25,5,number_format($retfiscproRB, 0, ',', ' '),1,0,'C');
	  $pdf->Cell(25,5,number_format($valeurttcRB, 0, ',', ' '),1,1,'C');
	}
//affichage les remises base(pr tous les articles)
 	  $pdf->SetFont('times','B',9);
	  $pdf->Cell(55,5,'Totaux :',1,0,'C');
	  $pdf->Cell(15,5,number_format($TTqterecu, 0, ',', ' '),1,0,'C');
	  $pdf->Cell(20,5,'//',1,0,'C');
	  $pdf->Cell(20,5,number_format($TTvaleurhtRB, 0, ',', ' '),1,0,'C');
	  $pdf->Cell(20,5,number_format($TTtvaRB, 0, ',', ' '),1,0,'C');
	  $pdf->Cell(25,5,number_format($TTretfiscproRB, 0, ',', ' '),1,0,'C');
	  $pdf->Cell(25,5,number_format($TTvaleurttcRB, 0, ',', ' '),1,1,'C');
	  
   //ici on calcule le bonus casse
 $pdf->SetFont('times','B',8);
 $pdf->Cell(180,5,'BONUS CASSE',1,1,'C',1);
 $pdf->Cell(30,5,'CA TTC',1,0,'C');
 $pdf->Cell(30,5,'TAUX HT',1,0,'C');
 $pdf->Cell(30,5,'VALEUR HT',1,0,'C');
 $pdf->Cell(30,5,'MT TVA',1,0,'C');
 $pdf->Cell(30,5,'RET. FISC. PRO.',1,0,'C');
 $pdf->Cell(30,5,'VALEUR TTC',1,1,'C');
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
 
 $valeurhtBC=0;
 $tvaBC=0;
 $capsa=0;
 $catva=0;

 //$valeurhtBC=$cattc*$bonuscasse/100;
 $valeurhtBC=($bonuscasse*$cattc)/(100+$tva+$retfiscpro);
 $tvaBC=$valeurhtBC*$tva/100;
 $retfiscproBC=$valeurhtBC*$retfiscpro/100;
 $valeurttcBC=$valeurhtBC+$tvaBC+$retfiscproBC; 
 
 //ici on affiche le les valeurs Bonus casse
 $pdf->Cell(30,5,number_format($cattc, 0, ',', ' '),1,0,'C');
 $pdf->Cell(30,5,$bonuscasse.'%',1,0,'C');
 $pdf->Cell(30,5,number_format($valeurhtBC, 0, ',', ' '),1,0,'C');
 $pdf->Cell(30,5,number_format($tvaBC, 0, ',', ' '),1,0,'C');
 $pdf->Cell(30,5,number_format($retfiscproBC, 0, ',', ' '),1,0,'C');
 $pdf->Cell(30,5,number_format($valeurttcBC, 0, ',', ' '),1,1,'C');
 
    //ici on calcule le total remise ttc
 $pdf->SetFont('times','B',8);
 $pdf->Cell(180,5,'TOTAL REMISES TTC',1,1,'C',1);
 $pdf->Cell(45,5,'VALEUR HT',1,0,'C');
 $pdf->Cell(45,5,'MT TVA',1,0,'C');
 $pdf->Cell(45,5,'RET. FISC. PRO',1,0,'C');
 $pdf->Cell(45,5,'VALEUT TTC',1,1,'C');
 
 $valeurhtTR=0;
 $tvaTR=0;
 $retfiscproTR=0;
 $valeurttcTR=0;

 $valeurhtTR=$TTvaleurhtRB+$valeurhtBC;
 $tvaTR=$TTtvaRB+$tvaBC;
 $retfiscproTR=$TTretfiscproRB+$retfiscproBC;
 $valeurttcTR=$valeurttcBC+$TTvaleurttcRB;
 
  //ici on affiche le total remises ttc
 $pdf->SetFont('times','',8);
 $pdf->Cell(45,5,number_format($valeurhtTR, 0, ',', ' '),1,0,'C');
 $pdf->Cell(45,5,number_format($tvaTR, 0, ',', ' '),1,0,'C');
 $pdf->Cell(45,5,number_format($retfiscproTR, 0, ',', ' '),1,0,'C');
 $pdf->Cell(45,5,number_format($valeurttcTR, 0, ',', ' '),1,1,'C');
 
     //ici on calcule la retenue tva
 $pdf->SetFont('times','B',8);
 $pdf->Cell(180,5,'RETENUE TVA',1,1,'C',1);
 $pdf->SetFont('times','',8);
 $pdf->Cell(60,5,'VALEUR TTC',1,0,'C');
 $pdf->Cell(120,5,'- '.number_format($tvaTR, 0, ',', ' '),1,1,'C');
      //ici on calcule le total remise hors tva
 $ttremisehtva=$valeurttcTR-$tvaTR;
 $pdf->SetFont('times','B',8);
 $pdf->Cell(180,5,'TOTAL REMISES HTVA',1,1,'C',1);
 $pdf->SetFont('times','',8);
 $pdf->Cell(60,5,'VALEUR TTC',1,0,'C');
 $pdf->Cell(120,5,number_format($ttremisehtva, 0, ',', ' '),1,1,'C');
 
     //ici on calcule le Depot de Garantie
 $valeurttcDG=$valeurttcTR*$depotgarantie/100;
 $pdf->SetFont('times','B',8);
 $pdf->Cell(180,5,'DEPOT DE GARANTIE',1,1,'C',1);
 $pdf->Cell(60,5,'REMISE TTC',1,0,'C');
 $pdf->Cell(60,5,'TAUX',1,0,'C');
 $pdf->Cell(60,5,'VALEUR TTC',1,1,'C');
 //ici on affiche le total Depot de Garantie
 $pdf->SetFont('times','',8);
 $pdf->Cell(60,5,number_format($valeurttcTR, 0, ',', ' '),1,0,'C');
 $pdf->Cell(60,5,number_format($depotgarantie, 0, ',', ' ').'%',1,0,'C');
 $pdf->Cell(60,5,number_format($valeurttcDG, 0, ',', ' '),1,1,'C');
 
    //ici on calcule le PSA
				
 $valeurhtPSA=$caht*$tauxpsaremise/100;
 $valeurremisesht=$valeurhtTR*$tauxremisesht/100;
 $valeurepargneachat=$valeurhtPSA-$valeurremisesht;
 
 $pdf->SetFont('times','B',8);
 $pdf->Cell(180,5,'PSA',1,1,'C',1);
 $pdf->Cell(30,5,'CA HT',1,0,'C');
 $pdf->Cell(25,5,'TAUX',1,0,'C');
 $pdf->Cell(30,5,'VALEUR HT',1,0,'C');
 $pdf->Cell(30,5,'TOTAL REMISE HT',1,0,'C');
 $pdf->Cell(35,5,'MOINS'.$tauxremisesht.'% REMISE HT',1,0,'C');
 $pdf->Cell(30,5,'VALEUR TTC',1,1,'C');
  //ici on affiche les valeurs PSA
  $pdf->SetFont('times','',8);
 $pdf->Cell(30,5,number_format($caht, 0, ',', ' '),1,0,'C');
 $pdf->Cell(25,5,number_format($tauxpsaremise, 0, ',', ' ').'%',1,0,'C');
 $pdf->Cell(30,5,number_format($valeurhtPSA, 0, ',', ' '),1,0,'C');
 $pdf->Cell(30,5,number_format($valeurhtTR, 0, ',', ' '),1,0,'C');
 $pdf->Cell(35,5,number_format($valeurremisesht, 0, ',', ' '),1,0,'C');
 $pdf->Cell(30,5,number_format($valeurepargneachat, 0, ',', ' '),1,1,'C');
     //ici on calcule le Total Remise Nettes à Payer
 $ttremisesnettes=$ttremisehtva-$valeurttcDG+$valeurepargneachat;
 $pdf->SetFont('times','B',8);
 $pdf->Cell(180,5,'RECAPITULATIF DES ELEMENTS DE CALCUL DU TOTAL DES REMISES',1,1,'C',1);
 $pdf->Cell(60,5,'TOTAL REMISES HTVA(A)',1,0,'C');
 $pdf->Cell(60,5,'DEPOT DE GARANTIE (B)',1,0,'C');
 $pdf->Cell(60,5,'REMBOUR. EPARGNE ACHAT(C)',1,1,'C');
   //ici on affiche les elements de calcul des Remises Nettes à Payer
 $pdf->SetFont('times','',8);
 $pdf->Cell(60,5,number_format($ttremisehtva, 0, ',', ' '),1,0,'C');
 $pdf->Cell(60,5,number_format($valeurttcDG, 0, ',', ' '),1,0,'C');
 $pdf->Cell(60,5,number_format($valeurepargneachat, 0, ',', ' '),1,1,'C');
    //ici on affiche Total Remise Nettes à Payer
 $pdf->SetFont('times','B',10);
  $pdf->Cell(90,5,'TOTAL REMISES NETTES A PAYER(A-B+C)',1,0,'C');
 $pdf->SetFillColor(200,200,200);
 $pdf->SetFont('times','B',14);
 $pdf->Cell(90,5,number_format($ttremisesnettes, 0, ',', ' ').' FCFA',1,1,'C',1);
  //ecriture des totaux

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
