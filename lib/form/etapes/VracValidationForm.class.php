<?php
class VracValidationForm extends VracForm 
{
	public function configure()
    {
    	parent::configure();
		$this->useFields(array(
           'valide'
		));
        $this->widgetSchema->setNameFormat('vrac_validation[%s]');
    }
}