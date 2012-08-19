<?php
class acVinVracActions extends sfActions
{
	public function init()
	{
		$this->interpro = $this->getInterpro();
		$this->interpro_name = strtolower($this->getInterproLibelle($this->interpro->_id));
		$this->configurationVrac = $this->getConfigurationVrac($this->interpro->_id);
		$this->configurationVracEtapes = $this->configurationVrac->getEtapes();
	}
	
	public function executeIndex(sfWebRequest $request)
	{
        $this->etablissement = null;
        if ($this->getRoute() instanceof InterfaceEtablissementRoute) {
            $this->etablissement = $this->getRoute()->getEtablissement();
        }

		$this->vracs = VracHistoryView::getInstance()->retrieveLastDocs();
	}

	public function executeNouveau(sfWebRequest $request)
	{
        $this->etablissement = $this->getRoute()->getEtablissement();
		$this->init();
		$vrac = new Vrac();
		$vrac->numero_contrat = $this->getNumeroContrat();
		$vrac->save();
		$this->redirect(array('sf_route' => 'vrac_etape', 
                              'sf_subject' => $vrac, 
                              'step' => $this->configurationVracEtapes->next($vrac->etape), 
                              'etablissement' => $this->etablissement));
	}

	public function executeEtape(sfWebRequest $request)
	{
		$this->forward404Unless($this->etape = $request->getParameter('step'));
		$this->init();
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->vrac = $this->getRoute()->getVrac();
		$this->vrac->setEtape($this->etape);
		$this->form = $this->getForm($this->interpro->_id, $this->etape, $this->configurationVrac, $this->vrac);
		if ($request->isMethod(sfWebRequest::POST)) {
			$this->form->bind($request->getParameter($this->form->getName()));
			if ($this->form->isValid()) {
				$this->form->save();

				if (!$this->configurationVracEtapes->next($this->vrac->etape)) {
    
			        return $this->redirect('vrac_termine', array('sf_subject' => $this->vrac, 'etablissement' => $this->etablissement));
				}

				if (!$this->vrac->has_transaction && $this->configurationVracEtapes->next($this->vrac->etape) == 'transaction') {
					
                    return $this->redirect(array('sf_route' => 'vrac_etape', 'sf_subject' => $this->vrac, 'step' => $this->configurationVracEtapes->next('transaction'), 'etablissement' => $this->etablissement));
				}
				
                return $this->redirect(array('sf_route' => 'vrac_etape', 'sf_subject' => $this->vrac, 'step' => $this->configurationVracEtapes->next($this->vrac->etape), 'etablissement' => $this->etablissement));
			}
		}
	}


  public function executeSetEtablissementInformations(sfWebRequest $request)
  {
        $this->vrac = $this->getRoute()->getVrac();
        $this->etablissement = $this->getRoute()->getEtablissement();   
        $this->soussigne = $request->getParameter('soussigne', null);
        $this->type = $request->getParameter('type', null);
        $this->etape = $request->getParameter('step', null);

        if (!$this->soussigne) {

        	throw new sfException('Numéro d\'établissement du soussigne requis');
        }

        if (!$this->type) {

        	throw new sfException('Type requis');
        }

        if (!$this->etape) {

        	throw new sfException('Etape requis');
        }

        $this->init();
        $this->soussigne = EtablissementClient::getInstance()->find($this->soussigne);
        if (!$this->vrac->exist($this->type)) {

            throw new sfException('Type '.$this->type.' n\'existe pas');
        }
  	
        $this->vrac->setInformations($this->type, $this->soussigne);
		$this->form = $this->getForm($this->interpro->_id, $this->etape, $this->configurationVrac, $this->vrac);
		
        if ($this->type != 'mandataire') {
			return $this->renderPartial('form_etablissement', array('form' => $this->form[$this->type]));
		}
		
    	return $this->renderPartial('form_mandataire', array('form' => $this->form[$this->type]));	
  }
	public function executeRecapitulatif(sfWebRequest $request)
	{
		$this->init();
		$this->vrac = $this->getRoute()->getVrac();
        $this->etablissement = $this->getRoute()->getEtablissement();
	}
	
	public function getForm($interproId, $etape, $configurationVrac, $vrac)
	{
		return VracFormFactory::create($etape, $configurationVrac, $vrac);
	}
	
	public function getNumeroContrat()
	{
		return VracClient::getInstance()->getNextNoContrat();
	}
	
	public function getInterproLibelle($interpro_id = null)
	{
		return ($interpro_id)? str_replace('INTERPRO-', '', $interpro_id) : '';
	}
	
	public function getInterpro()
	{
		return $this->getUser()->getInterpro();
	}
	
	public function getConfigurationVrac($interpro_id = null)
	{
		return ConfigurationClient::getCurrent()->getConfigurationVracByInterpro($interpro_id);
	}
}