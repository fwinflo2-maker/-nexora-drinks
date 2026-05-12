<?php
session_start();
if (isset ($_SESSION['habilitation']))
{
 include('fpdf2.php');
 include('fonctions.php');
 include('Connexion.php');
 //ici on recupere le versement

 $sql='SELECT  V.NUM_VERS, V.DATE_VERS, V.VENDEUR,VD.LOGIN, VD.NOM, V.OBSERVATION, V.MONTANT, V.DATE, V.HEURE, V.USER FROM VERSEMENT V, USER VD WHERE  V.VENDEUR=VD.LOGIN AND V.NUM_VERS="'.$_GET['Vers'].'"' ;
 $reponse= $DataBase->query($sql);
 while($rslt= $reponse->fetch())
 {
  $num_vers=$rslt['NUM_VERS'];
  $Date_vers=$rslt['DATE_VERS'];	 
  $vendeur=$rslt['NOM'];
  $observation=$rslt['OBSERVATION'];
  $montant=$rslt['MONTANT'];
  $date=$rslt['DATE'];
  $heure=$rslt['HEURE'];
  $user=$rslt['USER'];
 }
 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(true);
 $pdf->AddPage();
 $pdf->SetTitle('Recu');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('Recu Versement');
 $pdf->SetFont('arial','B',32);
 $pdf->Write(1, utf8_decode('                 SODIBONO'));
 $pdf->Ln(15);
 $pdf->SetFont('arial','B',19);
 $pdf->Write(1, utf8_decode('CONCESSIONNAIRE DES BRASSERIES DU CAMEROUN'));
 $pdf->Ln(15);
 $pdf->SetFont('arial','B',20);
  $pdf->Write(1, utf8_decode('CONT. M030100015674K  RCCM: RC/NGA/2010/B/165'));
 $pdf->Ln(15);
 $pdf->Write(1, utf8_decode('           Tel : +237 699 49 95 91  /  677 68 58 84   '));
 $pdf->Ln(15);
 $pdf->Write(1, utf8_decode('                     MAGASIN : TOUBORO'));
 $pdf->Ln(13);
 $pdf->Write(1, utf8_decode('                                   *************'));
 $pdf->SetFont('arial','B',24);
 $pdf->Ln(0);
 $pdf->Write(25, utf8_decode('N° Versement   :  '.$num_vers));
  $pdf->Ln(15); 
 $pdf->Write(25,'Vendeur  :  '.$vendeur);
 $pdf->Ln(15); 
 $pdf->Write(25,'Versement Du :  '.dateFormatFrancais($Date_vers));
  $pdf->Ln(15); 
 $pdf->Write(25,'Operateur :  '.$user);
 $pdf->SetFont('arial','I',20);
 $pdf->Write(25,' ('.dateFormatFrancais($date).' '.$heure.')');
 $pdf->SetFont('arial','B',22);
 $pdf->Ln(15); 
 $pdf->Write(25,'Montant (FCFA)  :  ');
 $pdf->Write(25,$montant);
 $pdf->Ln(15); 
 $pdf->Write(25,'Observation  :  ');
$pdf->SetFont('arial','I',22);
$pdf->Write(25,$observation);
 $pdf->Ln(30);        
 $pdf->SetFontSize(12);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFont('arial','B',24);
 $pdf->SetFillColor(255,255,255);
  $pdf->Cell(220,15,' EMBALLAGE(S): ',1,1,'C');
 $pdf->Cell(70,15,'Code',1,0,'L',1);
 $pdf->Cell(90,15,'Libelle',1,0,'L',1);
 $pdf->Cell(60,15,'QTE',1,1,'C',1);
 $pdf->SetFont('Arial','B',21);
 $pdf->SetTextColor(0,0,0);

  $ttemb=0;

  //AFFichage des emballages
  $sql2='SELECT  E.ID_EMBALLAGE, E.LIBELLE, EV.QTE FROM EMBALLAGE E, EMBALLAGE_VERS EV WHERE E.ID_EMBALLAGE= EV.ID_EMBALLAGE AND EV.NUM_VERS="'.$_GET['Vers'].'" ' ;
  $reponse2= $DataBase->query($sql2);
  while($rslt2= $reponse2->fetch())
 {
	//on compte les emb
  $ttemb=$ttemb+$rslt2['QTE'];
  $pdf->Cell(70,15,$rslt2['ID_EMBALLAGE'],1,0,'L',1);
  $pdf->Cell(90,15,$rslt2['LIBELLE'],1,0,'L',1);
  $pdf->Cell(60,15,$rslt2['QTE'],1,1,'C');
  }

 
 $pdf->Cell(220,15,'TOTAL EMBALLAGE(S): '.number_format($ttemb, 0, ',', ' '),1,0,'C');

  $pdf->Ln(20);
  $pdf->SetFont('arial','I',30);
  $pdf->Write(10,' Vendeur                       Responsable');
  $pdf->Ln(0);
  $pdf->Write(10,' _______                      ___________');

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
