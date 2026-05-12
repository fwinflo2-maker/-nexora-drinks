<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
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
<title>Consultation de l'etat des ventes par Ar</title>
</head>

<body>
<table id='liste' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          <td colspan=""><h3></h3></td>
          	<td colspan="6"><h3>Etat des ventes Magasin par Article</h3></td>
          </tr>
          <tr bgcolor="#CCCCCC">
          		<td align="center"><a href="Etat_Vente_Article.php?debut=<?php echo $_GET["DateD"];?>&fin=<?php echo $_GET["DateF"];?>&user=<?php echo $_GET['user'];?>"/><input type="button" style="background:#FF8000" value="Imprimer" /></a></td>
                <td colspan="3"><h4>Utilisateur : <?php echo $_GET['user']; ?></h4></td>
                <td colspan="3" align="center" ><h5>Période Du : <?php echo dateFormatFrancais($Debut); ?>  Au : <?php echo dateFormatFrancais($Fin); ?></h5>
                </td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center"><h5>Code </h5> </td>
                <td align="center"><h5>Conditionnement </h5> </td>
                <td align="center"><h5>Libellé</h5> </td>
                <td align="center"><h5>Qte</h5> </td>
                <td align="center"><h5>Prix Revient </h5> </td>
                <td align="center" ><h5>Prix Vente </h5> </td>
                <td align="center" ><h5>Marge Brute </h5> </td>
			</tr>
            
<?php
$couleur = "darkgray";
$i = 0;
$nbre=0;
$TTPR=0;
$TTPV=0;
$TTB=0;
$QTET=0;
$nbrecasier=0;
//Ici on recupre la liste sans doublons des articles vendus dans la periode 

 
if ($_GET['user']=='TOUS')
	{
 $sql='SELECT DISTINCT  AR.ID_ARTICLE FROM ARTICLEVENDU AR, SORTIE_STOCK ST  WHERE AR.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V" ORDER BY AR.ID_ARTICLE ';
	}
	else
	{
		 $sql='SELECT DISTINCT  AR.ID_ARTICLE FROM ARTICLEVENDU AR, SORTIE_STOCK ST  WHERE AR.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V" AND ST.LOGIN="'.$_GET['user'].'" ORDER BY AR.ID_ARTICLE ';
	} 
$reponse= $DataBase->query($sql);
  while($rslt= $reponse->fetch())
  {
//Ici on recupere les quantites de la meme article puis on somme
$qte=0;
$PRIXREVIENT=0;
$PRIXVENTE=0;
$BENEF=0;

if ($_GET['user']=='TOUS')
	{
		 $sql1 = 'SELECT  AR.ID_ARTICLE, AR.QTESORTIE,AR.PRIXVENTE,AR.PRIXREVIENT, AR.ID_SORTIESTOCK, ST.ID_SORTIESTOCK, ST.DATESORTIESTOCK FROM ARTICLEVENDU AR, SORTIE_STOCK ST WHERE AR.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND AR.ID_ARTICLE="'.$rslt['ID_ARTICLE'].'" AND ST.STATUT="V"';
	}
	else
	{
		$sql1 = 'SELECT  AR.ID_ARTICLE, AR.QTESORTIE,AR.PRIXVENTE,AR.PRIXREVIENT, AR.ID_SORTIESTOCK, ST.ID_SORTIESTOCK, ST.DATESORTIESTOCK FROM ARTICLEVENDU AR, SORTIE_STOCK ST WHERE AR.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND AR.ID_ARTICLE="'.$rslt['ID_ARTICLE'].'" AND ST.STATUT="V" AND ST.LOGIN="'.$_GET['user'].'"';
	}
$reponse1= $DataBase->query($sql1);
		while($rslt1= $reponse1->fetch())
		{
			$qte=$qte+$rslt1['QTESORTIE'];
			$PRIXREVIENT=$PRIXREVIENT+$rslt1['PRIXREVIENT'];
			$PRIXVENTE=$PRIXVENTE+$rslt1['PRIXVENTE'];
			$BENEF= ($BENEF + ($rslt1['PRIXVENTE']-$rslt1['PRIXREVIENT']));
		}
//ici on recupere le libelle, la qte et la marque de l'article
$sql2 = 'SELECT ID_ARTICLE, LIBELLE, MARQUE, NBREBTE, QTESTOCK FROM ARTICLE WHERE ID_ARTICLE="'.$rslt['ID_ARTICLE'].'"';
$reponse2= $DataBase->query($sql2);
		while($rslt2= $reponse2->fetch())
		{
			$marque=$rslt2['MARQUE'].' '.$rslt2['NBREBTE'];
    		$libelle=$rslt2['LIBELLE'];
			$conditionnement=$rslt2['MARQUE'];
		}
				  
			if ($i%2 == 0)
					$couleur = '#CCCCCC';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                		<td  align="center"> <?php echo $rslt['ID_ARTICLE']; ?> </td>
                        <td  align="center"> <?php echo $marque; ?> </td>
                        <td  align="center"> <?php echo $libelle; ?> </td>
                        <td  align="center"> <?php echo $qte; ?> </td>
                        <td  align="center"> <?php echo number_format($PRIXREVIENT, 0, ',', ' ').' F'; ?> </td>
                        <td  align="center"> <?php echo number_format($PRIXVENTE, 0, ',', ' ').' F'; ?> </td>
                        <td  align="center"> <?php echo number_format($BENEF, 0, ',', ' ').' F'; ?> </td>
                     </tr>
                <?php
				$i++;
				$nbre++;
				$TTPR = ($TTPR + $PRIXREVIENT);
				$TTPV = ($TTPV + $PRIXVENTE);
				$TTB = ($TTB + $BENEF);
				$QTET=$QTET+$qte;
				//on compte les casiers
				  if(($conditionnement=="CASIER") || ($conditionnement=="casier")|| ($conditionnement=="CASIERS")|| ($conditionnement=="casiers"))
				  {
					  $nbrecasier=$nbrecasier+$qte;
				  }
				
						//on compte les colis
				  //$ttcolis=$ttcolis+$rslt2['QTESORTIE'];
				  

		 }
