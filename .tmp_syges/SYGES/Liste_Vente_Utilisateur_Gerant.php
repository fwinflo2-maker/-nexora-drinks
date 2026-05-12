<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
{
 include('fpdf.php');
 include('Connexion.php');
 include('fonctions.php');
 $Debut=dateFormatAnglais($_GET["debut"]);
 $Fin=dateFormatAnglais($_GET["fin"]);
	if ($_GET['Statut']=='Mixte')
	{	
	$sql='SELECT V.ID_SORTIESTOCK, V.DATESORTIESTOCK, V.ID_CLIENT, V.LOGIN, V.OBSERVATION, V.STATUT, C.ID_CLIENT, C.NOM FROM SORTIE_STOCK V, CLIENT C WHERE V.ID_CLIENT=C.ID_CLIENT AND V.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND V.LOGIN="'.$_GET['user'].'" ORDER BY V.ID_SORTIESTOCK' ;
	}
	else
		if ($_GET['Statut']=='N')
		{
		$sql='SELECT V.ID_SORTIESTOCK, V.DATESORTIESTOCK, V.ID_CLIENT,V.LOGIN, V.OBSERVATION, V.STATUT, C.ID_CLIENT, C.NOM FROM SORTIE_STOCK V, CLIENT C WHERE V.STATUT="N" AND V.ID_CLIENT=C.ID_CLIENT AND V.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND V.LOGIN="'.$_GET['user'].'"  ORDER BY V.ID_SORTIESTOCK' ;
		}
		else
		{
		$sql='SELECT V.ID_SORTIESTOCK, V.DATESORTIESTOCK, V.ID_CLIENT, V.LOGIN, V.OBSERVATION, V.STATUT, C.ID_CLIENT, C.NOM FROM SORTIE_STOCK V, CLIENT C WHERE V.STATUT="V" AND V.ID_CLIENT=C.ID_CLIENT AND V.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND V.LOGIN="'.$_GET['user'].'" ORDER BY V.ID_SORTIESTOCK' ;
		}
 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('ETAT DES VENTES UTILISATEURS');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('Vente utilisateur');
 $pdf->Ln(5);
 $pdf->SetFont('arial','B',16);
 $pdf->Write(80,'                                                            ETAT DES VENTES');
 $pdf->Ln(1);
 $pdf->Write(80,'                                                               _____________');
 $pdf->Ln(50);                                
 $pdf->SetFont('arial','B',10);
 $pdf->Write(2,'Periode de vente:   Du :  ');
 $pdf->Write(2,dateFormatFrancais($Debut));
 $pdf->Write(2,'   Au :   ');
 $pdf->Write(2,dateFormatFrancais($Fin));
 $pdf->Write(2,'    Statut :  ');
 $pdf->Write(2,$_GET['Statut']);
 $pdf->Write(2,'      Utilisateur :  ');
 $pdf->Write(2,$_GET['user']);
 $pdf->Write(2,'  '.date("d/m/y").'  '.date('H:i'));
 $pdf->Ln(10);
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',5,17,180,30);
 $pdf->Cell(20,7,'DATE',1,0,'L',1);
 $pdf->Cell(20,7,'CODE',1,0,'L',1);
 $pdf->Cell(60,7,'CLIENT',1,0,'L',1);
 $pdf->Cell(30,7,'TT PRIX VENTE',1,0,'L',1);
 $pdf->Cell(20,7,'STATUT',1,0,'C',1);
 $pdf->Cell(50,7,'OBSERVATION',1,1,'L',1);
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
  

 $TTPV=0;
 $nbrevente=0;
 $nbrecolis=0;
 $reponse= $DataBase->query($sql);
  while($rslt= $reponse->fetch())
 {
	 //Ici on recupere les articles de chacune des ventes puis on somme les prix et benef
	 $PRIXVENTE=0;
	 $sql1='SELECT ID_SORTIESTOCK, PRIXREVIENT, PRIXVENTE , QTESORTIE FROM  ARTICLEVENDU WHERE ID_SORTIESTOCK= "'.$rslt['ID_SORTIESTOCK'].'" ' ;
	 $reponse1= $DataBase->query($sql1);
  while($rslt1= $reponse1->fetch())
 {

	 $PRIXVENTE= ($PRIXVENTE + $rslt1['PRIXVENTE']);
      $nbrecolis=$nbrecolis+$rslt1['QTESORTIE'];	
	 
 } 
	 //
	 
  $pdf->Cell(20,7,dateFormatFrancais($rslt['DATESORTIESTOCK']),1,0,'L');
  $pdf->Cell(20,7,$rslt['ID_SORTIESTOCK'],1);
  $pdf->Cell(60,7,$rslt['NOM'],1);
  $pdf->Cell(30,7,$PRIXVENTE,1,0,'C');
  $pdf->Cell(20,7,utf8_decode($rslt['STATUT']),1,0,'C');
  $pdf->Cell(50,7,utf8_decode($rslt['OBSERVATION']),1,1,'L');
  $nbrevente++;
  $TTPV = ($TTPV + $PRIXVENTE);
  }
  //Ecriture des totaux
 $pdf->SetFont('arial','B',9);
 $pdf->Cell(20,7,'Totaux :',1,0,'L',1);
 $pdf->Cell(80,7,'Nbre vente : '.$nbrevente.' /Total colis : '.$nbrecolis,1,0,'L',1);
 $pdf->Cell(30,7,$TTPV,1,0,'C',1);
 $pdf->Cell(20,7,'',1,0,'L',1);
 $pdf->Cell(50,7,'',1,1,'L',1);
  
 
  $pdf->SetFontSize(8);
  $pdf->Cell(12,270,'Page '.$pdf->PageNo(),0,0,'R');
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
