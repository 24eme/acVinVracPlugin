<?php
class VracConditionIrForm extends VracForm 
{
   	public function configure()
    {
		parent::configure();
		$this->useFields(array(
		   'annexe',
           'type_prix',
	       'date_limite_retiraison',
           'commentaires_conditions',
           'part_cvo',
           'conditions_paiement',
	       'vin_livre',
           'date_debut_retiraison',
           'calendrier_retiraison',
           'contrat_pluriannuel',
           'reference_contrat_pluriannuel',
	       'delai_paiement',
           'echeancier_paiement',
           'clause_reserve_retiraison',
		   'retiraisons',
		   'paiements'
		));
		$this->getWidget('type_prix')->setOption('expanded', true);
		$this->getWidget('conditions_paiement')->setOption('expanded', true);
		$this->getWidget('delai_paiement')->setOption('expanded', true);
		$this->widgetSchema->setNameFormat('vrac_condition[%s]');
    }
}