?>
<tr>
	<td><h4> Totaux :   </h4></td>
    <td colspan="" align="center"><h4>Nombre de Casier(s) :  <?php echo $nbrecasier; ?> </h4></td>
	<td colspan="" align="center"><h4>Nombre d'article(s) :  <?php echo $nbre; ?> </h4></td>
    <td colspan="" align="center"><h4>Nombre de Colis : <?php echo $QTET; ?></h4></td>
    <td colspan="" align="center"><h4> <?php echo number_format($TTPR, 0, ',', ' ').' F'; ?> </h4></td>
    <td colspan="" align="center"><h4> <?php echo '(1) '.number_format($TTPV, 0, ',', ' ').' F'; ?> </h4></td>
    <td colspan="" align="center"><h4> <?php echo number_format($TTB, 0, ',', ' ').' F'; ?> </h4></td>
</tr>

<?php

//ici on recupere les frais enlevement.
$mtvte=0;
$fraisenlevement=0;


if ($_GET['user']=='TOUS')
	{
 		$sql3='SELECT * FROM SORTIE_STOCK WHERE DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND STATUT="V"' ;
	}
	else
	{
		$sql3='SELECT * FROM SORTIE_STOCK WHERE DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND STATUT="V" AND LOGIN="'.$_GET['user'].'"' ;
	} 

$reponse3= $DataBase->query($sql3);
while($rslt3= $reponse3->fetch())
{
	$fraisenlevement = ($fraisenlevement + $rslt3['FRAISENLEVEMENT']);
}	

//ici on recupere les consignes clients
$mtcc=0;

if ($_GET['user']=='TOUS')
	{
 		$sql6='SELECT C.MONTANT FROM CONSIGNE C, SORTIE_STOCK ST WHERE C.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V"' ;
	}
	else
	{
		$sql6='SELECT C.MONTANT FROM CONSIGNE C, SORTIE_STOCK ST WHERE C.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V" AND ST.LOGIN="'.$_GET['user'].'"' ;
	} 

$reponse6= $DataBase->query($sql6);
		while($rslt6= $reponse6->fetch())
		{
				$mtcc=$mtcc+$rslt6['MONTANT'];
		}
		
//ici on recupere les deconsignes vte
$mtrevte=0;

if ($_GET['user']=='TOUS')
	{
 		$sql11='SELECT  R.MONTANT FROM RTREMBVTE R,SORTIE_STOCK ST WHERE R.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V"';
	}
	else
	{
		$sql11='SELECT  R.MONTANT FROM RTREMBVTE R,SORTIE_STOCK ST WHERE R.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V" AND ST.LOGIN="'.$_GET['user'].'"';
	} 

$reponse11= $DataBase->query($sql11);
while($rslt11= $reponse11->fetch())
		{
				$mtrevte=$mtrevte+$rslt11['MONTANT'];
		}
?>
<tr>

	<td colspan="2" align="center"><h4>Frais Enlevement(2) :  <?php echo number_format($fraisenlevement, 0, ',', ' ').' F'; ?> </h4></td>
    <td colspan="2" align="center"><h4>Consignes Clients(1) :  <?php echo number_format($mtcc, 0, ',', ' ').' F'; ?> </h4></td>
    <td colspan="3" align="center"><h4>Deconsignations Clients(4) :  <?php echo number_format($mtrevte, 0, ',', ' ').' F'; ?> </h4></td>

</tr>
<tr>

	<td colspan="7" align="center"><h4>Montant Total(1)+(2)+(3)-(4) :  <?php echo number_format($TTPV+$fraisenlevement+$mtcc-$mtrevte, 0, ',', ' ').' F'; ?> </h4></td>

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