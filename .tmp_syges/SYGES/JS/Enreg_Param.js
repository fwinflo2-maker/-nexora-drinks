// JavaScript Document

 function verif_form()
 {

 if(isNaN(document.getElementById('psa').value)||(document.getElementById('psa').value==""))
           {
            alert('La PSA est un numérique!');
			document.getElementById('psa').focus();
			return false;
           }	 
 if(isNaN(document.getElementById('tva').value)||(document.getElementById('tva').value==""))
           {
            alert('La TVA est un numérique!');
			document.getElementById('tva').focus();
			return false;
           }

 if(isNaN(document.getElementById('exercice').value)||(document.getElementById('exercice').value==0)||(document.getElementById('exercice').value==""))
           {
            alert('L\'Exercice encours est un numérique!');
			document.getElementById('exercice').focus();
			return false;
           }
 if(isNaN(document.getElementById('tauxacompteib').value)||(document.getElementById('tauxacompteib').value==""))
           {
            alert('Le taux de l\' acompte Impot Brut est un numérique!');
			document.getElementById('tauxacompteib').focus();
			return false;
           }
 if(isNaN(document.getElementById('tauxremisesht').value)||(document.getElementById('tauxremisesht').value==""))
           {
            alert('Le taux emises ht est un numerique!');
			document.getElementById('tauxremisesht').focus();
			return false;
           }
 if(isNaN(document.getElementById('tauxepargne').value)||(document.getElementById('tauxepargne').value==""))
           {
            alert('Le taux epargne est un numerique!');
			document.getElementById('tauxepargne').focus();
			return false;
           }
 if(isNaN(document.getElementById('tauxcacorrespondant').value)||(document.getElementById('tauxcacorrespondant').value==""))
           {
            alert('Le taux du CA correspondant est un numerique!');
			document.getElementById('tauxcacorrespondant').focus();
			return false;
           }
 if(isNaN(document.getElementById('precompte').value)||(document.getElementById('precompte').value==""))
           {
            alert('Le Precompte est un numerique!');
			document.getElementById('precompte').focus();
			return false;
           }
 if(isNaN(document.getElementById('psaremise').value)||(document.getElementById('psaremise').value==""))
           {
            alert('Le taux du psa remise est un numerique!');
			document.getElementById('psaremise').focus();
			return false;
           }
 if(isNaN(document.getElementById('RetFiscPro').value)||(document.getElementById('RetFiscPro').value==""))
           {
            alert('Le taux Ret. Fisc. Pro. remise est un numerique!');
			document.getElementById('RetFiscPro').focus();
			return false;
           }
 if(isNaN(document.getElementById('bonuscasse').value)||(document.getElementById('bonuscasse').value==""))
           {
            alert('Le taux bonus casse est un numerique!');
			document.getElementById('bonuscasse').focus();
			return false;
           }
 if(isNaN(document.getElementById('depotgarantie').value)||(document.getElementById('depotgarantie').value==""))
           {
            alert('Le taux depot de garantie est un numerique!');
			document.getElementById('depotgarantie').focus();
			return false;
           }
 if(isNaN(document.getElementById('tauxristournesht').value)||(document.getElementById('tauxristournesht').value==""))
           {
            alert('Le taux Ristournes HT est un numerique!');
			document.getElementById('tauxristournesht').focus();
			return false;
           }
 if(isNaN(document.getElementById('psaristournes').value)||(document.getElementById('psaristournes').value==""))
           {
            alert('Le taux psa ristournes est un numerique!');
			document.getElementById('psaristournes').focus();
			return false;
           }
 if(isNaN(document.getElementById('annuel').value)||(document.getElementById('annuel').value==0)||(document.getElementById('annuel').value==""))
           {
            alert('L\'Objectif Annuel est un numerique different de Zero!');
			document.getElementById('annuel').focus();
			return false;
           }
 if(isNaN(document.getElementById('janv').value)||(document.getElementById('janv').value==0)||(document.getElementById('janv').value==""))
           {
            alert('L\'Objectif de Janvier est un numerique different de Zero!');
			document.getElementById('janv').focus();
			return false;
           }
 if(isNaN(document.getElementById('fevr').value)||(document.getElementById('fevr').value==0)||(document.getElementById('fevr').value==""))
           {
            alert('L\'Objectif de Fevrier est un numerique different de Zero!');
			document.getElementById('fevr').focus();
			return false;
           }
 if(isNaN(document.getElementById('mars').value)||(document.getElementById('mars').value==0)||(document.getElementById('mars').value==""))
           {
            alert('L\'Objectif de mars est un numerique different de Zero!');
			document.getElementById('mars').focus();
			return false;
           }
 if(isNaN(document.getElementById('avri').value)||(document.getElementById('avri').value==0)||(document.getElementById('avri').value==""))
           {
            alert('L\'Objectif d\'avril est un numerique different de Zero!');
			document.getElementById('avri').focus();
			return false;
           }
 if(isNaN(document.getElementById('mai').value)||(document.getElementById('mai').value==0)||(document.getElementById('mai').value==""))
           {
            alert('L\'Objectif de mai est un numerique different de Zero!');
			document.getElementById('mai').focus();
			return false;
           }
 if(isNaN(document.getElementById('juin').value)||(document.getElementById('juin').value==0)||(document.getElementById('juin').value==""))
           {
            alert('L\'Objectif de juin est un numerique different de Zero!');
			document.getElementById('juin').focus();
			return false;
           }
 if(isNaN(document.getElementById('juil').value)||(document.getElementById('juil').value==0)||(document.getElementById('juil').value==""))
           {
            alert('L\'Objectif de juillet est un numerique different de Zero!');
			document.getElementById('juil').focus();
			return false;
           }
 if(isNaN(document.getElementById('aout').value)||(document.getElementById('aout').value==0)||(document.getElementById('aout').value==""))
           {
            alert('L\'Objectif d\'aout est un numerique different de Zero!');
			document.getElementById('aout').focus();
			return false;
           }
 if(isNaN(document.getElementById('sept').value)||(document.getElementById('sept').value==0)||(document.getElementById('sept').value==""))
           {
            alert('L\'Objectif de septembre est un numerique different de Zero!');
			document.getElementById('sept').focus();
			return false;
           }
 if(isNaN(document.getElementById('octo').value)||(document.getElementById('octo').value==0)||(document.getElementById('octo').value==""))
           {
            alert('L\'Objectif d\'octobre est un numerique different de Zero!');
			document.getElementById('octo').focus();
			return false;
           }
 if(isNaN(document.getElementById('nove').value)||(document.getElementById('nove').value==0)||(document.getElementById('nove').value==""))
           {
            alert('L\'Objectif de novembre est un numerique different de Zero!');
			document.getElementById('nove').focus();
			return false;
           }
 if(isNaN(document.getElementById('dece').value)||(document.getElementById('dece').value==0)||(document.getElementById('dece').value==""))
           {
            alert('L\'Objectif de decembre est un numerique different de Zero!');
			document.getElementById('dece').focus();
			return false;
           }
	return true;
 }
