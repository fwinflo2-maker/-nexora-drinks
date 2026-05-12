<?php
	include("Connexion.php");
?>
    <td colspan="2" align="right"><select name="typecharge" id="typecharge" style="width:200px;">
     <?php
	 $sql = " select id_typecharge,libelle from type_charge  where libelle like '%".$_POST['tcharge']."%' and statut='Actif' order by libelle  ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		 echo "<option value='".$rslt["id_typecharge"]."' >";
		 echo $rslt["libelle"];
		 echo '</option>';
		 }
		 ?>
    </select> </td>

