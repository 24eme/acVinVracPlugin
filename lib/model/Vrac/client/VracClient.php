<?php

class VracClient extends acCouchdbClient {
   
    
    const VRAC_VIEW_STATUT = 0;
    const VRAC_VIEW_NUMCONTRAT = 1;
    const VRAC_VIEW_ACHETEUR_ID = 2;
    const VRAC_VIEW_ACHETEUR_NOM = 3;
    const VRAC_VIEW_VENDEUR_ID = 4;
    const VRAC_VIEW_VENDEUR_NOM = 5;
    const VRAC_VIEW_MANDATAIRE_ID = 6;
    const VRAC_VIEW_MANDATAIRE_NOM = 7;    
    const VRAC_VIEW_TYPEPRODUIT = 8;
    const VRAC_VIEW_PRODUIT_ID = 9;
    const VRAC_VIEW_VOLPROP = 10;
    const VRAC_VIEW_VOLENLEVE = 11;

    const VRAC_SIMILAIRE_KEY_VENDEURID = 0;   
    const VRAC_SIMILAIRE_KEY_ACHETEURID = 1;
    const VRAC_SIMILAIRE_KEY_MANDATAIREID = 3; 
    const VRAC_SIMILAIRE_KEY_TYPE = 4;
    const VRAC_SIMILAIRE_KEY_PRODUIT = 5;
    const VRAC_SIMILAIRE_KEY_VOLPROP = 6;
    
    const VRAC_SIMILAIRE_VALUE_NUMCONTRAT = 0;   
    const VRAC_SIMILAIRE_VALUE_STATUT = 1;
    const VRAC_SIMILAIRE_VALUE_MILLESIME = 2;   
    const VRAC_SIMILAIRE_VALUE_VOLPROP = 3;
    
    
    const TYPE_TRANSACTION_RAISINS = 'raisins';
    const TYPE_TRANSACTION_MOUTS = 'mouts';
    const TYPE_TRANSACTION_VIN_VRAC = 'vin_vrac';
    const TYPE_TRANSACTION_VIN_BOUTEILLE = 'vin_bouteille';

    const TYPE_CONTRAT_SPOT = 'spot';
    const TYPE_CONTRAT_PLURIANNUEL = 'pluriannuel';

    const CVO_NATURE_MARCHE_DEFINITIF = 'marche_definitif';
    const CVO_NATURE_COMPENSATION = 'compensation';
    const CVO_NATURE_NON_FINANCIERE = 'non_financiere';
    const CVO_NATURE_VINAIGRERIE = 'vinaigrerie';
    
