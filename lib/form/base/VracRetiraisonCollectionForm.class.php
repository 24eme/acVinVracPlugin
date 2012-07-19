<?php
class VracRetiraisonCollectionForm extends acCouchdbObjectForm implements FormBindableInterface
{
	public function configure()
	{
		$key = 0;
		foreach ($this->getObject() as $key => $object) {
			$this->embedForm ($key, new VracRetiraisonForm($object));
		}
		$this->embedForm (($key+1), new VracRetiraisonForm($this->getObject()->add()));
	}

	public function bind(array $taintedValues = null, array $taintedFiles = null)
	{
		foreach ($this->embeddedForms as $key => $form) {
			if(!array_key_exists($key, $taintedValues)) {
				$this->unEmbedForm($key);
			}
		}
		foreach($taintedValues as $key => $values) {
			if(!is_array($values) || array_key_exists($key, $this->embeddedForms)) {
				continue;
			}
			$this->embedForm($key, new VracRetiraisonForm($this->getObject()->add()));
		}
		parent::bind($taintedValues, $taintedFiles);
	}
    
    public function unEmbedForm($key) 
    {
        unset($this->widgetSchema[$key]);
        unset($this->validatorSchema[$key]);
        unset($this->embeddedForms[$key]);
        $this->getObject()->remove($key);
    }
}