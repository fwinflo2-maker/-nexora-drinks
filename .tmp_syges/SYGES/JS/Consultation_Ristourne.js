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
function concat_date(input)
{
var regex = new RegExp("[/-]");
var date = input.split(regex);
var result=0;
result= date['2']+ date['1']+ date['0'];
return result;
}

 function verif_form()
 {

   if(!verif_date(document.getElementById('DateD').value))
           {
            alert('Date de debut de la période incorrect.');
			document.getElementById('DateD').focus();
			return false;
           }

   if(!verif_date(document.getElementById('DateF').value))
           {
            alert('Date de fin de la période incorrect.');
			document.getElementById('DateF').focus();
			return false;
           }
if (concat_date(document.getElementById('DateD').value) > concat_date(document.getElementById('DateF').value))
	{
			alert('Date de fin antérieure à la date de début.');
			document.getElementById('DateF').focus();
			return false;
	}
 if(isNaN(document.getElementById('Retfrigo').value)||(document.getElementById('Retfrigo').value==""))
           {
            alert('La Ret. Frigo est un numérique.');
			document.getElementById('Retfrigo').focus();
			return false;
           }
 if(isNaN(document.getElementById('RetDA').value)||(document.getElementById('RetDA').value==""))
           {
            alert('La Ret. Droit d\'Auteur est un numérique.');
			document.getElementById('RetDA').focus();
			return false;
           } 
if(isNaN(document.getElementById('RetCGA').value)||(document.getElementById('RetCGA').value==""))
           {
            alert('La Ret. CGA est un numérique.');
			document.getElementById('RetCGA').focus();
			return false;
           } 
if(isNaN(document.getElementById('RegRistourne').value)||(document.getElementById('RegRistourne').value==""))
           {
            alert('La Reg. Ristourne est un numérique.');
			document.getElementById('RegRistourne').focus();
			return false;
           }
 if(isNaN(document.getElementById('RegPSAEC').value)||(document.getElementById('RegPSAEC').value==""))
           {
            alert('La Reg. PSA Exercice Encours est un numérique.');
			document.getElementById('RegPSAEC').focus();
			return false;
           } 
 if(isNaN(document.getElementById('RegPSAAnt').value)||(document.getElementById('RegPSAAnt').value==""))
           {
            alert('La Reg. PSA Exercice Anterieur est un numérique.');
			document.getElementById('RegPSAAnt').focus();
			return false;
           } 
if(isNaN(document.getElementById('RegDA').value)||(document.getElementById('RegDA').value==""))
           {
            alert('La Reg. Droit d\'auteur est un numérique.');
			document.getElementById('RegDA').focus();
			return false;
           }
if(isNaN(document.getElementById('RegEntfrigo').value)||(document.getElementById('RegEntfrigo').value==""))
           {
            alert('La Reg. Entretien Frigo est un numérique.');
			document.getElementById('RegEntfrigo').focus();
			return false;
           }
if(isNaN(document.getElementById('RegCGA').value)||(document.getElementById('RegCGA').value==""))
           {
            alert('La Reg. CGA est un numérique.');
			document.getElementById('RegCGA').focus();
			return false;
           }
	return true;
 }// JavaScript Document