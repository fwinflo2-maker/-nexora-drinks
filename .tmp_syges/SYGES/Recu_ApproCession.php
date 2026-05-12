<?php
session_start();
if (isset ($_SESSION['habilitation']))
{
 include('fpdf.php');
 include('Connexion.php');
 include('fonctions.php');
 //ici on recupere les infos sur le client de la vente 
 $sql='SELECT U.LOGIN, U.NOM, A.LOGIN, A.ID_APPRO, A.DATE_APPRO, A.OBSERVATION FROM APPROCESSION A, USER U  WHERE U.LOGIN=A.LOGIN AND A.ID_APPRO="'.$_GET['Id'].'" ';
 $reponse= $DataBase->query($sql);
 while($rslt= $reponse->fetch())
 {
  $nom=$rslt['NOM'];
  $Log=$rslt['LOGIN'];
  $OB=$rslt['OBSERVATION'];
  $Date=dateFormatFrancais($rslt['DATE_APPRO']);
 }
 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(false);
 $pdf->AddPage();
 $pdf->SetTitle('Recu');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('Recu APPRO');
 $pdf->Ln(10);
 $pdf->SetFont('arial','B',16);
 $pdf->Write(65, utf8_decode('                      APPRO CESSION N° : '));
 $pdf->Write(65,strtoupper($_GET['Id']));
 $pdf->Ln(3);
 $pdf->Write(65,'                      ____________________________');
 $pdf->Ln(10);
 $pdf->SetFont('arial','',12);
 $pdf->Write(70,'    Utilisateur :   Login  : '.$Log.'       Nom :  '.$nom);
 $pdf->Ln(10);
 $pdf->Write(70,'    Date Appro. : '.$Date. '      Observation :   '.$OB);
 $pdf->Ln(40);                         
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFont('arial','B',9);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',0,10,180,30);
 $pdf->Cell(30,7,'CODE',1,0,'L',1);
 $pdf->Cell(60,7,'CONDITIONNEMENT',1,0,'L',1);
 $pdf->Cell(60,7,'LIBELLE',1,0,'L',1);
 $pdf->Cell(20,7,'QTE',1,1,'C',1);
 $pdf->SetFont('Arial','',9);
 $pdf->SetTextColor(0,0,0);
  $nbre=0;
  $MT=0;
  $NBREE=0;
  $sql2='SELECT A.ID_ARTICLE, A.LIBELLE, A.MARQUE, A.NBREBTE, AR.ID_ARTICLE, AR.QTERECU, AR.ID_APPRO FROM ARTICLE A, ARTICLE_RECU_CESSION AR WHERE A.ID_ARTICLE=AR.ID_ARTICLE AND AR.ID_APPRO="'.$_GET['Id'].'"';
  $reponse2= $DataBase->query($sql2);
  while($rslt2= $reponse2->fetch())
 {
  $pdf->Cell(30,7,$rslt2['ID_ARTICLE'],1,0,'L');
  $pdf->Cell(60,7,$rslt2['MARQUE'].' '.$rslt2['NBREBTE'],1,0,'L');
  $pdf->Cell(60,7,$rslt2['LIBELLE'],1);
  $pdf->Cell(20,7,$rslt2['QTERECU'],1,1,'C');
  $nbre=$nbre+$rslt2['QTERECU'];
  }
  $pdf->SetFont('arial','B',10);
  $pdf->Write(10,'                                                                               Nombre de colis : '.$nbre);
  $pdf->Ln(10);
//affichage des consignes emballages
$pdf->Cell(170,7,'CONSIGNE',1,1,'C',1);
$MTC=0;
$pdf->Cell(30,7,'CODE',1,0,'L');
$pdf->Cell(120,7,'LIBELLE',1);
$pdf->Cell(20,7,'QTE',1,1,'C');
$pdf->SetFont('arial','',10);
$sql3='SELECT E.ID_EMBALLAGE, E.LIBELLE, C.ID_EMBALLAGE, C.ID_APPRO, C.QTE FROM EMBALLAGE E, CONSIGNECESSION C WHERE E.ID_EMBALLAGE=C.ID_EMBALLAGE AND C.ID_APPRO="'.$_GET['Id'].'"';
$reponse3= $DataBase->query($sql3);
while($rslt3= $reponse3->fetch())
 {
  $NBREE=$NBREE+$rslt3['QTE'];
  $pdf->Cell(30,7,$rslt3['ID_EMBALLAGE'],1,0,'L');
  $pdf->Cell(120,7,$rslt3['LIBELLE'],1);
  $pdf->Cell(20,7,$rslt3['QTE'],1,1,'C');
  }
  $pdf->SetFont('arial','B',10);
  $pdf->Write(10,'                                                                             Total Consignes : '.number_format($NBREE, 0, ',', ' '));
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
