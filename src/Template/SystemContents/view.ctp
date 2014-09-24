<h2><?= __('system_contents.view') ?></h2>

<dl class="dl-horizontal">
	<dt><?= __('system_content.id') ?></dt>
	<dd><?= h($systemContent->id) ?></dd>

	<dt><?= __('system_content.identifier') ?></dt>
	<dd><?= h($systemContent->identifier) ?></dd>

	<dt><?= __('system_content.notes') ?></dt>
	<dd><?= h($systemContent->notes) ?></dd>

	<dt><?= __('system_content.created') ?></dt>
	<dd><?= h($systemContent->created) ?></dd>

	<dt><?= __('system_content.modified') ?></dt>
	<dd><?= h($systemContent->modified) ?></dd>

</dl>

<div class="actions">
	<h3><?= __('lists.actions'); ?></h3>
	<ul>
		<li><?= $this->Html->link(__('system_contents.edit'), ['action' => 'edit', $systemContent->id]) ?> </li>
		<li><?= $this->Form->postLink(__('system_contents.delete'), ['action' => 'delete', $systemContent->id], ['confirm' => __('lists.really_delete')]) ?></li>
		<li><?= $this->Html->link(__('lists.back_to_list'), ['action' => 'index']) ?> </li>
		<li><?= $this->Html->link(__('system_contents.add'), ['action' => 'add']) ?> </li>
	</ul>
</div>
