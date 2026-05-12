<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"|| $_SESSION['habilitation']=="Magasinier" ))
{
 include('fpdf.php');
 include('Connexion.php');
 include('fonctions.php');
 $Debut=dateFormatAnglais($_GET["debut"]);
 $Fin=dateFormatAnglais($_GET["fin"]);
 //ici on recupere le libelle et la marque de l'article
	if ($_GET['Art']=="Tous")
	{
		$libelle="Tous";
		$marque="Tous les Articles";
		$nbrebte="";
	}else {
			$sql2 = 'SELECT ID_ARTICLE, LIBELLE, NBREBTE,QTESTOCK, MARQUE FROM ARTICLE WHERE ID_ARTICLE="'.$_GET['Art'].'"';
		$reponse2= $DataBase->query($sql2);
				while($rslt2= $reponse2->fetch())
				{
					$marque=$rslt2['MARQUE'];
					$libelle=$rslt2['LIBELLE'];
					$nbrebte=$rslt2['NBREBTE'];
				}
	}
 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('ETAT DES ENTREES EN STOCKS ');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('ENTREE STOCK');
 $pdf->Ln(3);
 $pdf->SetFont('arial','B',16);
 $pdf->Write(60,'                             ETAT DES MOUVEMENTS DE STOCK');
 $pdf->Ln(1);
 $pdf->Write(60,'                             ________________________________');
 $pdf->Ln(38);                                
 $pdf->SetFont('arial','B',10);
 $pdf->Write(2,'                                     Article :  '.$marque.'('.$nbrebte.') '.$libelle);
 $pdf->Ln(5);
 $pdf->Write(2,'                                     Periode :   Du :  ');
 $pdf->Write(2,dateFormatFrancais($Debut));
 $pdf->Write(2,'   Au :   ');
 $pdf->Write(2,dateFormatFrancais($Fin));
 $pdf->Write(2,'       Date :   ');
 $pdf->Write(2,date("d/m/y").' '.date('H:i'));
 $pdf->Ln(10);
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',15,5,180,30);
 $pdf->Cell(17,7,'DATE',1,0,'L',1);
 $pdf->Cell(15,7,'HEURE',1,0,'L',1);
  $pdf->Cell(15,7,'ARTICLE',1,0,'L',1);
 $pdf->Cell(45,7,'OPERATION',1,0,'C',1);
 $pdf->Cell(35,7,'DETENTEUR',1,0,'C',1);
 $pdf->Cell(12,7,'QTE',1,0,'C',1);
 $pdf->Cell(15,7,'ST INI',1,0,'C',1);
 $pdf->Cell(15,7,'ST FIN',1,0,'C',1);
 $pdf->Cell(30,7,'UTILISATEUR',1,1,'C',1);
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
  
$nbre=0;
//Ici on recupere les quantites de la meme article puis on somme
if ($libelle=="Tous")
{
	$sql1 = 'select * from mouvementar m, article a where m.id_article=a.id_article and  m.date between "'.$Debut.'" AND "'.$Fin.'" order by m.id_mouv';
}else {
	$sql1 = 'select * from mouvementar m, article a where m.id_article = a.id_article and m.id_article="'.$_GET['Art'].'" and m.id_article=a.id_article and  m.date between "'.$Debut.'" AND "'.$Fin.'" order by m.id_mouv';
}
$reponse1= $DataBase->query($sql1);
		while($rslt1= $reponse1->fetch())
		{
		  	$pdf->Cell(17,7,dateFormatFrancais($rslt1['DATE']),1);
		  	$pdf->Cell(15,7,$rslt1['HEURE'],1,0,'L');
			$pdf->Cell(15,7,$rslt1['LIBELLE'],1,0,'L');
		  	$pdf->Cell(45,7,$rslt1['OPERATION'].' ('.$rslt1['ID_OPERATION'].')',1,0,'L');
			$pdf->Cell(35,7,substr($rslt1['DETENTEUR'],0,40),1,0,'L');
		    $pdf->Cell(12,7,$rslt1['QTE'],1,0,'C');
			$pdf->Cell(15,7,$rslt1['SI'],1,0,'C');
			$pdf->Cell(15,7,$rslt1['SF'],1,0,'C');
			$pdf->Cell(30,7,$rslt1['USER'],1,1,'C');
			$nbre++;
  		 }
  $pdf->SetFont('Arial','b',8);
  $pdf->Cell(199,7,'Nombre de Mouvement(s) : '.$nbre,1,0,'C');
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
