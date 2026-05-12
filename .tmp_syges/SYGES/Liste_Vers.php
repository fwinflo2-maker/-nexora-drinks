<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" || $_SESSION['habilitation']=="Caissier"  || $_SESSION['habilitation']=="Comptable"))
{
 include('fpdf.php');
 include('Connexion.php');
 include('fonctions.php');
 $Debut=dateFormatAnglais($_GET["debut"]);
 $Fin=dateFormatAnglais($_GET["fin"]);
	if ($_GET['user']=='TOUS')
	{	
		$sql='SELECT V.NUM_VERS, V.DATE_VERS, V.MONTANT, V.VENDEUR, V.OBSERVATION, V.DATE, VD.NOM FROM VERSEMENT V, USER VD WHERE V.VENDEUR=VD.LOGIN AND V.DATE BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND V.STATUT="V" ORDER BY V.DATE' ;
	}
	else
	{	
		$sql='SELECT V.NUM_VERS, V.DATE_VERS, V.MONTANT, V.VENDEUR, V.OBSERVATION, V.DATE, VD.NOM FROM VERSEMENT V, USER VD WHERE V.VENDEUR=VD.LOGIN AND V.DATE BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND V.VENDEUR="'.$_GET['user'].'" AND V.STATUT="V" ORDER BY V.DATE' ;
	}
 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('ETAT DES VERSEMENTS');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('Versements');
 $pdf->Ln(0);
 $pdf->SetFont('arial','B',12);
 $pdf->Write(65,'                                                             ETAT DES VERSEMENTS');
 $pdf->Ln(1);
 $pdf->Write(65,'                                                             ______________________');
 $pdf->Ln(40);                                
 $pdf->SetFont('arial','B',10);
 $pdf->Write(2,'Periode :  ');
 $pdf->Write(2,dateFormatFrancais($Debut));
 $pdf->Write(2,'   Au :   ');
 $pdf->Write(2,dateFormatFrancais($Fin));
 $pdf->Write(2,'                Vendeur :  ');
 $pdf->Write(2,$_GET['user']);
 
 $pdf->Ln(5);
 $pdf->SetFontSize(8);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',10,5,180,30);
 $pdf->Cell(17,7,'DATE',1,0,'L',1);
 $pdf->Cell(10,7,utf8_decode('N°'),1,0,'C',1);
 $pdf->Cell(17,7,'VERS. DU',1,0,'L',1);
 $pdf->Cell(40,7,'VENDEUR',1,0,'L',1);
 $pdf->Cell(20,7,'MONTANT',1,0,'C',1);
 $pdf->Cell(50,7,'EMBALLAGES',1,0,'C',1);
 $pdf->Cell(40,7,'OBSERVATION',1,1,'C',1);
 $pdf->SetFont('Arial','',7);
 $pdf->SetTextColor(0,0,0);

$nbrevers=0;
$nbreemb=0;
$TTmontant=0;
$reponse= $DataBase->query($sql);
while($rslt= $reponse->fetch())
{
//Ici on recupere les emballages
$listeemb="";
 $sql1='SELECT E.ID_EMBALLAGE, E.LIBELLE, EV.QTE FROM  EMBALLAGE E, EMBALLAGE_VERS EV WHERE E.ID_EMBALLAGE=EV.ID_EMBALLAGE AND EV.NUM_VERS= "'.$rslt['NUM_VERS'].'" ' ;
 $reponse1= $DataBase->query($sql1);
 while($rslt1= $reponse1->fetch())
 {
	 if ($listeemb=="")
	 {
		 $listeemb=$rslt1['LIBELLE'].' :'.$rslt1['QTE'];
	 }
	 else
	 {
		 $listeemb=$listeemb.' ; '.$rslt1['LIBELLE'].' :'.$rslt1['QTE'];
	 }
	 $nbreemb=$nbreemb+$rslt1['QTE'];	 
 } 
	 //
	 
  $pdf->Cell(17,7,dateFormatFrancais($rslt['DATE']),1,0,'L');
  $pdf->Cell(10,7,$rslt['NUM_VERS'],1,0,'C');
  $pdf->Cell(17,7,dateFormatFrancais($rslt['DATE_VERS']),1,0,'C');
  $pdf->Cell(40,7,$rslt['NOM'],1);
  $pdf->Cell(20,7,number_format($rslt['MONTANT'], 0, ',', ' '),1,0,'C');
   $pdf->SetFont('Arial','',6);
  $pdf->Cell(50,7,$listeemb,1,0,'L');
  $pdf->Cell(40,7,substr($rslt['OBSERVATION'],0,33),1,1,'C');
  $nbrevers++;
  $TTmontant=$TTmontant+$rslt['MONTANT'];
   $pdf->SetFont('Arial','',7);
  }
  //Ecriture des totaux
 $pdf->SetFont('arial','B',9);
 $pdf->Cell(27,7,'Totaux  ',1,0,'C',1);
  $pdf->Cell(77,7,'Nombre de Versement(5) : '.number_format($nbrevers, 0, ',', ' '),1,0,'C',1);
 $pdf->Cell(90,7,'Montant Total : '.number_format($TTmontant, 0, ',', ' '),1,1,'C',1);
  
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
