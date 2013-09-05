<?php

class VracClient extends acCouchdbClient {

    const VRAC_VIEW_CAMPAGNE = 0;
    const VRAC_VIEW_STATUT = 1;
    const VRAC_VIEW_ID = 2;
    const VRAC_VIEW_NUMCONTRAT = 3;
    const VRAC_VIEW_NUMARCHIVE = 4;
    const VRAC_VIEW_ACHETEUR_ID = 5;
    const VRAC_VIEW_ACHETEUR_NOM = 6;
    const VRAC_VIEW_VENDEUR_ID = 7;
    const VRAC_VIEW_VENDEUR_NOM = 8;
    const VRAC_VIEW_MANDATAIRE_ID = 9;
    const VRAC_VIEW_MANDATAIRE_NOM = 10;
    const VRAC_VIEW_TYPEPRODUIT = 11;
    const VRAC_VIEW_PRODUIT_ID = 12;
    const VRAC_VIEW_PRODUIT_LIBELLE = 13;
    const VRAC_VIEW_VOLPROP = 14;
    const VRAC_VIEW_VOLENLEVE = 15;
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
    const VRAC_SIMILAIRE_VALUE_NUMARCHIVE = 4;
    const TYPE_TRANSACTION_RAISINS = 'RAISINS';
    const TYPE_TRANSACTION_MOUTS = 'MOUTS';
    const TYPE_TRANSACTION_VIN_VRAC = 'VIN_VRAC';
    const TYPE_TRANSACTION_VIN_BOUTEILLE = 'VIN_BOUTEILLE';
    const TYPE_CONTRAT_SPOT = 'SPOT';
    const TYPE_CONTRAT_PLURIANNUEL = 'PLURIANNUEL';
    const CVO_NATURE_MARCHE_DEFINITIF = 'MARCHE_DEFINITIF';
    const CVO_NATURE_COMPENSATION = 'COMPENSATION';
    const CVO_NATURE_NON_FINANCIERE = 'NON_FINANCIERE';
    const CVO_NATURE_VINAIGRERIE = 'VINAIGRERIE';
    const CATEGORIE_VIN_GENERIQUE = 'GENERIQUE';
    const CATEGORIE_VIN_DOMAINE = 'DOMAINE';
    const STATUS_CONTRAT_SOLDE = 'SOLDE';
    const STATUS_CONTRAT_ANNULE = 'ANNULE';
    const STATUS_CONTRAT_NONSOLDE = 'NONSOLDE';
    const CVO_REPARTITION_50_50 = '50';
    const CVO_REPARTITION_100_VITI = '100';
    const CVO_REPARTITION_0_VINAIGRERIE = '0';
    const RESULTAT_LIMIT = 700;

    public static $types_transaction = array(VracClient::TYPE_TRANSACTION_RAISINS => 'Raisins',
        VracClient::TYPE_TRANSACTION_MOUTS => 'Moûts',
        VracClient::TYPE_TRANSACTION_VIN_VRAC => 'Vin en vrac',
        VracClient::TYPE_TRANSACTION_VIN_BOUTEILLE => 'Vin conditionné');
    public static $categories_vin = array(self::CATEGORIE_VIN_GENERIQUE => 'Générique', self::CATEGORIE_VIN_DOMAINE => 'Domaine');
    public static $types_transaction_vins = array(self::TYPE_TRANSACTION_VIN_VRAC, self::TYPE_TRANSACTION_VIN_BOUTEILLE);
    public static $types_transaction_non_vins = array(self::TYPE_TRANSACTION_RAISINS, self::TYPE_TRANSACTION_MOUTS);
    public static $cvo_repartition = array(self::CVO_REPARTITION_50_50 => '50/50',
        self::CVO_REPARTITION_100_VITI => '100% viticulteur',
        self::CVO_REPARTITION_0_VINAIGRERIE => 'Vinaigrerie');
    public static $statuts_valide = array(self::STATUS_CONTRAT_NONSOLDE, self::STATUS_CONTRAT_SOLDE);

