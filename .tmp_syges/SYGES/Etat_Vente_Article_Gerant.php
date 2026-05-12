<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"|| $_SESSION['habilitation']=="OPS" || $_SESSION['habilitation']=="Comptable" ))
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
 $pdf->SetTitle('ETAT DES SORTIES DE STOCK ');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('SORTIE STOCK');
 $pdf->Ln(0);
 $pdf->SetFont('arial','B',10);
 $pdf->Write(65,'                                                            		                  ETAT DES VENTES MAGASIN PAR ARTICLES');
 $pdf->Ln(1);
 $pdf->Write(65,'                                                                             ________________________________________');
 $pdf->Ln(38);                                
 $pdf->SetFont('arial','B',10);
 $pdf->Write(2,'Utilisateur :  ');
 $pdf->SetFont('arial','I',10);
 $pdf->Write(2,$_GET['user']); 
 $pdf->SetFont('arial','B',10);
 $pdf->Write(2,'    Periode Du : ');
 $pdf->SetFont('arial','I',10);
 $pdf->Write(2,dateFormatFrancais($Debut));
 $pdf->Write(2,' Au : ');
 $pdf->Write(2,dateFormatFrancais($Fin));
 $pdf->SetFont('arial','B',10);
 $pdf->Write(2,'          Date :   ');
 $pdf->SetFont('arial','I',10);
 $pdf->Write(2,date("d/m/Y").' '.date('H:i'));
 $pdf->SetFont('arial','B',10);
 $pdf->Ln(10);
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',15,5,180,30);
 $pdf->Cell(20,7,'CODE',1,0,'L',1);
 $pdf->Cell(30,7,'CONDITION.',1,0,'L',1);
 $pdf->Cell(85,7,' LIBELLE',1,0,'L',1);
 $pdf->Cell(30,7,'QTE',1,0,'C',1);
$pdf->Cell(30,7,'PRIX VENTE',1,1,'L',1);
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
 
$nbre=0;  
$TTPR=0;
$TTPV=0;
$TTB=0;
$QTET=0;
$nbrecasier=0;
//Ici on recupre la liste sans doublons des articles vendus dans la periode 
if ($_GET['user']=='TOUS')
	{
 $sql='SELECT DISTINCT  AR.ID_ARTICLE FROM ARTICLEVENDU AR, SORTIE_STOCK ST  WHERE AR.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V" ORDER BY AR.ID_ARTICLE ';
	}
	else
	{
		 $sql='SELECT DISTINCT  AR.ID_ARTICLE FROM ARTICLEVENDU AR, SORTIE_STOCK ST  WHERE AR.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V" AND ST.LOGIN="'.$_GET['user'].'" ORDER BY AR.ID_ARTICLE ';
	} 
$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
//Ici on recupere les quantites de la meme article puis on somme
$qte=0;
$PRIXREVIENT=0;
$PRIXVENTE=0;
$BENEF=0;
if ($_GET['user']=='TOUS')
	{
		 $sql1 = 'SELECT  AR.ID_ARTICLE, AR.QTESORTIE,AR.PRIXVENTE,AR.PRIXREVIENT, AR.ID_SORTIESTOCK, ST.ID_SORTIESTOCK, ST.DATESORTIESTOCK FROM ARTICLEVENDU AR, SORTIE_STOCK ST WHERE AR.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND AR.ID_ARTICLE="'.$rslt['ID_ARTICLE'].'" AND ST.STATUT="V"';
	}
	else
	{
		$sql1 = 'SELECT  AR.ID_ARTICLE, AR.QTESORTIE,AR.PRIXVENTE,AR.PRIXREVIENT, AR.ID_SORTIESTOCK, ST.ID_SORTIESTOCK, ST.DATESORTIESTOCK FROM ARTICLEVENDU AR, SORTIE_STOCK ST WHERE AR.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND AR.ID_ARTICLE="'.$rslt['ID_ARTICLE'].'" AND ST.STATUT="V" AND ST.LOGIN="'.$_GET['user'].'"';
	}
$reponse1= $DataBase->query($sql1);
		while($rslt1= $reponse1->fetch())
		{
			$qte=$qte+$rslt1['QTESORTIE'];
			$PRIXREVIENT=$PRIXREVIENT+$rslt1['PRIXREVIENT'];
			$PRIXVENTE=$PRIXVENTE+$rslt1['PRIXVENTE'];
			$BENEF= ($BENEF + ($rslt1['PRIXVENTE']-$rslt1['PRIXREVIENT']));
		}
