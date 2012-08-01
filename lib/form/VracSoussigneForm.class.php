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
   
   private $vendeur_famille = array(EtablissementFamilles::FAMILLE_PRODUCTEUR => 'Producteur', EtablissementFamilles::FAMILLE_NEGOCIANT => 'Negociant');
   private $acheteur_famille = array(EtablissementFamilles::FAMILLE_PRODUCTEUR => 'Producteur', EtablissementFamilles::FAMILLE_NEGOCIANT => 'Negociant');
   
   
    public function configure()
    {
        
        $this->setWidget('vendeur_famille',new sfWidgetFormChoice(array('choices' => $this->vendeur_famille,'expanded' => true)));
        
        $this->setWidget('vendeur_identifiant', new sfWidgetFormChoice(array('choices' =>  $this->getVendeurs()), array('class' => 'autocomplete')));
         
        $this->setWidget('acheteur_famille',new sfWidgetFormChoice(array('choices' => $this->acheteur_famille,'expanded' => true)));
        
        $this->setWidget('acheteur_identifiant', new sfWidgetFormChoice(array('choices' =>   $this->getAcheteurs()), array('class' => 'autocomplete')));
        
        $this->setWidget('mandataire_exist', new sfWidgetFormInputCheckbox());        
        
        $mandatant_identifiantChoice = array('vendeur' => 'vendeur','acheteur' => 'acheteur');
        
        $this->setWidget('mandatant', new sfWidgetFormChoice(array('expanded' => true, 'multiple'=> true , 'choices' => $mandatant_identifiantChoice)));
                
        $this->setWidget('mandataire_identifiant', new sfWidgetFormChoice(array('choices' => $this->getMandataires()), array('class' => 'autocomplete')));
        
        $this->widgetSchema->setLabels(array(
            'vendeur_famille' => '',
            'vendeur_identifiant' => 'Sélectionner un vendeur :',
            'acheteur_famille' => '',
            'acheteur_identifiant' => 'Sélectionner un acheteur :',
            'mandataire_identifiant' => 'Sélectionner un mandataire :',
            'mandataire_exist' => "Décocher s'il n'y a pas de mandataire",
            'mandatant' => 'Mandaté par : '
        ));
        
        $this->setValidators(array(
            'vendeur_famille' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->vendeur_famille))),
            'vendeur_identifiant' => new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getVendeurs()))),
            'acheteur_famille' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->acheteur_famille))),
            'acheteur_identifiant' => new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getAcheteurs()))),
            'mandataire_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->getMandataires()))),
            'mandataire_exist' => new sfValidatorBoolean(array('required' => false)),
            'mandatant' => new sfValidatorChoice(array('required' => false,'multiple'=> true, 'choices' => array_keys($mandatant_identifiantChoice)))
            ));
        
        
        $this->validatorSchema['vendeur_identifiant']->setMessage('required', 'Le choix d\'un vendeur est obligatoire');        
        $this->validatorSchema['acheteur_identifiant']->setMessage('required', 'Le choix d\'un acheteur est obligatoire');             
        
        $this->widgetSchema->setNameFormat('vrac[%s]');
    }
    
    public function getVendeurs()
    {
        if (is_null($this->vendeurs)) {
            $this->vendeurs = $this->getEtablissements(EtablissementFamilles::FAMILLE_PRODUCTEUR);
        }

        return $this->vendeurs;
    }

    public function getAcheteurs()
    {
        if (is_null($this->acheteurs)) {
            $this->acheteurs = $this->getEtablissements(EtablissementFamilles::FAMILLE_NEGOCIANT);
        }

        return $this->acheteurs;
    }

    public function getMandataires()
    {
        if (is_null($this->mandataires)) {
            $this->mandataires = $this->getEtablissements(EtablissementFamilles::FAMILLE_COURTIER);
        }

        return $this->mandataires;
    }

    public function getEtablissements($famille) {
        $etablissements = array('' => '');
        $datas = EtablissementClient::getInstance()->findByFamille($famille)->rows;
        foreach($datas as $data) {
            $labels = array($data->key[4], $data->key[3], $data->key[1]);
            $etablissements[$data->id] = implode(', ', array_filter($labels));
        }
        return $etablissements;
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
        parent::doUpdateObject($values);
        $this->getObject()->setInformations();
    }

}