    /**
     *
     * @return DRMClient
     */
    public static function getInstance() {
        return acCouchdbManager::getClient("Vrac");
    }

    public function getContenances() {
        $contenances = sfConfig::get('app_vrac_contenances');
        if (!$contenances)
            throw new sfException("Les contenances n'ont pas été renseignée dans le fichier de configuration app.yml");
        return $contenances;
    }

    public function getContenance($k) {
        $contenances = $this->getContenances();

        return $contenances[$k];
    }

    public function getId($id_or_numerocontrat) {
        $id = $id_or_numerocontrat;
        if (strpos($id_or_numerocontrat, 'VRAC-') === false) {
            $id = 'VRAC-' . $id_or_numerocontrat;
        }

        return $id;
    }

    public function getNumeroContrat($id_or_numerocontrat) {

        return str_replace('VRAC-', '', $id_or_numerocontrat);
    }

    public function buildCampagne($date) {

        return ConfigurationClient::getInstance()->buildCampagne($date);
    }

    public function getNextNoContrat() {
        $id = '';
        $date = date('Ymd');
        $contrats = self::getAtDate($date, acCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
        if (count($contrats) > 0) {
            $id .= ((double) str_replace('VRAC-', '', max($contrats)) + 1);
        } else {
            $id.= $date . '00001';
        }

        return $id;
    }

    public function getAtDate($date, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        return $this->startkey('VRAC-' . $date . '00000')->endkey('VRAC-' . $date . '99999')->execute($hydrate);
    }

    public function findByNumContrat($num_contrat, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        return $this->find($this->getId($num_contrat), $hydrate);
    }

    public function retrieveLastDocs($limit = self::RESULTAT_LIMIT) {
        return $this->descending(true)->limit($limit)->getView('vrac', 'history');
    }

    public function retrieveBySoussigne($soussigneId, $campagne, $limit = self::RESULTAT_LIMIT) {
        $soussigneId = EtablissementClient::getInstance()->getIdentifiant($soussigneId);
        if (!preg_match('/[0-9]*-[0-9]*/', $campagne))
            throw new sfException("wrong campagne format ($campagne)");
        $bySoussigneQuery = $this->startkey(array('STATUT', $soussigneId, $campagne))
                ->endkey(array('STATUT', $soussigneId, $campagne, array()));
        if ($limit) {
            $bySoussigneQuery = $bySoussigneQuery->limit($limit);
        }

        $bySoussigne = $bySoussigneQuery->reduce(false)->getView('vrac', 'soussigneidentifiant');
        return $bySoussigne;
    }

    public function retrieveByType($type, $campagne, $limit = self::RESULTAT_LIMIT) {
        $soussigneId = EtablissementClient::getInstance()->getIdentifiant($soussigneId);
        if (!preg_match('/[0-9]*-[0-9]*/', $campagne))
            throw new sfException("wrong campagne format ($campagne)");
        $bySoussigneTypeQuery = $this->startkey(array('TYPE', $soussigneId, $campagne, $type))
                ->endkey(array('TYPE', $soussigneId, $campagne, $type, array()));

        if ($limit) {
            $bySoussigneTypeQuery = $bySoussigneTypeQuery->limit($limit);
        }
        $bySoussigneType = $bySoussigneTypeQuery->reduce(false)->getView('vrac', 'soussigneidentifiant');
        return $bySoussigneType;
    }

    public function retrieveBySoussigneAndStatut($soussigneId, $campagne, $statut, $limit = self::RESULTAT_LIMIT) {
        $soussigneId = EtablissementClient::getInstance()->getIdentifiant($soussigneId);
        if (!preg_match('/[0-9]*-[0-9]*/', $campagne))
            throw new sfException("wrong campagne format ($campagne)");
        $bySoussigneStatutQuery = $this->startkey(array('STATUT', $soussigneId, $campagne, $statut))
                ->endkey(array('STATUT', $soussigneId, $campagne, $statut, array()));

        if ($limit) {
            $bySoussigneStatutQuery = $bySoussigneStatutQuery->limit($limit);
        }

        $bySoussigneStatut = $bySoussigneStatutQuery->reduce(false)->getView('vrac', 'soussigneidentifiant');
        return $bySoussigneStatut;
    }

    public function retrieveBySoussigneAndType($soussigneId, $campagne, $type, $limit = self::RESULTAT_LIMIT) {
        if (!preg_match('/[0-9]*-[0-9]*/', $campagne))
            throw new sfException("wrong campagne format ($campagne)");
        if (is_array($type)) {
            $typestart = $type['start'];
            $typeend = $type['end'];
        } else {
            $typestart = $type;
            $typeend = $type;
        }
        $soussigneId = EtablissementClient::getInstance()->getIdentifiant($soussigneId);
        $bySoussigneTypeQuery = $this->startkey(array('TYPE', $soussigneId, $campagne, $typestart))
                ->endkey(array('TYPE', $soussigneId, $campagne, $typeend, array()));

        if ($limit) {
            $bySoussigneTypeQuery = $bySoussigneTypeQuery->limit($limit);
        }
        $bySoussigneType = $bySoussigneTypeQuery->reduce(false)->getView('vrac', 'soussigneidentifiant');
        return $bySoussigneType;
    }

    public function retrieveBySoussigneStatutAndType($soussigneId, $campagne, $statut, $type, $limit = self::RESULTAT_LIMIT) {
        if (!preg_match('/[0-9]*-[0-9]*/', $campagne))
            throw new sfException("wrong campagne format ($campagne)");
        $soussigneId = EtablissementClient::getInstance()->getIdentifiant($soussigneId);
        $bySoussigneTypeQuery = $this->startkey(array('STATUT', $soussigneId, $campagne, $statut, $type))
                ->endkey(array('STATUT', $soussigneId, $campagne, $statut, $type, array()));

        if ($limit) {
            $bySoussigneTypeQuery = $bySoussigneTypeQuery->limit($limit);
        }
        $bySoussigneType = $bySoussigneTypeQuery->reduce(false)->getView('vrac', 'soussigneidentifiant');
        return $bySoussigneType;
    }

    public function listCampagneByEtablissementId($identifiant) {
        $rows = $this->startkey(array('STATUT', $identifiant))
                        ->endkey(array('STATUT', $identifiant, array()))
                        ->group_level(3)
                        ->getView('vrac', 'soussigneidentifiant')->rows;

        $current = ConfigurationClient::getInstance()->getCurrentCampagne();
        $list = array($current => $current);
        foreach ($rows as $r) {
            $c = $r->key[2];
            $list[$c] = $c;
        }
        krsort($list);
        return $list;
    }

    public static function getCsvForEtiquettes() {
        $vracs = VracStatutAndTypeView::getInstance()->findContatsByStatutsAndTypesAndOneDate(self::$statuts_valide, array_keys(self::$types_transaction), date('Y-m-d'));

        $result = "\xef\xbb\xbf";
        $result .="NUMERO CONTRAT;NUMERO ARCHIVAGE;";
        $result .="IDENTIFIANT VENDEUR;NOM VENDEUR;ADRESSE VENDEUR;";
        $result .="IDENTIFIANT ACHETEUR;NOM ACHETEUR;ADRESSE ACHETEUR;";
        $result .="IDENTIFIANT COURTIER;NOM COURTIER;ADRESSE COURTIER\n";
        
        foreach ($vracs as $key => $vrac_row) {
            
            $vrac = VracClient::getInstance()->find($vrac_row->id, acCouchdbClient::HYDRATE_JSON);
            $result.= $vrac->numero_contrat . ";";
            $result.= $vrac->numero_archive . ";";

            $vendeur = CompteClient::getInstance()->findByIdentifiant($vrac->vendeur_identifiant, acCouchdbClient::HYDRATE_JSON);
            $result.= $vrac->vendeur_identifiant . ";";
            $result.= $vendeur->nom_a_afficher . ";";
            $result.= $vendeur->adresse . " " . $vendeur->adresse_complementaire . " " . $vendeur->code_postal . " " . $vendeur->commune . ";";

            $acheteur = CompteClient::getInstance()->findByIdentifiant($vrac->acheteur_identifiant, acCouchdbClient::HYDRATE_JSON);
            $result.= $vrac->acheteur_identifiant . ";";
            $result.= $acheteur->nom_a_afficher . ";";
            $result.= $acheteur->adresse . " " . $acheteur->adresse_complementaire . " " . $acheteur->code_postal . " " . $acheteur->commune . ";";

            if ($vrac->mandataire_exist) {
                $mandataire = CompteClient::getInstance()->findByIdentifiant($vrac->mandataire_identifiant, acCouchdbClient::HYDRATE_JSON);
                $result.= $vrac->mandataire_identifiant . ";";
                $result.= $mandataire->nom_a_afficher . ";";
                $result.= $mandataire->adresse . " " . $mandataire->adresse_complementaire . " " . $mandataire->code_postal . " " . $mandataire->commune . ";";
            } else {
                $result.= ";;";
            }

            if($key < count($vracs)){
                $result.="\n";                
            }
        }
        return $result;
    }

    public static function getCsvBySoussigne($vracs) {
        $result = "\xef\xbb\xbf";
        $statuts_libelles = self::getStatuts();
        foreach ($vracs->rows as $value) {
            $cpt = 0;
            $elt = $value->getRawValue()->value;

            foreach ($elt as $key => $champs) {
                $cpt++;
                if ($key == self::VRAC_VIEW_STATUT)
                    $champs = (array_key_exists($champs, $statuts_libelles)) ? $statuts_libelles[$champs] : $champs;
                if ($key == self::VRAC_VIEW_NUMARCHIVE)
                    $champs = "" . $champs;
                if ($key == self::VRAC_VIEW_TYPEPRODUIT)
                    $champs = self::$types_transaction[$champs];
                if ($key == self::VRAC_VIEW_VOLPROP || $key == self::VRAC_VIEW_VOLENLEVE)
                    $champs = sprintf("%01.02f", round($champs, 2));
                $result.='"' . $champs . '"';
                if ($cpt < count($elt))
                    $result.=';';
            }
            $result.="\n";
        }
        return $result;
    }

    public function retrieveSimilaryContracts($vrac) {
        if (isset($vrac->vendeur_identifiant) || isset($vrac->acheteur_identifiant)) {
            return false;
        }
        $args = array();
        if ($vrac->mandataire_exist) {
            $args = array($vrac->vendeur_identifiant, $vrac->acheteur_identifiant, $vrac->mandataire_identifiant, $vrac->type_transaction);
        } else {
            $args = array($vrac->vendeur_identifiant, $vrac->acheteur_identifiant, '', $vrac->type_transaction);
        }
        if ($vrac->produit) {
            $args[] = $vrac->produit;

            if ($vrac->volume_propose) {
                $args[] = $vrac->volume_propose;
            }
        }
        $start = $this->startkey($args);
        $args[] = array();
        $view = $start->endkey($args)->limit(10)->getView('vrac', 'vracSimilaire');
        if ($vrac->_id)
            $this->filterSimilaryContracts($view, $vrac->_id);
        return $view->rows;
    }

    public function retrieveSimilaryContractsWithProdTypeVol($params) {
        if ((empty($params['vendeur'])) || (empty($params['acheteur'])) || (empty($params['type']))) {

            return false;
        }

        if (empty($params['produit']) && !empty($params['volume'])) {

            return false;
        }

        if (empty($params['volume']) && empty($params['produit'])) {

            return $this->startkey(array($params['vendeur'], $params['acheteur'], $params['mandataire'], $params['type']))
                            ->endkey(array($params['vendeur'], $params['acheteur'], $params['mandataire'], $params['type'], array()))->limit(10)->getView('vrac', 'vracSimilaire');
        }

        if (empty($params['volume'])) {

            return $this->startkey(array($params['vendeur'], $params['acheteur'], $params['mandataire'], $params['type'], $params['produit']))
                            ->endkey(array($params['vendeur'], $params['acheteur'], $params['mandataire'], $params['type'], $params['produit'], array()))->limit(10)->getView('vrac', 'vracSimilaire');
        }

        $volumeBas = ((float) $params['volume']) * 0.95;
        $volumeHaut = ((float) $params['volume']) * 1.05;

        return $this->startkey(array($params['vendeur'], $params['acheteur'], $params['mandataire'], $params['type'], $params['produit'], $volumeBas))
                        ->endkey(array($params['vendeur'], $params['acheteur'], $params['mandataire'], $params['type'], $params['produit'], $volumeHaut, array()))->limit(10)->getView('vrac', 'vracSimilaire');
    }

    public function filterSimilaryContracts($similaryContracts, $vracid) {

        foreach ($similaryContracts->rows as $key => $value) {
            if ($value->id === $vracid) {
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
            throw new sfException('Le hash du produit ne correpond pas au hash initial (' . $vrac->produit . '<->' . $hash . ')');
        return $vrac;
    }

    /**
     *
     * @param string $id
     * @param integer $hydrate
     * @return Vrac 
     */
    public function retrieveById($id, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {

        return parent::retrieveDocumentById('VRAC-' . $id, $hydrate);
    }

    public static function getTypes() {
        return array(self::TYPE_TRANSACTION_MOUTS => "Moûts",
            self::TYPE_TRANSACTION_RAISINS => "Raisins",
            self::TYPE_TRANSACTION_VIN_BOUTEILLE => "Conditionné",
            self::TYPE_TRANSACTION_VIN_VRAC => "Vrac");
    }

    public static function getStatuts() {
        return array(self::STATUS_CONTRAT_ANNULE => "Annulé",
            self::STATUS_CONTRAT_NONSOLDE => "Non soldé",
            self::STATUS_CONTRAT_SOLDE => "Soldé");
    }

    public function getLibelleFromId($id, $separation = " ") {
        $id = str_replace('VRAC-', '', $id);
        return sprintf('%s%s%s', substr($id, 0, 8), $separation, substr($id, 8, strlen($id) - 1));
    }

    public function getLibelleContratNum($id) {
        // if(strlen($id)!=13) throw new Exception(sprintf ('Le numéro de contrat %s ne possède pas un bon format.',$id));
        $annee = substr($id, 0, 4);
        $mois = substr($id, 4, 2);
        $jour = substr($id, 6, 2);
        $num = substr($id, 8);
        return $jour . '/' . $mois . '/' . $annee . ' n° ' . $num;
    }

    public function getNumeroArchiveEtDate($id) {
        $c = $this->findByNumContrat($id);
        return $c->numero_archive . ' du ' . $c->date_signature;
    }

    public function retreiveByStatutsTypes($statuts, $types) {
        return VracStatutAndTypeView::getInstance()->findContatsByStatutsAndTypes($statuts, $types);
    }

    public function retreiveByStatutsTypesAndDate($statuts, $types, $date) {
        return VracStatutAndTypeView::getInstance()->findContatsByStatutsAndTypesAndDate($statuts, $types, $date);
    }

    public function retreiveByWaitForOriginal() {
        return VracOriginalPrixDefinitifView::getInstance()->findContatsByWaitForOriginal();
    }

    public function findContatsByWaitForPrixDefinitif($date) {
        return VracOriginalPrixDefinitifView::getInstance()->findContatsByWaitForPrixDefinitif($date);
    }

    public function getMandatants() {

        return array('acheteur' => 'acheteur', 'vendeur' => 'vendeur');
    }

    public function getMaster($id) {
        return $this->find($id);
    }

}
