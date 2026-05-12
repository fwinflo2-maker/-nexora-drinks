<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"  || $_SESSION['habilitation']=="Comptable"))
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
 $pdf->Ln(5);
 $pdf->SetFont('arial','B',16);
 $pdf->Write(80,'                             ETAT DES SORTIES DE STOCK');
 $pdf->Ln(5);
 $pdf->Write(80,'                                             _____________');
 $pdf->Ln(45);                                
 $pdf->SetFont('arial','B',10);
 $pdf->Write(2,'                     Periode :   Du :  ');
 $pdf->Write(2,dateFormatFrancais($Debut));
 $pdf->Write(2,'   Au :   ');
 $pdf->Write(2,dateFormatFrancais($Fin));
 $pdf->Write(2,'                        Date :   ');
 $pdf->Write(2,date("d/m/y").' '.date('H:i'));
 $pdf->Ln(10);
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',15,17,180,30);
 $pdf->Cell(20,7,'CODE',1,0,'L',1);
 $pdf->Cell(35,7,'CONDITIONNEMENT',1,0,'L',1);
 $pdf->Cell(65,7,' LIBELLE',1,0,'L',1);
 $pdf->Cell(30,7,'QTE SORTIE',1,0,'L',1);
 $pdf->Cell(30,7,'QTE EN STOCK',1,1,'L',1);
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
$nbre=0;
$colis=0;
//Ici on recupre la liste sans doublons des articles vendus dans la periode 
 $sql='SELECT DISTINCT  AR.ID_ARTICLE FROM ARTICLEVENDU AR, SORTIE_STOCK ST  WHERE AR.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V" ORDER BY AR.ID_ARTICLE';
$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
//Ici on recupere les quantites de la meme article puis on somme
$qterecu=0;
$sql1 = 'SELECT  AR.ID_ARTICLE, AR.QTESORTIE, AR.ID_SORTIESTOCK, ST.ID_SORTIESTOCK, ST.DATESORTIESTOCK FROM ARTICLEVENDU AR, SORTIE_STOCK ST WHERE AR.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND AR.ID_ARTICLE="'.$rslt['ID_ARTICLE'].'" AND ST.STATUT="V"';
$reponse1= $DataBase->query($sql1);
		while($rslt1= $reponse1->fetch())
		{
			$qterecu=$qterecu+$rslt1['QTESORTIE'];
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
		}

  $pdf->Cell(20,7,$rslt['ID_ARTICLE'],1);
  $pdf->Cell(35,7,$marque.' '.$nbrebte,1,0,'L');
  $pdf->Cell(65,7,$libelle,1);
  $pdf->Cell(30,7,$qterecu,1,0,'C');
  $pdf->Cell(30,7,$qtestock,1,1,'C');
  
  $nbre++;
  $colis=$colis+$qterecu;
  }
   //Ecriture des Totaux
   $pdf->SetFont('Arial','B',10);
  $pdf->Cell(90,7,'Nombre d\'Articles : '.$nbre,1,0,'C');
  $pdf->Cell(90,7,'Nombre de Colis : '.$colis,1,1,'C');
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
