<div id="contenu">
    <div id="rub_contrats">
        <section id="principal">
             <?php include_partial('fil_ariane'); ?>
            <section id="contenu_etape">
                <?php include_partial('table_contrats', array('vracs' => $vracs, 'identifiant'=>$identifiant)); ?>
            </section>
        </section>
        <?php include_partial('actions'); ?>
    </div>
</div>


