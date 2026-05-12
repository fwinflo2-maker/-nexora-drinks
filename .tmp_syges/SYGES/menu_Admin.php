
    <ul class="art-hmenu">
        <li><a href="index.php?formulaire=Accueil">Accueil</a></li>
        
        <li><a href="#">Fichier</a>
        	<ul>
                 <li>
 					<a href="#">Client</a>
                    <ul>
                    	<a href="index.php?formulaire=Enreg_Client">Nouveau</a>
                        <a href="index.php?formulaire=Choisir_Client_Mod">Modifier</a>
                    </ul>
       			</li>

            	 <li>
                 	<a href="#">Article</a>
                    <ul>
                    	<a href="index.php?formulaire=Enreg_Article">Nouveau</a>
                        <a href="index.php?formulaire=Choisir_Article_Mod">Modifier</a>
                    </ul>
                 </li>
            	 <li>
                 	<a href="#">Famille</a>
                    <ul>
                    	<a href="index.php?formulaire=Enreg_Famille">Nouveau</a>
                        <a href="index.php?formulaire=Choisir_Famille_Mod">Modifier</a>
                    </ul>
                 </li>
                 
			   <?php
              if ($_SESSION['login']!="CC")
              {
              ?>	
                  <li><a href="index.php?formulaire=Tarifaire">Tarifaire</a></li>
               <?php
              }
              ?>
            	 <li>
                 	<a href="#">Categorie</a>
                    <ul>
                    	<a href="index.php?formulaire=Enreg_Categorie">Nouveau</a>
                        <a href="index.php?formulaire=Choisir_Cat_Mod">Modifier</a>
                    </ul>
                 </li>
            	 <li>
                 	<a href="#">Emballage</a>
                    <ul>
                    	<a href="index.php?formulaire=Enreg_Emballage">Nouveau</a>
                        <a href="index.php?formulaire=Choisir_Emb_Mod">Modifier</a>
                    </ul>
                 </li>

 			   <?php
              if ($_SESSION['login']!="CC")
              {
              ?>	
                 <li>
                 	<a href="#">Utilisateur</a>
                    <ul>
                    	<a href="index.php?formulaire=Enreg_Utilisateur">Nouveau</a>
                        <a href="index.php?formulaire=Choisir_Utilisateur_Mod">Modifier</a>
                    </ul>
                 </li>
               <?php
              }
              ?>
				<li>
                 	<a href="#">Fournisseur</a>
                    <ul>
                    	<a href="index.php?formulaire=Enreg_Fournisseur">Nouveau</a>
                        <a href="index.php?formulaire=Choisir_Fournisseur_Mod">Modifier</a>
                    </ul>
                 </li>
                 <li><a href="index.php?formulaire=Enreg_Param">Parametres</a></li>
                  <li>
                 	<a href="#">Type Charge</a>
                    <ul>
                    	<a href="index.php?formulaire=Enreg_TCharge">Nouveau</a>
                        <a href="index.php?formulaire=Choisir_TCharge_Mod">Modifier</a>
                    </ul>
                 </li>
           </ul>
        </li>
        <li>
        	<a href="#">Approvisionnement</a>
            <ul>
                <li>
            	<a href="#">Frigo</a>
            		<ul>
                    	<a href="index.php?formulaire=Enreg_Appro_Frigo">Nouveau</a>
                        <a href="index.php?formulaire=Choisir_Appro_Frigo_Mod">Modifier/Valider</a>
                        <a href="index.php?formulaire=Choisir_Appro_Frigo_Supp">Supprimer Instance</a>
                        
					<?php
                      if ($_SESSION['login']!="CC")
                      {
                      ?>	
                          <a href="index.php?formulaire=Choisir_Appro_Frigo_An_Val">Annuler Une Validation</a>
                       <?php
                      }
                   ?>
            		</ul>
           		</li>
                <li>
            	<a href="#">Emballage</a>
            		<ul>
                    	<a href="index.php?formulaire=Enreg_Appro_Emb">Nouveau</a>
                        <a href="index.php?formulaire=Choisir_Appro_Emb_Mod">Modifier/Valider</a>
                        <a href="index.php?formulaire=Choisir_Appro_Emb_Supp">Supprimer Instance</a>
                        
					<?php
                      if ($_SESSION['login']!="CC")
                      {
                      ?>	
                          <a href="index.php?formulaire=Choisir_Appro_Emb_An_Val">Annuler Une Validation</a>
                       <?php
                      }
                   ?>
            		</ul>
           		</li>
                <li>
            	<a href="#">Magasin</a>
            		<ul>
                    	<a href="index.php?formulaire=Enreg_Appro&Fs=FSR00001">Nouveau</a>
                        <a href="index.php?formulaire=Choisir_Appro_Mod">Modifier/Valider</a>
                        <a href="index.php?formulaire=Choisir_Appro_Supp">Supprimer Instance</a>
					<?php
                      if ($_SESSION['login']!="CC")
                      {
                      ?>	
                          <a href="index.php?formulaire=Choisir_Appro_An_Val">Annuler Une Validation</a>
                       <?php
                      }
                   ?>
                        
                        <a href="index.php?formulaire=Choisir_Appro_Mod_LHT">Modifier Liquide HT & Nbre Colis</a>
            		</ul>
           		</li>
            </ul>
        </li>
        <li><a href="#">Vente</a>
			<ul>
                   <li>
                    <a href="#">Frigo</a>
                    	<ul>
                    		<a href="index.php?formulaire=Vente_Frigo">Nouveau</a>
                        	<a href="index.php?formulaire=Choisir_Vente_Frigo_Mod">Modifier/Valider</a>
                        	<a href="index.php?formulaire=Choisir_Vente_Frigo_Supp">Supprimer Instance</a>
                            
					<?php
                      if ($_SESSION['login']!="CC")
                      {
                      ?>	
                          <a href="index.php?formulaire=Choisir_Vente_Frigo_An_Val">Annuler Une Validation</a>
                       <?php
                      }
                   ?>
                        </ul>
                   </li>
            		<li>
                    <a href="#">Magasin</a>
                    	<ul>
                    		<a href="index.php?formulaire=Enreg_Vente&Clt=CLT00001">Nouveau</a>
                        	<a href="index.php?formulaire=Choisir_Vente_Mod">Modifier/Valider</a>
                        	<a href="index.php?formulaire=Choisir_Vente_Supp">Supprimer Instance</a>
                            
					<?php
                      if ($_SESSION['login']!="CC")
                      {
                      ?>	
                          <a href="index.php?formulaire=Choisir_Vente_An_Val">Annuler Une Validation</a>
                       <?php
                      }
                   ?>
                        </ul>
                   </li>
            </ul>
        </li>
        <li><a href="#">Cession</a>
        	<ul>
                   <li>
                    <a href="#">Entée Stock</a>
                    	<ul>
                    		<a href="index.php?formulaire=Enreg_ApproCession">Nouveau</a>
                        	<a href="index.php?formulaire=Choisir_ApproCession_Mod">Modifier/Valider</a>
                        	<a href="index.php?formulaire=Choisir_ApproCession_Supp">Supprimer Instance</a>
                            
					<?php
                      if ($_SESSION['login']!="CC")
                      {
                      ?>	
                          <a href="index.php?formulaire=Choisir_ApproCession_An_Val">Annuler Une Validation</a>
                       <?php
                      }
                   ?>
                        </ul>
                   </li>
            		<li>
                    <a href="#">Sortie Stock</a>
                    	<ul>
                    		<a href="index.php?formulaire=Enreg_SortieCession">Nouveau</a>
                        	<a href="index.php?formulaire=Choisir_SortieCession_Mod">Modifier/Valider</a>
                        	<a href="index.php?formulaire=Choisir_SortieCession_Supp">Supprimer Instance</a>
                            
					<?php
                      if ($_SESSION['login']!="CC")
                      {
                      ?>	
                          <a href="index.php?formulaire=Choisir_SortieCession_An_Val">Annuler Une Validation</a>
                       <?php
                      }
                   ?>
                        </ul>
                   </li>
            </ul>
        </li>
        <li><a href="#">Charge</a>
			<ul>
                    	<a href="index.php?formulaire=Enreg_Charge">Nouveau</a>
                        <a href="index.php?formulaire=Choisir_Charge_Mod">Modifier/Valider</a>
                        <a href="index.php?formulaire=Choisir_Charge_Supp">Supprimer Instance</a>
                        
					<?php
                      if ($_SESSION['login']!="CC")
                      {
                      ?>	
                          <a href="index.php?formulaire=Choisir_Charge_An_Val">Annuler Une Validation</a>
                       <?php
                      }
                   ?>
            </ul>
        </li>
        <li><a href="#">Perte</a>
					<ul>
                    		<li><a href="index.php?formulaire=Vente_Perte">Nouveau</a></li>
                        	<li><a href="index.php?formulaire=Choisir_Perte_Mod">Modifier/Valider</a></li>
                        	<li><a href="index.php?formulaire=Choisir_Perte_Supp">Supprimer Instance</a></li>

            		</ul>
        </li>
        <li><a href="index.php?formulaire=Choisir_Vente_Cons">Consigne Client</a></li>
     </li>
     <li>
        <a href="#">Versement</a>
        <ul>
            <a href="index.php?formulaire=Enreg_Vers">Nouveau</a>
            <a href="index.php?formulaire=Choisir_Vers_Mod">Modifier/Valider</a>
            <a href="index.php?formulaire=Choisir_Vers_Supp">Supprimer Instance</a>
            
			<?php
              if ($_SESSION['login']!="CC")
              {
              ?>	
                  <a href="index.php?formulaire=Choisir_Vers_An_Val">Annuler Une Validation</a>
               <?php
              }
           ?>
        </ul>
     </li>
     <li><a href="#">Inventaire</a>
        <ul>
            <a href="index.php?formulaire=Enreg_Inv">Nouveau</a>
            <a href="index.php?formulaire=Choisir_Inv_Mod">Modifier/Imprimer</a>

      </ul>
  </li>
   
      <li>
        <a href="#">Apport Financier</a>
        <ul>
            <a href="index.php?formulaire=Enreg_Apport">Nouveau</a>
            <a href="index.php?formulaire=Choisir_Apport_Mod">Modifier/Valider</a>
            <a href="index.php?formulaire=Choisir_Apport_Supp">Supprimer Instance</a>
            
			<?php
              if ($_SESSION['login']!="CC")
              {
              ?>	
                  <a href="index.php?formulaire=Choisir_Apport_An_Val">Annuler Une Validation</a>
               <?php
              }
           ?>

        </ul>
     </li>


			<?php
              if ($_SESSION['login']!="CC")
              {
              ?>	
                 <li>
                 	<a href="#">Regularisation Stock</a>
                    <ul>
                    	<a href="index.php?formulaire=Choisir_Ar_Reg">Article</a>
                        <a href="index.php?formulaire=Choisir_Emb_Reg">Emballage</a>
                    </ul>
                 </li>
               <?php
              }
           ?>
        <li><a href="#">Consulter</a>
        	
        	<ul>
           		
                <li><a href="index.php?formulaire=Choisir_Parametre_Feuille_Route">Feuille de Route</a></li>
                <li><a href="#">Clients</a>
                	<ul>
                    	<li><a href="index.php?formulaire=Choisir_Parametre_CA_Client">CA Clients</a></li>
                        <li><a href="index.php?formulaire=Consultation_Etat_Client">Etat des Clients</a></li>
                        <li><a href="index.php?formulaire=Choisir_Parametre_Vente_Client">Etat des Achats d'un Client</a></li>
                        <li><a href="index.php?formulaire=Choisir_Parametre_Liste_Detaille_Vente_Client">Etat detaille des Achats d'un Client</a></li>
                    </ul>
               </li>
                <li><a href="index.php?formulaire=Choisir_Parametre_Etat_Perte">Pertes</a></li>

             	

             	
              	<li><a href="#">Ventes</a>
                	<ul>
 						<li><a href="index.php?formulaire=Choisir_Parametre_Liste_Vente">Etat des Ventes Magasin</a></li>
                        <li><a href="index.php?formulaire=Choisir_Parametre_Vente_Article">Etat des Ventes Magasin par Article </a></li>
                        <li><a href="index.php?formulaire=Choisir_Parametre_Vente_Utilisateur">Etat des Ventes Magasin d'un Utilisateur</a></li>
                        <!--<li><a href="index.php?formulaire=Choisir_Parametre_Liste_Detaille_Vente">Etat detaille des Ventes Magasin</a></li>-->
                         <li><a href="index.php?formulaire=Choisir_Parametre_Etat_Vente_Frigo">Etat des Ventes du Frigo</a></li>
                        <li><a href="index.php?formulaire=Choisir_Parametre_Vente_Frigo">Etat des Ventes Frigo d'un Utilisateur</a></li>
                	</ul>
                </li>
                <li><a href="index.php?formulaire=Consulter_Article">Articles</a></li>
                <li><a href="index.php?formulaire=Choisir_Parametre_Etat_Apport">Apports</a></li>
                <li><a href="#">Charges</a>
                	<ul>
						<li><a href="index.php?formulaire=Choisir_Parametre_Etat_Charge">Etat des Charges</a></li>
                        <li><a href="index.php?formulaire=Choisir_Parametre_Charge">Etat des Charges par type</a></li>
                	</ul>
                </li>
                <li><a href="#">Cessions</a>
                	<ul>
                		<li><a href="index.php?formulaire=Choisir_Parametre_Liste_SortieCession">Etat des Sorties Cession</a></li>
                		<li><a href="index.php?formulaire=Choisir_Parametre_Liste_ApproCession">Etat des Entrées Cession</a></li>
                        <li><a href="index.php?formulaire=Choisir_Parametre_Etat_Sortie_Cession_Ar">Etat des Sorties de Stock Cession par Article</a></li>
                		<li><a href="index.php?formulaire=Choisir_Parametre_Etat_Entree_Cession_Ar">Etat des Entrées en Stock Magasin par Article</a></li>
                	</ul>
                </li>
                 
                <li><a href="#">Consignes</a>
                	<ul>
                    	<li><a href="index.php?formulaire=Choisir_Parametre_Etat_Consigne">Etat des Consignes Clients</a></li>
                        <li><a href="index.php?formulaire=Choisir_Parametre_Etat_Consigne_App">Etat des Consignes Fournisseurs</a></li>
                	</ul>
                </li>
			<?php
              if ($_SESSION['login']!="CC")
              {
              ?>	
                <li><a href="index.php?formulaire=Consultation_Liste_Utilisateur">Utilisateurs</a></li> 
                 <li><a href="index.php?formulaire=Choisir_Param_Ann">Annulations</a></li>
               <?php
              }
           ?>
  
                <li><a href="index.php?formulaire=Choisir_Parametre_Liste_Vers">Versements</a></li>

                <li><a href="#">Fournisseurs</a>
                	<ul>
                    	<li><a href="index.php?formulaire=Consultation_Liste_Fournisseurs">Etat des Fournisseurs</a></li>
                		<li><a href="index.php?formulaire=Choisir_Parametre_Appro_Fournisseur">Etat des Approvisionnements d'un Fournisseur</a></li>
                    </ul>
                </li>
             	<li><a href="#">Stocks Articles</a>
                	<ul>
						<li><a href="index.php?formulaire=Consultation_Etat_Stock">Etat des Stocks des Articles</a></li>
                        <li><a href="index.php?formulaire=Choisir_Parametre_Etat_Sortie_Frigo">Etat des Sorties de Stock Frigo</a></li> 
                		<li><a href="index.php?formulaire=Choisir_Parametre_Etat_Entree_Frigo">Etat des Entrées en Stock Frigo</a></li>
                   		<li><a href="index.php?formulaire=Choisir_Parametre_Etat_Sortie">Etat des Sorties de Stock Magasin</a></li>
                		<li><a href="index.php?formulaire=Choisir_Parametre_Etat_Entree">Etat des Entrées en Stock Magasin</a></li>
                        <li><a href="index.php?formulaire=Choisir_Parametre_Mouv_Stock_Ar">Etat des Mouvements de Stock d'un Article</a></li>
                	</ul>
                </li>

			<?php
              if ($_SESSION['login']!="CC")
              {
              ?>	
             	<li><a href="#">Regularisations</a>
                	<ul>
                    	<li><a href="index.php?formulaire=Choisir_Parametre_Et_Reg_Ar">Etat des Regularisations des Stocks des Articles</a></li>
						<li><a href="index.php?formulaire=Choisir_Parametre_Et_Reg_Emb">Etat des Regularisations des Stocks des Emballages</a></li>
                	</ul>
                </li>
               <?php
              }
           ?>
            	<li><a href="#">Deconsignations</a>
                	<ul>
						<li><a href="index.php?formulaire=Choisir_Parametre_Etat_Deconsigne">Etat des Deconsignations Clients</a></li>
                        <li><a href="index.php?formulaire=Choisir_Parametre_Etat_RtrEmb">Etat des Retours des Emballages Fournisseurs </a></li>
                	</ul>
                </li>
                
             	<li><a href="#">Stock Emballages</a>
                	<ul>
                        <li><a href="index.php?formulaire=Consultation_Etat_Stock_Emballage">Etat des Stocks des Emballages</a></li>
                   		<li><a href="index.php?formulaire=Choisir_Parametre_Etat_Sortie_Emb">Etat des Sorties de Stock Emballages</a></li>
                		<li><a href="index.php?formulaire=Choisir_Parametre_Etat_Entree_Emb">Etat des Entrées en Stock Emballages</a></li>
                        <li><a href="index.php?formulaire=Choisir_Parametre_Mouv_Stock_Emb">Etat des Mouvements de Stock d'un Emballages</a></li>
                	</ul>
                </li>
                <li><a href="index.php?formulaire=Choisir_Parametre_Brouillard">Brouillard de caisse</a></li>
              	<li><a href="#">Approvisionnements</a>
                	<ul>
                    	<li><a href="index.php?formulaire=Choisir_Parametre_Liste_ApF">Etat des Approvisionnements Frigo</a></li>
						<li><a href="index.php?formulaire=Choisir_Parametre_Liste_Appro">Etat des Approvisionnements Magasin</a></li>
                	</ul>
                </li>
                <li><a href="index.php?formulaire=Choisir_Parametre_Psa">TVA et PSA Collectes</a>
                    <ul>
                    	<li><a href="index.php?formulaire=Choisir_Parametre_Psa">Sur Ventes</a></li>
						<li><a href="index.php?formulaire=Choisir_Parametre_TaxesRis">Sur Ristournes</a></li>
                	</ul>
                </li>
                <li><a href="index.php?formulaire=Choisir_Parametre_Brouillard_Vte">Brouillard des ventes</a></li> 
                <li><a href="index.php?formulaire=Choisir_Parametre_RapportExp">Rapport d'Exploitation</a></li> 
                 <li><a href="index.php?formulaire=Consultation_Brouillard_An_Vte">Brouillard Annuel des ventes</a></li>
                <li><a href="index.php?formulaire=Choisir_Parametre_Liste_Reglement">Reglements Factures Ventes</a></li>
                <li><a href="index.php?formulaire=Choisir_Parametre_Etat_Sauv_St_Ar">Sauvegarde de l'Etat du Stock</a></li>
                <li><a href="index.php?formulaire=Consultation_Objectifs">Evaluation de l'atteinte des Objectifs</a> </li>

			<?php
              if ($_SESSION['login']!="CC")
              {
              ?>	
                <li><a href="index.php?formulaire=Choisir_Parametre_Remise">Evaluation Avoir de Remises sur Achat</a> </li>
               <?php
              }
           ?>
                <li><a href="index.php?formulaire=Consultation_Evaluation_Stock_Ar">Evaluation Chiffrée du Stock des Articles</a> </li>
                <li><a href="#">Evaluation Avoir de Participation Ristourne</a> 
                     <ul>
						<li><a href="index.php?formulaire=Choisir_Parametre_Ristourne_Clt"> Evaluation Ristourne Client </a></li>
                         <li><a href="index.php?formulaire=Choisir_Parametre_Ristourne_aPayer">Etat des Ristournes à Payer</a></li>
                        <li><a href="index.php?formulaire=Choisir_Parametre_Ristourne">Evaluation Ristourne Globale (Sur Achats)</a></li> 
                        <!--<li><a href="index.php?formulaire=Choisir_Parametre_Ristourne_Vte">Evaluation Ristourne Globale (Sur Ventes)</a></li>--> 
                	</ul>
               </li>
            </ul>
        </li>
        <li><a href="#">Reimpression</a>
       		<ul>
                <li><a href="index.php?formulaire=Choisir_Vente_Recu">Reçu Vente</a></li>
                <li><a href="index.php?formulaire=Choisir_Appro_Recu">Reçu Appro</a></li>
                <li><a href="index.php?formulaire=Choisir_Vers_Recu">Reçu Versement</a></li>
                <li><a href="index.php?formulaire=Choisir_Appro_Frigo_Recu">Reçu Appro Frigo</a></li> 
                <li><a href="index.php?formulaire=Choisir_ApproCession_Recu">Reçu Entrée Cession</a></li>
                <li><a href="index.php?formulaire=Choisir_SortieCession_Recu">Reçu Sortie Cession</a></li>
            </ul>
       </li>
       <li><a href="index.php?formulaire=Modification_MDP">Mot de passe</a></li>
       <li><a href="index.php?formulaire=Choisir_Vente_Reglement">Gestion des Reglements</a></li>
       <li><a href="index.php?formulaire=Sauv_Etat_Stock">Sauvegarde de l'Etat du Stock</a></li>
       <li><a href="Deconnexion.php">Deconnexion</a></li>

</ul>
