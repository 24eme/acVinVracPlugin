<?php

function getCvoLabels($label)
{
   $cvo_nature = array(VracClient::CVO_NATURE_MARCHE_DEFINITIF => 'Marché définitif',
                       VracClient::CVO_NATURE_COMPENSATION => 'Compensation',
                       VracClient::CVO_NATURE_NON_FINANCIERE => 'Non financière',
                       VracClient::CVO_NATURE_VINAIGRERIE => 'Vinaigrerie');
   return $cvo_nature[$label];
}

function dateCampagneViticolePresent()
{
    $date = date('mY');
    $mois = substr($date, 0,2);
    $annee = substr($date, 2,6);
    $campagne = ($mois<8)? ($annee-1).'/'.$annee : $annee.'/'.($annee+1);
    return $campagne;
}

function dateCampagneViticole($date)
{
    $date_exploded = explode("/", $date);
    $mois = $date_exploded[1];
    $annee = $date_exploded[2];
    $campagne = ($mois<8)? ($annee-1).'/'.$annee : $annee.'/'.($annee+1);
    return $campagne;
}
 
function isARechercheParam($actifs,$label)
{
    return in_array($label, $actifs);
}

function statusColor($status)
{
    
    if(is_null($status)) return '';
    
    switch ($status)
    {
        case VracClient::STATUS_CONTRAT_ANNULE:
            return 'statut_annule';
        case VracClient::STATUS_CONTRAT_SOLDE:
            return 'statut_solde';
        case VracClient::STATUS_CONTRAT_NONSOLDE:
            return 'statut_non-solde';
        default :
            return '';
    }
}

function showRecapPrixUnitaire($vrac)
{
    if($type = $vrac->type_transaction)
    {
        switch ($type)
        {
            case 'raisins': return echoF($vrac->prix_unitaire).' €/kg, soit '.
                    echoF($vrac->prix_hl).' €/hl';
            case 'mouts': return echoF($vrac->prix_unitaire).' €/hl';
            case 'vin_vrac': return echoF($vrac->prix_unitaire).' €/hl';                   
            case 'vin_bouteille': 
                if ($vrac->bouteilles_quantite == 0 || $vrac->bouteilles_contenance_volume == 0) {
                    return 0;
                }
                return echoF($vrac->prix_unitaire).' €/btle, soit '.
                    echoF($vrac->prix_hl).' €/hl';
        }
    }    
    return '';
}

function showType($vrac)
{
    if($type = $vrac->type_transaction)
    {
        return showTypeFromLabel($type);
    }    
    return '';
}

function showTypeFromLabel($type)
{
    switch ($type)
        {
            case 'vin_vrac': return 'vin vrac';                   
            case 'vin_bouteille': return 'vin conditionné';
            default: return $type;
        }
}

function showRecapVolumePropose($vrac)
{
    if($type = $vrac->type_transaction)
    {
        switch ($type)
        {
            case 'raisins': 
                return echoF($vrac->raisin_quantite).' kg (raisins), soit '.echoF($vrac->volume_propose).' hl';
            case 'mouts': return echoF($vrac->volume_propose).' hl (moûts)';
            case 'vin_vrac': return echoF($vrac->volume_propose).' hl (vin vrac)';                   
            case 'vin_bouteille': 
                return echoF($vrac->bouteilles_quantite).
                    ' bouteilles ('.$vrac->bouteilles_contenance_libelle.'), soit '.echoF($vrac->volume_propose).' hl' ;
        }
    }    
    return '';
}

function showUnite($vrac)
{
    if($type = $vrac->type_transaction)
    {
        switch ($type)
        {
            case 'raisins': return 'kg';
            case 'mouts': return 'hl';
            case 'vin_vrac': return 'hl';                    
            case 'vin_bouteille': return 'btle';
        }
    }    
    return '';
}

      
function typeProduit($type)
{
    switch ($type) {
        case VracClient::TYPE_TRANSACTION_VIN_BOUTEILLE :
            return 'Btl';
        case VracClient::TYPE_TRANSACTION_VIN_VRAC :
            return 'V';
        case VracClient::TYPE_TRANSACTION_MOUTS :
            return 'M';
        case VracClient::TYPE_TRANSACTION_RAISINS :
            return 'R';
    }
    return '';
}   

function echoF($f){
    return sprintf("%01.02f", round($f, 2)); 
}
