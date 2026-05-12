<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"  || $_SESSION['habilitation']=="Comptable"))
{
	include('Connexion.php');
	include('fonctions.php');
	$Debut=dateFormatAnglais($_GET['DateD']);
	$Fin=dateFormatAnglais($_GET['DateF']);	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultation du Brouillard</title>
</head>

<body>
<table id='brouillard' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="5"><h3>Brouillard de Caisse</h3></td>
          </tr>
          <tr align="center" bgcolor="#CCCCCC">
          		<td colspan="5"><h5>Période : Du : <?php echo dateFormatFrancais($Debut); ?>   Au : <?php echo dateFormatFrancais($Fin); ?></h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center" ><h5>Date  </h5> </td>
                <td align="center" ><h5>Libelle </h5> </td>
                <td align="center" ><h5>Recette </h5> </td>
                <td align="center" ><h5>Depense </h5> </td>
                <td  align="center"><h5>Solde </h5></td>
			</tr>
            
<?php
$sldcourant=0;
$sldcourant=$_GET['Sld'];

//ici on reporte le solde initial
	?>
       	<tr>
    		<td  align="center"> <?php echo dateFormatFrancais($Debut); ?> </td>
            <td  align="left"> Report Solde Initial / Veille </td>
            <td  align="center"> <?php echo number_format($sldcourant, 0, ',', ' ').' F'; ?> </td>
            <td  align="center">  </td>
            <td  align="center"> <?php echo number_format($sldcourant, 0, ',', ' ').' F'; ?> </td>
         </tr>
<?php
//ici on recupere tous les apports
$mta=0;
$sql='SELECT * FROM APPORT WHERE DATE_APPORT BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND STATUT="V" ORDER BY DATE_APPORT' ;
$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
				$sldcourant=$sldcourant+$rslt['MONTANT'];
				$mta=$mta+$rslt['MONTANT'];
				?>
                	<tr bgcolor="#CCCCCC">
                		<td  align="center"> <?php echo dateFormatFrancais($rslt['DATE_APPORT']); ?> </td>
                        <td  align="left"> <?php echo $rslt['LIBELLE']; ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['MONTANT'], 0, ',', ' ').' F'; ?> </td>
                        <td  align="center">  </td>
                        <td  align="center"> <?php echo number_format($sldcourant, 0, ',', ' ').' F'; ?> </td>
                     </tr>
                <?php
		 }
//ici on recupere le total des ventes boutiques
$mtvte=0;
$fraisenlevement=0;
$sql2='SELECT * FROM SORTIE_STOCK WHERE DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND STATUT="V"' ;
$reponse2= $DataBase->query($sql2);
while($rslt2= $reponse2->fetch())
{
	//Ici on recupere les articles de chacune des ventes puis on somme les prix 
	 $sql3='SELECT * FROM  ARTICLEVENDU WHERE ID_SORTIESTOCK= "'.$rslt2['ID_SORTIESTOCK'].'" ' ;
	 $reponse3= $DataBase->query($sql3);
  	while($rslt3= $reponse3->fetch())
 	{
	 	$mtvte= ($mtvte + $rslt3['PRIXVENTE']);		
 	}
	$fraisenlevement = ($fraisenlevement + $rslt2['FRAISENLEVEMENT']);
}	
//on reporte le montant des ventes boutiques
$sldcourant=$sldcourant+$fraisenlevement;
	?>
       	<tr>
    		<td  align="center">Période</td>
            <td  align="left"> Vente Boutique/Magasin <?php echo 'Du : '.dateFormatFrancais($Debut).' Au : '.dateFormatFrancais($Fin); ?></td>
            <td  align="center"> <?php echo number_format($mtvte, 0, ',', ' ').' F'; ?> </td>
            <td  align="center">  </td>
            <td  align="center"> <?php echo number_format($sldcourant, 0, ',', ' ').' F'; ?> </td>
         </tr>
