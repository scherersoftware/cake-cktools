<?php
namespace CkTools\View\Helper;

use Cake\Utility\Hash;
use Cake\View\Helper;
use Cake\View\Helper\IdGeneratorTrait;
use Cake\View\View;

/**
 * TinyMce helper
 */
class TinyMceHelper extends Helper
{

    use IdGeneratorTrait;

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
    public function includeAssets()
    {
        $this->Html->script('CkTools.vendor/tinymce/jquery.tinymce.min.js', ['block' => true]);
        $this->Html->script('CkTools.vendor/moxiemanager/js/moxman.loader.min.js', ['block' => true]);
    }

    /**
     * Create filepicker input
     *
     * @return string
     */
    public function picker($fieldName, array $options = [])
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
