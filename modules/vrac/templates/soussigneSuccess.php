<?php
/* Fichier : soussigneSuccess.php
 * Description : Fichier php correspondant à la vue de vrac/nouveau-soussigne
 * Formulaire d'enregistrement de la partie soussigne des contrats (modification de contrat)
 * Auteur : Petit Mathurin - mpetit[at]actualys.com
 * Version : 1.0.0 
 * Derniere date de modification : 29-05-12
 */
if ($nouveau) {
    ?>
    <script type="text/javascript">
        $(document).ready(function() 
        {
            init_ajax_nouveau();        
        });                        
    </script>
    <?php
} else {
    $numero_contrat = $form->getObject()->numero_contrat;
    ?>
    <script type="text/javascript">
        $(document).ready(function() 
        {
            ajaxifyAutocompleteGet('getInfos',{autocomplete : '#vendeur_choice','numero_contrat' : '<?php echo $numero_contrat; ?>'},'#vendeur_informations');        
            ajaxifyAutocompleteGet('getInfos',{autocomplete : '#acheteur_choice','numero_contrat' : '<?php echo $numero_contrat; ?>'},'#acheteur_informations');
            ajaxifyAutocompleteGet('getInfos',{autocomplete : '#mandataire_choice','numero_contrat' : '<?php echo $numero_contrat; ?>'},'#mandataire_informations');
            majMandatairePanel();
            //$('#vrac_vendeur_famille_viticulteur').attr('checked','checked');
            //$('#vrac_acheteur_famille_negociant').attr('checked','checked');
        });
    </script>
    <?php
}
?>
<section id="principal">
<?php include_partial('headerVrac', array('vrac' => $form->getObject(), 'actif' => 1)); ?>
    <div id="contenu_etape">
        <form id="vrac_soussigne" method="post" action="<?php echo ($form->getObject()->isNew() && isset($etablissement)) ? url_for('vrac_nouveau', array('etablissement' => $etablissement)) : url_for('vrac_soussigne', $vrac); ?>">   
    <?php echo $form->renderHiddenFields() ?>
<?php echo $form->renderGlobalErrors() ?>

            <?php echo $form['vendeur_identifiant']->renderError(); ?>
            <div id="vendeur" class="block_overlay">   
                <!--  Affichage des vendeurs disponibles  -->
                <div id="vendeur_choice" class="section_label_maj">
<?php echo $form['vendeur_identifiant']->renderLabel() ?>
<?php echo $form['vendeur_identifiant']->render(array('class' => 'autocomplete combobox', 'data-btn-ajout-txt' => 'Ajouter un vendeur')) ?>
<?php if ($form->getObject()->isTeledeclare()): ?>
					<br /><br />
					<a href="<?php echo url_for('vrac_annuaire', array('numero_contrat' => $form->getObject()->_id,'sf_subject' => $form->getObject(), 'identifiant' => $etablissement, 'type' => AnnuaireClient::ANNUAIRE_RECOLTANTS_KEY, 'acteur' => 'vendeur')) ?>" class="ajouter_annuaire">Ajouter un contact</a>
<?php endif; ?>
                </div>

                <!--  Affichage des informations sur le vendeur sélectionné AJAXIFIED -->
                <div id="vendeur_informations">
<?php
$vendeurArray = array();
$vendeurArray['vendeur'] = $form->vendeur;
$vendeurArray['vendeur'] = ($nouveau) ? $form->getObject()->getVendeurObject() : $form->getObject()->getVendeurObject();
include_partial('vendeurInformations', $vendeurArray);
?>
                </div>
                <div class="btnModification">
                    <a id="vendeur_annulation_btn" class="btn_majeur btn_annuler" style="display: none;">Retour</a>
                    <?php if (!$form->getObject()->isTeledeclare()): ?>
                    <a id="vendeur_modification_btn" class="btn_majeur btn_modifier">Modifier</a>
                    <?php endif; ?>
                </div>
            </div>
<?php echo $form['acheteur_identifiant']->renderError(); ?>
            <!--  Affichage des acheteurs disponibles  -->
            <div id="acheteur" class="block_overlay"> 
                <div id="acheteur_choice" class="section_label_maj">
<?php echo $form['acheteur_identifiant']->renderLabel() ?>
<?php echo $form['acheteur_identifiant']->render() ?>
<?php if ($form->getObject()->isTeledeclare()): ?>
					<br /><br />
					<a href="<?php echo url_for('vrac_annuaire', array('numero_contrat' => $form->getObject()->_id, 'sf_subject' => $form->getObject(), 'identifiant' => $etablissement, 'type' => AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY, 'acteur' => 'acheteur')) ?>" class="ajouter_annuaire">Ajouter un contact</a>
<?php endif; ?>
                </div>

                <!--  Affichage des informations sur l'acheteur sélectionné AJAXIFIED -->
                <div id="acheteur_informations">
