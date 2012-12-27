<?php
/**
 * Model for Vrac
 *
 */

class Vrac extends BaseVrac {

    protected $archivage_document = null;

    public function  __construct() {
        parent::__construct();   
        $this->initDocuments();
    }

    public function __clone() {
        parent::__clone();
        $this->initDocuments();
    }   

    protected function initDocuments() {
        $this->archivage_document = new ArchivageDocument($this);
    }
    
    public function constructId() {
        $this->set('_id', 'VRAC-'.$this->numero_contrat);

        if(!$this->date_signature) {
            $this->date_signature = date('d/m/Y');
        }
        
        if(!$this->date_campagne) {
            $this->date_campagne = date('d/m/Y');
        }
    }

    public function getCampagne() {

        return $this->_get('campagne');
    } 

    public function setNumeroContrat($value) {
        $this->_set('numero_contrat', $value);
    }

    public function setProduit($value) {
        $this->_set('produit', $value);
        $this->produit_libelle = $this->getProduitObject()->getLibelleFormat(array(), "%g% %a% %m% %l% %co% %ce%");
    }
    
    public function setBouteillesContenanceLibelle($c) {
        $this->_set('bouteilles_contenance_libelle', $c);
        if ($c) {
            $this->setBouteillesContenanceVolume(VracClient::$contenance[$c]);
        }
    }

    public function update($params = array()) {
        
        $this->prix_total = null;
        switch ($this->type_transaction)
        {
            case VracClient::TYPE_TRANSACTION_RAISINS :
            {
                $this->prix_total = $this->raisin_quantite * $this->prix_unitaire;
                $this->bouteilles_contenance_libelle = null;
                $this->bouteilles_contenance_volume = null;
                $this->volume_propose = ( $this->raisin_quantite / $this->getDensite() ) / 100.0;
                break;
            }
            case VracClient::TYPE_TRANSACTION_VIN_BOUTEILLE :
            {
                $this->prix_total = $this->bouteilles_quantite * $this->prix_unitaire;
                $this->volume_propose = $this->bouteilles_quantite * $this->bouteilles_contenance_volume;
                break;
            }
            
            case VracClient::TYPE_TRANSACTION_MOUTS :
            case VracClient::TYPE_TRANSACTION_VIN_VRAC :
            {
                $this->prix_total = $this->jus_quantite * $this->prix_unitaire;              
                $this->bouteilles_contenance_libelle = '';
                $this->bouteilles_contenance_volume = null;
                $this->volume_propose = $this->jus_quantite;
                break;
            }              
        }
        if(isset($this->volume_propose) && $this->volume_propose!=0) $this->prix_hl = $this->prix_total/$this->volume_propose;
    }

    public function setInformations() 
    {        
        $this->setAcheteurInformations();
        $this->setVendeurInformations();
        if($this->mandataire_identifiant!=null && $this->mandataire_exist)
        {
            $this->setMandataireInformations();
            
        }
    }

    public function setVendeurIdentifiant($s) {
	return $this->_set('vendeur_identifiant', str_replace('ETABLISSEMENT-', '', $s));
    }

    public function setAcheteurIdentifiant($s) {
        return $this->_set('acheteur_identifiant', str_replace('ETABLISSEMENT-', '', $s));
    }

    public function setMandataireIdentifiant($s) {
        return $this->_set('mandataire_identifiant', str_replace('ETABLISSEMENT-', '', $s));
    }



    private function setAcheteurInformations() 
    {
        $acheteurObj = $this->getAcheteurObject();
        $this->acheteur->nom = $acheteurObj->nom;
        $this->acheteur->cvi = $acheteurObj->cvi;
        $this->acheteur->commune = $acheteurObj->siege->commune;
        $this->acheteur->code_postal = $acheteurObj->siege->code_postal;
    }
    