<?php
//on reporte le montant frais enlevement
$sldcourant=$sldcourant+$mtvte;
	?>
       	<tr>
    		<td  align="center">Période</td>
            <td  align="left"> Frais Enlevement <?php echo 'Du : '.dateFormatFrancais($Debut).' Au : '.dateFormatFrancais($Fin); ?></td>
            <td  align="center"> <?php echo number_format($fraisenlevement, 0, ',', ' ').' F'; ?> </td>
            <td  align="center">  </td>
            <td  align="center"> <?php echo number_format($sldcourant, 0, ',', ' ').' F'; ?> </td>
         </tr>
<?php
//ici on recupere le total des ventes frigo
$mtvtf=0;
$sql4='SELECT * FROM SORTIE_STOCK_FRIGO WHERE DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND STATUT="V"' ;
$reponse4= $DataBase->query($sql4);
while($rslt4= $reponse4->fetch())
{
	//Ici on recupere les articles de chacune des ventes frigo puis on somme les prix 
	 $sql5='SELECT * FROM  ARTICLEVENDU_FRIGO WHERE ID_SORTIESTOCK= "'.$rslt4['ID_SORTIESTOCK'].'" ' ;
	 $reponse5= $DataBase->query($sql5);
  	while($rslt5= $reponse5->fetch())
 	{
	 	$mtvtf= ($mtvtf + $rslt5['PRIXVENTE']);	 
 	}
}	
//on reporte le montant des ventes frigo
$sldcourant=$sldcourant+$mtvtf;
	?>
       	<tr bgcolor="#CCCCCC">
    		<td  align="center">Période</td>
            <td  align="left"> Vente Frigo <?php echo 'Du : '.dateFormatFrancais($Debut).' Au : '.dateFormatFrancais($Fin); ?></td>
            <td  align="center"> <?php echo number_format($mtvtf, 0, ',', ' ').' F'; ?> </td>
            <td  align="center">  </td>
            <td  align="center"> <?php echo number_format($sldcourant, 0, ',', ' ').' F'; ?> </td>
         </tr>

<tr>
<?php
//ici on recupere les consignes clients
$mtcc=0;
$sql6='SELECT C.ID_SORTIESTOCK, C.DATE_CONSIGNE, C.MONTANT, C.ID_EMBALLAGE,C.QTE, E.ID_EMBALLAGE, E.LIBELLE FROM CONSIGNE C, EMBALLAGE E WHERE C.ID_EMBALLAGE=E.ID_EMBALLAGE AND C.DATE_CONSIGNE BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND C.STATUT!="NV" ORDER BY C.DATE_CONSIGNE' ;
$reponse6= $DataBase->query($sql6);
		while($rslt6= $reponse6->fetch())
		{
				$sldcourant=$sldcourant+$rslt6['MONTANT'];
				$mtcc=$mtcc+$rslt6['MONTANT'];
				?>
                	<tr>
                		<td  align="center"> <?php echo dateFormatFrancais($rslt6['DATE_CONSIGNE']); ?> </td>
                        <td  align="left"> <?php echo 'Consigne : '.$rslt6['ID_SORTIESTOCK'].'/'.$rslt6['LIBELLE'].' ('.$rslt6['QTE'].')'; ?> </td>
                        <td  align="center"> <?php echo number_format($rslt6['MONTANT'], 0, ',', ' ').' F'; ?> </td>
                        <td  align="center">  </td>
                        <td  align="center"> <?php echo number_format($sldcourant, 0, ',', ' ').' F'; ?> </td>
                     </tr>
                <?php
		 }