<?php
$acheteurArray = array();
$acheteurArray['acheteur'] = $form->acheteur;
$acheteurArray['acheteur'] = ($nouveau) ? $form->getObject()->getAcheteurObject() : $form->getObject()->getAcheteurObject();
include_partial('acheteurInformations', $acheteurArray);
?>
                </div>
                <div class="btnModification">
                    <a id="acheteur_annulation_btn" class="btn_majeur btn_annuler" style="display: none;">Retour</a>
                    <?php if (!$form->getObject()->isTeledeclare()): ?>
                    <a id="acheteur_modification_btn" class="btn_majeur btn_modifier">Modifier</a>
                    <?php endif; ?>
                </div>
            </div>


            

            <!--  Affichage des mandataires disponibles  -->
			<?php if ($form->getObject()->isTeledeclare()): ?>
				<div id="">     
	                <div id="" class="section_label_maj">
	                    <?php if (isset($form['commercial'])): ?>
						<?php echo $form['commercial']->renderError(); ?>
						<?php echo $form['commercial']->renderLabel() ?>
	                    <?php echo $form['commercial']->render() ?>
						<br /><br />
						<a class="ajouter_annuaire" href="<?php echo url_for('vrac_annuaire_commercial', array('numero_contrat' => $form->getObject()->_id, 'sf_subject' => $form->getObject(), 'identifiant' => $etablissement)) ?>">Ajouter un contact</a>
	                	<?php endif; ?>
	                </div>
                </div>
                <?php if($form->getObject()->getMandataireExist()): ?>
                    <input id="vrac_mandataire_exist" type="hidden" value="1" name="vrac[mandataire_exist]">                
                    <input id="vrac_mandataire_identifiant" type="hidden" value="ETABLISSEMENT-<?php echo $form->getObject()->getMandataireIdentifiant(); ?>" name="vrac[mandataire_identifiant]">
                <?php endif; ?>
                    <?php else: ?>

            <div id="interne" class="block_overlay">            

<?php echo $form['interne']->render(); ?>
<?php echo $form['interne']->renderLabel(); ?>
                <?php echo $form['interne']->renderError(); ?>
            </div>
            
            <div id="has_mandataire" class="block_overlay">            
<?php echo $form['mandataire_exist']->render(); ?>
<?php echo $form['mandataire_exist']->renderLabel(); ?>
                <?php echo $form['mandataire_exist']->renderError(); ?>
            </div>
            <div id="mandataire" class="block_overlay">     
                <div id="mandatant" class="section_label_strong" >
<?php echo $form['mandatant']->renderError(); ?>
<?php echo $form['mandatant']->renderLabel() ?>
                    <?php echo $form['mandatant']->render() ?>        
                </div>

                <div id="mandataire_choice" class="section_label_maj">
<?php echo $form['mandataire_identifiant']->renderError(); ?>
<?php echo $form['mandataire_identifiant']->renderLabel() ?>
                    <?php echo $form['mandataire_identifiant']->render() ?>
                    <?php if (isset($form['commercial'])): ?>
                    <br /><br />
					<?php echo $form['commercial']->renderError(); ?>
					<?php echo $form['commercial']->renderLabel() ?>
                    <?php echo $form['commercial']->render() ?>
					<br /><br />
					<a class="ajouter_annuaire" href="<?php echo url_for('vrac_annuaire_commercial', array('numero_contrat' => $form->getObject()->_id, 'sf_subject' => $form->getObject(), 'identifiant' => $etablissement)) ?>">Ajouter un contact</a>
                	<?php endif; ?>
                </div>

                <!--  Affichage des informations sur le mandataire sélectionné AJAXIFIED -->
                <div id="mandataire_informations">
<?php
$mandataireArray = array();
$mandataireArray['mandataire'] = $form->mandataire;
if (!$nouveau)
    $mandataireArray['mandataire'] = (!$hasmandataire) ? $mandataireArray['mandataire'] : $form->getObject()->getMandataireObject();
include_partial('mandataireInformations', $mandataireArray);
?>    
                </div>
                <div class="btnModification">
                    <a id="mandataire_annulation_btn" class="btn_majeur btn_annuler" style="display: none;" href="#">Retour</a>
                    <a id="mandataire_modification_btn" class="btn_majeur">Modifier</a>
                </div>
            </div>
			<?php endif; ?>
            
            <div class="btn_etape block_overlay" id="ligne_btn">
<?php if ($nouveau): ?>
                    <a href="<?php echo url_for('vrac'); ?>" class="btn_majeur btn_annuler"><span>Annuler la saisie</span></a>
                <?php endif; ?>

                <button id="btn_soussigne_submit" class="btn_etape_suiv" type="submit"><span>Etape Suivante</span></button>
            </div>

        </form>
    </div>
</section>

<script type="text/javascript">
	$(document).ready(function () {
		$(".ajouter_annuaire").click(function() {
			$("#vrac_soussigne").attr('action', $(this).attr('href'));
			$("#vrac_soussigne").submit();
			return false;
		});
	});
</script>
    
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
 * Inclusion des Contacts
 */
end_slot();
endif;

?>
 