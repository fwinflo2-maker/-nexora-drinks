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
	
 if(isNaN(document.getElementById('mt').value)||(document.getElementById('mt').value=="")||(document.getElementById('mt').value==0))
           {
            alert('Le montant de la consigne est un numérique different de zero!');
			document.getElementById('mt').focus();
			return false;
           }
 if(isNaN(document.getElementById('qte').value)||(document.getElementById('qte').value=="")||(document.getElementById('qte').value==0))
           {
            alert('Le quantite des emballages est un numérique different de zero!');
			document.getElementById('mt').focus();
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
