<?php

class VracHistoryView extends acCouchdbView
{
	const VRAC_VIEW_STATUT = 0;
    const VRAC_VIEW_NUMCONTRAT = 1;
    const VRAC_VIEW_ACHETEUR_ID = 2;
    const VRAC_VIEW_ACHETEUR_NOM = 3;
    const VRAC_VIEW_ACHETEUR_RAISON_SOCIALE = 4;
    const VRAC_VIEW_VENDEUR_ID = 5;
    const VRAC_VIEW_VENDEUR_NOM = 6;
    const VRAC_VIEW_VENDEUR_RAISON_SOCIALE = 7;
    const VRAC_VIEW_MANDATAIRE_ID = 8;
    const VRAC_VIEW_MANDATAIRE_NOM = 9;    
    const VRAC_VIEW_MANDATAIRE_RAISON_SOCIALE = 10;    
    const VRAC_VIEW_TYPEPRODUIT = 11;
    const VRAC_VIEW_PRODUIT_ID = 12;
    const VRAC_VIEW_VOLPROP = 13;
    const VRAC_VIEW_VOLENLEVE = 14;
    const VRAC_VIEW_PRIXTOTAL = 15;

	public static function getInstance() {

        return acCouchdbManager::getView('vrac', 'history', 'Vrac');
    }

	public function findLast() {
      
        return $this->client->descending(true)
                            ->limit(300)
                            ->getView($this->design, $this->view);
    }
    
}  