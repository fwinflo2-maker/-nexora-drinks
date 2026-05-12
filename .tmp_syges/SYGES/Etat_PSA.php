<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"  || $_SESSION['habilitation']=="Comptable"))
{
 include('fpdf.php');
 include('Connexion.php');
 include('fonctions.php');
	$Debut=dateFormatAnglais($_GET['DateD']);
	$Fin=dateFormatAnglais($_GET['DateF']);
	//On recuere le taux de la categorie
	$sql2='SELECT  ID_CATEGORIE, TAUXTVA, LIBELLE, TAUXRETFISCPRO FROM CATEGORIE  WHERE ID_CATEGORIE="'.$_GET['Cat'].'" ' ;
    $reponse2= $DataBase->query($sql2);
		while($rslt2= $reponse2->fetch())
		{
			$codeC=$rslt2['ID_CATEGORIE'];
			$libelle=$rslt2['LIBELLE'];
			$tauxtva=$rslt2['TAUXTVA'];
			$tauxretfiscpro=$rslt2['TAUXRETFISCPRO'];
		}
 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('ETAT DES PRELEVEMENTS SUR ACHATS CLIENTS');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('PSA');
 $pdf->Ln(7);
 $pdf->SetFont('arial','B',10);
 $pdf->Write(70,'                                                                    ETAT DES PRELEVEMENTS SUR ACHATS CLIENTS');
 $pdf->Ln(1);
 $pdf->Write(70,'                                                                    _____________________________________________');
 $pdf->Ln(40);                                
 $pdf->SetFont('arial','B',8);
 $pdf->Ln(1);
 $pdf->Write(2,'Categorie/Regime                :  ');
 $pdf->Write(2,$libelle);
 $pdf->Ln(5);
 $pdf->Write(2,'Periode     Du :  ');
 $pdf->Write(2,dateFormatFrancais($Debut));
 $pdf->Write(2,'   Au :   ');
 $pdf->Write(2,dateFormatFrancais($Fin));
 $pdf->Write(2,'                     Date :   ');
 $pdf->Write(2,date("d/m/y").' '.date('H:i'));
 $pdf->Ln(7);
 $pdf->SetFontSize(7);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',15,15,180,30);
 $pdf->Cell(10,5,utf8_decode('N°'),1,0,'C',1); 
 $pdf->Cell(20,5,'DATE',1,0,'C',1);
 $pdf->Cell(20,5,'FACTURE',1,0,'C',1);
 $pdf->Cell(50,5,'CLIENT',1,0,'C',1);
 $pdf->Cell(25,5,'MT PRODUIT TTC',1,0,'C',1);
 $pdf->Cell(25,5,'MT PRODUIT HT',1,0,'C',1);
 $pdf->Cell(25,5,'TVA '.$tauxtva.'%',1,0,'C',1);
 $pdf->Cell(25,5,'PSA '.$tauxretfiscpro.'%',1,1,'C',1);
 $pdf->SetFont('Arial','',7);
 $pdf->SetTextColor(0,0,0);
 $couleur = "darkgray";
$i = 1;
$nbre=0;
$TTC=0;
$TVA=0;
$PSA=0;
$MT=0;
$MHT=0;
$sql='SELECT ST.ID_SORTIESTOCK, ST.DATESORTIESTOCK, C.ID_CATEGORIE, C.NOM FROM SORTIE_STOCK ST, CLIENT C WHERE C.ID_CLIENT=ST.ID_CLIENT AND C.ID_CATEGORIE="'.$codeC.'" AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V" ORDER BY ST.ID_SORTIESTOCK' ;
$reponse= $DataBase->query($sql);
while($rslt= $reponse->fetch())
{
	//Ici on recupere les articles de chacune des ventes puis on somme les prix et benef
	 $mtttc=0;
	 $sql1='SELECT AV.ID_SORTIESTOCK, AV.PRIXREVIENT, AV.PRIXVENTE, AV.QTESORTIE, A.TAUXRISTOURNE FROM  ARTICLEVENDU AV, ARTICLE A WHERE A.ID_ARTICLE=AV.ID_ARTICLE AND AV.ID_SORTIESTOCK= "'.$rslt['ID_SORTIESTOCK'].'" ' ;
	 $reponse1= $DataBase->query($sql1);
	  while($rslt1= $reponse1->fetch())
	 {
		 $mtttc= ($mtttc + $rslt1['PRIXVENTE']);	
	 }
	 
			 //Ici on calcule le montant de la retenue fis pro et la tva
	  $mtht=0;
	  $mttva=0;
	  $mtpsa=0;
	  $mtht=$mtttc*100/(100+$tauxtva+$tauxretfiscpro);
	  $mttva=$mtht*$tauxtva/100;
	  $mtpsa=$mtht*$tauxretfiscpro/100;
		  
  $pdf->Cell(10,5,$i,1,0,'C');		  
  $pdf->Cell(20,5,dateFormatFrancais($rslt['DATESORTIESTOCK']),1,0,'C');
  $pdf->Cell(20,5,$rslt['ID_SORTIESTOCK'],1,0,'C');
  $pdf->Cell(50,5,utf8_decode($rslt['NOM']),1,0,'C'); 
  $pdf->Cell(25,5,number_format($mtttc, 0, ',', ' '),1,0,'C');
  $pdf->Cell(25,5,number_format($mtht, 0, ',', ' '),1,0,'C');
  $pdf->Cell(25,5,number_format($mttva, 0, ',', ' '),1,0,'C');
  $pdf->Cell(25,5,number_format($mtpsa, 0, ',', ' '),1,1,'C');

  $i++;
  $nbre++;
  $MHT= $MHT+$mtht;
  $MT= $MT+$mtttc;
  $TVA= $TVA+$mttva;
  $PSA= $PSA+$mtpsa;
  }
  //nro de page
  $pdf->SetFont('arial','B',8);
   $pdf->Cell(100,5,'TOTAUX  ',1,0,'C');
   $pdf->Cell(25,5,number_format($MT, 0, ',', ' '),1,0,'C');
   $pdf->Cell(25,5,number_format($MHT, 0, ',', ' '),1,0,'C');
   $pdf->Cell(25,5,number_format($TVA, 0, ',', ' '),1,0,'C');
   $pdf->Cell(25,5,number_format($PSA, 0, ',', ' '),1,1,'C');
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
