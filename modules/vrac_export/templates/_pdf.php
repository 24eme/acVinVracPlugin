<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" class="no-js">
<head>
	<title>Vrac | Vins de Provence</title>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta name="author" content="Actualys" />
	<meta name="Description" content="" /> 
	<meta name="Keywords" content="" />
	<meta name="robots" content="index,follow" />
	<meta name="Content-Language" content="fr-FR" /> 
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="copyright" content="Vins de Provence - 2011" />

	<?php include_partial('drm_export/pdfCss') ?>
</head>

<body>
	<script type="text/php">
		if (isset($pdf)) {
			$w = $pdf->get_width();
			$h = $pdf->get_height();
			$font = Font_Metrics::get_font("helvetica");
			$pdf->page_text($w / 2 - 4, $h - 13, "f {PAGE_NUM} / {PAGE_COUNT}", $font, 8, array(0,0,0));
		}
	</script>
	<?php include_partial('vrac_export/pdfHeader', array('vrac' => $vrac)); ?>
	<?php include_partial('vrac_export/pdfFooter'); ?>
	<h2>Soussignes</h2>
	<?php if($vrac->mandataire_exist) $w = '33%'; else $w = '50%'; ?>
	<table class="bloc_bottom" width="100%">
		<tr>
			<td width="<?php echo $w ?>">
				<h2>Vendeur</h2>
				<p>Type : <?php echo $vrac->vendeur_type ?></p>
				<p>Raison sociale : <?php echo ($vrac->vendeur->raison_sociale)? $vrac->vendeur->raison_sociale : $vrac->vendeur->nom; ?></p>
				<p>N° RCS/SIRET : <?php echo $vrac->vendeur->siret ?></p>
				<p>N° CVI/EVV : <?php echo $vrac->vendeur->cvi ?></p>
				<p>Adresse : <?php echo $vrac->vendeur->adresse ?></p>
				<p>Code postal : <?php echo $vrac->vendeur->code_postal ?></p>
				<p>Commune : <?php echo $vrac->vendeur->commune ?></p>
				<p>Tel : <?php echo $vrac->vendeur->telephone ?>&nbsp;&nbsp;&nbsp;Fax : <?php echo $vrac->vendeur->fax ?></p>
				<?php if ($vrac->hasAdresseStockage()): ?>
				<br />
				<p>Adresse de stockage : <?php echo $vrac->adresse_stockage->libelle ?></p>
				<p>Adresse : <?php echo $vrac->adresse_stockage->adresse ?></p>
				<p>Code postal : <?php echo $vrac->adresse_stockage->code_postal ?></p>
				<p>Commune : <?php echo $vrac->adresse_stockage->commune ?></p>
				<?php endif; ?>
			</td>
			<?php if($vrac->mandataire_exist): ?>
			<td width="<?php echo $w ?>">
				<h2>Courtier</h2>
				<p>Raison sociale : <?php echo ($vrac->mandataire->raison_sociale)? $vrac->mandataire->raison_sociale : $vrac->mandataire->nom; ?></p>
				<p>N° Carte professionnelle : <?php echo $vrac->mandataire->carte_pro ?></p>
				<p>N° RCS/SIRET : <?php echo $vrac->mandataire->siret ?></p>
				<p>Adresse : <?php echo $vrac->mandataire->adresse ?></p>
				<p>Code postal : <?php echo $vrac->mandataire->code_postal ?></p>
				<p>Commune : <?php echo $vrac->mandataire->commune ?></p>
				<p>Tel : <?php echo $vrac->mandataire->telephone ?>&nbsp;&nbsp;&nbsp;Fax : <?php echo $vrac->mandataire->fax ?></p>
			</td>
			<?php endif; ?>
			<td width="<?php echo $w ?>">
				<h2>Acheteur</h2>
				<p>Type : <?php echo $vrac->acheteur_type ?></p>
				<p>Raison sociale : <?php echo ($vrac->acheteur->raison_sociale)? $vrac->acheteur->raison_sociale : $vrac->acheteur->nom; ?></p>
				<p>N° RCS/SIRET : <?php echo $vrac->acheteur->siret ?></p>
				<p>N° CVI/EVV : <?php echo $vrac->acheteur->cvi ?></p>
				<p>Adresse : <?php echo $vrac->acheteur->adresse ?></p>
				<p>Code postal : <?php echo $vrac->acheteur->code_postal ?></p>
				<p>Commune : <?php echo $vrac->acheteur->commune ?></p>
				<p>Tel : <?php echo $vrac->acheteur->telephone ?>&nbsp;&nbsp;&nbsp;Fax : <?php echo $vrac->acheteur->fax ?></p>
				<?php if ($vrac->hasAdresseLivraison()): ?>
				<br />
				<p>Adresse de livraison : <?php echo $vrac->adresse_livraison->libelle ?></p>
				<p>Adresse : <?php echo $vrac->adresse_livraison->adresse ?></p>
				<p>Code postal : <?php echo $vrac->adresse_livraison->code_postal ?></p>
				<p>Commune : <?php echo $vrac->adresse_livraison->commune ?></p>
				<?php endif; ?>
			</td>
		</tr>
	</table>
	<h2>Produit</h2>
	<p><?php echo ($vrac->produit)? $vrac->getLibelleProduit("%a% %l% %co% %ce%") : null; ?></p>
	<p>
		<?php echo ($vrac->millesime)? 'Millésime : '.$vrac->millesime.'&nbsp;&nbsp;' : ''; ?>
		<?php echo (count($vrac->labels) > 0)? 'Label : '.$configurationVrac->formatLabelsLibelle($vrac->labels).'&nbsp;&nbsp;' : ''; ?>
		<?php echo (count($vrac->mentions) > 0)? 'Mentions : '.$configurationVrac->formatMentionsLibelle($vrac->mentions) : ''; ?>
	</p>
	<h2>Volume / Prix</h2>
	<p>
		Volume total : <?php echo $vrac->volume_propose ?>&nbsp;HL&nbsp;&nbsp;Prix unitaire net HT hors cotisation <?php echo $vrac->prix_unitaire ?>&nbsp;€/HL
	</p>
	<h2>Conditions</h2>
	<p>
		Conditions Générales de Paiement : <?php echo $configurationVrac->formatConditionsPaiementLibelle(array($vrac->conditions_paiement)); ?>&nbsp;&nbsp;<?php echo ($vrac->reference_contrat_pluriannuel)? $vrac->reference_contrat_pluriannuel : ''; ?><br />
		Le vin sera <?php echo ($vrac->vin_livre == VracClient::STATUS_VIN_LIVRE)? 'livré' : 'retiré'; ?>&nbsp;&nbsp;<?php echo ($vrac->vin_livre == VracClient::STATUS_VIN_RETIRE)? 'Date limite : '.$vrac->date_limite_retiraison : ''; ?><br />
		Autres observations : <?php echo $vrac->commentaires ?>
	</p>
	<h2>Clauses</h2>
	<p>Le prix s'entend Net : Hors Taxe, hors cotisations, hors transport, hors frais divers et hors courtage éventuels qui seront à régler séparément.</p>
	<p>Si une partie demande renégociation du contrat, elle devra payer à la date de constitution du nouvel accord une avance de 15% à 10 jours du montant total du contrat (ou du montant relatif aux quantités restant à retirer).</p>
	<p>La partie constituée en faute (rupture ou non réalisation) sera redevable d'une indemnité de 15% du montant total du contrat (ou du montant relatif aux quantités restant à payer).</p>
	<p>Le vendeur confirme avoir reçu une proposition écrite de l'acheteur dans les mêmes termes avant la signature de ce contrat.</p>
	<hr />
	<?php if ($vrac->echeancier_paiement): ?>
	<h2>Calendrier de retiraison</h2>
	<p><?php echo ($vrac->vin_livre == VracClient::STATUS_VIN_RETIRE)? 'Date limite de retiraison : '.$vrac->date_limite_retiraison : ''; ?></p>
	<p>
		<table>
			<tr>
				<td></td>
				<td>
					<table>
						<tr>
							<td>Date</td>
							<td>Montant</td>
						</tr>
						<?php foreach ($vrac->paiements as $paiement): ?>
						<tr>
							<td><?php echo $paiement->date ?></td>
							<td><?php echo $paiement->montant ?>&nbsp;€</td>
						</tr>
						<?php endforeach; ?>
					</table>
				</td>
			</tr>
		</table>
	</p>
	<?php endif; ?>
	<?php if ($vrac->has_transaction): ?>
	<h2>Descriptif des lots</h2>
	<p>
		<table>
			<tr>
				<td>Numéro</td>
				<td>Cuve(s)</td>
				<td>Volume</td>
				<td>Date de retiraison</td>
				<td>Assemblage</td>
				<td>Degrés</td>
				<td>Allergènes</td>
				<td>Montant</td>
			</tr>
			<?php foreach ($vrac->lots as $lot): ?>
			<tr>
				<td><?php echo $lot->numero ?></td>
				<td><?php echo $lot->cuve ?></td>
				<td><?php echo $lot->volume ?>&nbsp;HL</td>
				<td><?php echo $lot->date_retiraison ?></td>
				<td>
					<?php if($lot->assemblage): ?>
					<ul>
					<?php foreach ($lot->millesimes as $millesime): ?>
					<li>
						<?php echo $millesime->annee ?> - <?php echo $millesime->pourcentage ?>&nbsp;%
					</li>
					<?php endforeach; ?>
					</ul>
					<?php else: ?>
					Pas d'assemblage
					<?php endif; ?>
				</td>
				<td><?php echo $lot->degre ?></td>
				<td><?php echo ($lot->presence_allergenes)? 'Oui' : 'Non'; ?></td>
				<td><?php echo $lot->montant ?>&nbsp;€</td>
			</tr>
			<?php endforeach; ?>
		</table>
	</p>
	<?php endif; ?>
	<h2>Informations complémentaires</h2>
	<p>En attente des informations complémentaires</p>
</body>
</html>
