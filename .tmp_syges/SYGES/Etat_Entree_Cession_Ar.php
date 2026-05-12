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
 $pdf->SetTitle('ETAT DES ENTREES EN STOCKS ');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('ENTREE STOCK');
 $pdf->Ln(3);
 $pdf->SetFont('arial','B',12);
 $pdf->Write(80,'                        ETAT DES ENTREES EN STOCKS CESSION PAR ARTICLE');
 $pdf->Ln(3);
 $pdf->Write(80,'                        _________________________________________________');
 $pdf->Ln(45);                                
 $pdf->SetFont('arial','B',10);
 $pdf->Write(2,'                             Periode :   Du :  ');
 $pdf->Write(2,dateFormatFrancais($Debut));
 $pdf->Write(2,'   Au :   ');
 $pdf->Write(2,dateFormatFrancais($Fin));
 $pdf->Write(2,'       Date :   ');
 $pdf->Write(2,date("d/m/y").' '.date('H:i'));
 $pdf->Ln(10);
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',10,17,180,30);
 $pdf->Cell(20,7,'CODE',1,0,'L',1);
 $pdf->Cell(60,7,'CONDITIONNEMENT',1,0,'L',1);
 $pdf->Cell(60,7,'LIBELLE',1,0,'L',1);
 $pdf->Cell(30,7,'QUANTITE',1,1,'C',1);
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
 $nbre=0;
 $TQTE=0;
$sql = 'SELECT DISTINCT AR.ID_ARTICLE FROM ARTICLE_RECU_CESSION AR, APPROCESSION A WHERE AR.ID_APPRO=A.ID_APPRO AND A.DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND A.STATUT="V"';
$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
//Ici on recupere les quantites de la meme article puis on somme
$qterecu=0;
$sql1 = 'SELECT  AR.ID_ARTICLE, AR.QTERECU, AR.ID_APPRO, A.ID_APPRO, A.DATE_APPRO FROM ARTICLE_RECU_CESSION AR, APPROCESSION A WHERE AR.ID_APPRO=A.ID_APPRO AND A.DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND AR.ID_ARTICLE="'.$rslt['ID_ARTICLE'].'" AND A.STATUT="V"';
$reponse1= $DataBase->query($sql1);
		while($rslt1= $reponse1->fetch())
		{
			$qterecu=$qterecu+$rslt1['QTERECU'];
		}
//ici on recupere le libelle et la marque de l'article
$sql2 = 'SELECT ID_ARTICLE, LIBELLE,NBREBTE, MARQUE FROM ARTICLE WHERE ID_ARTICLE="'.$rslt['ID_ARTICLE'].'"';
$reponse2= $DataBase->query($sql2);
		while($rslt2= $reponse2->fetch())
		{
			$marque=$rslt2['MARQUE'];
    		$libelle=$rslt2['LIBELLE'];
			$nbrebte=$rslt2['NBREBTE'];
		}
  $pdf->Cell(20,7,$rslt['ID_ARTICLE'],1);
  $pdf->Cell(60,7,$marque.' '.$nbrebte,1,0,'L');
  $pdf->Cell(60,7,$libelle,1);
  $pdf->Cell(30,7,$qterecu,1,1,'C');
  $nbre++;
  $TQTE=$TQTE+$qterecu;
  }
  $pdf->SetFont('arial','B',10);
  $pdf->Cell(80,7,'Nombre d\'articles : '.$nbre,1,0,'C');
  $pdf->Cell(90,7,'Nombre de Colis : '.$TQTE,1,1,'C'); 
   
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
