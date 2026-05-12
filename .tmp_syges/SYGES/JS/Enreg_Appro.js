// JavaScript Document
function verif_date(input)
{
var regex = new RegExp("[/-]");
var date = input.split(regex);
var nbJours = new Array('',31,28,31,30,31,30,31,31,30,31,30,31);
var result = true;

if ( date['2']%4 == 0 && date['2']%100 > 0 || date['2']%400 == 0 )
nbJours['2'] = 29;

if( isNaN(date['2']) )
result=false;

if ( isNaN(date['1']) || date['1'] > 12 || date['1'] < 1 )
result=false;

if ( isNaN(date['0']) || date['0'] > nbJours[Math.round(date['1'])] || date['0'] < 1 )
result=false;

return result;
}
 function verif_form()
 {
  if(document.getElementById('codefournisseur').value=="")
           {
            alert('BV Definir du codefournisseur.');
			document.getElementById('codefournisseur').focus();
			return false;
           }	 
	
  if(document.getElementById('codeappro').value=="")
           {
            alert('BV saisir le code de l\'appro.');
			document.getElementById('codeappro').focus();
			return false;
           }

 if(isNaN(document.getElementById('liquideht').value)||(document.getElementById('liquideht').value=="")||(document.getElementById('liquideht').value==0))
           {
            alert('Le Montant Liquide HT est un numérique et diff de zero!');
			document.getElementById('liquideht').focus();
			return false;
           }
 if(isNaN(document.getElementById('nbrecolis').value)||(document.getElementById('nbrecolis').value=="")||(document.getElementById('nbrecolis').value==0))
           {
            alert('Le Nombre de colis est un numérique et diff de zero!');
			document.getElementById('nbrecolis').focus();
			return false;
           }
   if(!verif_date(document.getElementById('date_appro').value))
           {
            alert('Date de l\'appro  incorrecte.');
			document.getElementById('date_appro').focus();
			return false;
           }

  if(document.getElementById('codeart').value=="")
           {
            alert('BV choisir le code de l\'article.');
			document.getElementById('codeart').focus();
			return false;
           }	
		   
 if(isNaN(document.getElementById('qterecu').value)||(document.getElementById('qterecu').value=="")||(document.getElementById('qterecu').value==0))
           {
            alert('Le quantite recu article 1 est un numérique diff de zero!');
			document.getElementById('qterecu').focus();
			return false;
           }	
 if(isNaN(document.getElementById('qterecu2').value))
           {
            alert('Le quantite recu article 2 est un numérique diff de zero!');
			document.getElementById('qterecu2').focus();
			return false;
           }
 if(isNaN(document.getElementById('qterecu3').value))
           {
            alert('Le quantite recu article 3 est un numérique diff de zero!');
			document.getElementById('qterecu3').focus();
			return false;
           }
  if((document.getElementById('codeart').value==document.getElementById('codeart2').value)&&(document.getElementById('qterecu2').value!=""))
           {
            alert('Article 1 identique a  l\'article 2.');
			document.getElementById('codeart2').focus();
			return false;
           }
  if((document.getElementById('codeart2').value==document.getElementById('codeart3').value)&&(document.getElementById('qterecu3').value!="")&&(document.getElementById('qterecu2').value!=""))
           {
            alert('Article 2 identique a  l\'article 3.');
			document.getElementById('codeart3').focus();
			return false;
           }
if((document.getElementById('codeart').value==document.getElementById('codeart3').value)&&(document.getElementById('qterecu3').value!=""))
           {
            alert('Article 1 identique a  l\'article 3.');
			document.getElementById('codeart3').focus();
			return false;
           }
 	return true;
 }