//ici on recupere le libelle et la marque de l'article
$sql2 = 'SELECT ID_ARTICLE, LIBELLE, MARQUE,NBREBTE, QTESTOCK FROM ARTICLE WHERE ID_ARTICLE="'.$rslt['ID_ARTICLE'].'"';
$reponse2= $DataBase->query($sql2);
		while($rslt2= $reponse2->fetch())
		{
			$marque=$rslt2['MARQUE'];
    		$libelle=$rslt2['LIBELLE'];
			$qtestock=$rslt2['QTESTOCK'];
			$nbrebte=$rslt2['NBREBTE'];
			$conditionnement=$rslt2['MARQUE'];
		}


  $pdf->Cell(20,7,$rslt['ID_ARTICLE'],1);
  $pdf->Cell(30,7,$marque.' '.$nbrebte,1,0,'L');
  $pdf->Cell(85,7,$libelle,1);
  $pdf->Cell(30,7,$qte,1,0,'C');
  $pdf->Cell(30,7,number_format($PRIXVENTE, 0, ',', ' ').' F',1,1,'C');
  
  $nbre++;
  $TTPR = ($TTPR + $PRIXREVIENT);
  $TTPV = ($TTPV + $PRIXVENTE);
  $TTB = ($TTB + $BENEF);
  $QTET=$QTET+$qte;
//on compte les casiers
  if(($conditionnement=="CASIER") || ($conditionnement=="casier")|| ($conditionnement=="CASIERS")|| ($conditionnement=="casiers"))
  {
	  $nbrecasier=$nbrecasier+$qte;
  }
  }
   //ici on recupere les frais enlevement.
$mtvte=0;
$fraisenlevement=0;
$sql3='SELECT * FROM SORTIE_STOCK WHERE DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND STATUT="V"' ;
$reponse3= $DataBase->query($sql3);
while($rslt3= $reponse3->fetch())
{
	$fraisenlevement = ($fraisenlevement + $rslt3['FRAISENLEVEMENT']);
}	

//ici on recupere les consignes clients
$mtcc=0;
$sql6='SELECT C.MONTANT FROM CONSIGNE C, SORTIE_STOCK ST WHERE C.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V"' ;
$reponse6= $DataBase->query($sql6);
		while($rslt6= $reponse6->fetch())
		{
				$mtcc=$mtcc+$rslt6['MONTANT'];
		}
		
//ici on recupere les deconsignes vte
$mtrevte=0;
$sql11='SELECT  R.MONTANT FROM RTREMBVTE R,SORTIE_STOCK ST WHERE R.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V"';
$reponse11= $DataBase->query($sql11);
while($rslt11= $reponse11->fetch())
		{
				$mtrevte=$mtrevte+$rslt11['MONTANT'];
		}
   //
  $pdf->SetFont('arial','B',7);
  $pdf->Cell(20,7,' TOTAUX : ',1,0,'C');
  $pdf->Cell(30,7,' CASIER(S) : '.number_format($nbrecasier, 0, ',', ' '),1,0,'C');
  $pdf->Cell(85,7,'  NBRE ARTICLE(S) : '.number_format($nbre, 0, ',', ' '),1,0,'C');
  $pdf->Cell(30,7,' COLIS : '.number_format($QTET, 0, ',', ' '),1,0,'C');
  $pdf->Cell(30,7,'(1)'.number_format($TTPV, 0, ',', ' ').' F',1,1,'C');
  
  
  $pdf->Cell(50,7,' FRAIS ENLEVEMENT(2) : '.number_format($fraisenlevement, 0, ',', ' ').' F',1,0,'C');
  $pdf->Cell(85,7,' CONSIGNES CLIENTS(3) : '.number_format($mtcc, 0, ',', ' ').' F',1,0,'C');
  $pdf->Cell(60,7,' DECONSIGNATIONS CLIENTS(4) : '.number_format($mtrevte, 0, ',', ' ').' F',1,1,'C');
  
  $pdf->SetFont('arial','B',10);
  $pdf->Cell(195,7,' MONTANT TOTAL (1)+(2)+(3)-(4) : '.number_format($TTPV+$fraisenlevement+$mtcc-$mtrevte, 0, ',', ' ').' F',1,1,'C');
   //
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
