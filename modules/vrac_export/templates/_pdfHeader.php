<div id="header">
   <center>
		<h1>Contrat d'achat interprofessionnel</h1>
	</center>
	<table>
	<tr>
		<td width="50%"><?php if ($vrac->premiere_mise_en_marche): ?>Première mise en marché<?php endif; ?></td>
   		<td width="50%" style="text-align: right;">Mode de saisie : DTI</td>
	</tr>
	<tr>
		<td width="50%">Saisie le <?php echo $vrac->getEuSaisieDate(); ?></td>
		<td width="50%" style="text-align: right;">N° de Visa du contrat : <?php echo $vrac->numero_contrat ?></td>
	</tr>
</table>
</div>
