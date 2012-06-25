<?php
use_helper('Display');
?>
<script type="text/javascript">
    $(document).ready(function() 
    { 
        init_informations('acheteur');       
       <?php
        if(!isset($numero_contrat))
        {
        ?>
        ajaxifyGet('modification','#vrac_acheteur_identifiant','#acheteur_modification_btn','#acheteur_informations');
        <?php
        }
        else
        {
        ?>        
        ajaxifyGet('modification',{field_0 : '#vrac_acheteur_identifiant',
                                   'type' : 'acheteur' ,
                                   'numero_contrat' : '<?php echo $numero_contrat;?>'
                                  }, '#acheteur_modification_btn','#acheteur_informations');
        <?php
        }
        ?>
    });
</script>



<div id="vendeur_infos" class="bloc_form">
    
    <div class="col">
        <div class="ligne_form ">
            <span>
                <label>Nom de l'acheteur*</label>
                <?php display_field($acheteur,'nom'); ?>
            </span>
        </div>
        <div class="ligne_form ligne_form_alt">
            <span>
                <label>N° CVI</label>
                <?php display_field($acheteur,'cvi'); ?>
            </span>
        </div>
        <div class="ligne_form">
            <span>
                <label>N° ACCISE*</label>
                <?php display_field($acheteur,'num_accise'); ?>
            </span>
        </div>
        <div class="ligne_form ligne_form_alt " >
            <span>
                <label>TVA Intracomm.</label>
                <?php display_field($acheteur,'num_tva_intracomm'); ?>
            </span>
        </div>
    </div>
    
    <div class="col col_right">
        <div class="ligne_form">
            <span>
                <label>Adresse*</label>
                <?php display_field($acheteur,'adresse');  ?>
            </span>
        </div>
        <div class="ligne_form ligne_form_alt">
            <span>
                <label>Code Postal*</label>
                <?php display_field($acheteur,'code_postal'); ?>
            </span>
        </div>
        <div class="ligne_form">
            <span>
                <label>Ville*</label>
                <?php display_field($acheteur,'commune'); ?>
            </span>
        </div>
        <div class="ligne_form ligne_form_alt">
            
        </div>
    </div>
<div>
