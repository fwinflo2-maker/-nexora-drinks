<?php
if (isset ($_SESSION['habilitation']))
{
	include("Connexion.php");
	include("fonctions.php");
	$sql2 = " select *  from mouvementar  where id_operation='".$_GET['Vte']."' and heure='".$_GET['Hre']."'";
	$reponse2= $DataBase->query($sql2);
	$rslt2= $reponse2->fetch();
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultation Annulation.</title>
<style type="text/css">
label
{
	display:block;
	width:120px;
	float: left;
	}
</style>
</head>
 
<body>

<form method="post">
<fieldset style=" width:1050px;"><legend>Informations sur l'operation</legend>
<table >
<tr>
	<td><label for="codevente"> Code * </label></td>
    <td><input type="text" id="codevente" name="codevente" readonly="readonly" style="width:200px; background-color:#ECECEC" value="<?php echo $rslt2['ID_OPERATION']; ?>"/></td>
	<td><label for="date_vente"> Date Suppression * </label> </td>
    <td><input type="text" id="date_vente" name="date_vente" style="width:200px; background-color:#ECECEC;" value="<?php echo dateFormatFrancais($rslt2['DATE_ANN']).' à '.$rslt2['HEURE']; ?>" readonly="readonly"/></td>
	<td><label for="date_vente"> Operateur Suppression * </label> </td>
    <td><input type="text" id="date_vente" name="date_vente" style="width:200px; background-color:#ECECEC;" value="<?php echo $rslt2['USER']; ?>" readonly="readonly"/></td>
</tr>
<tr>
   <td><label for="Detenteur"> Detenteur * </label></td>
    <td><input type="text" id="Detenteur" name="Detenteur"  value="<?php echo $rslt2['DETENTEUR']; ?>" readonly="readonly" style="width:200px; background:#ECECEC;"/></td>
    <td><label for="date_vente"> Date Operation * </label> </td>
    <td><input type="text" id="date_vente" name="date_vente" style="width:200px; background-color:#ECECEC;" value="<?php echo dateFormatFrancais($rslt2['DATE']); ?>" readonly="readonly"/></td>
	<td><label for="observationsortie"> Observation  </label></td>
    <td><input type="text" id="observationsortie" name="observationsortie" maxlength="" style="width:200px; background-color:#ECECEC;" value="<?php echo $rslt2['OPERATION']; ?>" readonly="readonly"/></td>
</tr>
</table>
</fieldset>
<table>
<tr>
    <td align="center" > <a href="Recu_Ann.php?Vte=<?php echo $_GET['Vte']; ?>&Hre=<?php echo $_GET['Hre']; ?>"> <input type="button" name="Imprimer" id="Imprimer" value="Imprimer" style="margin-left:850px; background:#F00;"/> </a></td>
</tr>
</table>
<table id='tarticle' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="8"><h5>Liste des Articles</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	
            	<td align="center" width="20%"><h5>Code </h5></td>
                <td  align="center"><h5> Libelle</h5></td>
                <td  align="center" ><h5> Conditionnement</h5></td>
                <td  align="center" ><h5>Qte </h5></td>
                <td  align="center" ><h5>Stock Avant </h5></td>
                <td  align="center" ><h5>Stock Apres </h5></td>
			</tr>
			
<?php
$couleur = "darkgray";
$i = 0;
$MT=0;	
$ttcolis=0;
$nbrecasier=0;
$sql='SELECT  A.ID_ARTICLE,A.LIBELLE, A.MARQUE,A.NBREBTE, M.QTE, M.SI, M.SF FROM  MOUVEMENTAR M, ARTICLE A WHERE A.ID_ARTICLE=M.ID_ARTICLE AND M.ID_OPERATION ="'.$_GET['Vte'].'" AND M.HEURE="'.$_GET['Hre'].'" AND  M.OPERATION LIKE "AN%"' ;
	$reponse= $DataBase->query($sql);
		while($rslt3= $reponse->fetch())
		{
			if ($i%2 == 0)
					$couleur = '#E0E0E0';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                        <td  align="center"> <?php echo $rslt3['ID_ARTICLE']; ?> </td>
                        <td  align="center"> <?php echo $rslt3['LIBELLE']; ?> </td>
                        <td  align="center"> <?php echo $rslt3['MARQUE'].' '.$rslt3['NBREBTE']; ?> </td>
                        <td  align="center"> <?php echo number_format($rslt3['QTE'], 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($rslt3['SI'], 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($rslt3['SF'], 0, ',', ' '); ?> </td>
                     </tr>
                <?php
				$i++;
				$ttcolis=$ttcolis+$rslt3['QTE'];
								 				//on compte les casiers
				if(($rslt3['MARQUE']=="CASIER") || ($rslt3['MARQUE']=="casier")|| ($rslt3['MARQUE']=="CASIERS")|| ($rslt3['MARQUE']=="casiers"))
				{
					$nbrecasier=$nbrecasier+$rslt3['QTE'];
				}
				
		 }
				?>
 <tr bgcolor="#E0E0E0"> 
        <td align="center" colspan="3"><h4> Total Colis :  <?php echo number_format($ttcolis, 0, ',', ' '); ?></h4></td>
    <td align="center" colspan="3"><h4> Total Casiers :  <?php echo number_format($nbrecasier, 0, ',', ' '); ?></h4></td>
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
