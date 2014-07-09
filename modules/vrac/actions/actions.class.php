<?php

/**
 * vrac actions.
 *
 * @package    vinsdeloire
 * @subpackage vrac
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class vracActions extends sfActions
{

    public function executeRedirect(sfWebRequest $request) {
        $vrac = VracClient::getInstance()->find($request->getParameter('identifiant_vrac'));
        $this->forward404Unless($vrac);
        return $this->redirect('vrac_visualisation', array('numero_contrat' => $vrac->numero_contrat));
    }
    
  public function executeIndex(sfWebRequest $request)
  {
      $this->vracs = VracClient::getInstance()->retrieveLastDocs(10);
      $this->etiquettesForm = new VracEtiquettesForm();
      $this->postFormEtablissement($request);
  }
  
  public function executeCreation(sfWebRequest $request) {
      $this->identifiant = str_replace('ETABLISSEMENT-', '', $request->getParameter('identifiant'));
      $etablissement = EtablissementClient::getInstance()->find($this->identifiant);
      $this->vrac = new Vrac();
      
      $this->vrac->setEtablissementCreateur($etablissement);
      $this->vrac->update();
      $this->vrac->setInformations();
      $this->vrac->numero_contrat = VracClient::getInstance()->getNextNoContrat();
      $this->vrac->save();
      $this->redirect('vrac_soussigne',array('numero_contrat' => $this->vrac->numero_contrat));
  }

  protected function postFormEtablissement(sfWebRequest $request) {
    if ($request->isMethod(sfWebRequest::POST)) {
        $form = new VracEtablissementChoiceForm('INTERPRO-inter-loire');
        $form->bind($request->getParameter($form->getName()));
        if ($form->isValid())
        {
            $etablissement = $form->getEtablissement();
            return $this->redirect(array('sf_route' => 'vrac_recherche', 'identifiant' => $etablissement->identifiant));
        }

        return $this->redirect('vrac');
    }
  }  
  
  public function executeRecherche(sfWebRequest $request) 
  { 
    $this->recherche = $this->getVracsFromRecherche($request, true);
      $this->form = new VracRechercheForm();
  }
   
  private function getVracsFromRecherche($request, $limited = true)
  {
      $this->isType = isset($request['type']);
      $this->isStatut = isset($request['statut']);
      $this->identifiant = str_replace('ETABLISSEMENT-', '', $request->getParameter('identifiant'));
      $soussigneObj = EtablissementClient::getInstance()->find($this->identifiant);
      $soussigneId = 'ETABLISSEMENT-'.$this->identifiant;
      $this->type = null;
      $this->statut = null;
      $this->multiCritereType = null;
      $this->multiCritereStatut = null;
      $this->actifs = array();
      $this->actifs['type'] = '';
      $this->actifs['statut'] = '';
      
      $this->campagne = $request->getParameter('campagne', ConfigurationClient::getInstance()->getCurrentCampagne());
      if($this->isType && $this->isStatut)
      {
          $this->statut = $request['statut'];
          $this->type = $request['type'];
          $this->vracs = ($limited)? 
	    VracClient::getInstance()->retrieveBySoussigneStatutAndType($soussigneId, $this->campagne, $this->statut,$this->type)
	    : VracClient::getInstance()->retrieveBySoussigneStatutAndType($soussigneId, $this->campagne, $this->statut,$this->type,false);
          $this->actifs['statut'] = $request['statut'];
          $this->actifs['type'] = $request['type'];     
          $this->multiCritereStatut = true;
          $this->multiCritereType = true;
      }      
      elseif($this->isStatut)
      {
          $this->statut = $request['statut'];
          $this->vracs = ($limited)? 
	    VracClient::getInstance()->retrieveBySoussigneAndStatut($soussigneId, $this->campagne, $request['statut'])
	    : VracClient::getInstance()->retrieveBySoussigneAndStatut($soussigneId,$this->campagne, $request['statut'],false);
          $this->actifs['statut'] = $request['statut'];
          $this->multiCritereType = true;
      }
      elseif ($this->isType)
      {
          $this->type = $request['type'];
          $this->vracs = ($limited)? 
	    VracClient::getInstance()->retrieveBySoussigneAndType($soussigneId, $this->campagne, $request['type'])
	    : VracClient::getInstance()->retrieveBySoussigneAndType($soussigneId, $this->campagne, $request['type'],false);
          $this->actifs['type'] = $request['type'];
          $this->multiCritereStatut = true;
      }
      else
      {          
          $this->vracs = ($limited)? 
	    VracClient::getInstance()->retrieveBySoussigne($soussigneId, $this->campagne)
	    : VracClient::getInstance()->retrieveBySoussigne($soussigneId, $this->campagne, false);
      }
            
      usort($this->vracs->rows, array("vracActions", "rechercheTriListOnID"));
            
      $this->etablissements = array('' => '');
      $soussignelabel = array($soussigneObj->nom, $soussigneObj->cvi, $soussigneObj->siege->commune);
      $this->etablissements[$this->identifiant] = trim(implode(',', array_filter($soussignelabel)));

      $datas = EtablissementClient::getInstance()->findAll()->rows;

      foreach($datas as $data) 
        {
                $labels = array($data->key[4], $data->key[3], $data->key[1]);
                $this->etablissements[$data->id] = trim(implode(',', array_filter($labels)));
        }
    return true;
  }

  static function rechercheTriListOnID($etb0, $etb1)
  {
    if ($etb0->id == $etb1->id) {
            
      return 0;
    }
    return ($etb0->id > $etb1->id) ? -1 : +1;
  }
  
  public function executeNouveau(sfWebRequest $request)
  {      
      $this->getResponse()->setTitle('Contrat - Nouveau');
      $this->vrac = ($this->getUser()->getAttribute('vrac_object'))? unserialize($this->getUser()->getAttribute('vrac_object')) : new Vrac();
      $this->vrac = $this->populateVracTiers($this->vrac);
      if($this->etablissement = $request->getParameter("etablissement")) {
        //$this->vrac->setAcheteurIdentifiant($this->etablissement);
        $this->vrac->createur_identifiant = $this->etablissement;
      }
      $this->form = new VracSoussigneForm($this->vrac);
 
      $this->init_soussigne($request,$this->form);
      $this->nouveau = true;
      $this->contratNonSolde = false;
      if ($request->isMethod(sfWebRequest::POST)) 
        {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid())
            {
                $this->maj_etape(1);
                $this->vrac->numero_contrat = VracClient::getInstance()->getNextNoContrat();
                $this->form->save();    
                return $this->redirect('vrac_marche', $this->vrac);
            }            
        }
      $this->setTemplate('soussigne');
  }
	
	public function executeAnnuaire(sfWebRequest $request)
	{
		$this->identifiant = str_replace('ETABLISSEMENT-', '', $request->getParameter('identifiant'));
		$this->type = $request->getParameter('type');
		$this->acteur = $request->getParameter('acteur');
		$types = array_keys(AnnuaireClient::getAnnuaireTypes());
		if (!in_array($this->type, $types)) {
			throw new sfError404Exception('Le type "'.$this->type.'" n\'est pas pris en charge.');
		}

		$this->vrac = ($request->getParameter('numero_contrat'))? VracClient::getInstance()->find($request->getParameter('numero_contrat')) : new Vrac() ;
		$this->vrac = $this->populateVracTiers($this->vrac);
		if($this->identifiant) {
        	$this->vrac->createur_identifiant = $this->identifiant;
      	}
		$this->form = new VracSoussigneAnnuaireForm($this->vrac);
		if ($request->isMethod(sfWebRequest::POST)) {
			$parameters = $request->getParameter($this->form->getName());
			unset($parameters['_csrf_token']);
    		$this->form->bind($parameters);
        	if ($this->form->isValid()) {
        		$this->vrac = $this->form->getUpdatedVrac();
        	} else {
        		throw new sfException($this->form->renderGlobalErrors());
        	}
		}
		$this->getUser()->setAttribute('vrac_object', serialize($this->vrac));
		$this->getUser()->setAttribute('vrac_acteur', $this->acteur);
		return $this->redirect('annuaire_selectionner', array('identifiant' => $this->identifiant, 'type' => $this->type));
	}
	
	public function executeAnnuaireCommercial(sfWebRequest $request)
	{
		$this->identifiant = str_replace('ETABLISSEMENT-', '', $request->getParameter('identifiant'));
		$this->vrac = ($request->getParameter('numero_contrat'))? VracClient::getInstance()->find($request->getParameter('numero_contrat')) : new Vrac() ;
		$this->vrac = $this->populateVracTiers($this->vrac);
		if($this->identifiant) {
        	$this->vrac->createur_identifiant = $this->identifiant;
      	}
		$this->form = new VracSoussigneAnnuaireForm($this->vrac);
		if ($request->isMethod(sfWebRequest::POST)) {
			$parameters = $request->getParameter($this->form->getName());
			unset($parameters['_csrf_token']);
    		$this->form->bind($parameters);
        	if ($this->form->isValid()) {
        		$this->vrac = $this->form->getUpdatedVrac();
        	} else {
        		throw new sfException($this->form->renderGlobalErrors());
        	}
		}
		$this->getUser()->setAttribute('vrac_object', serialize($this->vrac));
		$this->getUser()->setAttribute('vrac_acteur', $this->acteur);
		return $this->redirect('annuaire_commercial_ajouter', array('identifiant' => $this->identifiant));
	}
  
  private function init_soussigne($request,$form)
    {
        $form->vendeur = null;
        $form->acheteur = null;  
        $form->mandataire = null;  

        if(!is_null($request->getParameter('vrac')) && !$request->getParameter('vrac')=='')
        {
            $vracParam = $request->getParameter('vrac');

            if(isset($vracParam['vendeur_identifiant']) && $vracParam['vendeur_identifiant'])
            { 
                $form->vendeur = EtablissementClient::getInstance()->find($vracParam['vendeur_identifiant']);
            }
            if(isset($vracParam['acheteur_identifiant']) && $vracParam['acheteur_identifiant'])
            { 
                $form->acheteur = EtablissementClient::getInstance()->find($vracParam['acheteur_identifiant']);
            }
            if(isset($vracParam['mandataire_identifiant']) && $vracParam['mandataire_identifiant'])
            { 
                $form->mandataire = EtablissementClient::getInstance()->find($vracParam['mandataire_identifiant']);
            }
        }
    }
  
  public function executeSoussigne(sfWebRequest $request)
  {
      $this->getResponse()->setTitle(sprintf('Contrat N° %d - Soussignés', $request["numero_contrat"]));
      $this->vrac = $this->vrac = ($this->getUser()->getAttribute('vrac_object'))? unserialize($this->getUser()->getAttribute('vrac_object')) : $this->getRoute()->getVrac();
      $this->form = new VracSoussigneForm($this->vrac);
      
      $this->init_soussigne($request,$this->form);
      $this->nouveau = false;
      $this->hasmandataire = !is_null($this->vrac->mandataire_identifiant);
      $this->contratNonSolde = ((!is_null($this->vrac->valide->statut)) && ($this->vrac->valide->statut!=VracClient::STATUS_CONTRAT_SOLDE));

      if ($request->isMethod(sfWebRequest::POST)) 
        {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid())
            {
                $this->maj_etape(1);                
                $this->form->save();     
                $this->redirect('vrac_marche', $this->vrac);
            }
        }
  }  
  
  public function executeMarche(sfWebRequest $request)
  {
  		$this->getUser()->setAttribute('vrac_object', null);
    	$this->getUser()->setAttribute('vrac_acteur', null);
        $this->getResponse()->setTitle(sprintf('Contrat N° %d - Marché', $request["numero_contrat"]));
        $this->vrac = $this->getRoute()->getVrac();
        $this->form = new VracMarcheForm($this->vrac);   
      
        if ($request->isMethod(sfWebRequest::POST)) 
        {
            $this->form->bind($request->getParameter($this->form->getName()));           
            if ($this->form->isValid())
            {   
                    $this->maj_etape(2);
                    $this->form->save();
                    $this->redirect('vrac_condition', $this->vrac);
            }
        }
  }

  public function executeCondition(sfWebRequest $request)
  {
  	  $this->getUser()->setAttribute('vrac_object', null);
      $this->getUser()->setAttribute('vrac_acteur', null);
      $this->getResponse()->setTitle(sprintf('Contrat N° %d - Conditions', $request["numero_contrat"]));
      $this->vrac = $this->getRoute()->getVrac();
      $this->form = new VracConditionForm($this->vrac);
      $this->displayPartiePrixVariable = !(is_null($this->type_contrat) || ($this->type_contrat=='spot'));
      $this->displayPrixVariable = ($this->displayPartiePrixVariable && !is_null($vrac->prix_variable) && $vrac->prix_variable); 
      $this->contratNonSolde = ((!is_null($this->vrac->valide->statut)) && ($this->vrac->valide->statut!=VracClient::STATUS_CONTRAT_SOLDE));
      if ($request->isMethod(sfWebRequest::POST)) 
        {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid())
            {
                $this->maj_etape(3);
                $this->form->save();      
                $this->redirect('vrac_validation', $this->vrac);
            }
        }
  }

   public function executeValidation(sfWebRequest $request)
  {
  	    $this->getUser()->setAttribute('vrac_object', null);
    	$this->getUser()->setAttribute('vrac_acteur', null);
      $this->getResponse()->setTitle(sprintf('Contrat N° %d - Validation', $request["numero_contrat"]));
      $this->vrac = $this->getRoute()->getVrac();

      $this->contratNonSolde = ((!is_null($this->vrac->valide->statut)) && ($this->vrac->valide->statut!=VracClient::STATUS_CONTRAT_SOLDE));
      $this->vracs = VracClient::getInstance()->retrieveSimilaryContracts($this->vrac);
      $this->contratsSimilairesExist = (isset($this->vracs) && !$this->vracs && count($this->vracs)>0);

      $this->validation = new VracValidation($this->vrac);
      
      if ($request->isMethod(sfWebRequest::POST)) 
      {
            if($this->validation->isValide()){
                $this->maj_etape(4);
                $this->vrac->validate();
                $this->vrac->save();
                $this->getUser()->setFlash('postValidation', true);
                $this->redirect('vrac_visualisation', $this->vrac);
            }   
        }
  }
  
   
  public function executeVisualisation(sfWebRequest $request)
  {
  	   $this->getUser()->setAttribute('vrac_object', null);
    	$this->getUser()->setAttribute('vrac_acteur', null);
      $this->getResponse()->setTitle(sprintf('Contrat N° %d - Visualisation', $request["numero_contrat"]));
      $this->vrac = $this->getRoute()->getVrac();   
      $this->vrac->save();
      if ($request->isMethod(sfWebRequest::POST)) 
      {
            $this->majStatut(VracClient::STATUS_CONTRAT_ANNULE);
            $this->vrac->save();
      }
  }

  public function executeChangeStatut(sfWebRequest $request)
  {
      $this->vrac = $this->getRoute()->getVrac();
      switch ($statut = $this->vrac->valide->statut) {
          case VracClient::STATUS_CONTRAT_NONSOLDE:
              {
                $this->vrac->valide->statut = VracClient::STATUS_CONTRAT_SOLDE;
                $this->vrac->save();
                break;
              }
          case VracClient::STATUS_CONTRAT_SOLDE:
              {
                $this->vrac->valide->statut = VracClient::STATUS_CONTRAT_NONSOLDE;
                $this->vrac->save();
                break;
              }
          default:
              break;
      }
      $this->redirect('vrac_visualisation', $this->vrac);
  }
  
  public function executeGetInformations(sfWebRequest $request) 
  { 
      $etablissement =  EtablissementClient::getInstance()->find($request->getParameter('id'));
      $nouveau = is_null($request->getParameter('numero_contrat'));
      return $this->renderPartialInformations($etablissement,$nouveau);
  }
  
  public function executeGetModifications(sfWebRequest $request)
  {
        $nouveau = is_null($request->getParameter('numero_contrat'));
        $etablissementId = ($request->getParameter('id')==null)? $request->getParameter('vrac_'.$request->getParameter('type').'_identifiant') : $request->getParameter('id');      
        $etablissement =  EtablissementClient::getInstance()->find($etablissementId);
        
        $this->forward404Unless($etablissement);

        $this->form = new VracSoussigneModificationForm($etablissement);
        
        if ($request->isMethod(sfWebRequest::POST)) 
        {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid())
            {                
                $this->form->save();      
                return $this->renderPartialInformations($etablissement,$nouveau);
            }
        }
        
        $familleType = $etablissement->getFamilleType();
        if($familleType == 'vendeur' || $familleType == 'acheteur') $familleType = 'vendeurAcheteur';
        return $this->renderPartial($familleType.'Modification', array('form' => $this->form));
  }
  
  public function executeGetContratsSimilaires(sfWebRequest $params)
  {
       $vrac = VracClient::getInstance()->findByNumContrat($params['numero_contrat']);
       if(isset($params['type']) && $params['type']!="") $vrac->type_transaction = $params['type'];
       if(isset($params['produit']) && $params['produit']!="") $vrac->produit = $params['produit'];     
       if(isset($params['volume']) && $params['volume']!="") $vrac->volume_propose =  $params['volume']+0;
       return $this->renderPartial('contratsSimilaires', array('vrac' => $vrac));
  }

  public function executeVolumeEnleve(sfWebRequest $request)
  {
        $this->vrac = VracClient::getInstance()->findByNumContrat($request['numero_contrat']);

        $this->form = new VracVolumeEnleveForm($this->vrac);
        if ($request->isMethod(sfWebRequest::POST)) 
        {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid())
            {
                $this->form->save();      
                $this->redirect('vrac_visualisation', $this->vrac);
            }
        }
  }
  
  private function createCsvFilename($request)
  {
 
    $etablissement = EtablissementClient::getInstance()->find($request['identifiant']);
    $nom = $etablissement['nom'];
    $nom = str_replace('M. ','', $nom);
    $nom = str_replace('Mme ','', $nom);
    $nom = str_replace(' ','_', $nom);
    $statut = (isset($request['statut']) && !empty($request['statut']))? '_'.$request['statut'] : '';
    $type = (isset($request['type']) && !empty($request['type']))? '_'.$request['type'] : '';
    $date = date('Ymd');
    return 'exportCSV_'.$date.'_'.$nom.$statut.$type;
  }


  public function executeExportCsv(sfWebRequest $request) 
  {    
    $this->setLayout(false);
    $filename = $this->createCsvFilename($request);    
    
    $file = $this->getVracsFromRecherche($request, false);  
    
    $this->forward404Unless($this->vracs);
    
    $attachement = "attachment; filename=".$filename.".csv";
    
    $this->response->setContentType('text/csv');
    $this->response->setHttpHeader('Content-Disposition',$attachement );
  //  $this->response->setHttpHeader('Content-Length', filesize($file));    
  }
  
  
  public function executeExportEtiquette(sfWebRequest $request) 
  {
    $this->date_debut = $request['vrac_vignettes']['date_debut'];
    $this->date_fin = $request['vrac_vignettes']['date_fin'];
    ini_set('memory_limit','2048M');
    set_time_limit(0);
    $this->setLayout(false);
    $filename = 'exportCSV_etiquette_'.date('Ymd'); 
    
    $attachement = "attachment; filename=".$filename.".csv";
    
    $this->response->setContentType('text/csv');
    $this->response->setHttpHeader('Content-Disposition',$attachement ); 
  }
  
  public function executeLatex(sfWebRequest $request) {        
        $this->setLayout(false);
        $this->vrac = $this->getRoute()->getVrac();
        $this->forward404Unless($this->vrac);
	$latex = new VracLatex($this->vrac);
	$latex->echoWithHTTPHeader($request->getParameter('type'));
        exit;
    }
  

  private function renderPartialInformations($etablissement,$nouveau) {
      $familleType = $etablissement->getFamilleType();
      return $this->renderPartial($familleType.'Informations', 
        array($familleType => $etablissement, 'nouveau' => $nouveau));
  }
  
  private function maj_etape($etapeNum)
  {
      if($this->vrac->etape < $etapeNum) $this->vrac->etape = $etapeNum;
  }
            
  private function majStatut($statut)
  {
      $this->vrac->valide->statut = $statut;
  }
  
	protected function populateVracTiers($vrac)
    {
    	$vrac->setInformations();
		return $vrac;
    }
    
}
