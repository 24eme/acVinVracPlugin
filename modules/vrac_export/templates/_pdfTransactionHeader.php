<div id="header">
	<div id="logo">
		<img src="/images/visuels/logo_<?php echo strtolower($configurationVrac->getInterproId()) ?>.png" alt="<?php echo $configurationVrac->getInterproId() ?>" />
	</div>
	<center>
		<h1>Déclaration de transaction</h1>
	</center>
	<table>
	<tr>
		<td width="50%"><?php if ($vrac->premiere_mise_en_marche): ?>Première mise en marché<?php endif; ?></td>
   		<td width="50%" style="text-align: right;">Mode de saisie : DTI</td>
	</tr>
	<tr>
		<td width="50%">Saisie le <?php echo $vrac->getEuSaisieDate(); ?></td>
		<?php if ($vrac->isValide()): ?>
		<td width="50%" style="text-align: right;">N° de Visa du contrat : <?php echo $vrac->numero_contrat ?></td>
		<?php else: ?>
		<td width="50%" style="text-align: right;">&nbsp;</td>
		<?php endif; ?>
	</tr>
</table>
</div>