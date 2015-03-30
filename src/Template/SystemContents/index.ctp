<h2><?= __('system_contents.headline') ?></h2>
<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th><?= $this->Paginator->sort('identifier', __('system_content.identifier')) ?></th>
			<th><?= $this->Paginator->sort('title', __('system_content.title')) ?></th>
			<th><?= $this->Paginator->sort('created', __('system_content.created')) ?></th>
			<th><?= $this->Paginator->sort('modified', __('system_content.modified')) ?></th>
			<th class="actions"><?= __d('lists', actions') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($systemContents as $systemContent): ?>
		<tr>
			<td><?= h($systemContent->identifier) ?></td>
			<td><?= h($systemContent->title) ?></td>
			<td><?= h($systemContent->created) ?></td>
			<td><?= h($systemContent->modified) ?></td>
			<td class="actions">
				<?= $this->Html->link(__d('lists', edit'), ['action' => 'edit', $systemContent->id]) ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<p><?= $this->Paginator->counter() ?></p>
<?= $this->Paginator->numbers() ?>

<div class="actions">
	<h3><?= __d('lists', actions') ?></h3>
	<ul>
		<li><?= $this->Html->link(__('system_contents.add'), ['action' => 'add']) ?></li>
	</ul>
</div>