//ici on recupere les deconsignes fssr
$mtre=0;
$sql7='SELECT R.ID_APPRO, R.ID_EMBALLAGE,R.QTE,R.DATE_RTREMB, R.MONTANT, E.LIBELLE FROM RTREMBFSSR R, EMBALLAGE E WHERE R.ID_EMBALLAGE=E.ID_EMBALLAGE AND R.DATE_RTREMB BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND R.STATUT="OK" ORDER BY R.DATE_RTREMB';
$reponse7= $DataBase->query($sql7);
while($rslt7= $reponse7->fetch())
		{
				$sldcourant=$sldcourant+$rslt7['MONTANT'];
				$mtre=$mtre+$rslt7['MONTANT'];
				?>
                	<tr bgcolor="#CCCCCC">
                		<td  align="center"> <?php echo dateFormatFrancais($rslt7['DATE_RTREMB']); ?> </td>
                        <td  align="left"> <?php echo 'Déconsignation(s) Fournisseur(s) : '.$rslt7['ID_APPRO'].'/'.$rslt7['LIBELLE'].' ('.$rslt7['QTE'].')'; ?> </td>
                        <td  align="center"> <?php echo number_format($rslt7['MONTANT'], 0, ',', ' ').' F'; ?> </td>
                        <td  align="center">  </td>
                        <td  align="center"> <?php echo number_format($sldcourant, 0, ',', ' ').' F'; ?> </td>
                     </tr>
                <?php
		 }
//ici on recupere les deconsignes clt 
$mtdc=0;
$sql9='SELECT C.ID_SORTIESTOCK, C.ID_EMBALLAGE,C.QTE,C.DATE_DECONSIGNE, C.MONTANT, E.ID_EMBALLAGE, E.LIBELLE FROM CONSIGNE C, EMBALLAGE E WHERE C.ID_EMBALLAGE=E.ID_EMBALLAGE AND C.DATE_DECONSIGNE BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND C.STATUT="Deconsigne" ORDER BY C.DATE_DECONSIGNE';
$reponse9= $DataBase->query($sql9);
while($rslt9= $reponse9->fetch())
		{
				$sldcourant=$sldcourant-$rslt9['MONTANT'];
				$mtdc=$mtdc+$rslt9['MONTANT'];
				?>
                	<tr>
                		<td  align="center"> <?php echo dateFormatFrancais($rslt9['DATE_DECONSIGNE']); ?> </td>
                        <td  align="left"> <?php echo 'Déconsignation Client : '.$rslt9['ID_SORTIESTOCK'].'/'.$rslt9['LIBELLE'].' ('.$rslt9['QTE'].')'; ?> </td>
                        <td  align="center">  </td>
                        <td  align="center"> <?php echo number_format($rslt9['MONTANT'], 0, ',', ' ').' F'; ?> </td>
                        <td  align="center"> <?php echo number_format($sldcourant, 0, ',', ' ').' F'; ?> </td>
                     </tr>
                <?php
		 }
//ici on recupere les deconsignes vte
$mtrevte=0;
$sql11='SELECT R.ID_SORTIESTOCK, R.ID_EMBALLAGE,R.QTE,R.DATE_RTREMB, R.MONTANT, E.LIBELLE FROM RTREMBVTE R, EMBALLAGE E WHERE R.ID_EMBALLAGE=E.ID_EMBALLAGE AND R.DATE_RTREMB BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND R.STATUT="V" ORDER BY R.DATE_RTREMB';
$reponse11= $DataBase->query($sql11);
while($rslt11= $reponse11->fetch())
		{
				$sldcourant=$sldcourant-$rslt11['MONTANT'];
				$mtrevte=$mtrevte+$rslt11['MONTANT'];
				?>
                	<tr bgcolor="#CCCCCC">
                		<td  align="center"> <?php echo dateFormatFrancais($rslt11['DATE_RTREMB']); ?> </td>
                        <td  align="left"> <?php echo 'Deconsignation Vente : '.$rslt11['ID_SORTIESTOCK'].'/'.$rslt11['LIBELLE'].' ('.$rslt11['QTE'].')'; ?> </td>
                        <td  align="center">  </td>
                        <td  align="center"> <?php echo number_format($rslt11['MONTANT'], 0, ',', ' ').' F'; ?> </td>
                        
                        <td  align="center"> <?php echo number_format($sldcourant, 0, ',', ' ').' F'; ?> </td>
                     </tr>
                <?php
		 }

