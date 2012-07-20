<table id="tableau_contrat">    
    <thead>
        <tr>
            <th class="type">Type</th>
            <th>N° Contrat</th>
            <th>Soussignés</th>   
            <th>Produit</th>
            <th>Vol. enlevé. / Vol. prop.</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        foreach ($vracs->rows as $value):   
            $elt = $value->getRawValue()->value;
            if(!is_null($elt[VracHistoryView::VRAC_VIEW_STATUT])):
                $statusColor = statusColor($elt[VracHistoryView::VRAC_VIEW_STATUT]);
                $vracid = preg_replace('/VRAC-/', '', $elt[VracHistoryView::VRAC_VIEW_NUMCONTRAT]);
        ?>
        <tr class="<?php echo $statusColor; ?>" >
            <td class="type" ><span class="type_<?php echo $elt[VracHistoryView::VRAC_VIEW_TYPEPRODUIT]; ?>"><?php echo ($elt[VracHistoryView::VRAC_VIEW_TYPEPRODUIT])? typeProduit($elt[VracHistoryView::VRAC_VIEW_TYPEPRODUIT]) : ''; ?></span></td>
	    	<td id="num_contrat"><?php echo link_to(substr($vracid,0,8)."&nbsp;".substr($vracid,8,  strlen($vracid)-1), '@vrac_visualisation?numero_contrat='.$vracid); ?></td>
            <td>
                  <ul>  
                    <li>
                      <?php echo ($elt[VracHistoryView::VRAC_VIEW_VENDEUR_ID])? 
                                    'Vendeur : '.link_to($elt[VracHistoryView::VRAC_VIEW_VENDEUR_NOM],
                                            'vrac/recherche?identifiant='.preg_replace('/ETABLISSEMENT-/', '', $elt[VracHistoryView::VRAC_VIEW_VENDEUR_ID])) 
                                  : ''; ?>
                    </li>
                    <li>
                      <?php echo ($elt[VracHistoryView::VRAC_VIEW_ACHETEUR_ID])?
                                    'Acheteur : '.link_to($elt[VracHistoryView::VRAC_VIEW_ACHETEUR_NOM],
                                            'vrac/recherche?identifiant='.preg_replace('/ETABLISSEMENT-/', '', $elt[VracHistoryView::VRAC_VIEW_ACHETEUR_ID])) 
                                : ''; ?>
                    </li>
                    <li>
                      <?php echo ($elt[VracHistoryView::VRAC_VIEW_MANDATAIRE_ID]) ? 
                                    'Mandataire : '.link_to($elt[VracHistoryView::VRAC_VIEW_MANDATAIRE_NOM], 
                                            'vrac/recherche?identifiant='.preg_replace('/ETABLISSEMENT-/', '', $elt[VracHistoryView::VRAC_VIEW_MANDATAIRE_ID])) 
                                 : ''; ?>
                    </li>
                 </ul>
              </td>              
              <td><?php echo ($elt[VracHistoryView::VRAC_VIEW_PRODUIT_ID])? ConfigurationClient::getCurrent()->get($elt[VracHistoryView::VRAC_VIEW_PRODUIT_ID])->getLibelleFormat() : ''; ?></td>
              <td>           
                  <?php echo (isset($elt[VracHistoryView::VRAC_VIEW_VOLENLEVE]))? $elt[VracHistoryView::VRAC_VIEW_VOLENLEVE] : '0';
                        echo ' / ';
                        echo (isset($elt[VracHistoryView::VRAC_VIEW_VOLPROP]))? $elt[VracHistoryView::VRAC_VIEW_VOLPROP] : '0';
                   ?>
              </td>
        </tr>
        <?php
            endif;
        endforeach;
        ?>
    </tbody>
</table>