    private function setMandataireInformations() 
    {
        $mandataireObj = $this->getMandataireObject();
        $this->mandataire->nom = $mandataireObj->nom;
        //TODO : surement à changer
        $this->mandataire->carte_pro = $mandataireObj->identifiant;
        $this->mandataire->adresse = $mandataireObj->siege->commune.'  '.$mandataireObj->siege->code_postal;
    }
    
    private function setVendeurInformations() 
    {
        $vendeurObj = $this->getVendeurObject();
        $this->vendeur->nom = $vendeurObj->nom;
        $this->vendeur->cvi = $vendeurObj->cvi;
        $this->vendeur->commune = $vendeurObj->siege->commune;
        $this->vendeur->code_postal = $vendeurObj->siege->code_postal;       
    }

    public function setDate($attribut, $d) {
        if (preg_match('/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/', $d, $m)) {
              $d = $m[3].'-'.$m[2].'-'.$m[1];
        }
        return $this->_set($attribut, $d);
    }
    public function getDate($attribut, $format) {
        $d = $this->_get($attribut);
        if (!$format)
              return $d;
        $date = new DateTime($d);
        return $date->format($format);
    }
    public function setDateSignature($d) {
        return $this->setDate('date_signature', $d);
    }
    public function getDateSignature($format = 'd/m/Y') {
        return $this->getDate('date_signature', $format);
    }
 
    public function setDateCampagne($d) {
        $this->setDate('date_campagne', $d);
        $this->campagne = VracClient::getInstance()->buildCampagne($this->getDateCampagne('Y-m-d'));
    }
    
    public function setPrixDefinitifUnitaire($p) {
        $this->_set('prix_definitif_unitaire', $p);
        $prix_total_definitif = null;
        switch ($this->type_transaction)
        {
            case VracClient::TYPE_TRANSACTION_RAISINS :
            {
                $prix_total_definitif = $this->raisin_quantite * $this->prix_definitif_unitaire;
                break;
            }
            case VracClient::TYPE_TRANSACTION_VIN_BOUTEILLE :
            {
                $prix_total_definitif = $this->bouteilles_quantite * $this->prix_definitif_unitaire;
                break;
            }
            
            case VracClient::TYPE_TRANSACTION_MOUTS :
            case VracClient::TYPE_TRANSACTION_VIN_VRAC :
                $prix_total_definitif = $this->jus_quantite * $this->prix_definitif_unitaire;
                break;              
        }
        if($this->prix_definitif_unitaire) $this->prix_definitif_hl = $prix_total_definitif/$this->volume_propose;
    }


    public function getDateCampagne($format = 'd/m/Y') {
        return $this->getDate('date_campagne', $format);
    }

    public function getPeriode() {
      $date = $this->getDateSignature('');
      if ($date)
	return $date;
      return date('Y-m-d');
    }

    public function getDroitCVO() {
      return $this->getProduitObject()->getDroitCVO($this->getPeriode());
    }

    public function getProduitObject() 
    {
      return ConfigurationClient::getCurrent()->get($this->produit);
    }

    public function getVendeurObject() 
    {
        return EtablissementClient::getInstance()->find($this->vendeur_identifiant,acCouchdbClient::HYDRATE_DOCUMENT);
    }
    
    public function getAcheteurObject() 
    {
        return EtablissementClient::getInstance()->find($this->acheteur_identifiant,acCouchdbClient::HYDRATE_DOCUMENT);
    }
    
    public function getMandataireObject() 
    {
        return EtablissementClient::getInstance()->find($this->mandataire_identifiant,acCouchdbClient::HYDRATE_DOCUMENT);
    }
    
    public function getSoussigneObjectById($soussigneId) 
    {
        return EtablissementClient::getInstance()->find($soussigneId,acCouchdbClient::HYDRATE_DOCUMENT);
    }

    private function getDensite() 
    {
        return 1.3;
    }

    public function __toString() {

      if ($this->exist("numero_archive") && $this->numero_archive)
        return sprintf("%05d", $this->numero_archive);
      return $this->numero_contrat;
    }
    
