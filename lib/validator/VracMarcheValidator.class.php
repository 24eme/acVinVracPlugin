<?php

class VracMarcheValidator extends sfValidatorBase {

    public function configure($options = array(), $messages = array()) {
        $this->addOption('determination_prix_field', 'determination_prix');
    }
    
	protected function getTypePrixNeedDetermination() {

      return array("objectif", "acompte");
    }
    
    protected function doClean($values) {
    	if (isset($values['type_prix']) && in_array($values['type_prix'], $this->getTypePrixNeedDetermination())) {
    		if (isset($values['determination_prix']) && !($values['determination_prix'])) {
    			throw new sfValidatorErrorSchema($this, array($this->getOption('determination_prix_field') => new sfValidatorError($this, 'required')));
    		}
    	}
    	if (isset($values['millesime']) && $values['millesime']) {
    		if (!preg_match('/[0-9]{4}/', $values['millesime'])) {
    			throw new sfValidatorErrorSchema($this, array('millesime' => new sfValidatorError($this, 'invalid')));
    		}
    		if ($values['millesime'] > date('Y')) {
    			throw new sfValidatorErrorSchema($this, array('millesime' => new sfValidatorError($this, 'invalid')));
    		}
    	}
        return $values;
    }

}