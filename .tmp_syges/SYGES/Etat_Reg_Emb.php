<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
{
 include('fpdf.php');
 include('Connexion.php');
 include('fonctions.php');
 $Debut=dateFormatAnglais($_GET["debut"]);
 $Fin=dateFormatAnglais($_GET["fin"]);
 //Select ds bd
 $pdf=new FPDF('L','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('Etat des régularisations du stock des emballages ');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('REGULARISATION');
 $pdf->Ln(5);
 $pdf->SetFont('arial','B',12);
 $pdf->Write(80,'                                                            ETAT DES REGULARISATIONS DU STOCK DES EMBALLAGES');
 $pdf->Ln(3);
 $pdf->Write(80,'                                                            ______________________________________________________');
 $pdf->Ln(45);                                
 $pdf->SetFont('arial','B',10);
 $pdf->Write(2,'                                                                       Periode :  ');
 $pdf->Write(2,dateFormatFrancais($Debut));
 $pdf->Write(2,'   Au :   ');
 $pdf->Write(2,dateFormatFrancais($Fin));
 $pdf->Write(2,'              Date :  ');
 $pdf->Write(2,date("d/m/Y").' '.date('H:i'));
 $pdf->Ln(10);
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->SetFont('arial','B',9);
 $pdf->Image('IMG\logo.jpg',10,17,250,30);
 $pdf->Cell(20,7,'DATE',1,0,'L',1);
 $pdf->Cell(15,7,'HEURE',1,0,'L',1);
 $pdf->Cell(55,7,'EMBALLAGE',1,0,'L',1);
 $pdf->Cell(35,7,'ST TOTAL AVANT ',1,0,'L',1);
 $pdf->Cell(35,7,'ST TOTAL APRES',1,0,'L',1);
 $pdf->Cell(35,7,'ST DISPO. AVANT',1,0,'C',1);
 $pdf->Cell(30,7,'ST DISPO. APRES',1,0,'C',1);
  $pdf->Cell(45,7,'UTILISATEUR',1,1,'C',1);
 $pdf->SetFont('Arial','',8);
 $pdf->SetTextColor(0,0,0);
  
 $nbre=0;
 $sql='select * from regularisation_emb where date_regularisation between "'.$Debut.'" AND "'.$Fin.'" order by id_regularisation' ;
 $reponse= $DataBase->query($sql);
  while($rslt= $reponse->fetch())
 {
			//Ici on recupere le libelle de l'EMB

	 	$sql1='SELECT LIBELLE FROM EMBALLAGE WHERE ID_EMBALLAGE= "'.$rslt['ID_EMBALLAGE'].'" ' ;
	 	$reponse1= $DataBase->query($sql1);
  		while($rslt1= $reponse1->fetch())
		 {
 			$libelle=$rslt1['LIBELLE'];
 		 } 
			//Ici on recupere le NOM du user

	 	$sql2='SELECT NOM FROM USER WHERE LOGIN= "'.$rslt['LOGIN'].'" ' ;
	 	$reponse2= $DataBase->query($sql2);
  		while($rslt2= $reponse2->fetch())
		 {
 			$nom=$rslt2['NOM'];
 		 } 
	       //
  $pdf->Cell(20,7,dateFormatFrancais($rslt['DATE_REGULARISATION']),1,0,'L');
  $pdf->Cell(15,7,$rslt['HEURE_REGULARISATION'],1);
  $pdf->Cell(55,7,$rslt['ID_EMBALLAGE'].'/'.$libelle,1);
  $pdf->Cell(35,7, $rslt['STOCKTTAV'],1,0,'C');
  $pdf->Cell(35,7, $rslt['STOCKTTAP'],1,0,'C');
  $pdf->Cell(35,7,$rslt['QTESTOCKAV'],1,0,'C');
  $pdf->Cell(30,7,$rslt['QTESTOCKAP'],1,0,'C');
  $pdf->Cell(45,7,$nom.'('.$rslt['LOGIN'].')',1,1,'C');
  $nbre++;
  }
  //Ecriture des totaux
 $pdf->SetFont('arial','B',10);
 $pdf->Cell(270,7,'Nombre de Regularisations : '.$nbre,1,0,'C',1);
  
 
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
