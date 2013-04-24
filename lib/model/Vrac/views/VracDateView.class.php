<?php
class VracDateView extends acCouchdbView
{
	const KEY_INTERPRO_ID = 0;
	const KEY_DATES_SAISIE = 1;
	const KEY_VRAC_ID = 2;
	const KEY_PRODUIT_HASH = 3;

	const VALUE_VRAC_ID = 0;
	const VALUE_DATE_SAISIE = 1;
	const VALUE_ACHETEUR_CVI = 2;
	const VALUE_ACHETEUR_SIRET = 3;
	const VALUE_ACHETEUR_NOM = 4;
	const VALUE_VENDEUR_CVI = 5;
	const VALUE_VENDEUR_SIRET = 6;
	const VALUE_VENDEUR_NOM = 7;
	const VALUE_MANDATAIRE_SIRET = 8;
	const VALUE_MANDATAIRE_NOM = 9;
	const VALUE_TYPE_CONTRAT_LIBELLE = 10;
	const VALUE_PRODUIT_APPELLATION_LIBELLE = 11;
	const VALUE_PRODUIT_APPELLATION_CODE = 12;
	const VALUE_PRODUIT_GENRE_LIBELLE = 13;
	const VALUE_PRODUIT_GENRE_CODE = 14;
	const VALUE_PRODUIT_CERTIFICATION_LIBELLE = 15;
	const VALUE_PRODUIT_CERTIFICATION_CODE = 16;
	const VALUE_PRODUIT_LIEU_LIBELLE = 17;
	const VALUE_PRODUIT_LIEU_CODE = 18;
	const VALUE_PRODUIT_COULEUR_LIBELLE = 19;
	const VALUE_PRODUIT_COULEUR_CODE = 20;
	const VALUE_PRODUIT_CEPAGE_LIBELLE = 21;
	const VALUE_PRODUIT_CEPAGE_CODE = 22;
	const VALUE_MILLESIME = 23;
	const VALUE_LABELS_LIBELLE = 24;
	const VALUE_LABELS_CODE = 25;
	const VALUE_MENTIONS = 26;
	const VALUE_CAS_PARTICULIER_LIBELLE = 27;
	const VALUE_PREMIERE_MISE_EN_MARCHE = 28;
	const VALUE_ANNEXE = 29;
	const VALUE_VOLUME_PROPOSE = 30;
	const VALUE_PRIX_UNITAIRE = 31;
	const VALUE_TYPE_PRIX = 32;
	const VALUE_DETERMINATION_PRIX = 33;
	const VALUE_EXPORT = 34;
	const VALUE_CONDITIONS_PAIEMENT_LIBELLE = 35;
	const VALUE_REFERENCE_CONTRAT_PLURIANNUEL = 36;
	const VALUE_VIN_LIVRE = 37;
	const VALUE_DATE_LIMITE_RETIRAISON = 38;
	const VALUE_PAIEMENTS_DATE = 39;
	const VALUE_PAIEMENTS_VOLUME = 40;
	const VALUE_PAIEMENTS_MONTANT = 41;
	const VALUE_LOT_NUMERO = 42;
	const VALUE_LOT_ASSEMBLAGE = 43;
	const VALUE_LOT_CUVES_NUMERO = 44;
	const VALUE_LOT_CUVES_VOLUME = 45;
	const VALUE_LOT_CUVES_DATE = 46;
	const VALUE_LOT_MILLESIMES_ANNEE = 47;
	const VALUE_LOT_MILLESIMES_POURCENTAGE = 48;
	const VALUE_LOT_DEGRE = 49;
	const VALUE_LOT_PRESENCE_ALLERGENES = 50;
	const VALUE_LOT_BAILLEUR = 51;
	const VALUE_STATUT = 52;

	public static function getInstance() 
	{
        return acCouchdbManager::getView('vrac', 'date', 'Vrac');
    }

    public function findByInterproAndDate($interpro, $date) 
    {
      	return $this->client->startkey(array($interpro, $date))
                    		->endkey(array($interpro, $this->getEndISODateForView(), array()))
                    		->getView($this->design, $this->view);
    }
    
    public function getEndISODateForView() 
    {
    	return '9999-99-99T99:99:99'.date('P');
    }

}  