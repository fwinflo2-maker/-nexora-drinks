<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"  || $_SESSION['habilitation']=="Comptable"))
{
	include('Connexion.php');
	include('fonctions.php');
	$Debut=dateFormatAnglais($_GET['DateD']);
	$Fin=dateFormatAnglais($_GET['DateF']);
	
//on recupere les parametres
$tva=0;
$tauxpsa=0;
$exercice=0;
$tva=0;
$tca=0;
$tac=0;

 $sql2='SELECT  * FROM PARAMETRE ' ;
 $reponse2= $DataBase->query($sql2);
 while($rslt2= $reponse2->fetch())
		{
			
			$tva=$rslt2['TVA'];
			$tauxpsa=$rslt2['PSA'];
			$exercice=$rslt2['EXERCICE'];
			$tca=$rslt2['TAUXCACORRESPONDANT'];
			$tac=$rslt2['TAUXACOMPTEIB'];
			$prec=$rslt2['PRECOMPTE'];
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultation du brouillard des ventes</title>
</head>

<body>
<table id='liste' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="12"><h3>Brouillard des Ventes</h3></td>
          </tr>
          <tr align="center" bgcolor="#CCCCCC">
                <td colspan="12"><h5>Période Du : <?php echo dateFormatFrancais($Debut); ?> Au : <?php echo dateFormatFrancais($Fin); ?></h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center" ><h5>N° Ordre </h5> </td>
                <td align="center" ><h5>Facture </h5> </td>
                <td align="center" ><h5>TVA Liduide Nu</h5> </td>
                <td align="center" width="8%"><h5>PSA </h5> </td>
				<td align="center" width="10%"><h5>Liquide HT </h5></td>
                <td align="center" ><h5>CA Correspondant </h5></td>
                <td align="center" ><h5>I.B. TVA <?php echo $tva; ?>% </h5></td>
                <td align="center" ><h5>I.B. Acompte <?php echo $tac; ?>% </h5></td>
                <td align="center" ><h5>I.N. TVA <?php echo $tva; ?>% </h5></td>
                <td align="center" ><h5>I.N. Acompte <?php echo $tac; ?>% </h5></td>
                <td align="center" ><h5>Precompte <?php echo $prec; ?>% </h5></td>
                <td align="center" ><h5>Montant à Verser</h5></td>
			</tr>
            
<?php
$couleur = "darkgray";
$i = 0;
$n=1;
$nbre=0;
$liquideht=0;
$psa=0;
$tvaliquidenu=0;
$ca=0;
$tvaib=0;
$acompteib=0;
$tvain=0;
$precompte=0;
$acomptein=0;
$TTimpot=0;
$TTliquideht=0;
$TTpsa=0;
$TTtvaliquidenu=0;
$TTca=0;
$TTtvaib=0;
$TTacompteib=0;
$TTtvain=0;
$TTacomptein=0;
$TTprecompte=0;
$sql='SELECT ID_APPRO, LIQUIDEHT FROM APPROVISIONNEMENT  WHERE DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY DATE_APPRO' ;
$reponse= $DataBase->query($sql);
while($rslt= $reponse->fetch())
		{
			$liquideht=$rslt['LIQUIDEHT'];
			$tvaliquidenu=$tva*$liquideht/100;
			$psa=$tauxpsa*$liquideht/100;
			$ca=$liquideht+($tca*$liquideht/100);
			$tvaib=$tva*$ca/100;
			$acompteib=$tac*$ca/100;
			$tvain=$tvaib-$tvaliquidenu;
			$acomptein=$acompteib-$psa;
			$precompte=$ca*$prec/100;
			if ($i%2 == 0)
					$couleur = '#CCCCCC';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                		<td  align="center"> <?php echo $n; ?> </td>
                        <td  align="center"> <?php echo $rslt['ID_APPRO']; ?> </td>
                        <td  align="center"> <?php echo number_format($tvaliquidenu, 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($psa, 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($liquideht, 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($ca, 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($tvaib, 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($acompteib, 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($tvain, 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($acomptein, 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($precompte, 0, ',', ' '); ?> </td>
                        <td align="center"><?php echo number_format($tvain+$acomptein+$precompte, 0, ',', ' '); ?><h5></h5></td>
                     </tr>
                <?php
				$i++;
				$n++;
				$nbre++;
				
				$TTliquideht=$TTliquideht+$liquideht;
				$TTpsa=$TTpsa+$psa;
				$TTtvaliquidenu=$TTtvaliquidenu+$tvaliquidenu;
				$TTca=$TTca+$ca;
				$TTtvaib=$TTtvaib+$tvaib;
				$TTacompteib=$TTacompteib+$acompteib;
				$TTtvain=$TTtvain+$tvain;
				$TTprecompte=$TTprecompte+$precompte;
				$TTacomptein=$TTacomptein+$acomptein;
		 }
?>
<tr>
	<td colspan="2" align="center"><h5>Totaux : </h5></td></a>
	<td align="center"><h5><?php echo number_format($TTtvaliquidenu, 0, ',', ' '); ?></h5></td>
    <td align="center"><h5><?php echo number_format($TTpsa, 0, ',', ' '); ?></h5></td>
    <td align="center"><h5><?php echo number_format($TTliquideht, 0, ',', ' '); ?></h5></td>
    <td align="center"><h5><?php echo number_format($TTca, 0, ',', ' '); ?></h5></td>
    <td align="center"><h5><?php echo number_format($TTtvaib, 0, ',', ' '); ?></h5></td>
    <td align="center"><h5><?php echo number_format($TTacompteib, 0, ',', ' '); ?></h5></td>
    <td align="center"><h5><?php echo number_format($TTtvain, 0, ',', ' '); ?></h5></td>
    <td align="center"><h5><?php echo number_format($TTacomptein, 0, ',', ' '); ?></h5></td>
     <td align="center"><h5><?php echo number_format($TTprecompte, 0, ',', ' '); ?></h5></td>
    <td align="center"><h5><?php echo number_format($TTtvain+$TTacomptein+$TTprecompte, 0, ',', ' '); ?></h5></td>
</tr>
<tr>
	<td><a href="Etat_Brouillard_Vte.php?debut=<?php echo $_GET["DateD"];?>&fin=<?php echo $_GET["DateF"];?>"/><input type="button" value="Imprimer" /></td></a>
	<td colspan="12" align="center"><h4>MONTANT IMPOTS :  <?php echo number_format($TTtvain+$TTacomptein+$TTprecompte, 0, ',', ' ').' FCFA' ; ?> </h4></td>
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