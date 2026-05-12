<?php
session_start();
if (isset ($_SESSION['habilitation']))
{
 include('fpdf.php');
 include('Connexion.php');
 include('fonctions.php');
 //ici on recupere les infos sur le client de la vente 
 $id=htmlentities(htmlspecialchars($_GET['Id']), ENT_QUOTES, 'UTF-8');
 $sql='SELECT F.ID_FOURNISSEUR, F.NOM, A.ID_FOURNISSEUR, A.ID_APPRO, A.DATE_APPRO, A.OBSERVATION, A.NBRECOLIS, A.LIQUIDEHT FROM APPROVISIONNEMENT A, FOURNISSEUR F  WHERE F.ID_FOURNISSEUR=A.ID_FOURNISSEUR AND A.ID_APPRO="'.$id.'" ';
 $reponse= $DataBase->query($sql);
 while($rslt= $reponse->fetch())
 {
  $nom=$rslt['NOM'];
  $Fssr=$rslt['ID_FOURNISSEUR'];
  $Date=dateFormatFrancais($rslt['DATE_APPRO']);
  $OB=$rslt['OBSERVATION'];
  $NbreColis=$rslt['NBRECOLIS'];
  $Caht=$rslt['LIQUIDEHT'];
 }
 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(false);
 $pdf->AddPage();
 $pdf->SetTitle('Recu');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('Recu APPRO');
 $pdf->Ln(5);
 $pdf->SetFont('arial','B',16);
 $pdf->Write(65, utf8_decode('                      APPROVISIONNEMENT N° : '.strtoupper($id)));
 $pdf->Ln(2);
 $pdf->Write(65,'                      ______________________');
 $pdf->Ln(3);
 $pdf->SetFont('arial','',10);
 $pdf->Write(75,'    Fournisseur :   Code : '.$Fssr. '    Nom :  '.$nom);
 $pdf->Ln(7);
 $pdf->Write(75,'    Date Appro. : '.$Date. '     Observation :  '.$OB);
  $pdf->Ln(7);
 $pdf->Write(75,'    CA HT : '.number_format($Caht, 0, ',', ' ').' FCFA     Nombre de Colis :  '.$NbreColis);
 $pdf->Ln(45);                         
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFont('arial','B',9);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',0,10,180,30);
 $pdf->Cell(30,7,'CODE',1,0,'L',1);
 $pdf->Cell(50,7,'CONDITIONNEMENT',1,0,'L',1);
 $pdf->Cell(70,7,'LIBELLE',1,0,'L',1);
 $pdf->Cell(20,7,'QTE',1,1,'C',1);
 $pdf->SetFont('Arial','',9);
 $pdf->SetTextColor(0,0,0);
  $nbrear=0;
  $MT=0;
  $sql2='SELECT A.ID_ARTICLE, A.LIBELLE, A.MARQUE, A.NBREBTE, AR.ID_ARTICLE, AR.QTERECU, AR.ID_APPRO FROM ARTICLE A, ARTICLE_RECU AR WHERE A.ID_ARTICLE=AR.ID_ARTICLE AND AR.ID_APPRO="'.$_GET['Id'].'"';
  $reponse2= $DataBase->query($sql2);
  while($rslt2= $reponse2->fetch())
 {
  $pdf->Cell(30,7,$rslt2['ID_ARTICLE'],1,0,'L');
  $pdf->Cell(50,7,$rslt2['MARQUE'].' '.$rslt2['NBREBTE'],1,0,'L');
  $pdf->Cell(70,7,$rslt2['LIBELLE'],1);
  $pdf->Cell(20,7,$rslt2['QTERECU'],1,1,'C');
  $nbrear=$nbrear+$rslt2['QTERECU'];
  }
  $pdf->SetFont('arial','B',10);
  $pdf->Write(10,'                                                               Nombre de colis : '.$nbrear);
  $pdf->Ln(10);
//affichage des consignes emballages
$pdf->Cell(170,7,'CONSIGNE',1,1,'C',1);
$MTC=0;
$pdf->Cell(30,7,'CODE',1,0,'L');
$pdf->Cell(60,7,'LIBELLE',1);
$pdf->Cell(15,7,'QTE',1,0,'C');
$pdf->Cell(25,7,'P.U.',1,0,'C');
$pdf->Cell(40,7,'MONTANT TOTAL',1,1,'C');
$pdf->SetFont('arial','',10);
$sql3='SELECT E.ID_EMBALLAGE, E.LIBELLE, E.MT_CONSIGNE, C.ID_EMBALLAGE, C.ID_APPRO, C.QTE, C.MONTANT FROM EMBALLAGE E, CONSIGNEAPP C WHERE E.ID_EMBALLAGE=C.ID_EMBALLAGE AND C.ID_APPRO="'.$_GET['Id'].'"';
$reponse3= $DataBase->query($sql3);
while($rslt3= $reponse3->fetch())
 {
  $MTC=$MTC+$rslt3['MONTANT'];
  $pdf->Cell(30,7,$rslt3['ID_EMBALLAGE'],1,0,'L');
  $pdf->Cell(60,7,$rslt3['LIBELLE'],1);
  $pdf->Cell(15,7,$rslt3['QTE'],1,0,'C');
  $pdf->Cell(25,7,$rslt3['MT_CONSIGNE'].' F',1,0,'C');
  $pdf->Cell(40,7,number_format($rslt3['MONTANT'], 0, ',', ' ').' F',1,1,'C');
  }
  $pdf->SetFont('arial','B',10);
  $pdf->Write(10,'                                                        Montant Total Consignes : '.number_format($MTC, 0, ',', ' ').' FCFA');
  $pdf->Ln(10);
  //affichage des RTR emballages
$pdf->Cell(170,7,'RETOUR EMBALLAGE',1,1,'C',1);
$MTR=0;
$pdf->Cell(30,7,'CODE',1,0,'L');
$pdf->Cell(60,7,'LIBELLE',1);
$pdf->Cell(15,7,'QTE',1,0,'C');
$pdf->Cell(25,7,'P.U.',1,0,'C');
$pdf->Cell(40,7,'MONTANT TOTAL',1,1,'C');
$pdf->SetFont('arial','',10);
$sql4='SELECT E.ID_EMBALLAGE, E.LIBELLE, E.MT_CONSIGNE, R.ID_EMBALLAGE, R.ID_APPRO, R.QTE, R.MONTANT FROM EMBALLAGE E, RTREMBFSSR R WHERE E.ID_EMBALLAGE=R.ID_EMBALLAGE AND R.ID_APPRO="'.$_GET['Id'].'"';
$reponse4= $DataBase->query($sql4);
while($rslt4= $reponse4->fetch())
 {
  $MTR=$MTR+$rslt4['MONTANT'];
  $pdf->Cell(30,7,$rslt4['ID_EMBALLAGE'],1,0,'L');
  $pdf->Cell(60,7,$rslt4['LIBELLE'],1);
  $pdf->Cell(15,7,$rslt4['QTE'],1,0,'C');
  $pdf->Cell(25,7,$rslt4['MT_CONSIGNE'].' F',1,0,'C');
  $pdf->Cell(40,7,number_format($rslt4['MONTANT'], 0, ',', ' ').' F',1,1,'C');
  }
  $pdf->SetFont('arial','B',10);
  $pdf->Write(10,'                                                       Montant Total Retour  : '.number_format($MTR, 0, ',', ' ').' FCFA');
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
