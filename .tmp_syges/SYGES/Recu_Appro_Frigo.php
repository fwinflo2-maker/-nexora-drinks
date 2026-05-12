<?php
session_start();
if (isset ($_SESSION['habilitation']))
{
 include('fpdf.php');
 include('Connexion.php');
 include('fonctions.php');
 //ici on recupere les infos sur le User de la vente 
 $sql='SELECT U.LOGIN, U.NOM, A.LOGIN, A.ID_APPRO, A.DATE_APPRO FROM APPROFRIGO A, USER U  WHERE U.LOGIN = A.LOGIN AND A.ID_APPRO="'.$_GET['Id'].'" ';
 $reponse= $DataBase->query($sql);
 while($rslt= $reponse->fetch())
 {
  $nom=$rslt['NOM'];
  $Login=$rslt['LOGIN'];
  $Date=dateFormatFrancais($rslt['DATE_APPRO']);
 }
 //Select ds bd
 $pdf=new FPDF('P','mm','A4');
 $pdf->SetAutoPageBreak(false);
 $pdf->AddPage();
 $pdf->SetTitle('Recu');
 $pdf->SetAuthor('SYGES');
 $pdf->SetSubject('Recu APPRO');

 $pdf->SetFont('arial','B',16);
 $pdf->Write(70, utf8_decode('                      APPROVISIONNEMENT FRIGO N° : '));
 $pdf->Write(70,$_GET['Id']);
 $pdf->Ln(3);
 $pdf->Write(70,'                      __________________________');
 $pdf->Ln(10);
 $pdf->SetFont('arial','',12);
 $pdf->Write(75,'Code Utilisateur     :    '.$Login);
 $pdf->Ln(10);
 $pdf->Write(75,'Nom de Utilisateur :    '.$nom);
 $pdf->Write(75,'         Date Appro. :'.$Date);
 $pdf->Ln(50);                         
 $pdf->SetFontSize(9);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFont('arial','B',9);
 $pdf->SetFillColor(255,255,255);
 $pdf->Image('IMG\logo.jpg',15,10,180,30);
 $pdf->Cell(30,7,'CODE',1,0,'L',1);
  $pdf->Cell(60,7,'CONDITIONNEMENT',1,0,'L',1);
 $pdf->Cell(60,7,'LIBELLE',1,0,'L',1);
 $pdf->Cell(20,7,'QTE',1,1,'C',1);
 $pdf->SetFont('Arial','',9);
 $pdf->SetTextColor(0,0,0);
  
  $MT=0;
  $sql2='SELECT A.ID_ARTICLE, A.LIBELLE, A.MARQUE, A.NBREBTE, AR.ID_ARTICLE, AR.QTERECU, AR.ID_APPRO FROM ARTICLE A, ARTICLE_RECU_FRIGO AR WHERE A.ID_ARTICLE=AR.ID_ARTICLE AND AR.ID_APPRO="'.$_GET['Id'].'"';
  $reponse2= $DataBase->query($sql2);
  while($rslt2= $reponse2->fetch())
 {
  $pdf->Cell(30,7,$rslt2['ID_ARTICLE'],1,0,'L');
  $pdf->Cell(60,7,$rslt2['MARQUE'].' '.$rslt2['NBREBTE'],1,0,'L');
  $pdf->Cell(60,7,$rslt2['LIBELLE'],1);
  $pdf->Cell(20,7,$rslt2['QTERECU'],1,1,'C');
  }
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
