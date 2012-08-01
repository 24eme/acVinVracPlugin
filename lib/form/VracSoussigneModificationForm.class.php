<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class VracSoussigneModificationForm
 * @author mathurin
 */
class VracSoussigneModificationForm extends acCouchdbObjectForm {
   
    public function configure()
    {      
        if($this->getObject()->famille == EtablissementFamilles::FAMILLE_PRODUCTEUR) $this->configureAcheteurVendeur('vendeur');
        if($this->getObject()->famille == EtablissementFamilles::FAMILLE_NEGOCIANT) $this->configureAcheteurVendeur('acheteur');
        if($this->getObject()->famille == EtablissementFamilles::FAMILLE_COURTIER) $this->configureMandataire();

        $formSiege = new VracSoussigneModificationSiegeForm($this->getObject()->siege);
        $this->embedForm('siege', $formSiege);

        $this->widgetSchema->setNameFormat('vrac[%s]');    
    }
    
    private function configureAcheteurVendeur($label)
    {         
        $this->setWidget('no_accises', new sfWidgetFormInput()); 
        $this->setWidget('no_tva_intracommunautaire', new sfWidgetFormInput());
            
        $this->widgetSchema->setLabels(array(
            'no_accises' => 'N° ACCISE',
            'no_tva_intracommunautaire' => 'TVA Intracomm.'
        ));
                
        $this->setValidators(array(
            'no_accises' => new sfValidatorString(array('required' => false)),
            'no_tva_intracommunautaire' => new sfValidatorString(array('required' => false))
        )); 
    }
    
    private function configureMandataire() {
                
        $this->setWidget('carte_pro', new sfWidgetFormInput());          
        
        $this->widgetSchema->setLabels(array(
            'carte_pro' => 'N° carte professionnelle',
        ));
        
       $this->setValidators(
       array(
            'carte_pro' => new sfValidatorNumber(array('required' => false)),
            ));
           
    }
}


