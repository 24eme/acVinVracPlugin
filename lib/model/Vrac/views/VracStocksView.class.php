<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class VracOriginalPrixDefinitifView
 * @author mathurin
 */
class VracStocksView extends acCouchdbView {

    const KEY_CAMPAGNE = 0;
    const KEY_TYPE_TRANSACTION = 1;
    const KEY_SOCIETE_IDENTIFIANT = 2;
    const KEY_ETABLISSEMENT_IDENTIFIANT = 3;
    const KEY_PRODUIT_HASH = 4;
    const KEY_NUMERO_CONTRAT = 5;

    public static function getInstance() {
        return acCouchdbManager::getView('vrac', 'stocks', 'Vrac');
    }

    public function getVolumeRestantVin($campagne, $etablissement, $hash_produit) {

        return $this->getVolumeRestantWithManyTypesTransaction($campagne, VracClient::$types_transaction_vins, $etablissement, $hash_produit);
    }

    public function getVolumeRestantWithManyTypesTransaction($campagne, array $types_transaction, Etablissement $etablissement, $hash_produit) {
        $volume = 0;
        foreach($types_transaction as $type_transaction) {
            $volume += $this->getVolumeRestant($campagne, $type_transaction, $etablissement, $hash_produit);
        }

        return $volume;
    }

    public function getVolumeRestant($campagne, $type_transaction, Etablissement $etablissement, $hash_produit) {

        return $this->_getVolumeRestant($campagne, $type_transaction, null, $etablissement->identifiant, $hash_produit);
    }

    protected function _getVolumeRestant($campagne, $type_transaction, $societe, $etablissement, $hash_produit) {
        $rows = $this->client->startkey(array($campagne, $type_transaction, $societe, $etablissement, $hash_produit))
                     ->endkey(array($campagne, $type_transaction, $societe, $etablissement,$hash_produit, array()))
                     ->group_level(self::KEY_PRODUIT_HASH + 1)
                     ->getView($this->design, $this->view)->rows;
        
        foreach($rows as $row) {
            return $row->value;
        }

        return 0;
    }
    
}
