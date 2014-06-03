<?php
/* Fichier : conditionSuccess.php
 * Description : Fichier php correspondant à la vue de vrac/XXXXXXXXXXX/condition
 * Formulaire concernant les conditions du contrat
 * Auteur : Petit Mathurin - mpetit[at]actualys.com
 * Version : 1.0.0 
 * Derniere date de modification : 28-05-12
 */
?>
<section id="principal">
    <?php include_partial('headerVrac', array('vrac' => $form->getObject(), 'actif' => 3)); ?>
    <div id="contenu_etape"> 
        <form id="vrac_condition" method="post" action="<?php echo url_for('vrac_condition', $vrac) ?>">  
            <?php echo $form->renderHiddenFields() ?>
            <?php echo $form->renderGlobalErrors() ?>
            <div id="condition">
                <!--  Affichage du type de contrat (si standard la suite n'est pas affiché JS)  -->
                <div id="type_contrat" class="section_label_maj">
                    <?php echo $form['type_contrat']->renderError() ?>        
                    <?php echo $form['type_contrat']->renderLabel() ?>
                    <?php echo $form['type_contrat']->render() ?>
                </div>
                <!--  Affichage de la présence de la part variable du contrat (si non la suite n'est pas affiché JS) -->
                <div id="prix_isVariable" class="section_label_maj" <?php echo ($displayPartiePrixVariable) ? '' : 'style="display:none;"'; ?>>
                    <?php echo $form['prix_variable']->renderError() ?>        
                    <?php echo $form['prix_variable']->renderLabel() ?> 
                    <?php echo $form['prix_variable']->render() ?>        
                </div>

                <!--  Affiché si et seulement si type de contrat = 'pluriannuel' et partie de prix variable = 'Oui' -->
                <div id="vrac_marche_prixVariable">
                    <?php
                    include_partial('condition_prixVariable', array('form' => $form, 'displayPrixVariable' => $displayPrixVariable));
                    ?>
                </div>

                <div class="section_label_maj">
                    <label>Dates</label>
                    <div class="bloc_form">
                        <!--  Affichage de la date de signature -->
                        <div id="date_signature" class="ligne_form champ_datepicker">
                            <?php echo $form['date_signature']->renderError() ?>        
                            <?php echo $form['date_signature']->renderLabel() ?>
                            <?php echo $form['date_signature']->render() ?>   
                            <span>(Date figurant sur le contrat)</span>
                        </div>
                        <!--  Affichage de la date de statistique -->
                        <div id="date_campagne" class="ligne_form ligne_form_alt champ_datepicker">
                            <?php echo $form['date_campagne']->renderError() ?>        
                            <?php echo $form['date_campagne']->renderLabel() ?>
                            <?php echo $form['date_campagne']->render() ?>  
                            <span>(Modifiable ultérieurement)</span>
                        </div>
                    </div>
                </div>
                <div id="commentaire" class="section_label_maj">
                    <?php echo $form['commentaire']->renderLabel() ?>
                    <div class="bloc_form">
                        <?php echo $form['commentaire']->renderError() ?>       
                        <?php echo $form['commentaire']->render() ?>
                    </div>
                </div>
            <div class="btn_etape">
                <a href="<?php echo url_for('vrac_marche', $vrac); ?>" class="btn_etape_prec"><span>Etape précédente</span></a>
                <button class="btn_etape_suiv" type="submit"><span>Etape Suivante</span></button>
            </div>
            </div>
        </form>
    </div>
</section>
<?php
slot('colApplications');
/*
 * Inclusion du panel de progression d'édition du contrat
 */
if(!$contratNonSolde) include_partial('contrat_progression', array('vrac' => $vrac));

/*
 * Inclusion du panel pour les contrats similaires
 */
include_partial('contratsSimilaires', array('vrac' => $vrac));

/*
 * Inclusion des Contacts
 */
include_partial('contrat_infos_contact', array('vrac' => $vrac));

end_slot();
?>  


