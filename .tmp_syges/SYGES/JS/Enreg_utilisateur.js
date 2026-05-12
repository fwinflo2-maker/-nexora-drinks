// JavaScript Document

 function verif_form()
 {	 
	
  if((document.getElementById('nom').value==""))
           {
            alert('BV saisir le Nom.');
			document.getElementById('nom').focus();
			return false;
           }
 
  if((document.getElementById('Login').value==""))
           {
            alert('BV saisir le Login.');
			document.getElementById('Login').focus();
			return false;
           }
  if((document.getElementById('MDP').value==""))
           {
            alert('MDP');
			document.getElementById('BV saisir le mot de passe.').focus();
			return false;
           }	
		   
 if(document.getElementById('ConMDP').value=="")
           {
            alert('BV saisir la confirmation du Mot de passe ');
			document.getElementById('ConMDP').focus();
			return false;
           }
   if(document.getElementById('ConMDP').value != document.getElementById('MDP').value)
           {
            alert('Le Mot de Passe est différent de la Confirmation.');
			document.getElementById('ConMDP').focus();
			return false;
           }
	return true;
 }
