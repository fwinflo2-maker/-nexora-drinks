<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur"))
{
	include('Connexion.php');
	include('fonctions.php');
	$Debut=dateFormatAnglais($_GET['DateD']);
	$Fin=dateFormatAnglais($_GET['DateF']);
	//On recuere les infos clients 
	$tauxristournesht=0;
    $tauxpsaristournes=0;
	$tva=0;
	$tauxretfiscpro=0;
	$sql2='SELECT  C.ID_CLIENT, C.EMAIL, C.NOM, C.TAUXRISTOURNEHT, C.PSARISTOURNES, CAT.TAUXRETFISCPRO, CAT.TAUXTVA FROM CLIENT C, CATEGORIE CAT WHERE C.ID_CATEGORIE=CAT.ID_CATEGORIE AND  C.ID_CLIENT="'.$_GET['Clt'].'" ' ;
    $reponse2= $DataBase->query($sql2);
		while($rslt2= $reponse2->fetch())
		{
			$codeC=$rslt2['ID_CLIENT'];
			$nomC=$rslt2['NOM'];
			$emailC=$rslt2['EMAIL'];
			$tauxristournesht=$rslt2['TAUXRISTOURNEHT'];
			$tauxretfiscpro=$rslt2['TAUXRETFISCPRO'];
			$tva=$rslt2['TAUXTVA'];
			$tauxpsaristournes=$rslt2['PSARISTOURNES'];
		}
	
//on recupere les parametres

$tauxpsa=0;
$bonuscasse=0;
$depotgarantie=0;
$retfiscpro=0;
$tauxremisesht=0;



 $sql='SELECT  * FROM PARAMETRE ' ;
 $reponse= $DataBase->query($sql);
 while($rslt= $reponse->fetch())
		{
			
			$tauxpsaremise=$rslt['PSAREMISE'];
			$tauxepargne=$rslt['TAUXEPARGNE'];
			$tauxpsa=$rslt['PSA'];
			$tauxremisesht=$rslt['TAUXREMISESHT'];
		}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultation des Ristournes Client</title>
</head>

<body>
<!--ici on calcule le CA HT les prelements (psa et epargne) ainsi que le CA TTC -->
<table id='ca' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="5"><h3>Avoir De Participation Ristournes Client</h3></td>
          </tr>
          <tr>
                <td align="center" ><h4>  Client : (<?php echo $codeC; ?>) <?php echo $nomC; ?> </h4> </td>
                <td align="center" colspan="2"><h4><?php echo $emailC; ?> </h4></td>
          </tr>
          <tr align="center" bgcolor="#CCCCCC">
                <td colspan="5"><h5>Période Du : <?php echo dateFormatFrancais($Debut); ?> Au : <?php echo dateFormatFrancais($Fin); ?></h5></td>
          </tr>
          <tr align="center">
          		<td align="center"><a href="Etat_Ristourne_Clt.php?Clt=<?php echo $_GET["Clt"];?>&DateD=<?php echo $_GET["DateD"];?>&DateF=<?php echo $_GET["DateF"];?>&Retfr=<?php echo $_GET["Retfr"];?>&RetDA=<?php echo $_GET["RetDA"];?>&RetCGA=<?php echo $_GET["RetCGA"];?>&RegR=<?php echo $_GET["RegR"];?>&RegPSAEC=<?php echo $_GET["RegPSAEC"];?>&RegPSAAnt=<?php echo $_GET["RegPSAAnt"];?>&RegDA=<?php echo $_GET["RegDA"];?>&RegEntfr=<?php echo $_GET["RegEntfr"];?>&RegCGA=<?php echo $_GET["RegCGA"];?>"/><input type="button" value="Imprimer" style="background:#FF6600" /></td></a>
                <td colspan="4" align="center"><h4>Chiffre d'Affaire</h4></td>
          </tr> 
          <tr bgcolor="#CCCCCC">
                <td align="center"><h5>CA HT</h5> </td>
                <td align="center"><h5>TVA (<?php echo $tva; ?>%)</h5> </td>
				<td align="center"><h5>CA TTC</h5></td>
		  </tr>
            
<?php
$caht=0;
$cattc=0;
$caepargne=0;
$capsa=0;
$catva=0;

  //ici on recupere le total des achats boutiques du client
$sql10='SELECT * FROM SORTIE_STOCK WHERE DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ID_CLIENT="'.$codeC.'" AND STATUT="V"' ;
$reponse10= $DataBase->query($sql10);
while($rslt10= $reponse10->fetch())
{
	//Ici on recupere les articles de chacune des ventes puis on somme les prix 
	 $sql11='SELECT * FROM  ARTICLEVENDU WHERE ID_SORTIESTOCK= "'.$rslt10['ID_SORTIESTOCK'].'" ' ;
	 $reponse11= $DataBase->query($sql11);
  	while($rslt11= $reponse11->fetch())
 	{
	 	$cattc= ($cattc + $rslt11['PRIXVENTE']);	 
 	}
}
			//$capsa=$caht*$tauxpsa/100;
			//$caepargne=$caht*$tauxepargne/100;
			$caht=($cattc*100)/($tva+100); 
			$catva=$caht*$tva/100;  
			?>
             <tr>
                  <td  align="center"> <?php echo number_format($caht, 0, ',', ' '); ?> </td>
                  <td  align="center"> <?php echo number_format($catva, 0, ',', ' '); ?> </td>
                  <td  align="center"> <?php echo number_format($cattc, 0, ',', ' '); ?> </td>
              </tr>
</table>



<!--ici on calcule les ristournes de base -->
<table id='RistourneBase' border="1" width="100%" align="center">
      <tr align="center">
             <td colspan="5" align="center"><h4>Achats et Ristournes Correspondants</h4></td>
       </tr>
       <tr bgcolor="#CCCCCC">
       		<td align="left"><h5>LIBELLE</h5> </td>
            <td align="center"><h5>QTE</h5> </td>
            <td align="center"><h5>TAUX AU COLIS </h5> </td>
            <!--<td align="center"><h5>VALEUR HT</h5> </td>-->
            <!--<td align="center"><h5>MT TVA</h5> </td>-->
            <!--<td align="center"><h5>RETENU FISC</h5> </td>-->
			<td align="center"><h5>VALEUR </h5></td>
	   </tr>
            
<?php
//parametres calcul ristourne pour un article
$valeurR=0;
$qteR=0;
//parametres calcul pour tous les articles
$TTvaleurttcS=0;
$TTqteS=0;
$ttqtesortie=0;

//Ici on recupre la liste sans doublons des articles livres dans la periode 
$sql2='SELECT DISTINCT  AV.ID_ARTICLE FROM ARTICLEVENDU AV, SORTIE_STOCK ST  WHERE AV.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND ST.ID_CLIENT="'.$codeC.'" AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V" ORDER BY AV.ID_ARTICLE  ';
$reponse2= $DataBase->query($sql2);
while($rslt2= $reponse2->fetch())
{
	//Ici on recupere les quantites de la meme article puis on somme
	$qtesortie=0;
	$tauxristournettc=0;
	$sql3 = 'SELECT  AV.ID_ARTICLE, AV.QTESORTIE, AV.ID_SORTIESTOCK, ST.ID_SORTIESTOCK, ST.DATESORTIESTOCK FROM ARTICLEVENDU AV, SORTIE_STOCK ST WHERE AV.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND ST.ID_CLIENT="'.$codeC.'" AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND AV.ID_ARTICLE="'.$rslt2['ID_ARTICLE'].'" AND ST.STATUT="V"';
	$reponse3= $DataBase->query($sql3);
	while($rslt3= $reponse3->fetch())
	{
		$qtesortie=$qtesortie+$rslt3['QTESORTIE'];
	}
	//ici on recupere le libelle et le taux ristourne de l'article
	$sql4 = 'SELECT ID_ARTICLE, LIBELLE, TAUXRISTOURNE FROM ARTICLE WHERE ID_ARTICLE="'.$rslt2['ID_ARTICLE'].'"';
	$reponse4= $DataBase->query($sql4);
	while($rslt4= $reponse4->fetch())
	{
    	$libelle=$rslt4['LIBELLE'];
		$tauxristournear=$rslt4['TAUXRISTOURNE'];
	}
	//calcul des valeurs pr l'article
	
		//on met la TVA et le retenu fisc pro a zero
		//$tva=0;
		//$tauxretfiscpro=0;
		
	    $tauxristournettc=((100+$tva+$tauxretfiscpro)/100)*$tauxristournear;
		
		$valeurS=number_format($tauxristournettc, 0, ',', ' ')*$qtesortie;

		$tvaS=$valeurS*$tva/100;
	//calcul des valeurs totales
		$TTvaleurttcS=$TTvaleurttcS+$valeurS;
		$ttqtesortie=$ttqtesortie+$qtesortie;
		
			?>
             <tr>
             	  <td  align="left"> <?php echo $libelle; ?> </td>
                  <td  align="center"> <?php echo number_format($qtesortie, 0, ',', ' '); ?> </td> 
                  <td  align="center"> <?php echo number_format($tauxristournettc, 0, ',', ' '); ?> </td>
                  <td  align="center"> <?php echo number_format($valeurS, 0, ',', ' '); ?> </td>
              </tr>
              
<?php
}
//affichage les ristournes (pr tous les articles)
?>
              <tr>
             	  <td  align="center"><h5> <?php echo 'Totaux  : '; ?> </h5></td>
                  <td  align="center"> <h5><?php echo number_format($ttqtesortie, 0, ',', ' '); ?> </h5></td> 
                  <td  align="center"> <h5>//<h5></td>
                  <td  align="center"> <h5><?php echo number_format($TTvaleurttcS, 0, ',', ' '); ?> </h5></td> 
              </tr>
</table>
<!--ici on calcule la participation ristourne -->


<table id='participationristourne' border="1" width="100%" align="center">
          <tr align="center">
                <td colspan="11" align="center"><h5>Participation Ristourne (Quantité * Taux)</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center"><h5>VALEUR </h5> </td>
                <td align="center"><h5>MT TVA <?php echo $tva.' %'; ?> </h5> </td>
                <td align="center"><h5>PSA <?php echo $tauxretfiscpro.' %'; ?> </h5> </td>
				<td align="center"><h5>VALEUR TTC</h5></td>
			</tr>
            
<?php

$valeurristourneht=0;
$tvaristourne=0;
$retfiscpro=0;


			$valeurristourneht=(100*$TTvaleurttcS)/(100+$tauxretfiscpro+$tva);
			$tvaristourne=($valeurristourneht*$tva)/100;
			$retfiscpro=($valeurristourneht*$tauxretfiscpro)/100;
			?>
             <tr>
                  <td  align="center"> <?php echo number_format($valeurristourneht, 0, ',', ' '); ?> </td>
                  <td  align="center"> <?php echo number_format($tvaristourne, 0, ',', ' '); ?> </td>
                  <td  align="center"> <?php echo number_format($retfiscpro, 0, ',', ' '); ?> </td>
                  <td  align="center"> <?php echo number_format($TTvaleurttcS, 0, ',', ' '); ?> </td>
              </tr>
</table>


<!--Rappel de la Retenu TVA -->

        <!--<table id='Retenue TVA' border="1" width="100%" align="center">
               <tr align="center">
                     <td colspan="7" align="center"><h5>Rappel de la Retenue </h5></td>
               </tr>
               <tr bgcolor="#CCCCCC">
                    <td align="center" width="460px"><h5>Retenue TVA  </h5> </td>
                    <td align="center"><h5><?php /*?><?php echo '- '.number_format($tvaristourne, 0, ',', ' '); ?><?php */?></h5> </td>
               </tr>
        </table>
--><!--Total Ristourne Hors TVA -->
<?php
	$ttristourneshorstva=0;
	$ttristourneshorstva=$TTvaleurttcS-$tvaristourne;
	
 ?>
        <!--<table id='totalremisehtva' border="1" width="100%" align="center">
               <tr align="center">
                     <td colspan="7" align="center" ><h5>Total Ristourne Hors TVA</h5></td>
               </tr>
               <tr bgcolor="#CCCCCC">
                    <td align="center" width="460px"><h5>Total Ristourne Hors TVA </h5> </td>
                    <td align="center"><h5><?php /*?><?php echo number_format($ttristourneshorstva, 0, ',', ' '); ?><?php */?></h5> </td>
               </tr>
        </table>
-->
<!--RETROCESSION PSA -->
<table id='PSA' border="1" width="100%" align="center">
       <tr align="center">
             <td colspan="7" align="center" ><h5>Retrocession PSA Clients</h5></td>
       </tr>
       <?php
			$valeurristournestaux=0;
			$valeurhtPSA=0;
			$valeurhtPSA=$caht*$tauxpsaristournes/100;
			$valeurristournestaux=$valeurristourneht*$tauxristournesht/100;
			$valeurepargneachat=$valeurhtPSA-$valeurristournestaux;
	  ?>
      		<tr bgcolor="#CCCCCC">
                <td align="center"><h5>CHIFFRE D'AFFAIRE HT</h5> </td>
                <td align="center"><h5>TAUX PSA RISTOURNES HT </h5> </td>
				<td align="center"><h5>PRELEVEMENT SUR CA</h5></td>
                <td align="center"><h5>TOTAL RISTOURNES HT</h5> </td>
                <td align="center"><h5>MOINS <?php  echo $tauxristournesht; ?> % RISTOURNE HT</h5> </td>
				<td align="center"><h5>REMBOURSEMENT EPARGNE ACHAT</h5></td>
			</tr>
            <tr>
                  <td  align="center"> <?php echo number_format($caht, 0, ',', ' '); ?> </td>
                  <td  align="center"> <?php echo number_format($tauxpsaristournes, 1, ',', ' ').'%'; ?> </td>
                  <td  align="center"> <?php echo number_format($valeurhtPSA, 0, ',', ' '); ?> </td>
                  <td  align="center"> <?php echo number_format($valeurristourneht, 0, ',', ' '); ?> </td>
                  <td  align="center"> <?php echo number_format($valeurristournestaux, 0, ',', ' '); ?> </td>
                  <td  align="center"> <?php echo number_format($valeurepargneachat, 0, ',', ' '); ?> </td>
</table>
<!--Retenu Diverses -->
<?php 
	$retenuefrigo=0;
	$retenueDA=0;
	$retenueCGA=0;
	
	$retenuefrigo=$_GET['Retfr'];
	$retenueDA=$_GET['RetDA'];
	$retenueCGA=$_GET['RetCGA'];
	
	$ttretenues=$retenuefrigo+$retenueDA+$retenueCGA; 
?>
<table id='RetenueDiverses' border="1" width="100%" align="center">
    
       <tr align="center">
             <td colspan="7" align="center"><h5> Retenues Diverses </h5></td>
       </tr>
       <tr bgcolor="#CCCCCC">
       		<td  width="460px"><h5>Retenue  </h5> </td>
            <td align="center" width="460px"><h5>Montant  </h5> </td>
       </tr>
       <tr>
       		<td  width="460px"><h5>Retenue Frigo  </h5> </td>
            <td align="center"><h5><?php echo number_format($retenuefrigo, 0, ',', ' '); ?></h5> </td>
	   </tr>
       <tr>
       		<td  width="460px"><h5>Retenue Droit d'auteur  </h5> </td>
            <td align="center"><h5><?php echo number_format($retenueDA, 0, ',', ' '); ?></h5> </td>
	   </tr>
       <tr>
       		<td  width="460px"><h5>Retenue CGA à la Source  </h5> </td>
            <td align="center"><h5><?php echo number_format($retenueCGA, 0, ',', ' '); ?></h5> </td>
	   </tr>
       
       
</table>

<!--Regularisations Diverses -->

<?php 
	$reguristourne=0;
	$regpsaencours=0;
	$regpsaanterieur=0;
	$regpsaanterieur=0;
	$regDA=0;
	$regEntFrigo=0;
	$regCGA=0;
	
	$reguristourne=$_GET['RegR'];
	$regpsaencours=$_GET['RegPSAEC'];
	$regpsaanterieur=$_GET['RegPSAAnt'];
	$regDA=$_GET['RegDA'];
	$regEntFrigo=$_GET['RegEntfr'];
	$regCGA=$_GET['RegCGA'];
	
	$ttregularisations=$reguristourne+$regpsaencours+$regpsaanterieur+$regDA+$regEntFrigo+$regCGA; 
?>
<table id='Regularisations' border="1" width="100%" align="center">
       <tr align="center">
             <td colspan="7" align="center"><h5> Regularisations Diverses </h5></td>
       </tr>
       <tr bgcolor="#CCCCCC">
       		<td  width="460px"><h5>Libelle  </h5> </td>
            <td align="center" width="460px"><h5>Montant  </h5> </td>
       </tr>
       <tr>
       		<td  width="460px"><h5>Regularisation Ristournes  </h5> </td>
            <td align="center"><h5><?php echo number_format($reguristourne, 0, ',', ' '); ?></h5> </td>
	   </tr>
       <tr>
       		<td  width="460px"><h5>Regularisation PSA exercice encours  </h5> </td>
            <td align="center"><h5><?php echo number_format($regpsaencours, 0, ',', ' '); ?></h5> </td>
	   </tr>
       <tr>
       		<td  width="460px"><h5>Regularisation PSA exercice anterieur  </h5> </td>
            <td align="center"><h5><?php echo number_format($regpsaanterieur, 0, ',', ' '); ?></h5> </td>
	   </tr>
       <tr>
       		<td  width="460px"><h5>Regularisation droit d'auteur  </h5> </td>
            <td align="center"><h5><?php echo number_format($regDA, 0, ',', ' '); ?></h5> </td>
	   </tr>
       <tr>
       		<td  width="460px"><h5>Regularisation Entretien frigo  </h5> </td>
            <td align="center"><h5><?php echo number_format($regEntFrigo, 0, ',', ' '); ?></h5> </td>
	   </tr>
       <tr>
       		<td  width="460px"><h5>Regularisation CGA  </h5> </td>
            <td align="center"><h5><?php echo number_format($regCGA, 0, ',', ' '); ?></h5> </td>
	   </tr>
</table>

<!--total ristournes nettes a payer -->
<table id='ristournesnettes' border="1" width="100%" align="center">
       <tr align="center">
             <td colspan="7" align="center" ><h4>Total Ristournes Nettes à Payer</h4></td>
       </tr>
       <?php
			$ttristournesnettes=0;
			$ttristournesnettes=$TTvaleurttcS+$valeurepargneachat-$ttretenues+$ttregularisations;
	  ?>
      		<tr bgcolor="#CCCCCC">
                <td align="center"><h3>NET A PAYER</h3></td>
                <td  align="center" bgcolor="#FF6600"> <h3><?php echo number_format($ttristournesnettes, 0, ',', ' ').' FCFA'; ?></h3> </td>
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