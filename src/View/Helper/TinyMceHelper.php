<?php
declare(strict_types = 1);
namespace CkTools\View\Helper;

use Cake\Utility\Hash;
use Cake\View\Helper;
use Cake\View\Helper\IdGeneratorTrait;

/**
 * @property \Cake\View\Helper\HtmlHelper $Html
 * @property \Cake\View\Helper\FormHelper $Form
 */
class TinyMceHelper extends Helper
{

    use IdGeneratorTrait;

    /**
     * Used helpers
     *
     * @var array
     */
    public $helpers = ['Html', 'Form'];

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * Includes all assets needed using HtmlHelper::script()
     *
     * @return void
     */
    public function includeAssets(): void
    {
        $this->Html->script('CkTools.vendor/tinymce/jquery.tinymce.min.js', ['block' => true]);
        $this->Html->script('CkTools.vendor/moxiemanager/js/moxman.loader.min.js', ['block' => true]);
    }

    /**
     * Create filepicker input
     *
     * @param string $fieldName The name of the input field
     * @param array $options options
     * @return string
     */
    public function picker(string $fieldName, array $options = []): string
    {
        if (!isset($options['class'])) {
            $options['class'] = '';
        }
        $options['class'] .= ' mox-picker';

        $options = Hash::merge([
            'append' => $this->Html->link(__d('tinymce', 'pick_file'), '#', ['class' => 'mox-picker-btn'])
        ], $options);

        $picker = $this->Form->input($fieldName, $options);

        return $picker;
    }
}
