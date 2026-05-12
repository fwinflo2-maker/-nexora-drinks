<?php
session_start();
if (isset ($_SESSION['habilitation']))
{
 include('fpdf.php');
 include('Connexion.php');
 include('fonctions.php');
 //ici on recupere les infos sur le client de la vente 
 $id=htmlentities(htmlspecialchars(strtoupper($_GET["Id"])), ENT_QUOTES, 'UTF-8');
 $sql='SELECT U.LOGIN, U.NOM, A.LOGIN, A.ID_SORTIESTOCK, A.DATESORTIESTOCK,A.OBSERVATION FROM SORTIE_STOCK_CESSION A, USER U  WHERE U.LOGIN=A.LOGIN AND A.ID_SORTIESTOCK="'.$_GET['Id'].'" ';
 $reponse= $DataBase->query($sql);
 while($rslt= $reponse->fetch())
 {
  $nom=$rslt['NOM'];
  $Log=$rslt['LOGIN'];
  $ob=$rslt['OBSERVATION'];
  $Date=dateFormatFrancais($rslt['DATESORTIESTOCK']);
 }
 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(false);
 $pdf->AddPage();
 $pdf->SetTitle('Recu');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('Recu SORTIE CESSION');
 $pdf->SetFont('arial','B',14);
 $pdf->Write(70, utf8_decode('                               SORTIE CESSION N° : '));
 $pdf->Write(70,$id);
 $pdf->Ln(2);
 $pdf->Write(70,'                                _____________________________');
 $pdf->Ln(10);
 $pdf->SetFont('arial','',12);
 $pdf->Write(70,'    Utilisateur : '.$Log.'        Nom : '.$nom);
 $pdf->Ln(10);
 $pdf->Write(70,'    Date Cession : '.$Date.'          Observation :  '.$ob);
 $pdf->Ln(40);                         
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFont('arial','B',9);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',10,10,180,30);
 $pdf->Cell(30,7,'CODE',1,0,'L',1);
 $pdf->Cell(60,7,'CONDITIONNEMENT',1,0,'L',1);
 $pdf->Cell(60,7,'LIBELLE',1,0,'L',1);
 $pdf->Cell(20,7,'QTE',1,1,'C',1);
 $pdf->SetFont('Arial','',9);
 $pdf->SetTextColor(0,0,0);
  $nbre=0;
  $MT=0;
  $sql2='SELECT A.ID_ARTICLE, A.LIBELLE, A.MARQUE, A.NBREBTE, AR.ID_ARTICLE, AR.QTESORTIE, AR.ID_SORTIESTOCK FROM ARTICLE A, ARTICLESORTIE_CESSION AR WHERE A.ID_ARTICLE=AR.ID_ARTICLE AND AR.ID_SORTIESTOCK="'.$_GET['Id'].'"';
  $reponse2= $DataBase->query($sql2);
  while($rslt2= $reponse2->fetch())
 {
  $pdf->Cell(30,7,$rslt2['ID_ARTICLE'],1,0,'L');
  $pdf->Cell(60,7,$rslt2['MARQUE'].' '.$rslt2['NBREBTE'],1,0,'L');
  $pdf->Cell(60,7,$rslt2['LIBELLE'],1);
  $pdf->Cell(20,7,$rslt2['QTESORTIE'],1,1,'C');
  $nbre=$nbre+$rslt2['QTESORTIE'];
  }
$pdf->SetFont('arial','B',10);
$pdf->Write(10,'                                                               Nombre de colis : '.$nbre);
 $pdf->Ln(10);
  //affichage des emballages
$pdf->Cell(170,7,'CONSIGNE',1,1,'C',1);
$NBREE=0;
$pdf->Cell(30,7,'CODE',1,0,'L');
$pdf->Cell(120,7,'LIBELLE',1);
$pdf->Cell(20,7,'QTE',1,1,'C');
$pdf->SetFont('arial','',10);
$sql3='SELECT E.ID_EMBALLAGE, E.LIBELLE, ESC.ID_EMBALLAGE, ESC.ID_SORTIESTOCK, ESC.QTE FROM EMBALLAGE E, EMBALLAGESORTIECESSION ESC WHERE E.ID_EMBALLAGE=ESC.ID_EMBALLAGE AND ESC.ID_SORTIESTOCK="'.$_GET['Id'].'"';
$reponse3= $DataBase->query($sql3);
while($rslt3= $reponse3->fetch())
 {
  $NBREE=$NBREE+$rslt3['QTE'];
  $pdf->Cell(30,7,$rslt3['ID_EMBALLAGE'],1,0,'L');
  $pdf->Cell(120,7,$rslt3['LIBELLE'],1);
  $pdf->Cell(20,7,$rslt3['QTE'],1,1,'C');
  }
  $pdf->SetFont('arial','B',10);
  $pdf->Write(10,'                                                                 Total Consignes : '.number_format($NBREE, 0, ',', ' '));
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