//ici on recupere les consignes fssr
$mtcf=0;
$sql8='SELECT C.ID_APPRO, C.ID_EMBALLAGE, C.DATE_CONSIGNE, C.QTE, C.MONTANT, E.ID_EMBALLAGE, E.LIBELLE FROM CONSIGNEAPP C, EMBALLAGE E WHERE C.ID_EMBALLAGE=E.ID_EMBALLAGE AND C.DATE_CONSIGNE BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND C.STATUT!="NV" ORDER BY C.DATE_CONSIGNE';
$reponse8= $DataBase->query($sql8);
while($rslt8= $reponse8->fetch())
		{
				$sldcourant=$sldcourant-$rslt8['MONTANT'];
				$mtcf=$mtcf+$rslt8['MONTANT'];
				?>
                	<tr bgcolor="#CCCCCC">
                		<td  align="center"> <?php echo dateFormatFrancais($rslt8['DATE_CONSIGNE']); ?> </td>
                        <td  align="left"> <?php echo 'Consigne : '.$rslt8['ID_APPRO'].'/'.$rslt8['LIBELLE'].' ('.$rslt8['QTE'].')'; ?> </td>
                        <td  align="center">  </td>
                        <td  align="center"> <?php echo number_format($rslt8['MONTANT'], 0, ',', ' ').' F'; ?> </td>
                        <td  align="center"> <?php echo number_format($sldcourant, 0, ',', ' ').' F'; ?> </td>
                     </tr>
                <?php
		 }
//ici on recupere les charges
$mtchg=0;
$sql10='SELECT C.ID_CHARGE, C.ID_TYPECHARGE, C.DESCRIPTION, C.MONTANT, C.DATE_CHARGE, TC.LIBELLE, TC.ID_TYPECHARGE FROM CHARGE C, TYPE_CHARGE TC WHERE C.ID_TYPECHARGE=TC.ID_TYPECHARGE  AND C.STATUT="V" AND C.DATE_CHARGE BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY C.DATE_CHARGE' ;
$reponse10= $DataBase->query($sql10);
while($rslt10= $reponse10->fetch())
		{
				$sldcourant=$sldcourant-$rslt10['MONTANT'];
				$mtchg=$mtchg+$rslt10['MONTANT'];
				?>
                	<tr>
                		<td  align="center"> <?php echo dateFormatFrancais($rslt10['DATE_CHARGE']); ?> </td>
                        <td  align="left"> <?php echo $rslt10['LIBELLE'].'/'.$rslt10['DESCRIPTION']; ?> </td>
                        <td  align="center">  </td>
                        <td  align="center"> <?php echo number_format($rslt10['MONTANT'], 0, ',', ' ').' F'; ?> </td>
                        <td  align="center"> <?php echo number_format($sldcourant, 0, ',', ' ').' F'; ?> </td>
                     </tr>
                <?php
		 }
?>

<tr>
	<td colspan="2" align="right" bgcolor=><h5>Sous-Totaux  :      </h5></td>
    <td  align="center"><h5> <?php echo number_format($_GET['Sld']+$mta+$mtvte+$mtvtf+$mtcc+$mtre+$fraisenlevement, 0, ',', ' ').' F'; ?> </h5></td>
    <td  align="center"><h5> <?php echo number_format($mtdc+$mtcf+$mtchg+$mtrevte, 0, ',', ' ').' F'; ?> </h5></td>
    <td  align="center"><h5> <?php echo number_format($sldcourant, 0, ',', ' ').' F'; ?> </h5></td>
</tr>
<tr>
	<td align="center"><a href="Brouillard.php?debut=<?php echo $_GET["DateD"];?>&fin=<?php echo $_GET["DateF"];?>&Sld=<?php echo $_GET["Sld"];?>"/><input type="button" value="Imprimer" /></td></a>
	<td colspan="5" align="center" bgcolor="#FF6666"><h4>Solde Final  :  <?php echo number_format($sldcourant, 0, ',', ' ').' FCFA'; ?> </h4></td>
</tr>
</table>
</body>
</html>
<?php 
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