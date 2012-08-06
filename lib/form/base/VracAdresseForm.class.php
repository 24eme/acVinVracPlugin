<?php
class VracAdresseForm extends acCouchdbObjectForm
{
	public function configure()
	{
		$this->setWidgets(array(
	       'libelle' => new sfWidgetFormInputText(),
		   'adresse' => new sfWidgetFormInputText(),
	       'code_postal' => new sfWidgetFormInputText(),
	       'commune' => new sfWidgetFormInputText()
		));
		$this->widgetSchema->setLabels(array(
		   'libelle' => 'Libellé:',
	       'adresse' => 'Adresse:',
	       'code_postal' => 'Code postal:',
	       'commune' => 'Commune:'
		));
		$this->setValidators(array(
		   'libelle' => new sfValidatorString(array('required' => false)),
	       'adresse' => new sfValidatorString(array('required' => false)),
	       'code_postal' => new sfValidatorString(array('required' => false)),
	       'commune' => new sfValidatorString(array('required' => false))
		));
		$this->widgetSchema->setNameFormat('vrac_adresse[%s]');
	}
}