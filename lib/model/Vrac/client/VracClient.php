<?php

class VracClient extends acCouchdbClient {
   
	/*
	 * @todo Rendre generique les constantes de transaction
	 */
    const TYPE_TRANSACTION_RAISINS = 'raisins';
    const TYPE_TRANSACTION_MOUTS = 'mouts';
    const TYPE_TRANSACTION_VIN_VRAC = 'vin_vrac';
    const TYPE_TRANSACTION_VIN_BOUTEILLE = 'vin_bouteille';
    
    
    const STATUS_CONTRAT_SOLDE = 'SOLDE';
    const STATUS_CONTRAT_ANNULE = 'ANNULE';
    const STATUS_CONTRAT_NONSOLDE = 'NONSOLDE';
    

    /**
     *
     * @return DRMClient
     */
    public static function getInstance()
    {
      return acCouchdbManager::getClient("Vrac");
    }

    public function getId($numeroContrat)
    {
      return 'VRAC-'.$numeroContrat;
    }

    public function getNextNoContrat()
    {   
        $id = '';
    	$date = date('Ymd');
    	$contrats = self::getAtDate($date, acCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
        if (count($contrats) > 0) {
            $id .= ((double)str_replace('VRAC-', '', max($contrats)) + 1);
        } else {
            $id.= $date.'001';
        }

        return $id;
    }
    
    public function getAtDate($date, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) 
    {
        return $this->startkey('VRAC-'.$date.'000')->endkey('VRAC-'.$date.'999')->execute($hydrate);        
    }
    
    public function findByNumContrat($num_contrat) 
    {
      return $this->find($this->getId($num_contrat));
    }
    
    public function retrieveLastDocs() 
    {
      return $this->descending(true)->limit(300)->getView('vrac', 'history');
    }
    
    public function retrieveBySoussigne($soussigneParam) 
    {
      return $this->startkey(array($soussigneParam))
              ->endkey(array($soussigneParam, array()))->limit(300)->getView('vrac', 'soussigneidentifiant');
    }
 }
