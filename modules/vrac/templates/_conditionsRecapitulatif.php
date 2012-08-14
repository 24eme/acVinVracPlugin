<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
use_helper('Vrac');
?>
<div class="bloc_form">
    <div id="conditions_recapitulatif_typeContrat" class="ligne_form">
        <label>Type de contrat&nbsp;:</label>
        <span><?php echo $vrac->type_contrat; ?></span>
    </div>
    <div id="conditions_recapitulatif_isvariable" class="ligne_form ligne_form_alt">
        <label>prix variable ?</label>
        <span><?php echo ($vrac->prix_variable) ? 'Oui' : 'Non';
        echo ($vrac->prix_variable)? ' ('.$vrac->part_variable.'%)' : '';
        ?>
        </span>
    </div>

    <div id="conditions_recapitulatif_cvo" class="ligne_form">
        <label>CVO&nbsp;: </label>
        <span><?php 
        echo getCvoLabels($vrac->cvo_nature).' ('. $vrac->cvo_repartition.')';
        ?></span>
    </div>
    
    <div id="conditions_recapitulatif_commentaires" class="ligne_form ligne_form_alt">
        <label>Commentaires&nbsp;: </label>
             <span style="width: 100%; height: 100%;"><?php 
            echo $vrac->commentaires;
            ?></span>
        </textarea>
    </div>
    
</div>