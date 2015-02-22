<?php
namespace CkTools\Lib;

use Cake\Core\Configure;
use Cake\Core\InstanceConfigTrait;
use Cake\Utility\Hash;
use Cake\View\View;
use mPDF;

/**
 * Wrapper for generating PDF files with MPDF using CakePHP's view files.
 *
 * @package default
 */
class PdfGenerator
{
    use InstanceConfigTrait;

    // Return the MPDF instance
    const TARGET_RETURN = 'return';

    // send the file to the browser
    const TARGET_BROWSER = 'browser';

    // write the PDF to a file
    const TARGET_FILE = 'file';

    protected $_defaultConfig = [
        'helpers' => ['Html'],
        'viewParams' => [],
        'mpdfSettings' => [
            'mode' => 'utf8-s',
            'format' => 'A4',
            'font_size' => 0,
            'font' => 'Arial',
            'margin_left' => 24,
            'margin_right' => 15,
            'margin_top' => 25,
            'margin_bottom' => 16,
            'margin_header' => 9,
            'margin_footer' => 9
        ],
        // Path to a PDF file to render the view on
        'pdfSourceFile' => null,
        'cssFile' => null,
        'cssStyles' => null
    ];

    /**
     * Constructor
     *
     * @param array $config Instance Config
     */
    public function __construct(array $config = [])
    {
        $this->config($config);
        if ($this->_config['pdfSourceFile'] && !is_readable($this->_config['pdfSourceFile'])) {
            throw new \Exception("pdfSourceFile {$this->_config['pdfSourceFile']} is not readable.");
        }
    }

    /**
     * Prepares a View instance with which the given view file
     * will be rendered.
     *
     * @return View
     */
    protected function _getView()
    {
        $view = new View();
        $view->webroot = null;
        $view->helpers = $this->config('helpers');
        $view->params = $this->config('viewParams');
        return $view;
    }

    /**
     * Instanciate and configure the MPDF instance
     *
     * @return MPDF
     */
    protected function _preparePdf()
    {
        $c = $this->_config['mpdfSettings'];
        $mpdf = new mPDF($c['mode'], $c['format'], $c['font_size'], $c['font'], $c['margin_left'], $c['margin_right'], $c['margin_top'], $c['margin_bottom'], $c['margin_header'], $c['margin_footer']);

        if ($this->_config['pdfSourceFile']) {
            $mpdf->SetImportUse();
            $pagecount = $mpdf->SetSourceFile($this->_config['pdfSourceFile']);
            $tplId = $mpdf->ImportPage($pagecount);
            $mpdf->SetPageTemplate($tplId);
        }

        $styles = '';
        if ($this->_config['cssFile']) {
            $styles = file_get_contents($this->_config['cssFile']);
        }
        if ($this->_config['cssStyles']) {
            $styles .= $this->_config['cssStyles'];
        }
        if (!empty($styles)) {
            $mpdf->WriteHTML($styles, 1);
        }
        return $mpdf;
    }

    /**
     * Render a view file
     *
     * @param string $viewFile Path to the View file to render
     * @param array $options Options
     *                  - target
     *                      - TARGET_RETURN: Return the MPDF instance
     *                      - TARGET_BROWSER: Send the rendered PDF file to the browser
     *                      - TARGET_FILE: Save the PDF to the given file
     *                  - viewVars: Variables to pass to the $viewFile
     *                  - filename: Used with TARGET_BROWSER and TARGET_FILE
     * @return void
     */
    public function render($viewFile, array $options = [])
    {
        $options = Hash::merge([
            'target' => self::TARGET_RETURN,
            'filename' => 'pdf.pdf'
        ], $options);

        // mPDF throws lots of notices, sadly
        $oldErrorReporing = error_reporting();
        error_reporting(0);

        $mpdf = $this->_preparePdf();
        $options['viewVars']['mpdf'] = $mpdf;
        $this->_getView()->element($viewFile, $options['viewVars']);

        switch ($options['target']) {
            case self::TARGET_RETURN:
                error_reporting($oldErrorReporing);
                return $mpdf;
                break;
            case self::TARGET_BROWSER:
                $mpdf->Output($options['filename'], 'I');
                error_reporting($oldErrorReporing);
                break;
            case self::TARGET_FILE:
                $mpdf->Output($options['filename'], 'F');
                error_reporting($oldErrorReporing);
                break;
            default:
                error_reporting($oldErrorReporing);
                throw new \InvalidArgumentException("{$options['target']} is not a valid target");
                break;
        }
    }
}
