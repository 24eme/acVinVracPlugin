<?php
/* Fichier : recapitulatifSuccess.php
 * Description : Fichier php correspondant à la vue de vrac/XXXXXXXXXXX/recapitulatif
 * Affichage des dernières information de la saisie : numero de contrat
 * Auteur : Petit Mathurin - mpetit[at]actualys.com
 * Version : 1.0.0 
 * Derniere date de modification : 29-05-12
 */
use_helper('Vrac');
?>
<section id="principal">      
    <div id="contenu_etape">
        <div id="vrac_visualisation">
            <h2 class="titre_page">
                <span>Visualisation du contrat</span>

                <div class="statut_contrat">
                    <?php if ((!$isTeledeclarationMode) && ($vrac->valide->statut != VracClient::STATUS_CONTRAT_ANNULE)) : ?>
                        <?php if ($vrac->valide->statut == VracClient::STATUS_CONTRAT_NONSOLDE) : ?>
                            <a href="<?php echo url_for('vrac_solder', $vrac) ?>" class="btn_majeur btn_vert f_right">Solder le contrat</a>
                        <?php endif; ?>
                        <?php if ($vrac->valide->statut == VracClient::STATUS_CONTRAT_SOLDE) : ?>
                            <a href="<?php echo url_for('vrac_nonsolder', $vrac) ?>" class="btn_majeur btn_orange f_right">Annuler le solde</a>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if (!$isTeledeclarationMode): ?>
                        <span class="statut <?php echo getClassStatutPicto($vrac, $isTeledeclarationMode); ?>"></span><span class="legende_statut_texte"><?php echo $vrac->getStatutLabel(); ?></span>
                    <?php else: ?>
                        <?php 
                            $classStatut = strtolower($vrac->valide->statut);
                            if(($vrac->valide->statut == VracClient::STATUS_CONTRAT_ATTENTE_SIGNATURE) && $vrac->isSocieteHasSigned($societe)){
                                $classStatut = 'attente_signature_autres';
                            }
                            
                        ?>
                        <span class="<?php echo $classStatut; ?>"><?php echo "Contrat de " . showType($vrac) . '&nbsp;-&nbsp;' . $vrac->getTeledeclarationStatutLabel(); ?></span>
                    <?php endif; ?>                           
                </div>
            </h2>

            <?php if ($vrac->isVise()): ?>
                <div class="num_enregistrement">
                    <a id="btn_pdf_contrat" href="<?php echo url_for('vrac_pdf', $vrac); ?>">
                        <span class="style_label">N° d'enregistrement du contrat : <?php echo $vrac->numero_archive ?> (<?php echo $vrac->campagne ?>)</span>
                    </a>
                </div> 
            <?php endif; ?>  
            <form id="vrac_condition" method="post" action="<?php echo url_for('vrac_visualisation', $vrac) ?>"> 
                <div id="ligne_btn">
                    <?php
                    if (!is_null($vrac->valide->statut) && $vrac->valide->statut != VracClient::STATUS_CONTRAT_ANNULE && (is_null($vrac->volume_enleve) || ($vrac->volume_enleve == 0))):
                        if (!$isTeledeclare && !$isTeledeclarationMode):
                            ?>
                            <a id="btn_editer_contrat" href="<?php echo url_for('vrac_soussigne', $vrac); ?>"> Editer le contrat</a>
                        <?php endif; ?>
                        <?php
                        if (($isTeledeclarationMode && $isTeledeclare && $isProprietaire) ||
                                (!$isTeledeclarationMode && $vrac->valide->statut != VracClient::STATUS_CONTRAT_ATTENTE_SIGNATURE && $vrac->valide->statut != VracClient::STATUS_CONTRAT_ANNULE && $vrac->valide->statut != VracClient::STATUS_CONTRAT_VALIDE)):
                            ?>
                            <button id="btn_annuler_contrat" type="submit">Annuler le contrat</button>   
                        <?php endif; ?>        
                    <?php endif; ?>                                 
                </div>
            </form>


            <?php if ($signatureDemande): ?>
                <a id="signature_popup_haut" href="#signature_popup_content" class="signature_popup btn_majeur btn_vert f_right">Signer le contrat</a> 

            <?php endif; ?>

            <?php include_partial('showContrat', array('vrac' => $vrac, 'societe' => $societe, 'isTeledeclarationMode' => $isTeledeclarationMode)); ?>
            <?php if ($isTeledeclarationMode): ?>
                <div class="ligne_btn">
                    <?php if ($vrac->isVise() && ($isTeledeclare)): ?>
                        <div class="txt_centre">
                            <a href="<?php echo url_for('vrac_pdf', $vrac) ?>" class="btn_majeur btn_pdf">Télécharger le PDF</a>  
                        </div>
                    <?php else: ?>
                        <a href="<?php echo url_for('vrac_societe', array('identifiant' => str_replace('COMPTE-', '', $societe->compte_societe))); ?>" class="btn_orange btn_majeur" style="float: left;">Retourner à l'espace contrats</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php if ($signatureDemande): ?>
        <a id="signature_popup_bas" href="#signature_popup_content" class="signature_popup btn_majeur btn_vert f_right">Signer le contrat</a>      
        <?php include_partial('signature_popup', array('vrac' => $vrac, 'societe' => $societe, 'etablissementPrincipal' => $etablissementPrincipal)); ?>
    <?php endif; ?>
</section>
<?php
if ($sf_user->hasTeledeclaration()):
    include_partial('colonne_droite', array('societe' => $societe, 'etablissementPrincipal' => $etablissementPrincipal, 'retour' => true));
else:
    slot('colButtons');
    include_partial('actions_visu', array('vrac' => $vrac));
    end_slot();

    slot('colApplications');
    include_partial('contrat_infos_contact', array('vrac' => $vrac));

    end_slot();
endif;
?>