    const STATUS_CONTRAT_SOLDE = 'SOLDE';
    const STATUS_CONTRAT_ANNULE = 'ANNULE';
    const STATUS_CONTRAT_NONSOLDE = 'NONSOLDE';
    
    
    public static $contenance = array('75 cl' => 0.0075,
                                   '1 L' => 0.01,
                                     '1.5 L'=> 0.015,
                                     '3 L' => 0.03,
                                        'BIB 3 L' => 0.03,
                                    '6 L' => 0.06);

    
    

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
            $id.= $date.'00001';
        }

        return $id;
    }
    
    public function getAtDate($date, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        return $this->startkey('VRAC-'.$date.'00000')->endkey('VRAC-'.$date.'99999')->execute($hydrate);        
    }
    
    public function findByNumContrat($num_contrat) {
      return $this->find($this->getId($num_contrat));
    }
    
    public function retrieveLastDocs($limit = 300) {
      return $this->descending(true)->limit($limit)->getView('vrac', 'history');
    }
    
    public function retrieveBySoussigne($soussigneId,$limit=300) {
      $soussigneId = EtablissementClient::getInstance()->getIdentifiant($soussigneId);
      $bySoussigneQuery = $this->startkey(array('STATUT',$soussigneId))
              ->endkey(array('STATUT',$soussigneId, array()));
      if ($limit){
            $bySoussigneQuery =  $bySoussigneQuery->limit($limit);
        }
      
      $bySoussigne = $bySoussigneQuery->getView('vrac', 'soussigneidentifiant');
      return $bySoussigne;
    }
    
    public function retrieveBySoussigneAndStatut($soussigneId,$statut,$limit=300) {
      $soussigneId = EtablissementClient::getInstance()->getIdentifiant($soussigneId);
        $bySoussigneStatutQuery =  $this->startkey(array('STATUT',$soussigneId,$statut))
                ->endkey(array('STATUT',$soussigneId,$statut, array()));

        if ($limit){
            $bySoussigneStatutQuery =  $bySoussigneStatutQuery->limit($limit);
        }
      
        $bySoussigneStatut = $bySoussigneStatutQuery->getView('vrac', 'soussigneidentifiant');
        return $bySoussigneStatut;
    }
    
    public function retrieveBySoussigneAndType($soussigneId,$type,$limit=300) {
      $soussigneId = EtablissementClient::getInstance()->getIdentifiant($soussigneId);
      $bySoussigneTypeQuery = $this->startkey(array('TYPE',$soussigneId,$type))
              ->endkey(array('TYPE',$soussigneId,$type, array()));
    
    if ($limit){
            $bySoussigneTypeQuery =  $bySoussigneTypeQuery->limit($limit);
        }
    $bySoussigneType = $bySoussigneTypeQuery->getView('vrac', 'soussigneidentifiant');
    return $bySoussigneType;
    }
        
    public function retrieveBySoussigneStatutAndType($soussigneId,$statut,$type,$limit=300) {
      $soussigneId = EtablissementClient::getInstance()->getIdentifiant($soussigneId);
      $bySoussigneTypeQuery = $this->startkey(array('STATUT',$soussigneId,$statut,$type))
                ->endkey(array('STATUT',$soussigneId,$statut,$type, array()));
      
      if ($limit){
              $bySoussigneTypeQuery =  $bySoussigneTypeQuery->limit($limit);
          }
      $bySoussigneType = $bySoussigneTypeQuery->getView('vrac', 'soussigneidentifiant');
      return $bySoussigneType;
    }
    public static function getCsvBySoussigne($vracs)
    {
        $result ="\xef\xbb\xbf";
        foreach ($vracs->rows as $value)
        {   
            $cpt=0;
            $elt = $value->getRawValue()->value;
            
            foreach ($elt as $key => $champs)
            {
                $cpt++;
                if(($key == self::VRAC_VIEW_PRODUIT_ID) && ($champs!= ""))
                   $champs = ConfigurationClient::getCurrent()->get($champs)->libelleProduit(array(),"%c% %g% %a% %m% %l% %co% %ce% %la%");                   
                $result.='"'.$champs.'"';
                if($cpt < count($elt)) $result.=';';              
            }
            $result.="\n";
        }
        return $result;
    }

    public function retrieveSimilaryContracts($vrac) {       
        if(isset($vrac->vendeur_identifiant) || isset($vrac->acheteur_identifiant)) {            
        	return false;
    	} 
        if(is_null($vrac->produit)){            
                return 
                ($vrac->mandataire_exist)?
                    $this->startkey(array('M',$vrac->vendeur_identifiant,$vrac->acheteur_identifiant,$vrac->mandataire_identifiant,$vrac->type_transaction))
                         ->endkey(array('M',$vrac->vendeur_identifiant,$vrac->acheteur_identifiant,$vrac->mandataire_identifiant,$vrac->type_transaction, array()))->limit(10)->getView('vrac', 'vracSimilaire')
                  : $this->startkey(array('',$vrac->vendeur_identifiant,$vrac->acheteur_identifiant,$vrac->type_transaction))
                         ->endkey(array('',$vrac->vendeur_identifiant,$vrac->acheteur_identifiant,$vrac->type_transaction, array()))->limit(10)->getView('vrac', 'vracSimilaire');
        
                            
                            
        }
        if(is_null($vrac->volume_propose)){
                return 
                ($vrac->mandataire_exist)?
                    $this->startkey(array('M',$vrac->vendeur_identifiant,$vrac->acheteur_identifiant,$vrac->mandataire_identifiant,$vrac->type_transaction,$vrac->produit))
                         ->endkey(array('M',$vrac->vendeur_identifiant,$vrac->acheteur_identifiant,$vrac->mandataire_identifiant,$vrac->type_transaction,$vrac->produit, array()))->limit(10)->getView('vrac', 'vracSimilaire')
                  : $this->startkey(array('',$vrac->vendeur_identifiant,$vrac->acheteur_identifiant,$vrac->type_transaction,$vrac->produit))
                         ->endkey(array('',$vrac->vendeur_identifiant,$vrac->acheteur_identifiant,$vrac->type_transaction,$vrac->produit, array()))->limit(10)->getView('vrac', 'vracSimilaire');
        
                            
                            
        }
        return ($vrac->mandataire_exist)?
                    $this->startkey(array('M',$vrac->vendeur_identifiant,$vrac->acheteur_identifiant,$vrac->mandataire_identifiant,$vrac->type_transaction,$vrac->produit,$vrac->volume_propose))
                         ->endkey(array('M',$vrac->vendeur_identifiant,$vrac->acheteur_identifiant,$vrac->mandataire_identifiant,$vrac->type_transaction,$vrac->produit,$vrac->volume_propose, array()))->limit(10)->getView('vrac', 'vracSimilaire')
                  : $this->startkey(array('',$vrac->vendeur_identifiant,$vrac->acheteur_identifiant,$vrac->type_transaction,$vrac->produit,$vrac->volume_propose))
                         ->endkey(array('',$vrac->vendeur_identifiant,$vrac->acheteur_identifiant,$vrac->type_transaction,$vrac->produit,$vrac->volume_propose, array()))->limit(10)->getView('vrac', 'vracSimilaire');

    }
    
    public function retrieveSimilaryContractsWithProdTypeVol($params) {
        if((empty($params['vendeur']))
          || (empty($params['acheteur']))
          || (empty($params['type']))) {

        	return false;
    	}

        if(empty($params['produit']) && !empty($params['volume'])) {

        	return false;
        }
        
        if(empty($params['volume']) && empty($params['produit'])) {
            
            return $this->startkey(array($params['vendeur'],$params['acheteur'],$params['mandataire'],$params['type']))
               ->endkey(array($params['vendeur'],$params['acheteur'],$params['mandataire'],$params['type'], array()))->limit(10)->getView('vrac', 'vracSimilaire');
        }
        
        if(empty($params['volume'])) {

        	return $this->startkey(array($params['vendeur'],$params['acheteur'],$params['mandataire'],$params['type'],$params['produit']))
               ->endkey(array($params['vendeur'],$params['acheteur'],$params['mandataire'],$params['type'],$params['produit'], array()))->limit(10)->getView('vrac', 'vracSimilaire');
        }

        $volumeBas = ((float) $params['volume'])*0.95;
        $volumeHaut = ((float) $params['volume'])*1.05;
        
        return $this->startkey(array($params['vendeur'],$params['acheteur'],$params['mandataire'],$params['type'],$params['produit'],$volumeBas))
               ->endkey(array($params['vendeur'],$params['acheteur'],$params['mandataire'],$params['type'],$params['produit'],$volumeHaut, array()))->limit(10)->getView('vrac', 'vracSimilaire');            
    }
    
    public function filterSimilaryContracts($vrac,$similaryContracts) {
        foreach ($similaryContracts->rows as $key => $value) {
            if($value->id === $vrac->_id){
                unset($similaryContracts->rows[$key]);
                return;
            }
        }
    }


    public function retrieveByNumeroAndEtablissementAndHashOrCreateIt($id, $etablissement, $hash, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
      $vrac = $this->retrieveById($id);
      if (!$vrac) {
	$vrac = new Vrac();
	$vrac->vendeur_identifiant = $etablissement;
	$vrac->numero_contrat = $id;
	$vrac->produit = $hash;
      }
      if ($etablissement != $vrac->vendeur_identifiant)
	throw new sfException('le vendeur ne correpond pas à l\'établissement initial');
      if (!preg_match("|^$hash|", $vrac->produit))
	throw new sfException('Le hash du produit ne correpond pas au hash initial ('.$vrac->produit.'<->'.$hash.')');
      return $vrac;
    }
    
    /**
     *
     * @param string $id
     * @param integer $hydrate
     * @return Vrac 
     */
    public function retrieveById($id, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        return parent::retrieveDocumentById('VRAC-'.$id, $hydrate);
    }       

    public static function getTypes() {
        return array(self::TYPE_TRANSACTION_MOUTS => self::TYPE_TRANSACTION_MOUTS,
                     self::TYPE_TRANSACTION_RAISINS => self::TYPE_TRANSACTION_RAISINS,
                     self::TYPE_TRANSACTION_VIN_BOUTEILLE => self::TYPE_TRANSACTION_VIN_BOUTEILLE,
                     self::TYPE_TRANSACTION_VIN_VRAC => self::TYPE_TRANSACTION_VIN_VRAC);
    }

    public static function getStatuts() {
        return array(self::STATUS_CONTRAT_ANNULE => self::STATUS_CONTRAT_ANNULE,
                     self::STATUS_CONTRAT_NONSOLDE => self::STATUS_CONTRAT_NONSOLDE,
                     self::STATUS_CONTRAT_SOLDE => self::STATUS_CONTRAT_SOLDE);
    }
    
}
