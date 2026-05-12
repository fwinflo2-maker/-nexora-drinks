// JavaScript Document

 function verif_form()
 {
  if(document.getElementById('code').value=="")
           {
            alert('BV Definir le code.');
			document.getElementById('code').focus();
			return false;
           }	 
	
  if(document.getElementById('libelle').value=="")
           {
            alert('BV saisir le libelle.');
			document.getElementById('libelle').focus();
			return false;
           }

  if(document.getElementById('marque').value=="")
           {
            alert('BV definir la conditionnement.');
			document.getElementById('marque').focus();
			return false;
           }	
 if(isNaN(document.getElementById('nbrebte').value)||(document.getElementById('nbrebte').value=="")||(document.getElementById('nbrebte').value==0))
           {
            alert('Le nombre de bouteille est un numérique different de zero!');
			document.getElementById('nbrebte').focus();
			return false;
           }
 if(isNaN(document.getElementById('prixrevient').value)||(document.getElementById('prixrevient').value=="")||(document.getElementById('prixrevient').value==0))
           {
            alert('Le prix de revient est un numérique different de zero!');
			document.getElementById('prixrevient').focus();
			return false;
           }
 if(isNaN(document.getElementById('prixvente').value)||(document.getElementById('prixvente').value==0)||(document.getElementById('prixrevient').value==""))
           {
            alert('Le prix de vente est un numérique different de zero!');
			document.getElementById('prixvente').focus();
			return false;
           }
 if(isNaN(document.getElementById('prixdetail').value)||(document.getElementById('prixdetail').value==0)||(document.getElementById('prixdetail').value==""))
           {
            alert('Le prix de vente au detail est un numérique different de zero!');
			document.getElementById('prixdetail').focus();
			return false;
           }
 if(isNaN(document.getElementById('tauxremise').value)||(document.getElementById('tauxremise').value==""))
           {
            alert('Le taux remise HT est un numérique different de zero!');
			document.getElementById('tauxremise').focus();
			return false;
           }
 if(isNaN(document.getElementById('tauxristourne').value)||(document.getElementById('tauxristourne').value==""))
           {
            alert('Le taux ristourne est un numérique.');
			document.getElementById('tauxristourne').focus();
			return false;
           }
  if(document.getElementById('famille').value=="")
           {
            alert('BV saisir le famille.');
			document.getElementById('famille').focus();
			return false;
           }
 if(document.getElementById('statut').value=="")
           {
            alert('BV choisir le statut.');
			document.getElementById('statut').focus();
			return false;
           }
		 
	return true;
 }
