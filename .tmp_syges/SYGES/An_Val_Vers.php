<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur"))
{
	include("Connexion.php");
	include("fonctions.php");
	$sql2 = " select *  from versement  where num_vers='".$_GET['Vers']."'";
	$reponse2= $DataBase->query($sql2);
	$rslt2= $reponse2->fetch();
	$sql = " select *  from user  where login='".$_GET['VD']."'";
	$reponse= $DataBase->query($sql);
	$rslt= $reponse->fetch();
	

?>
<!DOCTYPE html PUBLIC >
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire  d'Annulation de la validation d'un Versement.</title>
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

<form action="CTRL/Ctrl_An_Val_Vers.php" method="post">
<fieldset style=" width:1050px;"><legend>Informations sur le Versement à Annuler</legend>
<table >
<tr>
	<td><label for="num_vers"> N° Versement * </label></td>
    <td><input type="text" id="num_vers" name="num_vers" readonly="readonly" style="width:200px; background-color:#ECECEC" value="<?php echo $rslt2['NUM_VERS']; ?>"/></td>
    <td><label for="montant"> Montant * </label></td>
    <td><input type="text" id="montant" name="montant" style="width:200px; background-color:#ECECEC;" readonly="readonly" value="<?php echo number_format($rslt2['MONTANT'], 0, ',', ' '); ?>"></td>
</tr>
<tr>
   <td><label for="vendeur"> Vendeur * </label></td>
    <td><input type="text" id="vendeur" name="vendeur"  value="<?php echo $rslt['NOM']; ?>" readonly="readonly" style="width:200px; background:#ECECEC;"/></td>
    <td><label for="date"> Date  * </label> </td>
    <td><input type="text" id="date" name="date" style="width:200px; background-color:#ECECEC;" value="<?php echo dateFormatFrancais($rslt2['DATE_VERS']); ?>" readonly="readonly"/></td>
	<td><label for="observation"> Observation  </label></td>
    <td><input type="text" id="observation" name="observation" maxlength="" style="width:200px; background-color:#ECECEC;" value="<?php echo $rslt2['OBSERVATION']; ?>" readonly="readonly"/></td>
</tr>
</table>
</fieldset>
<table>
<tr>
    <td align="center" > <input type="submit" name="Annuler" id="Annuler" value="Annuler" style="margin-left:850px; background:#F00;"/></td>
</tr>
</table>
<table id='tarticle' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="3"><h5>Liste des Emballages</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	
            	<td align="center" width="20%"><h5>Code </h5></td>
                <td  align="center"><h5> Libelle</h5></td>
                <td  align="center" ><h5>Qte </h5></td>
			</tr>
			
<?php
$couleur = "darkgray";
$i = 0;	
$ttemb=0;
$sql='SELECT  E.ID_EMBALLAGE, E.LIBELLE, EV.QTE FROM EMBALLAGE E, EMBALLAGE_VERS EV WHERE E.ID_EMBALLAGE= EV.ID_EMBALLAGE AND EV.NUM_VERS="'.$_GET['Vers'].'" ' ;
$reponse= $DataBase->query($sql);
while($rslt3= $reponse->fetch())
		{
			if ($i%2 == 0)
					$couleur = '#E0E0E0';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                        <td  align="center"> <?php echo $rslt3['ID_EMBALLAGE']; ?> </td>
                        <td  align="center"> <?php echo $rslt3['LIBELLE']; ?> </td>
                        <td  align="center"> <?php echo $rslt3['QTE']; ?> </td>
                     </tr>
                <?php
				$i++;
				$ttemb=$ttemb+$rslt3['QTE'];
		 }
				?>
 <tr bgcolor="#E0E0E0"> 
    <td align="center" colspan="3"><h4> Total Emballage (s):  <?php echo number_format($ttemb, 0, ',', ' '); ?></h4></td>
</tr>
</table>
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
