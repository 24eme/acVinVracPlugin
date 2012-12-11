<?php
/* Fichier : validationSuccess.php
 * Description : Fichier php correspondant à la vue de vrac/XXXXXXXXXXX/validation
 * Formulaire d'enregistrement de la partie validation d'un contrat donnant le récapitulatif
 * Auteur : Petit Mathurin - mpetit[at]actualys.com
 * Version : 1.0.0 
 * Derniere date de modification : 28-05-12
 */
use_helper('Vrac');
?>
<script type="text/javascript">
    $(document).ready(function()
    {
<?php echo ($contratsSimilairesExist) ? 'initValidationWithPopup();' : 'initValidation();'; ?>
        });
</script>

<div id="contenu">
    <div id="rub_contrats" >
        <section id="principal">
            <?php include_partial('headerVrac', array('vrac' => $vrac, 'actif' => 4)); ?>        
            <div id="contenu_etape"> 
                <form id="vrac_validation" method="post" action="<?php echo url_for('vrac_validation', $vrac) ?>">
                    
                    <?php if(count($validation->getErrors()) > 0): ?>
                        <div id="points_bloquants">
                            <p>Points bloquants :</p>
                            <ul>
                                <?php foreach($validation->getErrors() as $message): ?>
                                    <li class="error">
                                        <span><?php echo $message ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(count($validation->getWarnings()) > 0): ?>
                        <div id="points_vigilance">
                            <p>Points de vigilance :</p>
                            <ul>
                                <?php foreach($validation->getWarnings() as $message): ?>
                                    <li class="warning">
                                        <span><?php echo $message ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <div id="titre"><span class="style_label">Récapitulatif de la saisie</span></div>

                    <?php include_partial('showContrat', array('vrac' => $vrac)); ?>

                    <div class="btn_etape">
                        <a href="<?php echo url_for('vrac_condition', $vrac); ?>" class="btn_etape_prec"><span>Etape précédente</span></a>
                        <?php if ($validation->isValid()) : ?>
                            <a id="btn_validation" class="btn_validation"><span>Terminer la saisie</span></a>  
                        <?php endif; ?>
                    </div> 
                </form>
            </div>
        </section>
        <aside id="colonne">
            <?php include_partial('colonne', array('vrac' => $vrac, 'contratNonSolde' => $contratNonSolde)); ?>
        </aside>       
        <?php
        if ($contratsSimilairesExist)
            include_partial('contratsSimilaires_warning_popup', array('vrac' => $vrac));
        ?>
    </div>
</div>