<h2><?= __('system contents.headline') ?></h2>
<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th><?= $this->Paginator->sort('id', __('system_content.id')) ?></th>
			<th><?= $this->Paginator->sort('identifier', __('system_content.identifier')) ?></th>
			<th><?= $this->Paginator->sort('notes', __('system_content.notes')) ?></th>
			<th><?= $this->Paginator->sort('created', __('system_content.created')) ?></th>
			<th><?= $this->Paginator->sort('modified', __('system_content.modified')) ?></th>
			<th class="actions"><?= __('lists.actions') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($systemContents as $systemContent): ?>
		<tr>
			<td><?= h($systemContent->id) ?></td>
			<td><?= h($systemContent->identifier) ?></td>
			<td><?= h($systemContent->notes) ?></td>
			<td><?= h($systemContent->created) ?></td>
			<td><?= h($systemContent->modified) ?></td>
			<td class="actions">
				<?= $this->Html->link(__('lists.view'), ['action' => 'view', $systemContent->id]) ?>
				<?= $this->Html->link(__('lists.edit'), ['action' => 'edit', $systemContent->id]) ?>
				<?= $this->Form->postLink(__('lists.delete'), ['action' => 'delete', $systemContent->id], ['confirm' => __('lists.really_delete')]) ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<p><?= $this->Paginator->counter() ?></p>
<?= $this->Bootstrap->pagination() ?>

<div class="actions">
	<h3><?= __('lists.actions') ?></h3>
	<ul>
		<li><?= $this->Html->link(__('system contents.add'), ['action' => 'add']) ?></li>
	</ul>
</div>
