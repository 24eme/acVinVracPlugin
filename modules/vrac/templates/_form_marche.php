
    <form class="popup_form" method="post" action="<?php echo url_for('vrac_etape', array('sf_subject' => $form->getObject(), 'step' => $etape, 'etablissement' => $etablissement)) ?>">
        <?php echo $form->renderHiddenFields() ?>
        <?php echo $form->renderGlobalErrors() ?>

        <div>
            <div class="section_label_strong">
                <?php echo $form['type_transaction']->renderError() ?>
                <?php echo $form['type_transaction']->renderLabel() ?>
                <?php echo $form['type_transaction']->render() ?>
            </div>
            <div  class="section_label_strong">
                <?php echo $form['produit']->renderError() ?>
                <?php echo $form['produit']->renderLabel() ?>
                <?php echo $form['produit']->render() ?>
            </div>
            <div  class="section_label_strong">
                <?php echo $form['millesime']->renderError() ?>
                <?php echo $form['millesime']->renderLabel() ?>
                <?php echo $form['millesime']->render() ?>
            </div>
            <div class="section_label_strong">
                <?php echo $form['labels']->renderError() ?>
                <?php echo $form['labels']->renderLabel() ?>
                <?php echo $form['labels']->render() ?>
            </div>
            <div class="section_label_strong">
                <?php echo $form['mentions']->renderError() ?>
                <?php echo $form['mentions']->renderLabel() ?>
                <?php echo $form['mentions']->render() ?>
            </div>
            <div class="section_label_strong">
                <?php echo $form['volume_propose']->renderError() ?>
                <?php echo $form['volume_propose']->renderLabel() ?>
                <?php echo $form['volume_propose']->render() ?> hl
            </div>
            <div class="section_label_strong">
                <?php echo $form['has_transaction']->renderError() ?>
                <?php echo $form['has_transaction']->renderLabel() ?>
                <?php echo $form['has_transaction']->render() ?> 
            </div>
            <div class="section_label_strong">
                <?php echo $form['prix_unitaire']->renderError() ?>
                <?php echo $form['prix_unitaire']->renderLabel() ?>
                <?php echo $form['prix_unitaire']->render() ?> €/hl
            </div>
            <div class="section_label_strong">
                <?php echo $form['prix_total']->renderError() ?>
                <?php echo $form['prix_total']->renderLabel() ?>
                <?php echo $form['prix_total']->render(array('disabled' => 'disabled')) ?> €
            </div>
            <div class="section_label_strong">
                <label>Part CVO acheteur :</label>
                xxx €
            </div>	
            <div class="section_label_strong">
                <label>Prix total CVO incluse :</label> xxx €
            </div>
        </div>

        <div class="ligne_form_btn">
            <button class="valider_etape" type="submit"><span>Etape Suivante</span></button>
        </div>
    </form>