    public function enleverVolume($vol)
    {
        $this->volume_enleve += $vol;

        if($this->volume_enleve < 0 ) {

            throw new sfException(sprintf("Suite à un enlevement le volume enleve sur le contrat '%s' est négatif, ce n'est pas normal !", $this->get('_id')));
        }
        
        if($this->volume_propose <= $this->volume_enleve) { 
          $this->solder();
        } else {
          $this->desolder();
        }
    }

    public function isSolde() {
        return $this->valide->statut == VracClient::STATUS_CONTRAT_SOLDE;
    }


    public function solder() {
        $this->valide->statut = VracClient::STATUS_CONTRAT_SOLDE;
    }

    public function desolder() {
        $this->valide->statut = VracClient::STATUS_CONTRAT_NONSOLDE;
    }

    public function isValidee() {
        
        return in_array($this->valide->statut, array(VracClient::STATUS_CONTRAT_SOLDE,  VracClient::STATUS_CONTRAT_NONSOLDE));
    }
    
    public function prixDefinitifExist() {
        return ($this->prix_variable) && ($this->part_variable != null);
    }

    public function isRaisinMoutNegoHorsIL() {
        $isRaisinMout = (($this->type_transaction == VracClient::TYPE_TRANSACTION_RAISINS) || 
                        ($this->type_transaction == VracClient::TYPE_TRANSACTION_MOUTS));
        if(!$isRaisinMout) return false;
        $nego = EtablissementClient::getInstance()->findByIdentifiant($this->acheteur_identifiant);
        return !$nego->isInterLoire();
    }

    protected function preSave() {
        $this->archivage_document->preSave();
    }

    /*** ARCHIVAGE ***/

    public function getNumeroArchive() {

        return $this->_get('numero_archive');
    }

    public function isArchivageCanBeSet() {

        return $this->isValidee();
    }
    
    /*** FIN ARCHIVAGE ***/

    public function isVin() {

        return in_array($this->type_transaction, VracClient::$types_transaction_vins);
    }

    public function getStockCommercialisable() {
        if (!$this->isVin()) {
            return null;
        }
        
        $stock = DRMStocksView::getInstance()->getStockFin($this->campagne, $this->getVendeurObject(), $this->produit);
        $volume_restant = VracStocksView::getInstance()->getVolumeRestantVin($this->campagne, $this->getVendeurObject(), $this->produit);

        return $stock - $volume_restant;
    }
    
    
    public function setPrixUnitaire($p) {
       $this->_set('prix_unitaire', $this->convertStringToFloat($p));   
    }
    
    public function setRaisinQuantite($q) {
       $this->_set('raisin_quantite', $this->convertStringToFloat($q));   
    }
    
    public function setJusQuantite($q) {
        $this->_set('jus_quantite', $this->convertStringToFloat($q));      
    }
    
    private function convertStringToFloat($q){
        $qstring = str_replace(',','.',$q);
        $qfloat = floatval($qstring);
        if(!is_float($qfloat)) throw new sfException("La valeur $qstring n'est pas un nombre valide");
        return $qfloat;
    }

    public function getCoordonneesVendeur(){
        return $this->getCoordonnees($this->vendeur_identifiant);
    }

    public function getCoordonneesAcheteur(){
        return $this->getCoordonnees($this->acheteur_identifiant);
    }
    
    public function getCoordonneesMandataire(){
        return $this->getCoordonnees($this->mandataire_identifiant);
    }
    
    public function getCoordonnees($id_etb) {
        if($etb = EtablissementClient::getInstance()->retrieveById($id_etb))
             return $etb->getContact();
        $compte = new stdClass();
        $compte->nom_a_afficher = 'Nom Prénom';
        $compte->telephone_bureau = '00 00 00 00 00';
        $compte->telephone_mobile = '00 00 00 00 00';
        $compte->fax = '00 00 00 00 00';
        $compte->email = 'email@email.com';
        return $compte;
    }
    
}
