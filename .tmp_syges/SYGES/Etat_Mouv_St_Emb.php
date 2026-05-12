<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
{
 include('fpdf.php');
 include('Connexion.php');
 include('fonctions.php');
 $Debut=dateFormatAnglais($_GET["debut"]);
 $Fin=dateFormatAnglais($_GET["fin"]);
	//ici on recupere les info sur l'emb
$sql2 = 'SELECT ID_EMBALLAGE, LIBELLE, QTE,QTESTOCK FROM EMBALLAGE WHERE ID_EMBALLAGE="'.$_GET['Emb'].'"';
$reponse2= $DataBase->query($sql2);
		while($rslt2= $reponse2->fetch())
		{
			$stock=$rslt2['QTESTOCK'];
    		$libelle=$rslt2['LIBELLE'];
			$qte=$rslt2['QTE'];
		}

 //Select ds bd
 $pdf=new FPDF('L','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('ETAT DES MOUV EN STOCKS ');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('MOUV STOCK');
 $pdf->Ln(5);
 $pdf->SetFont('arial','B',16);
 $pdf->Write(80,'                                                          ETAT DES MOUVEMENTS DE STOCK');
 $pdf->Ln(3);
 $pdf->Write(80,'                                                          ________________________________');
 $pdf->Ln(45);                                
 $pdf->SetFont('arial','B',10);
 $pdf->Write(2,'EMBALLAGE :  '.$libelle);
 $pdf->Ln(10);
 $pdf->Write(2,'Periode :   Du :  ');
 $pdf->Write(2,dateFormatFrancais($Debut));
 $pdf->Write(2,'   Au :   ');
 $pdf->Write(2,dateFormatFrancais($Fin));
 $pdf->Write(2,'       Date :   ');
 $pdf->Write(2,date("d/m/y").' '.date('H:i'));
 $pdf->Ln(10);
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',10,17,270,30);
 $pdf->Cell(17,7,'DATE',1,0,'L',1);
 $pdf->Cell(15,7,'HEURE',1,0,'L',1);
 $pdf->Cell(75,7,'OPERATION',1,0,'C',1);
 $pdf->Cell(23,7,'QTE MOUV',1,0,'C',1);
 $pdf->Cell(25,7,'ST INI TOTAL',1,0,'C',1);
 $pdf->Cell(25,7,'ST FIN TOTAL ',1,0,'C',1);
 $pdf->Cell(20,7,'QTE MOUV',1,0,'C',1);
 $pdf->Cell(25,7,'ST INI DISPO',1,0,'C',1);
 $pdf->Cell(25,7,'ST FIN DISPO',1,0,'C',1);
 $pdf->Cell(35,7,'UTILISATEUR',1,1,'C',1);
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
  
//Ici on recupere les quantites de la meme article puis on somme
$nbre=0;
$sql1 = 'select * from mouvementemb where id_emballage="'.$_GET['Emb'].'" and date between "'.$Debut.'" AND "'.$Fin.'" order by id_mouv';
$reponse1= $DataBase->query($sql1);
		while($rslt1= $reponse1->fetch())
		{
		  	$pdf->Cell(17,7,dateFormatFrancais($rslt1['DATE']),1);
		  	$pdf->Cell(15,7,$rslt1['HEURE'],1,0,'L');
		  	$pdf->Cell(75,7,$rslt1['OPERATION'].' ('.$rslt1['ID_OPERATION'].')',1,0,'L');
		    $pdf->Cell(23,7,$rslt1['SFQTE']-$rslt1['SIQTE'],1,0,'C');
			$pdf->Cell(25,7,$rslt1['SIQTE'],1,0,'C');
			$pdf->Cell(25,7,$rslt1['SFQTE'],1,0,'C');
		    $pdf->Cell(20,7,$rslt1['SFSTOCK']-$rslt1['SISTOCK'],1,0,'C');
			$pdf->Cell(25,7,$rslt1['SISTOCK'],1,0,'C');
			$pdf->Cell(25,7,$rslt1['SFSTOCK'],1,0,'C');
			$pdf->Cell(35,7,$rslt1['USER'],1,1,'C');
			$nbre++;
  		 }
  $pdf->SetFont('Arial','b',8);
  $pdf->Cell(285,7,'Nombre de Mouvement(s) : '.$nbre,1,0,'C');
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
