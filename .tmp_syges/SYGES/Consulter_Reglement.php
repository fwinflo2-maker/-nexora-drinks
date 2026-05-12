<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"))
{
	include("Connexion.php");
	include("fonctions.php");
	$sql2 = " select *  from client c, sortie_stock st where c.id_client=st.id_client and st.id_sortiestock='".$_GET['Id']."'";
	$reponse2= $DataBase->query($sql2);
	$rslt2= $reponse2->fetch();
	
//on recupere le mt restant et de la vente dans le dernier paiement
$reste=0;
$sql5='SELECT MAX(ID_REGLEMENT) AS ID FROM REGLEMENT WHERE ID_SORTIESTOCK="'.$_GET['Id'].'" ' ;
$reponse5= $DataBase->query($sql5);
while($rslt5= $reponse5->fetch())
	{
		$sql6='SELECT MTRESTANT, MONTANT FROM REGLEMENT WHERE ID_REGLEMENT="'.$rslt5['ID'].'" ' ;
		$reponse6= $DataBase->query($sql6);
		while($rslt6= $reponse6->fetch())
		{
			$reste=$rslt6['MTRESTANT'];
			$montant=$rslt6['MONTANT'];
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire  de consultation des reglements d'une facture .</title>
<style type="text/css">
label
{
	display:block;
	width:110px;
	float: left;
	}
</style>
</head>
 
<body>

<form action="CTRL/Ctrl_Mod_Vente.php" method="post">
<fieldset style=" width:1050px;"><legend>Informations sur la vente</legend>
<table >
<tr>
	<td><label for="codevente"> Code  </label></td>
    <td><input type="text" id="codevente" name="codevente" readonly="readonly" style="width:300px; background-color:#ECECEC;" value="<?php echo $rslt2['ID_SORTIESTOCK']; ?>"/></td>
    <td><label for="date_vente"> Date vente  </label> </td>
    <td><input type="text" id="date_vente" name="date_vente" style="width:200px; background-color:#ECECEC" value="<?php echo dateFormatFrancais($rslt2['DATESORTIESTOCK']); ?>" readonly="readonly"/></td>
 	<td><label for="date_vente"> Montant Totale  </label> </td>
    <td><input type="text" id="date_vente" name="date_vente" style="width:200px; background-color:#ECECEC" value="<?php echo $montant; ?>" readonly="readonly"/></td>
</tr>
<tr>
    <td><label for="codeclient"> Client * </label></td>
	<td><input type="text" id="codeclient" name="codeclient" readonly="readonly" style="width:300px; background-color:#ECECEC" value="<?php echo $rslt2['NOM'].' ('.$rslt2['ID_CLIENT'].')'; ?>"/></td>

	<td><label for="observationsortie"> Observation  </label></td>
    <td><input type="text" id="observationsortie" name="observationsortie" maxlength="35" style="width:200px; background-color:#ECECEC" value="<?php echo $rslt2['OBSERVATION']; ?>" readonly="readonly" /></td>
	<td><label for="mtrestant"> Montant Restant  </label></td>
    <td><input type="text" id="mtrestant" name="mtrestant" maxlength="35" style="width:200px; background:#F00;"  value="<?php echo $reste; ?>" readonly="readonly" /></td>
</tr>

</table>
</fieldset>
<table>
<tr>
		<td align="center" > <a href="Historique_Reglement.php?Vte=<?php echo $rslt2['ID_SORTIESTOCK'];?>"> <input type="button" name="historiquereg" id="historiquereg" value="Imprimer Historique" style="margin-left:10px; background:#F00;"/> </a></td>

	<td align="center" > <a href="index.php?formulaire=Ajout_Reglement&Vte=<?php echo $rslt2['ID_SORTIESTOCK'];?>"> <input type="button" name="ajoutart" id="ajoutart" value="Nouveau Reglement" style="margin-left:700px; width:210px"/> </a></td>
</tr>
</table>
<table id='treglement' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="7"><h5>Historique des Reglements</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	<td align="center"><h5>N° </h5></td>
                <td  align="center"><h5>Date </h5></td>
                <td  align="center" ><h5>MT Facture </h5></td>
                <td  align="center" ><h5>Avance </h5></td>
                <td  align="center" ><h5> MT Restant </h5></td>
                <td  align="center" ><h5>Statut</h5> </td>
                <td  align="center" ><h5>Utilisateur</h5> </td>
			</tr>
			
<?php
$couleur = "darkgray";
$i = 1;
$MT=0;			
$sql7='select * from reglement where id_sortiestock="'.$_GET['Id'].'" order by id_reglement asc' ;
$reponse7= $DataBase->query($sql7);
while($rslt7= $reponse7->fetch())
		{
			if ($i%2 == 0)
					$couleur = '#E0E0E0';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                        <td  align="center"> <?php echo $i; ?> </td>
                        <td  align="center"> <?php echo dateFormatFrancais($rslt7['DATEAVANCE']); ?> </td>
                        <td  align="center"> <?php echo number_format($rslt7['MONTANT'], 0, ',', ' ').' FCFA'; ?> </td>
                        <td  align="center"> <?php echo number_format($rslt7['MTAVANCE'], 0, ',', ' ').' FCFA'; ?> </td>
                        <td  align="center"> <?php echo number_format($rslt7['MTRESTANT'], 0, ',', ' ').' FCFA'; ?> </td>
                        <td  align="center"> <?php echo $rslt7['STATUT']; ?> </td>
                        <td  align="center"> <?php echo $rslt7['USER']; ?> </td>
                     </tr>
                <?php
				$i++;
				$MT=$MT+$rslt7['MTAVANCE'];
		 }
				?>
 <tr bgcolor="#E0E0E0"> 
    <td align="center" colspan="8"><h4>Montant Total Reglement(s) :  <?php echo number_format($MT, 0, ',', ' ').' Franc CFA'; ?></h4></td>
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
