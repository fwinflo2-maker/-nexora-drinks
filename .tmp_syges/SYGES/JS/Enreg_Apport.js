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
	
  if((document.getElementById('Code').value==""))
           {
            alert('BV saisir le Code.');
			document.getElementById('Code').focus();
			return false;
           }
 
  if((document.getElementById('Libelle').value==""))
           {
            alert('BV saisir la Libelle');
			document.getElementById('Libelle').focus();
			return false;
           }
 if(isNaN(document.getElementById('Montant').value)||(document.getElementById('Montant').value=="")||(document.getElementById('Montant').value==0))
           {
            alert('Le Montant est un numérique et diff de zero!');
			document.getElementById('Montant').focus();
			return false;
           }
   if(!verif_date(document.getElementById('Date').value))
           {
            alert('Date de la charge.');
			document.getElementById('Date').focus();
			return false;
           }
	return true;
 }
