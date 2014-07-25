<?php
/* Fichier : marcheSuccess.php
 * Description : Fichier php correspondant à la vue de vrac/XXXXXXXXXXX/marche
 * Formulaire d'enregistrement de la partie marche d'un contrat
 * Auteur : Petit Mathurin - mpetit[at]actualys.com
 * Version : 1.0.0 
 * Derniere date de modification : ${date}
 */
$contratNonSolde = ((!is_null($form->getObject()->valide->statut)) && ($form->getObject()->valide->statut != VracClient::STATUS_CONTRAT_SOLDE));
?>
<script type="text/javascript">
    $(document).ready(function()
    {
        var ajaxParams = { 'numero_contrat' : '<?php echo $form->getObject()->numero_contrat ?>',
            'vendeur' : '<?php echo $form->getObject()->vendeur_identifiant ?>',
            'acheteur' : '<?php echo $form->getObject()->acheteur_identifiant ?>',
            'mandataire' : '<?php echo $form->getObject()->mandataire_identifiant ?>' };
                      
        $('#produit input').live( "autocompleteselect", function(event, ui)
        {
           
            var integrite = getContratSimilaireParams(ajaxParams,ui);
            refreshContratsSimilaire(integrite,ajaxParams);
                
        });
       
        $('#volume_total').change(function()
        {
            var integrite = getContratSimilaireParams(ajaxParams,null);
            refreshContratsSimilaire(integrite,ajaxParams);     
        });
       
        $('#type_transaction input').change(function()
        {
            var integrite = getContratSimilaireParams(ajaxParams,null);
            changeMillesimeLabel();
            refreshContratsSimilaire(integrite,ajaxParams);                 
        });
       
       var changeMillesimeLabel = function(){
           switch($("#type_transaction input:checked").val()){
               case "<?php echo VracClient::TYPE_TRANSACTION_MOUTS ?>":
               case "<?php echo VracClient::TYPE_TRANSACTION_RAISINS ?>":
                  $("div#millesime label").text('Récolte');
                  break;
               
               case "<?php echo VracClient::TYPE_TRANSACTION_VIN_VRAC ?>":
               case "<?php echo VracClient::TYPE_TRANSACTION_VIN_BOUTEILLE ?>":
                  $("div#millesime label").text('Millésime');
                  break;
           }
       }
       
        var refreshContratsSimilaire = function(integrite,ajaxParams) 
        {
            if(integrite) 
            {
                $.get('getContratsSimilaires',ajaxParams,
                function(data)
                {
                    $('#contrats_similaires').html(data);
                });               
            }      
        }
           
       
        var getContratSimilaireParams = function(ajaxParams,ui)
        {
            var type = $('#type_transaction input:checked').val();
            if(type=='') return false;
            ajaxParams['type'] = type;
           
            if(ui==null){
                ajaxParams['produit'] = $('#produit option:selected').val();
            }
            else{
                ajaxParams['produit'] = ui.item.option.value;
            }
           
            var volume = $('#volume_total').val();
            if((volume!='') && (ajaxParams['produit']=='')) return false;
            ajaxParams['volume'] = volume;
           
            return true;
        }
    });
    
        var densites = [];
        <?php
        foreach ($form->getProduits() as $key => $prod) :
            if ($key != "") :
                ?>
        densites["<?php echo $key ?>"] = "<?php echo ConfigurationClient::getCurrent()->get($key)->getDensite(); ?>";
                <?php
            endif;
        endforeach;
        ?>
    
</script>
<section id="principal">
    <?php include_partial('headerVrac', array('vrac' => $vrac, 'compte' => $compte, 'actif' => 2)); ?>
    <div id="contenu_etape">  
        <form id="vrac_marche" method="post" action="<?php echo url_for('vrac_marche', $vrac) ?>">    

            <?php echo $form->renderHiddenFields() ?>
            <?php echo $form->renderGlobalErrors() ?>


            <div id="marche">

                <?php if (isset($form['attente_original'])): ?>
                <!--  Affichage des l'option original  -->
                <div id="original" class="original section_label_strong">
                    <?php echo $form['attente_original']->renderLabel() ?>
                    <?php echo $form['attente_original']->render() ?>        
                    <?php echo $form['attente_original']->renderError(); ?>
                </div>
                <?php endif; ?>

                <!--  Affichage des transactions disponibles  -->
                <div id="type_transaction" class="type_transaction section_label_maj">
                    <?php echo $form['type_transaction']->renderLabel() ?>
                    <?php echo $form['type_transaction']->renderError(); ?>                    
                    <?php echo $form['type_transaction']->render() ?>        
                </div>

                <!--  Affichage des produits, des labels et du stock disponible  -->
                <div id="vrac_marche_produitLabel" class="section_label_maj">
                    <?php include_partial('marche_produitLabel', array('form' => $form)); ?>
                </div>

                <!--  Affichage des volumes et des prix correspondant  -->
                <div id="vrac_marche_volumePrix" class="section_label_maj">
                    <?php include_partial('marche_volumePrix', array('form' => $form)); ?>
                </div>

            </div>

            <div class="btn_etape">
                <a href="<?php echo url_for('vrac_soussigne', $vrac); ?>" class="btn_etape_prec"><span>Etape précédente</span></a>
                <button class="btn_etape_suiv" type="submit"><span>Etape Suivante</span></button>     
            </div>
        </form>
    </div>      
</section>

<?php

if($vrac->isTeledeclare()):
include_partial('colonne_droite', array('societe' => $societe, 'etablissementPrincipal' => $etablissementPrincipal));
else:
slot('colApplications');
/*
 * Inclusion du panel de progression d'édition du contrat
 */
if (!$contratNonSolde)
    include_partial('contrat_progression', array('vrac' => $vrac));

/*
 * Inclusion du panel pour les contrats similaires
 */
include_partial('contratsSimilaires', array('vrac' => $vrac));

/*
 * Inclusion des Contacts
 */
include_partial('contrat_infos_contact', array('vrac' => $vrac));

end_slot();
endif;
?>  