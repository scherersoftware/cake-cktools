
<?= $this->Form->create($systemContent, ['horizontal' => true]) ?>
	<fieldset>
		<legend><?= __('system_contents.form'); ?></legend>
		<?php
			echo $this->Form->input('identifier', [
				'label' => __('system_content.identifier')
			]);
			echo $this->Form->input('notes', [
				'label' => __('system_content.notes')
			]);
		?>
	</fieldset>
<?= $this->Form->formActions() ?>


<div class="actions">
	<h3><?= __('forms.actions') ?></h3>
	<ul>
		<?php if($this->request->action == 'edit'): ?>
			<li><?= $this->Form->postLink(__('lists.delete'), ['action' => 'delete', $systemContent->id], ['confirm' => __('lists.really_delete')]) ?></li>
		<?php endif; ?>
		<li><?= $this->Html->link(__('lists.back_to_list'), ['action' => 'index']) ?></li>
	</ul>
</div>