<?php
declare(strict_types = 1);
namespace CkTools\View\Helper;

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
    }
}
