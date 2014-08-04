<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class VracSoussigneForm
 * @author mathurin
 */
class VracSoussigneForm extends acCouchdbObjectForm {

   private $vendeurs = null;
   private $acheteurs = null;
   private $mandataires = null;
   
	public function getAnnuaire()
   	{
		return AnnuaireClient::getInstance()->findOrCreateAnnuaire(str_replace('ETABLISSEMENT-', '', $this->getObject()->createur_identifiant));
   	}
   
    public function configure()
    {
        if ($this->getObject()->isTeledeclare() && $this->getObject()->createur_identifiant) {
        	$vendeurs = $this->getRecoltants();
        	$acheteurs = $this->getNegociants();
        	$commerciaux = $this->getCommerciaux();
        	$this->setWidget('vendeur_identifiant', new sfWidgetFormChoice(array('choices' => $vendeurs), array('class' => 'autocomplete')));
        	$this->setWidget('acheteur_identifiant', new sfWidgetFormChoice(array('choices' => $acheteurs), array('class' => 'autocomplete')));
        	$this->setWidget('commercial', new sfWidgetFormChoice(array('choices' => $commerciaux), array('class' => 'autocomplete')));
            $this->setValidator('vendeur_identifiant', new sfValidatorChoice(array('required' => true, 'choices' => array_keys($vendeurs))));
        	$this->setValidator('acheteur_identifiant', new sfValidatorChoice(array('required' => true, 'choices' => array_keys($acheteurs))));
        	$this->setValidator('commercial', new sfValidatorChoice(array('required' => false, 'choices' => array_keys($commerciaux))));
        	$this->widgetSchema->setLabel('commercial', 'Sélectionner un interlocuteur commercial :');
        } else {
        	$this->setWidget('vendeur_identifiant', new WidgetEtablissement(array('interpro_id' => 'INTERPRO-inter-loire', 'familles' => EtablissementFamilles::FAMILLE_PRODUCTEUR)));
            $this->setWidget('acheteur_identifiant', new WidgetEtablissement(array('interpro_id' => 'INTERPRO-inter-loire','familles' =>  EtablissementFamilles::FAMILLE_NEGOCIANT)));
            $this->setValidator('vendeur_identifiant', new ValidatorEtablissement(array('required' => true, 'familles' => EtablissementFamilles::FAMILLE_PRODUCTEUR)));
        	$this->setValidator('acheteur_identifiant', new ValidatorEtablissement(array('required' => true, 'familles' => EtablissementFamilles::FAMILLE_NEGOCIANT)));
        }
        
        
        $this->setWidget('interne', new sfWidgetFormInputCheckbox());
        
        $this->setWidget('mandataire_exist', new sfWidgetFormInputCheckbox());        
        
        $this->setWidget('mandatant', new sfWidgetFormChoice(array('expanded' => true, 'multiple'=> true , 'choices' => VracClient::getInstance()->getMandatants())));
                
        $this->setWidget('mandataire_identifiant', new WidgetEtablissement(array('interpro_id' => 'INTERPRO-inter-loire', 'familles' =>  EtablissementFamilles::FAMILLE_COURTIER)));
        
        $this->widgetSchema->setLabels(array(
            'vendeur_famille' => '',
            'vendeur_identifiant' => 'Sélectionner un vendeur :',
            'acheteur_famille' => '',
            'acheteur_identifiant' => 'Sélectionner un acheteur :',
            'interne' => 'Cocher si le contrat est interne',
            'mandataire_identifiant' => 'Sélectionner un courtier :',
            'mandataire_exist' => "Décocher s'il n'y a pas de courtier",
            'mandatant' => 'Mandaté par : '
        ));
        
        $this->setValidator('interne', new sfValidatorBoolean(array('required' => false)));
        $this->setValidator('mandataire_identifiant', new ValidatorEtablissement(array('required' => false, 'familles' => EtablissementFamilles::FAMILLE_COURTIER)));
        $this->setValidator('mandataire_exist', new sfValidatorBoolean(array('required' => false)));
        $this->setValidator('mandatant', new sfValidatorChoice(array('required' => false,'multiple'=> true, 'choices' => array_keys(VracClient::getInstance()->getMandatants()))));
        
        $this->validatorSchema['vendeur_identifiant']->setMessage('required', 'Le choix d\'un vendeur est obligatoire');        
        $this->validatorSchema['acheteur_identifiant']->setMessage('required', 'Le choix d\'un acheteur est obligatoire');             
        
        $this->widgetSchema->setNameFormat('vrac[%s]');
    }
    
	protected function updateDefaultsFromObject() {
        parent::updateDefaultsFromObject();
        $defaults = $this->getDefaults();
        if ($this->getObject()->vendeur_identifiant) {
        	$defaults['vendeur_identifiant'] = 'ETABLISSEMENT-'.$this->getObject()->vendeur_identifiant;
        }
        if ($this->getObject()->acheteur_identifiant) {
        	$defaults['acheteur_identifiant'] = 'ETABLISSEMENT-'.$this->getObject()->acheteur_identifiant;
        }
        if ($this->getObject()->interlocuteur_commercial->nom) {
        	$defaults['commercial'] = $this->getObject()->interlocuteur_commercial->nom;
        }
        
        $this->setDefaults($defaults); 
    }
    
    public function doUpdateObject($values) {
        if(isset($values['mandataire_exist']) && !$values['mandataire_exist'])
        {
            $values['mandataire_identifiant'] = null;
        }
        if(!isset($values['mandataire_identifiant']) || !$values['mandataire_identifiant'])
        {
            $values['mandatant'] = null;
            $values['mandataire_exist'] = false;
        }
        if ($values['commercial']) {
            $this->getObject()->storeInterlocuteurCommercialInformations($values['commercial'], $this->getAnnuaire()->commerciaux->get($values['commercial']));
    	} else {
    		$this->getObject()->remove('interlocuteur_commercial');
    		$this->getObject()->add('interlocuteur_commercial');
    	}
        parent::doUpdateObject($values);
        $this->getObject()->setInformations();
    }


    public function getUrlAutocomplete($famille) {

        return sfContext::getInstance()->getRouting()->generate('etablissement_autocomplete_byfamilles', array('familles' => $famille));
    }
    
    public function getRecoltants()
    {
    	$annuaire = $this->getAnnuaire();
    	if (!$annuaire) {
    		return array();
    	}
    	$result = array();
    	foreach ($annuaire->recoltants as $key => $value) {
    		$num = explode('-', $key);
    		$result[$key] = $value." (".$num[1].")";
    	}
    	return array_merge(array('' => ''), $result);
    }
    
    public function getNegociants()
    {
    	$annuaire = $this->getAnnuaire();
    	if (!$annuaire) {
    		return array();
    	}
    	$result = array();
    	foreach ($annuaire->negociants as $key => $value) {
    		$num = explode('-', $key);
    		$result[$key] = $value." (".$num[1].")";
    	}
    	return array_merge(array('' => ''), $result);
    }
    
    public function getCommerciaux()
    {
    	$annuaire = $this->getAnnuaire();
    	if (!$annuaire) {
    		return array();
    	}
    	$commerciaux = $annuaire->commerciaux->toArray();
    	$choices = array();
    	foreach ($commerciaux as $key => $commercial) {
    		$choices[$key] = $key;
    	}
    	return array_merge(array('' => ''), $choices);
    }
}

