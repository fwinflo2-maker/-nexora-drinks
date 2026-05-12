<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
{
 include('fpdf.php');
 include('Connexion.php');
 include('fonctions.php');
 $Debut=dateFormatAnglais($_GET["debut"]);
 $Fin=dateFormatAnglais($_GET["fin"]);
if ($_GET['Stat']=='Mixte')
	{	
		if ($_GET['user']=='TOUS')
		{
			$sql='SELECT ID_SORTIESTOCK, DATESORTIESTOCK, LOGIN, OBSERVATION, STATUT FROM SORTIE_STOCK_FRIGO  WHERE DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY ID_SORTIESTOCK' ;
		}
		else
		{
			$sql='SELECT ID_SORTIESTOCK, DATESORTIESTOCK, LOGIN, OBSERVATION, STATUT FROM SORTIE_STOCK_FRIGO  WHERE DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND LOGIN="'.$_GET['user'].'" ORDER BY ID_SORTIESTOCK' ;
		
		}	
	}
	else 
		if ($_GET['Stat']=='N')
		{
			if ($_GET['user']=='TOUS')
			{
				$sql='SELECT ID_SORTIESTOCK, DATESORTIESTOCK, LOGIN, OBSERVATION, STATUT FROM SORTIE_STOCK_FRIGO  WHERE DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND STATUT="N" ORDER BY ID_SORTIESTOCK' ;
			}
			else
			{
			$sql='SELECT ID_SORTIESTOCK, DATESORTIESTOCK, LOGIN, OBSERVATION, STATUT FROM SORTIE_STOCK_FRIGO  WHERE DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND LOGIN="'.$_GET['user'].'" AND STATUT="N" ORDER BY ID_SORTIESTOCK' ;
			}
		}
		else
		{
		if ($_GET['user']=='TOUS')
			{
				$sql='SELECT ID_SORTIESTOCK, DATESORTIESTOCK, LOGIN, OBSERVATION, STATUT FROM SORTIE_STOCK_FRIGO  WHERE DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND STATUT="V" ORDER BY ID_SORTIESTOCK' ;
			}
			else
			{
			$sql='SELECT ID_SORTIESTOCK, DATESORTIESTOCK, LOGIN, OBSERVATION, STATUT FROM SORTIE_STOCK_FRIGO  WHERE DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND LOGIN="'.$_GET['user'].'" AND STATUT="V" ORDER BY ID_SORTIESTOCK' ;
			}
		}
 //Select ds bd
 $pdf=new FPDF('L','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('ETAT DES VENTES FRIGO/UTILISATEUR');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('Vente utilisateur');
 $pdf->Ln(1);
 $pdf->SetFont('arial','B',16);
 $pdf->Write(65,'                                                             ETAT DES VENTES FRIGO');
 $pdf->Ln(1);
 $pdf->Write(65,'                                                             ______________________');
 $pdf->Ln(40);                                
 $pdf->SetFont('arial','B',10);
 $pdf->Write(2,'Periode de vente:   Du :  ');
 $pdf->Write(2,dateFormatFrancais($Debut));
 $pdf->Write(2,'   Au :   ');
 $pdf->Write(2,dateFormatFrancais($Fin));
 $pdf->Write(2,'              Statut :  ');
 $pdf->Write(2,$_GET['Stat']);
 $pdf->Write(2,'              Utilisateur :  ');
 $pdf->Write(2,$_GET['user']);
 $pdf->Write(2,'              Date :  ');
 $pdf->Write(2,date("d/m/y").' '.date('H:i'));
 $pdf->Ln(10);
 $pdf->SetFontSize(8);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',15,5,250,30);
 $pdf->Cell(30,7,'DATE',1,0,'L',1);
 $pdf->Cell(30,7,'CODE',1,0,'L',1);
 $pdf->Cell(70,7,'UTILISATEUR',1,0,'L',1);
 $pdf->Cell(30,7,'TT PRIX VENTE',1,0,'L',1);
 $pdf->Cell(20,7,'STATUT',1,0,'C',1);
 $pdf->Cell(70,7,'OBSERVATION',1,1,'L',1);
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
  
 $TTPV=0;
 $nbrevente=0;
 $reponse= $DataBase->query($sql);
  while($rslt= $reponse->fetch())
 {
	 //Ici on recupere les articles de chacune des ventes puis on somme les prix et benef
	 $PRIXVENTE=0;
	 $sql1='SELECT ID_SORTIESTOCK, PRIXVENTE, PRIXREVIENT FROM  ARTICLEVENDU_FRIGO WHERE ID_SORTIESTOCK= "'.$rslt['ID_SORTIESTOCK'].'" ' ;
	 $reponse1= $DataBase->query($sql1);
  while($rslt1= $reponse1->fetch())
 {
	 $PRIXVENTE= ($PRIXVENTE + $rslt1['PRIXVENTE']);		 
 } 
	 //
	 
  $pdf->Cell(30,7,dateFormatFrancais($rslt['DATESORTIESTOCK']),1,0,'L');
  $pdf->Cell(30,7,$rslt['ID_SORTIESTOCK'],1);
  $pdf->Cell(70,7,$rslt['LOGIN'],1);
  $pdf->Cell(30,7,$PRIXVENTE.' FCFA',1,0,'C');
  $pdf->Cell(20,7,utf8_decode($rslt['STATUT']),1,0,'C');
  $pdf->Cell(70,7,utf8_decode($rslt['OBSERVATION']),1,1,'L');
  $nbrevente++;
  $TTPV = ($TTPV + $PRIXVENTE);
  }
  //Ecriture des totaux
 $pdf->SetFont('arial','B',9);
 $pdf->Cell(30,7,'Totaux :',1,0,'L',1);
 $pdf->Cell(30,7,'Nbre vente : ',1,0,'L',1);
 $pdf->Cell(70,7,$nbrevente,1,0,'L',1);
 $pdf->Cell(30,7,$TTPV.' FCFA',1,0,'C',1);
 $pdf->Cell(20,7,'',1,0,'L',1);
 $pdf->Cell(70,7,'',1,1,'L',1);
  
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
