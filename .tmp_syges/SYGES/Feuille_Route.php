<?php
session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="OPSC" || $_SESSION['habilitation']=="Caissier" || $_SESSION['habilitation']=="DGA" || $_SESSION['habilitation']=="Superviseur"))
{
 include('fpdf.php');
 include('fonctions.php');
 include('Connexion.php');
  $Debut=dateFormatAnglais($_GET['DateD']);
  $Fin=dateFormatAnglais($_GET['DateF']);
  $Users=$_GET['user'];
 //Select ds bd
 $pdf=new FPDF('L','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('FEUILLE DE ROUTE ');
 $pdf->SetAuthor('SIGED');
 $pdf->SetSubject('Stock');
 $pdf->Ln(0);
 $pdf->SetFont('arial','B',10);
 $pdf->Write(75,'FEUILLE DE ROUTE DES PREVENTES CLIENTS');
 $pdf->Ln(1);
 $pdf->Write(75,'_________________________________________');
  $pdf->SetFont('arial','',9); 
 $pdf->Write(75,utf8_decode('                                                                                    Edité le :   '));
  $pdf->Write(75,date("d/m/Y").' '.date('H:i'));
 $pdf->Ln(7);
 $pdf->SetFont('arial','',9);
 $pdf->Write(75,'Periode du :  '.$_GET['DateD'].'   Au :  '.$_GET['DateF']);
 $pdf->Write(75,'         /     Utilisateur :  '.$_GET['user']);

 $pdf->Ln(45);                                
 $pdf->SetFontSize(8);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',30,5,210,30);
  $pdf->SetFont('Arial','B',5);
 $pdf->Cell(40,5,'CLIENT',1,0,'L',1);
	  // Liste sans doublons des articles de la periode
  $sql='select distinct a.libelle from article a, sortie_stock st, articlevendu av where a.id_article=av.id_article and av.id_sortiestock=st.id_sortiestock and st.login="'.$Users.'" and st.datesortiestock BETWEEN "'.$Debut.'" AND "'.$Fin.'"    and st.statut="V" ORDER BY a.libelle' ;
  $reponse= $DataBase->query($sql);
  while($rslt= $reponse->fetch())
 {
		$pdf->Cell(10,5,$rslt['libelle'],1,0,'L',1);
 }
 $pdf->Cell(0.1,5,'',1,1,'L',1);
//Ici on recupere la liste des factures concernés
		$sql2='select c.id_client, c.nom, st.id_sortiestock from client c, sortie_stock st where c.id_client=st.id_client and st.login="'.$Users.'" and st.datesortiestock BETWEEN "'.$Debut.'" AND "'.$Fin.'"   and st.statut="V"  ORDER BY c.nom' ;
		$reponse2= $DataBase->query($sql2);
		while($rslt2= $reponse2->fetch())
	   {
			//on ecris le nom du client
			$pdf->SetFont('Arial','',6);
			$pdf->Cell(40,5,$rslt2['nom'],1,0,'L',1);
			// Liste sans doublons des articles de la periode
			$sql4='select distinct a.libelle, a.id_article from article a, sortie_stock st, articlevendu av where a.id_article=av.id_article and av.id_sortiestock=st.id_sortiestock and st.login="'.$Users.'" and st.datesortiestock BETWEEN "'.$Debut.'" AND "'.$Fin.'"   and st.statut="V" ORDER BY a.libelle' ;
			$reponse4= $DataBase->query($sql4);
			while($rslt4= $reponse4->fetch())
		   {
				//Pour Chaque facture et produit du client on affiche les qtes pour chaque article 
				$sql5='select av.qtesortie from sortie_stock st, articlevendu av where av.id_sortiestock=st.id_sortiestock  and av.id_article="'.$rslt4['id_article'].'"  and st.id_sortiestock="'.$rslt2['id_sortiestock'].'"' ;
				$reponse5= $DataBase->query($sql5);
				$rslt5= $reponse5->fetch();
				if ($rslt5!="")
			   {
						   
				  //on ecris la QTE
				  $pdf->Cell(10,5,$rslt5['qtesortie'],1,0,'C',1);

				}
			   else
			   {
					//on ecris rien-->
					$pdf->Cell(10,5,'',1,0,'L',1);
			   }
			 }
	   $pdf->Cell(0.1,5,'',1,1,'L',1);
	}


// TOTAUX 
$tcasier=0;
$tcolis=0;
 $pdf->SetFont('Arial','B',6);
 $pdf->SetTextColor(0,0,0);
 $pdf->Cell(40,5,'TOTAUX',1,0,'L',1); 
		$sql8='select distinct a.libelle, a.id_article, a.marque  from article a, sortie_stock st, articlevendu av where a.id_article=av.id_article and av.id_sortiestock=st.id_sortiestock and st.login="'.$Users.'" and st.datesortiestock BETWEEN "'.$Debut.'" AND "'.$Fin.'"    and st.statut="V" ORDER BY a.libelle' ;
	  $reponse8= $DataBase->query($sql8);
	  while($rslt8= $reponse8->fetch())
	 {
		 $qtesortie=0;
		 $sql9='select a.id_article,av.qtesortie  from article a, sortie_stock st, articlevendu av where a.id_article=av.id_article and av.id_sortiestock=st.id_sortiestock and st.login="'.$Users.'" and st.datesortiestock BETWEEN "'.$Debut.'" AND "'.$Fin.'"    and st.statut="V" and a.id_article="'.$rslt8['id_article'].'" ORDER BY a.libelle' ;
		  $reponse9= $DataBase->query($sql9);
		  while($rslt9= $reponse9->fetch())
		 {
			 $qtesortie=$qtesortie+$rslt9['qtesortie'];
		 }

			  $pdf->Cell(10,5,$qtesortie,1,0,'C',1);
		 //total casier
		  if(($rslt8['marque']=="CASIER") || ($rslt8['marque']=="casier")|| ($rslt8['marque']=="CASIERS")|| ($rslt8['marque']=="casiers"))
		  {
			  $tcasier=$tcasier+$qtesortie;
		  }
		  //total colis
		  $tcolis=$tcolis+$qtesortie;
	 } 
  $pdf->Cell(0.1,5,'',1,1,'L',1);
  $pdf->SetFont('Arial','B',6);
  $pdf->Cell(50,5,'CASIERS : '.$tcasier.'   PET : '.($tcolis-$tcasier).'   COLIS : '.$tcolis,1,1,'L',1);
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
