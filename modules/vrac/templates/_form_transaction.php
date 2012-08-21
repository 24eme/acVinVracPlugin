	<form class="popup_form" method="post" action="<?php echo url_for('vrac_etape', array('sf_subject' => $form->getObject(), 'step' => $etape, 'etablissement' => $etablissement)) ?>">
		<?php echo $form->renderHiddenFields() ?>
		<?php echo $form->renderGlobalErrors() ?>
    <div>
        <div class="section_label_strong">
            <?php echo $form['export']->renderError() ?>
            <?php echo $form['export']->renderLabel() ?>
            <?php echo $form['export']->render() ?>
        </div>
        <div> 
            <div class="clearfix">
                <div id="table_lots">
                    <?php foreach ($form['lots'] as $formLot): ?>
                        <?php include_partial('form_lots_item', array('form' => $formLot, 'form_parent' => $form)) ?>
                    <?php endforeach; ?>
                </div>
                </table>
                <div class="btn_ajouter_transaction"> 
                <a class="btn_ajouter_ligne_template" data-container="#table_lots" data-template="#template_form_lots_item" href="#"><span>Ajouter une transaction</span></a>
                </div>
            </div>
            <div class="bloc_form_commentaire bloc_form ">
                <div class="vracs_ligne_form vracs_ligne_form_alt">
                    <?php echo $form['commentaires']->renderError() ?>
                    <?php echo $form['commentaires']->renderLabel() ?>
                    <?php echo $form['commentaires']->render() ?>
                </div>
            </div>
        </div>
        <div class="ligne_form_btn">
            <button class="valider_etape" type="submit"><span>Etape Suivante</span></button>

        </div>
    </div>  
</form>

<script id="template_form_lots_item" class="template_form" type="text/x-jquery-tmpl">
    <?php echo include_partial('form_lots_item', array('form' => $form->getFormTemplateLots(), 'form_parent' => $form)); ?>
</script>
