<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
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
 $pdf->SetFont('arial','B',12);
 $pdf->Write(65,'                                  				            ETAT DES VENTES FRIGO PAR ARTICLE');
 $pdf->Ln(1);
 $pdf->Write(65,'                                                 ____________________________________');
 $pdf->Ln(38);                                
 $pdf->SetFont('arial','B',10);
 $pdf->Write(2,'                     Periode :   Du :  ');
 $pdf->Write(2,dateFormatFrancais($Debut));
 $pdf->Write(2,'   Au :   ');
 $pdf->Write(2,dateFormatFrancais($Fin));
 $pdf->Write(2,'                        Date :   ');
 $pdf->Write(2,date("d/m/Y").' '.date('H:i'));
 $pdf->Ln(7);
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->SetFont('arial','B',9);
 $pdf->Image('IMG\logo.jpg',15,5,180,30);
 $pdf->Cell(20,7,'CODE',1,0,'L',1);
 $pdf->Cell(70,7,' LIBELLE',1,0,'L',1);
 $pdf->Cell(20,7,'QTE(BTLE)',1,0,'C',1);
 $pdf->Cell(25,7,'PRIX REVIENT ',1,0,'L',1);
 $pdf->Cell(25,7,'PRIX VENTE',1,0,'L',1);
 $pdf->Cell(20,7,'BENEFICE',1,1,'L',1);
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
  
$QTE=0;
$TTPV=0;
$TTPR=0;
$TTB=0;
//Ici on recupre la liste sans doublons des articles vendus dans la periode 
 $sql='SELECT DISTINCT  AR.ID_ARTICLE FROM ARTICLEVENDU_FRIGO AR, SORTIE_STOCK_FRIGO ST  WHERE AR.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V" ORDER BY AR.ID_ARTICLE';
$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
//Ici on recupere les quantites de la meme article puis on somme
$qte=0;
$PRIXVENTE=0;
$PRIXREVIENT=0;
$BENEF=0;
$sql1 = 'SELECT  AR.ID_ARTICLE, AR.QTESORTIE, AR.PRIXVENTE,AR.PRIXREVIENT, AR.ID_SORTIESTOCK, ST.ID_SORTIESTOCK, ST.DATESORTIESTOCK FROM ARTICLEVENDU_FRIGO AR, SORTIE_STOCK_FRIGO ST WHERE AR.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND AR.ID_ARTICLE="'.$rslt['ID_ARTICLE'].'" AND ST.STATUT="V"';
$reponse1= $DataBase->query($sql1);
		while($rslt1= $reponse1->fetch())
		{
			$qte=$qte+$rslt1['QTESORTIE'];
			$PRIXVENTE=$PRIXVENTE+$rslt1['PRIXVENTE'];
	 		$PRIXREVIENT= ($PRIXREVIENT + $rslt1['PRIXREVIENT']);
	 		$BENEF= ($PRIXVENTE-$PRIXREVIENT);
		}
//ici on recupere le libelle et la marque de l'article
$sql2 = 'SELECT ID_ARTICLE, LIBELLE, MARQUE,NBREBTE FROM ARTICLE WHERE ID_ARTICLE="'.$rslt['ID_ARTICLE'].'"';
$reponse2= $DataBase->query($sql2);
		while($rslt2= $reponse2->fetch())
		{
    		$libelle=$rslt2['LIBELLE'];
		}

			  $pdf->Cell(20,7,$rslt['ID_ARTICLE'],1);
			  $pdf->Cell(70,7,$libelle,1);
			  $pdf->Cell(20,7,$qte,1,0,'C');
			  $pdf->Cell(25,7,$PRIXREVIENT.' F',1,0,'C');
			  $pdf->Cell(25,7,$PRIXVENTE.' F',1,0,'C');
			  $pdf->Cell(20,7,$BENEF.' F',1,1,'C');
			  $TTPV = ($TTPV + $PRIXVENTE);
			  $TTPR = ($TTPR + $PRIXREVIENT);
			  $TTB = ($TTB + $BENEF);
			  $QTE=$QTE+$qte;
		}
  $pdf->SetFont('Arial','B',9);
  $pdf->Cell(90,7,'TOTAUX : ',1,0,'C');
  $pdf->Cell(20,7,$QTE,1,0,'C');
  $pdf->Cell(25,7,number_format($TTPR, 0, ',', ' ').' F',1,0,'C');
  $pdf->Cell(25,7,number_format($TTPV, 0, ',', ' ').' F',1,0,'C');
  $pdf->Cell(20,7,number_format($TTB, 0, ',', ' ').' F',1,1,'C');
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
