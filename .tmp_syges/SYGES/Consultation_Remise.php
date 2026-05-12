<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur"))
{
	include('Connexion.php');
	include('fonctions.php');
	$Debut=dateFormatAnglais($_GET['DateD']);
	$Fin=dateFormatAnglais($_GET['DateF']);
	
//on recupere les parametres
$tva=0;
$tauxpsa=0;
$bonuscasse=0;
$depotgarantie=0;
$retfiscpro=0;
$tauxremisesht=0;

 $sql='SELECT  * FROM PARAMETRE ' ;
 $reponse= $DataBase->query($sql);
 while($rslt= $reponse->fetch())
		{
			
			$tva=$rslt['TVA'];
			$tauxpsaremise=$rslt['PSAREMISE'];
			$bonuscasse=$rslt['BONUSCASSE'];
			$depotgarantie=$rslt['DEPOTGARANTIE'];
			$tauxepargne=$rslt['TAUXEPARGNE'];
			$tauxpsa=$rslt['PSA'];
			$retfiscpro=$rslt['TAUXRETFISCPRO'];
			$tauxremisesht=$rslt['TAUXREMISESHT'];
		}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultation des remises</title>
</head>

<body>
<!--ici on calcule le CA HT les prelements (psa et epargne) ainsi que le CA TTC -->
<table id='ca' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="5"><h3>Avoir de Remises sur Achat</h3></td>
          </tr>
          <tr align="center" bgcolor="#CCCCCC">
                <td colspan="5"><h5>Période Du : <?php echo dateFormatFrancais($Debut); ?> Au : <?php echo dateFormatFrancais($Fin); ?></h5></td>
          </tr>
          <tr align="center">
          		<td align="center"><a href="Etat_Remise.php?debut=<?php echo $_GET['DateD'];?>&fin=<?php echo $_GET['DateF'] ;?>"/><input type="button" value="Imprimer" style="background:#F00" /></td></a>
                <td colspan="4" align="center"><h4>Chiffre d'Affaire</h4></td>
          </tr> 
          <tr bgcolor="#CCCCCC">
                <td align="center"><h5>CA HT</h5> </td>
                <td align="center"><h5>PSA (<?php echo $tauxpsa; ?>%)</h5> </td>
                <td align="center"><h5>EPARGNE (<?php echo $tauxepargne; ?>%)</h5> </td>
                <td align="center"><h5>TVA (<?php echo $tva; ?>%)</h5> </td>
				<td align="center"><h5>CA TTC</h5></td>
		  </tr>
            
<?php
$caht=0;
$caepargne=0;
$capsa=0;
$catva=0;

$sql1='SELECT LIQUIDEHT FROM APPROVISIONNEMENT  WHERE DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'"' ;
$reponse1= $DataBase->query($sql1);
while($rslt1= $reponse1->fetch())
		{
			$caht=$caht+$rslt1['LIQUIDEHT'];
	    }
			$capsa=$caht*$tauxpsa/100;
			$caepargne=$caht*$tauxepargne/100;
			$catva=$caht*$tva/100; 
			$cattc=$caht+$capsa+$caepargne+$catva;
			?>
             <tr>
                  <td  align="center"> <?php echo number_format($caht, 0, ',', ' '); ?> </td>
                  <td  align="center"> <?php echo number_format($capsa, 0, ',', ' '); ?> </td>
                  <td  align="center"> <?php echo number_format($caepargne, 0, ',', ' '); ?> </td>
                  <td  align="center"> <?php echo number_format($catva, 0, ',', ' '); ?> </td>
                  <td  align="center"> <?php echo number_format($cattc, 0, ',', ' '); ?> </td>
              </tr>
</table>


<!--ici on calcule les remises de base -->
<table id='RemiseBase' border="1" width="100%" align="center">
      <tr align="center">
             <td colspan="7" align="center"><h4>Remise de Base</h4></td>
       </tr>
       <tr bgcolor="#CCCCCC">
       		<td align="left"><h5>LIBELLE</h5> </td>
            <td align="center"><h5>QTE</h5> </td>
            <td align="center"><h5>TAUX HT</h5> </td>
            <td align="center"><h5>VALEUR HT</h5> </td>
            <td align="center"><h5>MT TVA</h5> </td>
            <td align="center"><h5>RETENU FISC</h5> </td>
			<td align="center"><h5>VALEUR TTC</h5></td>
	   </tr>
            
<?php
//parametres calcul remise pour un article
$valeurhtRB=0;
$tvaRB=0;
$retfiscproRB=0;
$valeurttcRB=0;
$qteRB=0;
//parametres calcul pour tous les articles
$TTvaleurhtRB=0;
$TTtvaRB=0;
$TTretfiscproRB=0;
$TTvaleurttcRB=0;
$TTqteRB=0;
$TTqterecu=0;

//Ici on recupre la liste sans doublons des articles livres dans la periode 
$sql2='SELECT DISTINCT  AR.ID_ARTICLE FROM ARTICLE_RECU AR, APPROVISIONNEMENT A  WHERE AR.ID_APPRO=A.ID_APPRO AND A.DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND A.STATUT="V" ORDER BY AR.ID_ARTICLE  ';
$reponse2= $DataBase->query($sql2);
while($rslt2= $reponse2->fetch())
{
	//Ici on recupere les quantites de la meme article puis on somme
	$qterecu=0;
	$sql3 = 'SELECT  AR.ID_ARTICLE, AR.QTERECU, AR.ID_APPRO, A.ID_APPRO, A.DATE_APPRO FROM ARTICLE_RECU AR, APPROVISIONNEMENT A WHERE AR.ID_APPRO=A.ID_APPRO AND A.DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND AR.ID_ARTICLE="'.$rslt2['ID_ARTICLE'].'" AND A.STATUT="V"';
	$reponse3= $DataBase->query($sql3);
	while($rslt3= $reponse3->fetch())
	{
		$qterecu=$qterecu+$rslt3['QTERECU'];
	}
	//ici on recupere le libelle et le taux remise de l'article
	$sql4 = 'SELECT ID_ARTICLE, LIBELLE, TAUXREMISE FROM ARTICLE WHERE ID_ARTICLE="'.$rslt2['ID_ARTICLE'].'"';
	$reponse4= $DataBase->query($sql4);
	while($rslt4= $reponse4->fetch())
	{
    	$libelle=$rslt4['LIBELLE'];
		$tauxremisear=$rslt4['TAUXREMISE'];
	}
	//calcul des valeurs pr l'article
		$valeurhtRB=$qterecu*$tauxremisear;
		$tvaRB=$valeurhtRB*$tva/100;
		$retfiscproRB=$valeurhtRB*$retfiscpro/100;
		$valeurttcRB=$valeurhtRB+$tvaRB+$retfiscproRB;
	//calcul des valeurs totales
		$TTvaleurhtRB=$TTvaleurhtRB+$valeurhtRB;
		$TTtvaRB=$TTtvaRB+$tvaRB;
		$TTretfiscproRB=$TTretfiscproRB+$retfiscproRB;
		$TTvaleurttcRB=$TTvaleurttcRB+$valeurttcRB;
		$TTqterecu=$TTqterecu+$qterecu;
		
			?>
             <tr>
             	  <td  align="left"> <?php echo $libelle; ?> </td>
                  <td  align="center"> <?php echo number_format($qterecu, 0, ',', ' '); ?> </td> 
                  <td  align="center"> <?php echo $tauxremisear; ?> </td>
                  <td  align="center"> <?php echo number_format($valeurhtRB, 0, ',', ' '); ?> </td>
                  <td  align="center"> <?php echo number_format($tvaRB, 0, ',', ' '); ?> </td>
                  <td  align="center"> <?php echo number_format($retfiscproRB, 0, ',', ' '); ?> </td>
                  <td  align="center"> <?php echo number_format($valeurttcRB, 0, ',', ' '); ?> </td>
              </tr>
              
<?php
}
//affichage les remises base(pr tous les articles)
?>
              <tr>
             	  <td  align="center"><h5> <?php echo 'Totaux  : '; ?> </h5></td>
                  <td  align="center"> <h5><?php echo number_format($TTqterecu, 0, ',', ' '); ?> </h5></td> 
                  <td  align="center"> <h5>//<h5></td>
                  <td  align="center"> <h5><?php echo number_format($TTvaleurhtRB, 0, ',', ' '); ?> </h5></td>
                  <td  align="center"> <h5><?php echo number_format($TTtvaRB, 0, ',', ' '); ?> </h5></td>
                  <td  align="center"> <h5><?php echo number_format($TTretfiscproRB, 0, ',', ' '); ?> </h5></td>
                  <td  align="center"> <h5><?php echo number_format($TTvaleurttcRB, 0, ',', ' '); ?> </h5></td> 
              </tr>
</table>
<!--ici on calcule le bonus casse -->


<table id='BonusCasse' border="1" width="100%" align="center">
          <tr align="center">
                <td colspan="11" align="center"><h4>Bonus Casse</h4></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center"><h5>CA TTC</h5> </td>
                <td align="center"><h5>TAUX HT</h5> </td>
                <td align="center"><h5>VALEUR HT</h5> </td>
                <td align="center"><h5>MT TVA</h5> </td>
                <td align="center"><h5>RETENU FISC</h5> </td>
				<td align="center"><h5>VALEUR TTC</h5></td>
			</tr>
            
<?php

$valeurhtBC=0;
$tvaBC=0;
$capsa=0;
$catva=0;

			
			//$valeurhtBC=$cattc*$bonuscasse/100;
			$valeurhtBC=($bonuscasse*$cattc)/(100+$tva+$retfiscpro);
			$tvaBC=$valeurhtBC*$tva/100;
			$retfiscproBC=$valeurhtBC*$retfiscpro/100;
			$valeurttcBC=$valeurhtBC+$tvaBC+$retfiscproBC; 
			?>
             <tr>
                  <td  align="center"> <?php echo number_format($cattc, 0, ',', ' '); ?> </td>
                  <td  align="center"> <?php echo $bonuscasse.'%'; ?> </td>
                  <td  align="center"> <?php echo number_format($valeurhtBC, 0, ',', ' '); ?> </td>
                  <td  align="center"> <?php echo number_format($tvaBC, 0, ',', ' '); ?> </td>
                  <td  align="center"> <?php echo number_format($retfiscproBC, 0, ',', ' '); ?> </td>
                  <td  align="center"> <?php echo number_format($valeurttcBC, 0, ',', ' '); ?> </td>
              </tr>
</table>

<!--ici on calcule le total remises TTC -->


<table id='totalremisettc' border="1" width="100%" align="center">
          <tr align="center">
                <td colspan="11" align="center"><h4>Total Remise TTC</h4></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center"><h5>VALEUR HT</h5> </td>
                <td align="center"><h5>MT TVA</h5> </td>
                <td align="center"><h5>RETENU FISC</h5> </td>
				<td align="center"><h5>VALEUR TTC</h5></td>
			</tr>
            
<?php

$valeurhtTR=0;
$tvaTR=0;
$retfiscproTR=0;
$valeurttcTR=0;

			$valeurhtTR=$TTvaleurhtRB+$valeurhtBC;
			$tvaTR=$TTtvaRB+$tvaBC;
			$retfiscproTR=$TTretfiscproRB+$retfiscproBC;
			$valeurttcTR=$valeurttcBC+$TTvaleurttcRB; 
			?>
             <tr>
                  <td  align="center"> <?php echo number_format($valeurhtTR, 0, ',', ' '); ?> </td>
                  <td  align="center"> <?php echo number_format($tvaTR, 0, ',', ' '); ?> </td>
                  <td  align="center"> <?php echo number_format($retfiscproTR, 0, ',', ' '); ?> </td>
                  <td  align="center"> <?php echo number_format($valeurttcTR, 0, ',', ' '); ?> </td>
              </tr>
</table>

<!--Retenu TVA -->

<table id='Retenue TVA' border="1" width="100%" align="center">
       <tr align="center">
             <td colspan="7" align="center"><h4>Retenue TVA</h4></td>
       </tr>
       <tr bgcolor="#CCCCCC">
       		<td align="center" width="460px"><h5>Retenue TVA : </h5> </td>
            <td align="center"><h5><?php echo '- '.number_format($tvaTR, 0, ',', ' '); ?></h5> </td>
	   </tr>
</table>
<table id='totalremisehtva' border="1" width="100%" align="center">
       <tr align="center">
             <td colspan="7" align="center" ><h4>Total Remise Hors TVA</h4></td>
       </tr>
       <?php
			
			$ttremisehtva=$valeurttcTR-$tvaTR;
	  ?>
       <tr bgcolor="#CCCCCC">
       		<td align="center" width="460px"><h5>Total Remise Hors TVA </h5> </td>
            <td align="center"><h5><?php echo number_format($ttremisehtva, 0, ',', ' '); ?></h5> </td>
	   </tr>
</table>
<!--Depot de garantie -->
<table id='depotgarantie' border="1" width="100%" align="center">
       <tr align="center">
             <td colspan="7" align="center" ><h4>Dépôt de Garantie</h4></td>
       </tr>
       <?php
			
			$valeurttcDG=$valeurttcTR*$depotgarantie/100;
	  ?>
      		<tr bgcolor="#CCCCCC">
                <td align="center"><h5>REMISE TTC</h5> </td>
                <td align="center"><h5>TAUX</h5> </td>
				<td align="center"><h5>VALEUR TTC</h5></td>
			</tr>
             <tr>
                  <td  align="center"> <?php echo number_format($valeurttcTR, 0, ',', ' '); ?> </td>
                  <td  align="center"> <?php echo number_format($depotgarantie, 0, ',', ' ').'%'; ?> </td>
                  <td  align="center"> <?php echo number_format($valeurttcDG, 0, ',', ' '); ?> </td>
</table>
<!--PSA -->
<table id='PSA' border="1" width="100%" align="center">
       <tr align="center">
             <td colspan="7" align="center" ><h4>PSA</h4></td>
       </tr>
       <?php
			
			$valeurhtPSA=$caht*$tauxpsaremise/100;
			$valeurremisesht=$valeurhtTR*$tauxremisesht/100;
			$valeurepargneachat=$valeurhtPSA-$valeurremisesht;
	  ?>
      		<tr bgcolor="#CCCCCC">
                <td align="center"><h5>CA HT</h5> </td>
                <td align="center"><h5>TAUX</h5> </td>
				<td align="center"><h5>VALEUR HT</h5></td>
                <td align="center"><h5>TOTAL REMISE HT</h5> </td>
                <td align="center"><h5>MOINS <?php  echo $tauxremisesht; ?> % REMISE HT</h5> </td>
				<td align="center"><h5>REMBOURSEMENT EPARGNE ACHAT</h5></td>
			</tr>
             <tr>
                  <td  align="center"> <?php echo number_format($caht, 0, ',', ' '); ?> </td>
                  <td  align="center"> <?php echo number_format($tauxpsaremise, 0, ',', ' ').'%'; ?> </td>
                  <td  align="center"> <?php echo number_format($valeurhtPSA, 0, ',', ' '); ?> </td>
                  <td  align="center"> <?php echo number_format($valeurhtTR, 0, ',', ' '); ?> </td>
                  <td  align="center"> <?php echo number_format($valeurremisesht, 0, ',', ' '); ?> </td>
                  <td  align="center"> <?php echo number_format($valeurepargneachat, 0, ',', ' '); ?> </td>
</table>
<!--total remises nettes a payer -->
<table id='remisesnettes' border="1" width="100%" align="center">
       <tr align="center">
             <td colspan="7" align="center" ><h4>Total Remises Nettes à Payer</h4></td>
       </tr>
       <?php
			
			$ttremisesnettes=$ttremisehtva-$valeurttcDG+$valeurepargneachat;
	  ?>
      		<tr bgcolor="#CCCCCC">
                <td align="center"><h5>Total Remises HTVA</h5> </td>
                <td align="center"><h5>Dépôt Garantie</h5> </td>
				<td align="center"><h5>REMBOURSEMENT EPARGNE ACHAT</h5></td>
                <td align="center"><h5>NET A PAYER</h5></td>
			</tr>
             <tr>
                  <td  align="center"> <?php echo number_format($ttremisehtva, 0, ',', ' '); ?> </td>
                  <td  align="center"> <?php echo number_format($valeurttcDG, 0, ',', ' ').'%'; ?> </td>
                  <td  align="center"> <?php echo number_format($valeurepargneachat, 0, ',', ' '); ?> </td>
                  <td  align="center" bgcolor="#FF6600"> <h3><?php echo number_format($ttremisesnettes, 0, ',', ' ').' FCFA'; ?></h3> </td>
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