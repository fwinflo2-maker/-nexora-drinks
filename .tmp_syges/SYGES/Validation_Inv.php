<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="DGA" || $_SESSION['habilitation']=="Superviseur" || $_SESSION['habilitation']=="Comptable" ))
{
	include("Connexion.php");
	include("fonctions.php");
	
	$sql2 = " select *  from inventaire  where id_inv='".$_GET['Id']."'";
	$reponse2= $DataBase->query($sql2);
	$rslt2= $reponse2->fetch();
?>
<!DOCTYPE html >
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire  de Validation d'inventaire.</title>
<style type="text/css">
label
{
	display:block;
	width:130px;
	float: left;
	}
</style>
<script src="JS/Enreg_Inv.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen" />
<script type="text/javascript" src="dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
</head>
 
<body>
<table id='liste' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="8"><h3>INVENTAIRE : N° <?php echo $_GET['Id']; ?> DU  <?php echo dateFormatFrancais($rslt2['DATE']); ?> A <?php echo $rslt2['HEURE']; ?></h3></td>
          </tr>
          <tr align="center" bgcolor="#CCCCCC">
          <td align="center"><a href="Etat_Inv.php?Id=<?php echo $_GET['Id'];?>"/><input type="button" value="Imprimer" style="background:#FF9000" /></td></a>
          	<td colspan="8"><h4> ARTICLES  </h4></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center" ><h5>Libellé</h5> </td>
                <td align="center" ><h5>Condition. </h5> </td>
                <td  align="center"><h5>Qte </h5></td>
                <td  align="center"><h5>PU</h5></td>
                <td  align="center"><h5>Valeur Liquide </h5></td>
                <td  align="center"><h5>Valeur Emballage </h5></td>
                <td  align="center"><h5>Frais Enlevement </h5></td>
                <td  align="center"><h5>Valeur Stock</h5></td>
			</tr>
            
<?php
$couleur = "darkgray";
$i = 0;
$nbre=0;
$totalcolis=0;
$montantliquide=0;
$montantemb=0;
$valeurstock=0;
$mtfraisenlement=0;
$montantfraisenlement=0;
$sql3 = "SELECT DISTINCT ID_FAMILLE FROM ARTICLE_INV WHERE ID_INV='".$_GET['Id']."'";
$reponse3= $DataBase->query($sql3);
while($rslt3= $reponse3->fetch())
{
	?>
		<td colspan="8"><h4> FAMILLE :<?php echo $rslt3['ID_FAMILLE'];?> </h4></td>
    <?php
	$sql = "SELECT * FROM ARTICLE_INV WHERE ID_INV='".$_GET['Id']."' AND ID_FAMILLE='".$rslt3['ID_FAMILLE']."' ORDER BY LIBELLE";
	$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			$mtmag=$rslt['PRIXVENTE']*$rslt['QTESTOCK'];
			if(($rslt['MARQUE']=="CASIER") || ($rslt['MARQUE']=="casier")|| ($rslt['MARQUE']=="CASIERS")|| ($rslt['MARQUE']=="casiers"))
			{
				$mtemb=$rslt2['PU_EMB_PLEIN']*$rslt['QTESTOCK'];
			}	
			else
			{
				$mtemb=0;
			}
			$mtfraisenlement=$rslt2['FRAISENLEVEMENT']*$rslt['QTESTOCK'];
			if ($i%2 == 0)
					$couleur = '#CCCCCC';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                        <td  align="center"> <?php echo $rslt['LIBELLE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['MARQUE'].' '.$rslt['NBREBTE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['QTESTOCK']; ?> </td>
                        <td  align="center"> <?php echo $rslt['PRIXVENTE']; ?> </td>
                        <td  align="center"> <?php echo number_format($mtmag, 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($mtemb, 0, ',', ' '); ?></td>
                        <td  align="center"> <?php echo number_format($mtfraisenlement, 0, ',', ' '); ?></td>
                        <td  align="center"> <?php echo number_format($mtmag+$mtemb+$mtfraisenlement, 0, ',', ' '); ?></td>
                     </tr>
                <?php
				$i++;
				$nbre++;
				$totalcolis=$totalcolis+$rslt['QTESTOCK'];
				$montantliquide=$montantliquide+$mtmag;
				$montantemb=$montantemb+$mtemb;
				$montantfraisenlement=$montantfraisenlement+$mtfraisenlement;
				$valeurstock=$montantliquide+$montantemb+$montantfraisenlement;
		 }
}
?>
<tr>
	<td colspan="2" align="center"><h4>Nombre d'article : <?php echo $nbre; ?> </h4></td>
    <td colspan="" align="center"><h4><?php echo $totalcolis; ?> </h4></td>
    <td colspan="" align="center"><h4><?php echo '//'; ?> </h4></td>
    <td colspan="" align="center" ><h4><?php echo number_format($montantliquide, 0, ',', ' '); ?> </h4></td>
    <td colspan="" align="center"><h4><?php echo number_format($montantemb, 0, ',', ' '); ?> </h4></td>
    <td colspan="" align="center"><h4><?php echo number_format($montantfraisenlement, 0, ',', ' '); ?> </h4></td>
    <td colspan="" align="center" bgcolor="#FF6600"><h4><?php echo number_format($valeurstock, 0, ',', ' '); ?> </h4></td>
</tr>
</table>


<table id='listeemballage' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="4"><h4> EMBALLAGES</h4></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center" ><h5>Libellé </h5> </td>
                <td  align="center"><h5>Qte </h5></td>
                <td  align="center"><h5>PU</h5></td>
                <td  align="center"><h5>Valeur Stock </h5></td>
			</tr>
            
<?php
$couleur = "darkgray";
$mtstock=0;
$Valeurstockemb=0;

				?>
                	<tr>
                        <td  align="center"> <?php echo "PALETTE BOIS(S)"; ?> </td>
                        <td  align="center"> <?php echo $rslt2['PALETTEBOIS']; ?> </td>
                        <td  align="center"> <?php echo $rslt2['PU_PALETTEBOIS']; ?> </td>
                        <td  align="center"> <?php echo number_format($rslt2['PALETTEBOIS']*$rslt2['PU_PALETTEBOIS'], 0, ',', ' '); ?> </td>                     </tr>
                 	<tr>
                        <td  align="center"> <?php echo "PALETTE PLASTIQUE(S)"; ?> </td>
                        <td  align="center"> <?php echo $rslt2['PALETTEPLASTIQUE']; ?> </td>
                        <td  align="center"> <?php echo $rslt2['PU_PALETTEPLASTIQUE']; ?> </td>
                        <td  align="center"> <?php echo number_format($rslt2['PALETTEPLASTIQUE']*$rslt2['PU_PALETTEPLASTIQUE'], 0, ',', ' '); ?> </td>                     </tr>
                <?php
					echo "<tr bgcolor=#CCCCCC>";
				?>
                        <td  align="center"> <?php echo "EMBALLAGES PLEINS (S)"; ?> </td>
                        <td  align="center"> <?php echo $rslt2['EMB_PLEIN']; ?> </td>
                        <td  align="center"> <?php echo $rslt2['PU_EMB_PLEIN']; ?> </td>
                        <td  align="center"> <?php echo number_format($rslt2['EMB_PLEIN']*$rslt2['PU_EMB_PLEIN'], 0, ',', ' '); ?> </td>                     </tr>
                <?php
					echo "<tr>";
				?>
                        <td  align="center"> <?php echo "EMBALLAGES VIDES (S)"; ?> </td>
                        <td  align="center"> <?php echo $rslt2['EMB_VIDE']; ?> </td>
                        <td  align="center"> <?php echo $rslt2['PU_EMB_VIDE']; ?> </td>
                        <td  align="center"> <?php echo number_format($rslt2['EMB_VIDE']*$rslt2['PU_EMB_VIDE'], 0, ',', ' '); ?> </td>                     </tr>
                <?php	 
				$Valeurstockemb=($rslt2['PALETTEBOIS']*$rslt2['PU_PALETTEBOIS'])+($rslt2['PALETTEPLASTIQUE']*$rslt2['PU_PALETTEPLASTIQUE'])+($rslt2['EMB_PLEIN']*$rslt2['PU_EMB_PLEIN'])+$rslt2['EMB_VIDE']*($rslt2['PU_EMB_VIDE']);
?>

<tr>
    <td colspan="" align="center"><h4>Totaux : </h4></td>
    <td colspan="" align="center"><h4><?php echo  number_format($rslt2['PALETTEBOIS']+$rslt2['PALETTEPLASTIQUE']+$rslt2['EMB_PLEIN']+$rslt2['EMB_VIDE'], 0, ',', ' '); ?> </h4></td>
    <td colspan="" align="center"><h4><?php echo '//'; ?> </h4></td>
    <td colspan="" align="center" bgcolor="#FF6600"><h4><?php echo number_format($Valeurstockemb, 0, ',', ' '); ?> </h4></td>
</tr>

</table>

<table id='Global' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="4"><h4> SITUATION GLOBALE</h4></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center" ><h5>Libellé </h5> </td>
                <td  align="center"><h5>Montant </h5></td>
                <td align="center" ><h5>Libellé </h5> </td>
                <td  align="center"><h5>Montant </h5></td>
			</tr>
            
<?php

$TotalGlobal=$valeurstock+$rslt2['SOLDECAISSE']+$rslt2['SOLDESABC']+$rslt2['SOLDEOM']+$rslt2['SOLDEMOMO']+$rslt2['CREDITCLIENT']+$rslt2['CREDITEMBALLAGE']+$rslt2['SOLDEBANQUE']+$rslt2['AUTRECREDIT']+$Valeurstockemb-$rslt2['CREDITBRASSERIES']-$rslt2['CREDITBANQUE']-$rslt2['RISTOURNESCLIENTS']-$rslt2['AUTRESDEBIT'];
				?>
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="4" align="left"><h4> VALEUR DES STOCKS</h4></td>
          </tr>
                      <tr>  
                        <td  align="center"> <?php echo "VALEUR STOCK "; ?> </td>
                        <td  align="center"> <?php echo number_format($valeurstock, 0, ',', ' '); ?> </td>   
                      </tr>
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="4" align="left"><h4> CREDIT</h4></td>
          </tr>
                      <tr>     
                        	<td  align="center"> <?php echo "SOLDE CAISSE "; ?> </td>
                        	<td  align="center"> <?php echo number_format($rslt2['SOLDECAISSE'], 0, ',', ' '); ?> </td>                 
                        	<td  align="center"> <?php echo "SOLDE BRASSERIES "; ?> </td>
                        	<td  align="center"> <?php echo number_format($rslt2['SOLDESABC'], 0, ',', ' '); ?> </td>               
                      </tr>
                      <tr>     
                        	<td  align="center"> <?php echo "SOLDE OM "; ?> </td>
                        	<td  align="center"> <?php echo number_format($rslt2['SOLDEOM'], 0, ',', ' '); ?> </td>                  
                        	<td  align="center"> <?php echo "SOLDE MOMO "; ?> </td>
                        	<td  align="center"> <?php echo number_format($rslt2['SOLDEMOMO'], 0, ',', ' '); ?> </td>               
                      </tr>
                      <tr>     
                        	<td  align="center"> <?php echo "CREDIT CLIENT "; ?> </td>
                        	<td  align="center"> <?php echo number_format($rslt2['CREDITCLIENT'], 0, ',', ' '); ?> </td>    
                        	<td  align="center"> <?php echo "CREDIT EMBALLAGE "; ?> </td>
                        	<td  align="center"> <?php echo number_format($rslt2['CREDITEMBALLAGE'], 0, ',', ' '); ?> </td>                
                      </tr>                                                                                                      
                      <tr>
                        	<td  align="center"> <?php echo "SOLDE BANQUE"; ?> </td>
                        	<td  align="center"> <?php echo number_format($rslt2['SOLDEBANQUE'], 0, ',', ' '); ?> </td>               
  							<td  align="center"> <?php echo "AUTRES (CREDIT)"; ?> </td>
                        	<td  align="center"> <?php echo number_format($rslt2['AUTRECREDIT'], 0, ',', ' '); ?> </td>                     
                      </tr>
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="4" align="left"><h4> DEBIT</h4></td>
          </tr>
                      <tr>     
                        	<td  align="center"> <?php echo "CREDIT BRASSERIES "; ?> </td>
                        	<td  align="center"> <?php echo number_format($rslt2['CREDITBRASSERIES'], 0, ',', ' '); ?> </td>                 
                        	<td  align="center"> <?php echo "CREDIT BANQUE "; ?> </td>
                        	<td  align="center"> <?php echo number_format($rslt2['CREDITBANQUE'], 0, ',', ' '); ?> </td>               
                      </tr>
                      <tr>     
                        	<td  align="center"> <?php echo "RISTOURNES CLIENTS "; ?> </td>
                        	<td  align="center"> <?php echo number_format($rslt2['RISTOURNESCLIENTS'], 0, ',', ' '); ?> </td>                  
                        	<td  align="center"> <?php echo "AUTRES (DEBIT) "; ?> </td>
                        	<td  align="center"> <?php echo number_format($rslt2['AUTRESDEBIT'], 0, ',', ' '); ?> </td>               
                      </tr>
<tr>
    <td colspan="" align="center"><h4>TOTAL GLOBAL  </h4></td>
    <td colspan="" align="center" bgcolor="#FF6600"><h4><?php echo number_format($TotalGlobal, 0, ',', ' ').' F'; ?> </h4></td>
</tr>

</table>
</form>
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
