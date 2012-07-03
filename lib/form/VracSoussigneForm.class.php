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
   
	private $vendeur_famille = array('Producteur' => 'Producteur', 'Negociant' => 'Négociant');
	private $acheteur_famille = array('Producteur' => 'Producteur', 'Negociant' => 'Négociant');

	public function getInterpro() {

		return $this->getOption('interpro', null);
	}

   	public function configure()
    {

    	$this->widgetSchema->setNameFormat('vrac[%s]');
    	
        $this->setWidget('vendeur_identifiant', new sfWidgetFormChoice(array('choices' => array($this->getVendeurs())), 																   array('class' => 'autocomplete',
        																				'data-ajax-structure' => $this->getUrlAutocompleteStructure())));
        $this->setWidget('vendeur_famille',new sfWidgetFormChoice(array('choices' => $this->vendeur_famille,'expanded' => true), array('data-autocomplete' => $this['vendeur_identifiant']->renderId())));

       
        $this->setWidget('acheteur_identifiant', new sfWidgetFormChoice(array('choices' => array($this->getVendeurs())), 																						array('class' => 'autocomplete', 
        																				 'data-ajax-structure' => $this->getUrlAutocompleteStructure())));

        $this->setWidget('acheteur_famille',new sfWidgetFormChoice(array('choices' => $this->acheteur_famille,'expanded' => true),array('data-autocomplete' => $this['acheteur_identifiant']->renderId())));        
        
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
        	'vendeur_famille' => new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->vendeur_famille))),
            'vendeur_identifiant' => new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getVendeurs()))),
        	'acheteur_famille' => new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->acheteur_famille))),
            'acheteur_identifiant' => new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getAcheteurs()))),
            'mandataire_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->getMandataires()))),
            'mandataire_exist' => new sfValidatorBoolean(array('required' => false)),
            'mandatant' => new sfValidatorChoice(array('required' => false,'multiple'=> true, 'choices' => array_keys($mandatant_identifiantChoice)))
            ));
        
        
    }

    protected function updateDefaultsFromObject() {
    	parent::updateDefaultsFromObject();

    	if (!$this->getObject()->vendeur_famille) {
        	$this->defaults['vendeur_famille'] = 'Producteur';
        }
        if (!$this->getObject()->acheteur_famille) {
        	$this->defaults['acheteur_famille'] = 'Negociant';
        }

        $this->getWidget('vendeur_identifiant')->setAttribute('data-ajax', $this->getUrlAutocomplete($this->defaults['vendeur_famille']));
        
        $this->getWidget('acheteur_identifiant')->setAttribute('data-ajax', $this->getUrlAutocomplete($this->defaults['acheteur_famille']));

    }
    
    public function getVendeurs()
    {
        if (is_null($this->vendeurs)) {
            $this->vendeurs = $this->getEtablissements();
        }

        return $this->vendeurs;
    }

    public function getAcheteurs()
    {
        if (is_null($this->acheteurs)) {
            $this->acheteurs = $this->getEtablissements();
        }

        return $this->acheteurs;
    }

    public function getMandataires()
    {
        if (is_null($this->mandataires)) {
            $this->mandataires = $this->getEtablissementsByFamille('Courtier');
        }

        return $this->mandataires;
    }

    public function getEtablissements() {

    	return $this->formatEtablissements(EtablissementAllView::getInstance()->findByInterpro($this->getInterpro())->rows);
    }

    public function getEtablissementsByFamille($famille) {        

    	return $this->formatEtablissements(EtablissementAllView::getInstance()->findByInterproAndFamille($this->getInterpro(), $famille)->rows);
    }

    protected function formatEtablissements($datas) {
    	$etablissements = array('' => '');
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
        parent::doUpdateObject($values);
        $this->getObject()->setInformations();
    }

    public function getUrlAutocomplete($familles) {

        return sfContext::getInstance()->getRouting()->generate('etablissement_autocomplete_byfamilles', array('familles' => $familles));
    }

    public function getUrlAutocompleteStructure() {

        return sfContext::getInstance()->getRouting()->generate('etablissement_autocomplete_byfamilles', array('familles' => '%familles%'));
    }

}
?>
