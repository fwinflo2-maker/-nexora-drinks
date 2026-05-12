<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur"))
{
 include('fpdf.php');
 include('Connexion.php');
 include('fonctions.php');
 $Debut=dateFormatAnglais($_GET["debut"]);
 $Fin=dateFormatAnglais($_GET["fin"]);

 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(false);
 $pdf->AddPage();
 $pdf->SetTitle('Chiffre Affaire ');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('Chiffre affaire');
 //Determination du benefice de la periode
 $PR=0;
 $PV=0;
 $BENEF=0;
 $sql = 'SELECT ST.ID_SORTIESTOCK, ST.DATESORTIESTOCK, ST.STATUT, AV.ID_SORTIESTOCK, AV.PRIXREVIENT, AV.PRIXVENTE FROM SORTIE_STOCK ST, ARTICLEVENDU AV WHERE ST.ID_SORTIESTOCK=AV.ID_SORTIESTOCK AND ST.STATUT="V" AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" ';
$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			$PR=$PR+$rslt['PRIXREVIENT'];
			$PV=$PV+$rslt['PRIXVENTE'];
			$BENEF=$BENEF+($rslt['PRIXVENTE']-$rslt['PRIXREVIENT']);
		}
 //Determination des charges
 
$CHRG=0;
$sql2 = 'SELECT MONTANT FROM CHARGE WHERE STATUT="V" AND DATE_CHARGE BETWEEN "'.$Debut.'" AND "'.$Fin.'" ';
$reponse2= $DataBase->query($sql2);
		while($rslt2= $reponse2->fetch())
		{
			$CHRG=$CHRG+$rslt2['MONTANT'];
		}
		//Determination CA
 $CA=$BENEF-$CHRG;
 
 $pdf->Ln(40);
 $pdf->SetFont('arial','B',16);
 $pdf->Write(80,'                             CALCUL CHIFFRE D\'AFFAIRE');
 $pdf->Ln(5);
 $pdf->Write(80,'                                 _____________________');
 $pdf->Ln(70);                                
 $pdf->SetFont('arial','B',14);
 $pdf->Write(2,'             Periode :   Du :  ');
 $pdf->Write(2,dateFormatFrancais($Debut));
 $pdf->Write(2,'   Au :   ');
 $pdf->Write(2,dateFormatFrancais($Fin));
 $pdf->Write(2,'   Date :   ');
 $pdf->Write(2,date("d/m/y"));
 $pdf->Ln(30);
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',0,17,180,30);
 $pdf->Cell(80,9,'BENEFICES  ',1,0,'L',1);
 $pdf->Cell(90,9,$BENEF,1,1,'L',1);
 $pdf->Cell(80,9,'CHARGES',1,0,'L',1);
 $pdf->Cell(90,9,$CHRG,1,1,'L',1);
 $pdf->SetFont('arial','B',14);
 $pdf->Cell(80,9,'CHIFFRE D\'AFFAIRE ',1,0,'L',1);
 $pdf->Cell(90,9,$CA,1,1,'L',1);

 
  //nro de page
  $pdf->SetFontSize(8);
  $pdf->Cell(90,270,utf8_decode('       Généré par SYGES 1.0               Page ').$pdf->PageNo(),0,0,'R');
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
