<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TeledeclarationImportAnnuaireTask
 *
 * @author mathurin
 */
class TeledeclarationImportAnnuaireTask extends sfBaseTask {

    protected function configure() {


        $this->addArguments(array(
            new sfCommandArgument('soussigneId', sfCommandArgument::REQUIRED, 'soussigne Identifiant'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'vinsdeloire'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace = 'teledeclaration';
        $this->name = 'importAnnuaire';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [generateAlertes|INFO] task does things.
Call it with:

  [php symfony generatePDF|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
        $context = sfContext::createInstance($this->configuration);

        //  $societesContrat = $this->getSocieteContrats();
        $soussigneId = $arguments['soussigneId'];
        $soussignesContrat = array($soussigneId => $soussigneId);

        foreach ($soussignesContrat as $etbId) {
            $this->fillSoussignesAnnuaire($etbId);
        }
    }

    protected function getSoussignesContrat() {
        // TODO
        return;
    }

    protected function fillSoussignesAnnuaire($etbId) {
        $societe = SocieteClient::getInstance()->findByIdentifiantSociete(substr($etbId, 0, 6));
        $contrats = VracClient::getInstance()->retrieveBySocieteWithInfosLimit($societe, $etbId);

        $annuaire = AnnuaireClient::getInstance()->findOrCreateAnnuaire($etbId);
        echo "Ajout dans annuaire de " . $societe->identifiant . " (" . $societe->type_societe . ")\n ------ \n";
        
        foreach ($contrats->contrats as $contrat) {

            $vendeur_typeKey = AnnuaireClient::ANNUAIRE_RECOLTANTS_KEY;
            $vendeurId = $contrat->value[VracClient::VRAC_VIEW_VENDEUR_ID];
            $vendeurNom = $contrat->value[VracClient::VRAC_VIEW_VENDEUR_NOM];

            $acheteur_typeKey = AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY;
            $acheteurId = $contrat->value[VracClient::VRAC_VIEW_ACHETEUR_ID];
            $acheteurNom = $contrat->value[VracClient::VRAC_VIEW_ACHETEUR_NOM];

            $courtier_typeKey = AnnuaireClient::ANNUAIRE_COMMERICAUX_KEY;
            if ($societe->isCourtier()) {
                $vendeur = $annuaire->get($vendeur_typeKey)->add($vendeurId, $vendeurNom);
                echo "Ajout dans l'annuaire de C " . $societe->identifiant . " Vendeur " . $vendeurId . " (" . $vendeurNom . ")\n";

                $acheteur = $annuaire->get($acheteur_typeKey)->add($acheteurId, $acheteurNom);
                echo "Ajout dans l'annuaire de C " . $societe->identifiant . " Acheteur " . $acheteurId . " (" . $acheteurNom . ")\n";
            } else {
                if ($mandataireId = $contrat->value[VracClient::VRAC_VIEW_MANDATAIRE_ID]) {
                    $mandataireNom = $contrat->value[VracClient::VRAC_VIEW_MANDATAIRE_NOM];
                    $mandataire = $annuaire->get($courtier_typeKey)->add($mandataireId, $mandataireNom);
                    echo "Ajout dans l'annuaire de " . $societe->identifiant . " Courtier " . $mandataireId . " (" . $mandataireNom . ")\n";
                }
                $identifiant_vendeur = substr(str_replace('ETABLISSEMENT-', '', $vendeurId), 0, 6);
                $identifiant_acheteur = substr(str_replace('ETABLISSEMENT-', '', $acheteurId), 0, 6);
//                if (substr($etbId, 0, 6) == $identifiant_vendeur) {
//                    $acheteur = $annuaire->get($acheteur_typeKey)->add($acheteurId, $acheteurNom);
//                    echo "Ajout dans l'annuaire de A " . $societe->identifiant . " Acheteur " . $acheteurId . " (" . $acheteurNom . ")\n";
//                }
                if (substr($etbId, 0, 6) == $identifiant_acheteur) {
                    $vendeur = $annuaire->get($vendeur_typeKey)->add($vendeurId, $vendeurNom);
                    echo "Ajout dans l'annuaire de V " . $societe->identifiant . " Vendeur " . $vendeurId . " (" . $vendeurNom . ")\n";
                }
            }
        }
        $annuaire->save();
        echo " ------ \n";
    }

}
