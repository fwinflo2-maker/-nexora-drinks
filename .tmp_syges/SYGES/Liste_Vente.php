<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" || $_SESSION['habilitation']=="OPS" || $_SESSION['habilitation']=="Comptable" ))
{
 include('fpdf.php');
 include('Connexion.php');
 include('fonctions.php');
 $Debut=dateFormatAnglais($_GET["debut"]);
 $Fin=dateFormatAnglais($_GET["fin"]);
	if ($_GET['Statut']=='Mixte')
	{	
	$sql='SELECT V.ID_SORTIESTOCK, V.DATESORTIESTOCK,V.MTFACTURE, V.FRAISENLEVEMENT, V.ID_CLIENT, V.OBSERVATION, V.STATUT, C.ID_CLIENT, C.NOM FROM SORTIE_STOCK V, CLIENT C WHERE V.ID_CLIENT=C.ID_CLIENT AND V.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY V.ID_SORTIESTOCK' ;
	}
	else
		if ($_GET['Statut']=='N')
		{
		$sql='SELECT V.ID_SORTIESTOCK, V.DATESORTIESTOCK,V.MTFACTURE, V.FRAISENLEVEMENT, V.ID_CLIENT, V.OBSERVATION, V.STATUT, C.ID_CLIENT, C.NOM FROM SORTIE_STOCK V, CLIENT C WHERE V.STATUT="N" AND V.ID_CLIENT=C.ID_CLIENT AND V.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY V.ID_SORTIESTOCK' ;
		}
		else
		{
		$sql='SELECT V.ID_SORTIESTOCK, V.DATESORTIESTOCK, V.MTFACTURE, V.FRAISENLEVEMENT, V.ID_CLIENT, V.OBSERVATION, V.STATUT, C.ID_CLIENT, C.NOM FROM SORTIE_STOCK V, CLIENT C WHERE V.STATUT="V" AND V.ID_CLIENT=C.ID_CLIENT AND V.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY V.ID_SORTIESTOCK' ;
		}
 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('ETAT DES VENTES ');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('Vente');
 $pdf->Ln(0);
 $pdf->SetFont('arial','B',12);
 $pdf->Write(65,'                                                             ETAT DES VENTES');
 $pdf->Ln(1);
 $pdf->Write(65,'                                                             _________________');
 $pdf->Ln(40);                                
 $pdf->SetFont('arial','B',10);
 $pdf->Write(2,'Periode :  ');
 $pdf->Write(2,dateFormatFrancais($Debut));
 $pdf->Write(2,'   Au :   ');
 $pdf->Write(2,dateFormatFrancais($Fin));
 $pdf->Write(2,'              Statut :  ');
 $pdf->Write(2,$_GET['Statut']);
 $pdf->Write(2,'              Date :  ');
 $pdf->Write(2,date("d/m/Y").' '.date('H:i'));
 $pdf->Ln(5);
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->SetFont('arial','B',9);
 $pdf->Image('IMG\logo.jpg',10,5,180,30);
 $pdf->Cell(17,7,'DATE',1,0,'L',1);
 $pdf->Cell(20,7,'CODE',1,0,'L',1);
 $pdf->Cell(50,7,'CLIENT',1,0,'L',1);
 $pdf->Cell(20,7,'PR ART.',1,0,'C',1);
 $pdf->Cell(20,7,'PV ART.',1,0,'C',1);
 $pdf->Cell(20,7,'MARG. BRU.',1,0,'C',1);
 $pdf->Cell(20,7,'FR. ENLEV',1,0,'C',1);
 $pdf->Cell(23,7,'MT FACTURE',1,0,'C',1);
 $pdf->Cell(10,7,'ST',1,1,'C',1);
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
  
 $TTPR=0;
 $TTPV=0;
 $TTB=0;
 $TTFE=0;
$TTMTF=0;
 $nbrevente=0;
 $reponse= $DataBase->query($sql);
  while($rslt= $reponse->fetch())
 {
	 //Ici on recupere les articles de chacune des ventes puis on somme les prix et benef
	 $PRIXACHAT=0;
	 $PRIXREVIENT=0;
	 $PRIXVENTE=0;
	 $BENEF=0;
	 $sql1='SELECT ID_SORTIESTOCK, PRIXREVIENT, PRIXVENTE FROM  ARTICLEVENDU WHERE ID_SORTIESTOCK= "'.$rslt['ID_SORTIESTOCK'].'" ' ;
	 $reponse1= $DataBase->query($sql1);
  while($rslt1= $reponse1->fetch())
 {
	 $PRIXREVIENT= ($PRIXREVIENT + $rslt1['PRIXREVIENT']);
	 $PRIXVENTE= ($PRIXVENTE + $rslt1['PRIXVENTE']);	
 	 $BENEF= ($BENEF + ($rslt1['PRIXVENTE']-$rslt1['PRIXREVIENT']));	 
 } 
	 //
	 
  $pdf->Cell(17,7,dateFormatFrancais($rslt['DATESORTIESTOCK']),1,0,'L');
  $pdf->Cell(20,7,$rslt['ID_SORTIESTOCK'],1);
  $pdf->Cell(50,7,$rslt['NOM'],1);
  $pdf->Cell(20,7,$PRIXREVIENT,1,0,'C');
  $pdf->Cell(20,7,$PRIXVENTE,1,0,'C');
  $pdf->Cell(20,7,$BENEF,1,0,'C');
  $pdf->Cell(20,7,$rslt['FRAISENLEVEMENT'],1,0,'C');
  $pdf->Cell(23,7,$rslt['MTFACTURE'],1,0,'C');
  $pdf->Cell(10,7,utf8_decode($rslt['STATUT']),1,1,'C');
  $nbrevente++;
  $TTPR = ($TTPR + $PRIXREVIENT);
  $TTPV = ($TTPV + $PRIXVENTE);
  $TTB = ($TTB + $BENEF);
  $TTFE=$TTFE+$rslt['FRAISENLEVEMENT'];
  $TTMTF=$TTMTF+$rslt['MTFACTURE'];
  }
  //Ecriture des totaux
 $pdf->SetFont('arial','B',10);
 $pdf->Cell(17,7,'Totaux :',1,0,'L',1);
 $pdf->Cell(70,7,'Nbre vente : '.$nbrevente,1,0,'L',1);
 $pdf->Cell(20,7,number_format($TTPR, 0, ',', ' '),1,0,'C',1);
 $pdf->Cell(20,7,number_format($TTPV, 0, ',', ' '),1,0,'C',1);
 $pdf->Cell(20,7,number_format($TTB, 0, ',', ' '),1,0,'C',1);
 $pdf->Cell(20,7,number_format($TTFE, 0, ',', ' '),1,0,'C',1);
 $pdf->Cell(23,7,number_format($TTMTF, 0, ',', ' '),1,0,'C',1);
 $pdf->Cell(10,7,'//',1,1,'C',1);
  
 
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
