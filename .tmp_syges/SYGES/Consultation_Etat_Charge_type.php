<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" )||($_SESSION['habilitation']=="Caissier"  || $_SESSION['habilitation']=="Comptable" ))
{
	include('Connexion.php');
	include('fonctions.php');
	$Debut=dateFormatAnglais($_GET['DateD']);
	$Fin=dateFormatAnglais($_GET['DateF']);	
	//on recupere le libelle charge
	$sql2='SELECT LIBELLE FROM TYPE_CHARGE WHERE ID_TYPECHARGE="'.$_GET['Chg'].'"';
	$reponse2= $DataBase->query($sql2);
		while($rslt2= $reponse2->fetch())
		{
		$libelle=$rslt2['LIBELLE'];
	    }
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultation de L'etat des charges</title>
</head>

<body>
<table id='liste' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="8"><h3>Etat des charges  </h3></td>
          </tr>
          <tr align="center" bgcolor="#CCCCCC">
          		<td colspan=""><h5>Période  </h5></td>
                <td colspan=""><h5>Du : <?php echo dateFormatFrancais($Debut); ?> </h5></td>
          		<td colspan=""><h5>Au : <?php echo dateFormatFrancais($Fin); ?> </h5></td>
                <td colspan=""><h5>Type Charge : </h5></td>
                <td colspan="4" align="left"><h5><?php echo $libelle; ?></h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center" width="10%"><h5>Date  </h5> </td>
                <td align="center" width="17%"><h5>Libelle </h5> </td>
                <td align="center" width="25%"><h5>Description </h5> </td>
                <td align="center" width="25%"><h5>Montant </h5> </td>
                <td  align="center"><h5>Statut </h5></td>
			</tr>
            
<?php
$couleur = "darkgray";
$i = 0;
$mt=0;
$sql='SELECT C.ID_CHARGE, TC.LIBELLE, TC.ID_TYPECHARGE, C.ID_TYPECHARGE, C.DESCRIPTION, C.MONTANT, C.DATE_CHARGE, C.STATUT FROM CHARGE C, TYPE_CHARGE TC WHERE C.ID_TYPECHARGE=TC.ID_TYPECHARGE AND TC.ID_TYPECHARGE="'.$_GET['Chg'].'" AND C.DATE_CHARGE BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY C.ID_CHARGE' ;
$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			if ($i%2 == 0)
					$couleur = '#CCCCCC';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                		<td  align="center"> <?php echo dateFormatFrancais($rslt['DATE_CHARGE']); ?> </td>
                        <td  align="center"> <?php echo $rslt['LIBELLE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['DESCRIPTION']; ?> </td>
                        <td  align="center"> <?php echo $rslt['MONTANT'].' FCFA'; ?> </td>
                        <td  align="left"> <?php echo $rslt['STATUT']; ?> </td>
                     </tr>
                <?php
				$i++;
				$mt=$mt+$rslt['MONTANT'];
		 }
?>
<tr>
	<td><a href="Etat_Charge_Type.php?debut=<?php echo $_GET["DateD"];?>&fin=<?php echo $_GET["DateF"];?>&Chg=<?php echo $_GET["Chg"];?>"/><input type="button" value="Imprimer" /></td></a>
	<td colspan="8" align="center"><h4>Montant  :  <?php echo number_format($mt, 0, ',', ' ').' FCFA'; ?>  FCFA </h4></td>
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