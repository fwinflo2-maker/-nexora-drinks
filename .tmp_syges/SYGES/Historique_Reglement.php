<?php
 session_start();
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"))
{
 include('fpdf.php');
 include('Connexion.php');
 include('fonctions.php');
	//On recuere le nom du client
	$sql2 = " select c.id_client, c.nom, st.datesortiestock  from client c, sortie_stock st where c.id_client=st.id_client and st.id_sortiestock='".$_GET['Vte']."'";
	$reponse2= $DataBase->query($sql2);
	$rslt2= $reponse2->fetch();
	
	//on recupere le mt restant et de la vente dans le dernier paiement
	$reste=0;
	$sql5='SELECT MAX(ID_REGLEMENT) AS ID FROM REGLEMENT WHERE ID_SORTIESTOCK="'.$_GET['Vte'].'" ' ;
	$reponse5= $DataBase->query($sql5);
	while($rslt5= $reponse5->fetch())
		{
			$sql6='SELECT MTRESTANT, MONTANT FROM REGLEMENT WHERE ID_REGLEMENT="'.$rslt5['ID'].'" ' ;
			$reponse6= $DataBase->query($sql6);
			while($rslt6= $reponse6->fetch())
			{
				$reste=$rslt6['MTRESTANT'];
				$montant=$rslt6['MONTANT'];
			}
		}
 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(true,15);
 $pdf->AddPage();
 $pdf->SetTitle('HISTORIQUE REGLEMENT');
 $pdf->SetAuthor('SIGED');
 $pdf->SetSubject('HISTORIQUE');
 $pdf->Ln(15);
 $pdf->SetFont('times','B',12);
 $pdf->Write(75,'                                        HISTORIQUE DES REGLEMENTS');
 $pdf->Ln(3);
 $pdf->Write(75,'                                        _________________________________           ');
 $pdf->SetFont('times','B',10);
 $pdf->Write(75,date("d/m/Y").' '.date('H:i'));
 $pdf->Ln(50);                                
 $pdf->Write(2,'VENTE :  '.$_GET['Vte']); 
 $pdf->Write(2,'         DATE : '.dateFormatFrancais($rslt2['datesortiestock'])); 
 $pdf->Ln(7);
 $pdf->Write(2,'CLIENT :  '.$rslt2['nom'].' ('.$rslt2['id_client'].')');
 $pdf->Ln(7);
 $pdf->Write(2,'MONTANT TOTAL :  '.number_format($montant, 0, ',', ' ').' F'.'        MONTANT RESTANT :  '.number_format($reste, 0, ',', ' ').' F');
 $pdf->SetFontSize(10);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',10,17,180,30);
 $pdf->Ln(10);
 $pdf->Cell(10,7,utf8_decode('N°'),1,0,'L',1); 
 $pdf->Cell(20,7,'DATE',1,0,'C',1);
 $pdf->Cell(30,7,'MT FACTURE',1,0,'L',1);
 $pdf->Cell(30,7,'AVANCE',1,0,'C',1);
 $pdf->Cell(30,7,'MT RESTANT',1,0,'C',1);
 $pdf->Cell(20,7,'STATUT',1,0,'C',1);
 $pdf->Cell(40,7,'UTILISATEUR',1,1,'C',1);
 $pdf->SetFont('times','',10);
 $pdf->SetTextColor(0,0,0);
  
$i = 1;
$MT=0;	
$nbre=0;		
$sql7='select * from reglement where id_sortiestock="'.$_GET['Vte'].'" order by id_reglement asc' ;
$reponse7= $DataBase->query($sql7);
while($rslt7= $reponse7->fetch())
		{
		  $pdf->Cell(10,7,$i,1,0,'C',1);
		  $pdf->Cell(20,7,dateFormatFrancais($rslt7['DATEAVANCE']),1,0,'C',1);
		  $pdf->Cell(30,7,number_format($rslt7['MONTANT'], 0, ',', ' ').' F',1,0,'C',1);
		  $pdf->Cell(30,7,number_format($rslt7['MTAVANCE'], 0, ',', ' ').' F',1,0,'C',1);
		  $pdf->Cell(30,7,number_format($rslt7['MTRESTANT'], 0, ',', ' ').' F',1,0,'C',1);
		  $pdf->Cell(20,7,$rslt7['STATUT'],1,0,'C');
		  $pdf->Cell(40,7,$rslt7['USER'],1,1,'C');
		  $i++;
		  $nbre++;
		  $MT=$MT+$rslt7['MTAVANCE'];
        }
  //Ecriture des totaux
 $pdf->SetFont('times','B',9);
 $pdf->Cell(80,7,'Nombre Reglement(s) : '.$nbre,1,0,'L',1);
 $pdf->Cell(100,7,'Total Reglement(s) : '.number_format($MT, 0, ',', ' ').' F',1,0,'C',1);
 $pdf->Ln(3);
 $pdf->Write(25,'                                                                                                                                                   LA DIRECTION ');
  $pdf->Ln(1);
  $pdf->Write(25,'                                                                                                                                                    _______________');
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
