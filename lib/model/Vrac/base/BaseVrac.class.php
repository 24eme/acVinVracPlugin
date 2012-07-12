<?php
/**
 * BaseVrac
 * 
 * Base model for Vrac
 *
 * @property string $_id
 * @property string $_rev
 * @property string $type
 * @property string $numero_contrat
 * @property string $etape
 * @property string $vendeur_type
 * @property string $vendeur_identifiant
 * @property acCouchdbJson $vendeur
 * @property string $vendeur_tva
 * @property acCouchdbJson $adresse_stockage
 * @property string $acheteur_type
 * @property string $acheteur_identifiant
 * @property acCouchdbJson $acheteur
 * @property string $acheteur_tva
 * @property acCouchdbJson $adresse_livraison
 * @property string $mandataire_exist
 * @property acCouchdbJson $mandatant
 * @property string $mandataire_identifiant
 * @property acCouchdbJson $mandataire
 * @property string $premiere_mise_en_marche
 * @property string $production_otna
 * @property string $apport_union
 * @property string $cession_interne
 * @property string $original
 * @property string $type_transaction
 * @property string $produit
 * @property string $contient_domaine
 * @property string $domaine
 * @property acCouchdbJson $labels
 * @property acCouchdbJson $mentions
 * @property string $volume_propose
 * @property string $annexe
 * @property string $prix_unitaire
 * @property string $type_prix
 * @property string $determination_prix
 * @property string $date_limite_retiraison
 * @property string $commentaires_conditions
 * @property string $part_cvo
 * @property string $prix_total
 * @property acCouchdbJson $conditions_paiement
 * @property string $type_echeancier_paiement
 * @property string $vin_livre
 * @property string $date_debut_retiraison
 * @property string $calendrier_retiraison
 * @property acCouchdbJson $retiraisons
 * @property string $contrat_pluriannuel
 * @property string $reference_contrat_pluriannuel
 * @property string $delai_paiement
 * @property string $echeancier_paiement
 * @property acCouchdbJson $paiements
 * @property string $clause_reserve_retiraison
 * @property string $export
 * @property acCouchdbJson $lots
 * @property string $type_contrat
 * @property string $prix_variable
 * @property string $part_variable
 * @property string $cvo_nature
 * @property string $cvo_repartition
 * @property string $date_stats
 * @property string $date_signature
 * @property string $volume_enleve
 * @property acCouchdbJson $valide
 * @property string $nature_document
 * @property string $date_signature_vendeur
 * @property string $date_signature_acheteur
 * @property string $date_signature_mandataire
 * @property string $commentaires

 * @method string get_id()
 * @method string set_id()
 * @method string get_rev()
 * @method string set_rev()
 * @method string getType()
 * @method string setType()
 * @method string getNumeroContrat()
 * @method string setNumeroContrat()
 * @method string getEtape()
 * @method string setEtape()
 * @method string getVendeurType()
 * @method string setVendeurType()
 * @method string getVendeurIdentifiant()
 * @method string setVendeurIdentifiant()
 * @method acCouchdbJson getVendeur()
 * @method acCouchdbJson setVendeur()
 * @method string getVendeurTva()
 * @method string setVendeurTva()
 * @method acCouchdbJson getAdresseStockage()
 * @method acCouchdbJson setAdresseStockage()
 * @method string getAcheteurType()
 * @method string setAcheteurType()
 * @method string getAcheteurIdentifiant()
 * @method string setAcheteurIdentifiant()
 * @method acCouchdbJson getAcheteur()
 * @method acCouchdbJson setAcheteur()
 * @method string getAcheteurTva()
 * @method string setAcheteurTva()
 * @method acCouchdbJson getAdresseLivraison()
 * @method acCouchdbJson setAdresseLivraison()
 * @method string getMandataireExist()
 * @method string setMandataireExist()
 * @method acCouchdbJson getMandatant()
 * @method acCouchdbJson setMandatant()
 * @method string getMandataireIdentifiant()
 * @method string setMandataireIdentifiant()
 * @method acCouchdbJson getMandataire()
 * @method acCouchdbJson setMandataire()
 * @method string getPremiereMiseEnMarche()
 * @method string setPremiereMiseEnMarche()
 * @method string getProductionOtna()
 * @method string setProductionOtna()
 * @method string getApportUnion()
 * @method string setApportUnion()
 * @method string getCessionInterne()
 * @method string setCessionInterne()
 * @method string getOriginal()
 * @method string setOriginal()
 * @method string getTypeTransaction()
 * @method string setTypeTransaction()
 * @method string getProduit()
 * @method string setProduit()
 * @method string getContientDomaine()
 * @method string setContientDomaine()
 * @method string getDomaine()
 * @method string setDomaine()
 * @method acCouchdbJson getLabels()
 * @method acCouchdbJson setLabels()
 * @method acCouchdbJson getMentions()
 * @method acCouchdbJson setMentions()
 * @method string getVolumePropose()
 * @method string setVolumePropose()
 * @method string getAnnexe()
 * @method string setAnnexe()
 * @method string getPrixUnitaire()
 * @method string setPrixUnitaire()
 * @method string getTypePrix()
 * @method string setTypePrix()
 * @method string getDeterminationPrix()
 * @method string setDeterminationPrix()
 * @method string getDateLimiteRetiraison()
 * @method string setDateLimiteRetiraison()
 * @method string getCommentairesConditions()
 * @method string setCommentairesConditions()
 * @method string getPartCvo()
 * @method string setPartCvo()
 * @method string getPrixTotal()
 * @method string setPrixTotal()
 * @method acCouchdbJson getConditionsPaiement()
 * @method acCouchdbJson setConditionsPaiement()
 * @method string getTypeEcheancierPaiement()
 * @method string setTypeEcheancierPaiement()
 * @method string getVinLivre()
 * @method string setVinLivre()
 * @method string getDateDebutRetiraison()
 * @method string setDateDebutRetiraison()
 * @method string getCalendrierRetiraison()
 * @method string setCalendrierRetiraison()
 * @method acCouchdbJson getRetiraisons()
 * @method acCouchdbJson setRetiraisons()
 * @method string getContratPluriannuel()
 * @method string setContratPluriannuel()
 * @method string getReferenceContratPluriannuel()
 * @method string setReferenceContratPluriannuel()
 * @method string getDelaiPaiement()
 * @method string setDelaiPaiement()
 * @method string getEcheancierPaiement()
 * @method string setEcheancierPaiement()
 * @method acCouchdbJson getPaiements()
 * @method acCouchdbJson setPaiements()
 * @method string getClauseReserveRetiraison()
 * @method string setClauseReserveRetiraison()
 * @method string getExport()
 * @method string setExport()
 * @method acCouchdbJson getLots()
 * @method acCouchdbJson setLots()
 * @method string getTypeContrat()
 * @method string setTypeContrat()
 * @method string getPrixVariable()
 * @method string setPrixVariable()
 * @method string getPartVariable()
 * @method string setPartVariable()
 * @method string getCvoNature()
 * @method string setCvoNature()
 * @method string getCvoRepartition()
 * @method string setCvoRepartition()
 * @method string getDateStats()
 * @method string setDateStats()
 * @method string getDateSignature()
 * @method string setDateSignature()
 * @method string getVolumeEnleve()
 * @method string setVolumeEnleve()
 * @method acCouchdbJson getValide()
 * @method acCouchdbJson setValide()
 * @method string getNatureDocument()
 * @method string setNatureDocument()
 * @method string getDateSignatureVendeur()
 * @method string setDateSignatureVendeur()
 * @method string getDateSignatureAcheteur()
 * @method string setDateSignatureAcheteur()
 * @method string getDateSignatureMandataire()
 * @method string setDateSignatureMandataire()
 * @method string getCommentaires()
 * @method string setCommentaires()
 
 */
 
abstract class BaseVrac extends acCouchdbDocument {

    public function getDocumentDefinitionModel() {
        return 'Vrac';
    }
    
}