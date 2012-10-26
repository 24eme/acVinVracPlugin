<ol>
	<li>
		<h3>Soussignés</h3>
        <ul>
			<li>
				<span>Vendeur :</span>
				<span>
					<?php if($vrac->vendeur->nom): ?>
						<?php echo $vrac->vendeur->nom ?>
					<?php else: ?>
						<?php echo $vrac->vendeur->raison_sociale ?>
					<?php endif; ?>
					<?php echo ($vrac->vendeur_tva)? '(Assujetti à la TVA)' : ''; ?>
				</span>
			</li>
			<li>
				<span>Acheteur :</span>
				<span>
					<?php if($vrac->acheteur->nom): ?>
						<?php echo $vrac->acheteur->nom ?>
					<?php else: ?>
						<?php echo $vrac->acheteur->raison_sociale ?>
					<?php endif; ?>
					<?php echo ($vrac->acheteur_tva)? '(Assujetti à la TVA)' : ''; ?>
				</span>
			</li>
			<?php if($vrac->mandataire_exist): ?>
			<li>
				<span>Courtier :</span>
				<span><?php echo $vrac->mandataire->nom ?></span>
			</li>
			<?php endif; ?>
			<li>
				<span>Première mise en marché :</span>
				<span><?php echo ($vrac->premiere_mise_en_marche)? 'Oui' : 'Non'; ?></span>
			</li>
			<li>
				<span>Condition particulière :</span>
				<span><?php echo $configurationVrac->formatCasParticulierLibelle(array($vrac->cas_particulier)); ?></span>
			</li>
    	</ul>
    	<?php if($editer_etape): ?>
    	<p><a href="<?php echo url_for('vrac_etape', array('sf_subject' => $vrac, 'step' => 'soussigne', 'etablissement' => $etablissement)) ?>" class="modifier">modifier</a></p>
   		<?php endif; ?>
    </li>
	<li>
	    <h3>Marché</h3>
        <ul>
			<li>
				<span>Type de transaction :</span>
				<span><?php echo $configurationVrac->formatTypesTransactionLibelle(array($vrac->type_transaction)); ?></span>
			</li>
			<li>
				<span>Produit :</span>
				<span><?php echo ($vrac->produit)? $vrac->getLibelleProduit() : null; ?></span>
			</li>
			<li>
				<span>Millésime :</span>
				<span><?php echo ($vrac->millesime)? $vrac->millesime : 'Non millésimé'; ?></span>
			</li>
			<li>
				<span>Label :</span>
				<span><?php echo $configurationVrac->formatLabelsLibelle(array($vrac->labels)) ?></span>				
			</li>
			<li>
				<span>Mention(s) :</span>
				<span><?php echo $configurationVrac->formatMentionsLibelle($vrac->mentions->getRawValue()->toArray()) ?></span>				
			</li>
			<li>
				<span>Expédition export :</span>
				<span><?php echo ($vrac->export)? 'Oui' : 'Non'; ?></span>
			</li>
			<li>
				<span>Prix :</span>
				<span><?php echo $vrac->prix_unitaire ?> € HT/HL</span>
			</li>
			<li>
				<span>Volume :</span>
				<span><?php echo $vrac->volume_propose ?> HL</span>
			</li>
			<?php if ($vrac->has_cotisation_cvo && $vrac->part_cvo > 0): ?>
			<li>
				<span>Cotisation interprofessionnelle :</span>
				<span><?php echo $vrac->getCvoUnitaire() ?> € HT/HL</span>
			</li>
			<li>
				<span>Prix total unitaire :</span>
				<span><?php echo $vrac->getTotalUnitaire() ?> € HT/HL</span>
			</li>
			<?php endif; ?>
			<li>
				<span>Type de prix :</span>
				<span><?php echo $configurationVrac->formatTypesPrixLibelle(array($vrac->type_prix)) ?></span>
			</li>
			<?php if ($vrac->determination_prix): ?>
			<li>
				<span>Mode de détermination du prix définitif :</span>
				<span><?php echo $vrac->determination_prix ?></span>
			</li>
			<?php endif; ?>
			<?php if ($vrac->annexe): ?>
			<li>
				<span>Présence d'une annexe :</span>
				<span>Oui</span>
			</li>
			<?php endif; ?>
			
		</ul>
		<?php if($editer_etape): ?>
		<p><a href="<?php echo url_for('vrac_etape', array('sf_subject' => $vrac, 'step' => 'marche', 'etablissement' => $etablissement)) ?>" class="modifier">modifier</a></p>
		<?php endif; ?>
    </li>
    <li>
		<h3>Conditions</h3>
        <ul>
			<li>
				<span>Conditions générales de paiement :</span>
				<span><?php echo $configurationVrac->formatConditionsPaiementLibelle(array($vrac->conditions_paiement)); ?></span>
			</li>
			<?php if ($vrac->reference_contrat_pluriannuel): ?>
			<li>
				<span>Référence contrat pluriannuel :</span>
				<span><?php echo $vrac->reference_contrat_pluriannuel ?></span>
			</li>
			<?php endif; ?>
			<?php if ($vrac->conditions_paiement == ConfigurationVrac::CONDITION_PAIEMENT_ECHEANCIER): ?>
			<li>
				<table id="table_paiements">
					<thead>
						<tr>
							<th>Date</th>
							<th>Volume</th>
							<th>Montant</th>
		            	</tr>
		            </thead>
		            <tbody>
						<?php foreach ($vrac->paiements as $paiement): ?>
						<tr>
							<td><?php echo $paiement->date ?></td>
							<td><?php echo $paiement->volume ?> HL</td>
							<td><?php echo $paiement->montant ?> €</td>
						</tr>
						<?php endforeach; ?>
		            </tbody>
		        </table>
			</li>
			<?php endif; ?>
			<?php if(!is_null($vrac->delai_paiement)): ?>
			<li>
				<span>Delai de paiement :</span>
				<span><?php echo $configurationVrac->formatDelaisPaiementLibelle(array($vrac->delai_paiement)) ?></span>
			</li>
			<?php endif; ?>
			<li>
				<span>Le vin sera :</span>
				<span><?php $statut = VracClient::getInstance()->getStatutsVin(); echo $statut[$vrac->vin_livre] ?></span>
			</li>
			<?php if($vrac->date_debut_retiraison): ?>
			<li>
				<span>Date de début de retiraison :</span>
				<span><?php echo $vrac->date_debut_retiraison ?></span>
			</li>
			<?php endif; ?>
			<li>
				<span>Date limite de retiraison :</span>
				<span><?php echo $vrac->date_limite_retiraison ?></span>
			</li>
			<?php if(!is_null($vrac->clause_reserve_retiraison)): ?>
			<li>
				<span>Clause de reserve de propriété :</span>
				<span><?php echo ($vrac->clause_reserve_retiraison)? 'Oui' : 'Non'; ?></span>
			</li>
			<?php endif; ?>
		</ul>
		<?php if($editer_etape): ?>
			<p><a href="<?php echo url_for('vrac_etape', array('sf_subject' => $vrac, 'step' => 'condition', 'etablissement' => $etablissement)) ?>" class="modifier">modifier</a></p>
		<?php endif; ?>
    </li>
	<?php if ($vrac->has_transaction): ?>
    <li id="recap_transaction">
		<h3>Transaction</h3>
        <ul>
			<li class="lots">
				<?php foreach ($vrac->lots as $lot): ?>
				<div class="lot">

					<p><span>Numéro du lot :</span> <?php echo $lot->numero ?></p>
					
					<?php if($lot->cuves): ?>
					<div class="cuves">
						<p><span>Détail des cuves / contenants :</span></p>
						<table>
							<thead>
								<tr>
									<th>Cuve</th>
									<th>Volume</th>
									<th>Date</th>
				            	</tr>
				            </thead>
				            <tbody>
								<?php foreach ($lot->cuves as $cuve): ?>
								<tr>
									<td><?php echo $cuve->numero ?></td>
									<td><?php echo $cuve->volume ?></td>
									<td><?php echo $cuve->date ?></td>
								</tr>
								<?php endforeach; ?>
				            </tbody>
				        </table>
					</div>
					<?php endif; ?>

					<?php if($lot->millesimes[0]->annee): ?>
					<div class="millesimes">
						<p><span>Assemblage de millésimes :</span></p>

						<table>
							<thead>
								<tr>
									<th>Année</th>
									<th>Pourcentage</th>
				            	</tr>
				            </thead>
				            <tbody>
								<?php foreach ($lot->millesimes as $millesime): ?>
								<tr>
									<td><?php echo $millesime->annee ?></td>
									<td><?php echo $millesime->pourcentage ?></td>
								</tr>
								<?php endforeach; ?>
				            </tbody>
				        </table>
					</div>
					<?php endif; ?>
					
					<?php if (!is_null($lot->metayage)): ?>
					<p><span>Métayage :</span> <?php echo ($lot->metayage)? 'Oui' : 'Non'; ?></p>
						<?php if($lot->bailleur): ?>	
						<p><span>Nom du bailleur et volumes :</span> <?php echo $lot->bailleur ?></p>	
						<?php endif; ?>
					<?php endif; ?>
					
					<?php if ($lot->degre): ?>
					<p><span>Degré :</span> <?php echo $lot->degre ?></p>
					<?php endif; ?>
					
					<?php if (!is_null($lot->presence_allergenes)): ?>
					<p><span>Présence d'allergènes :</span> <?php echo ($lot->presence_allergenes)? 'Oui' : 'Non'; ?></p>
					<?php endif; ?>
				
				</div>
				<?php endforeach; ?>
			</li>
		</ul>
		<?php if($editer_etape): ?>
			<p><a href="<?php echo url_for('vrac_etape', array('sf_subject' => $vrac, 'step' => 'transaction', 'etablissement' => $etablissement)) ?>" class="modifier">modifier</a></p>
		<?php endif; ?>
	</li>
    <?php endif; ?>
</ol>
