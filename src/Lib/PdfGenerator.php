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

    // Will return the rendered PDF's binary data
    const TARGET_BINARY = 'binary';

    // Will download the file to the browser
    const TARGET_DOWNLOAD = 'download';

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
        'cssStyles' => null,
        'mpdfConfigurationCallback' => null
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
     * @param string|array $viewFile    One or more view files
     * @param array        $viewVars    View variables
     * @return MPDF
     */
    protected function _preparePdf($viewFile, $viewVars)
    {
        $c = $this->_config['mpdfSettings'];
        $mpdf = new mPDF($c['mode'], $c['format'], $c['font_size'], $c['font'], $c['margin_left'], $c['margin_right'], $c['margin_top'], $c['margin_bottom'], $c['margin_header'], $c['margin_footer']);
        if (is_callable($this->_config['mpdfConfigurationCallback'])) {
            $this->_config['mpdfConfigurationCallback']($mpdf);
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

        if ($this->_config['pdfSourceFile']) {
            $mpdf->SetImportUse();
            $pagecount = $mpdf->SetSourceFile($this->_config['pdfSourceFile']);
            if ($pagecount > 1) {
                for ($i = 0; $i <= $pagecount; ++$i) {
                    // Import next page from the pdfSourceFile
                    $pageNumber = $i + 1;
                    if ($pageNumber <= $pagecount) {
                        $importPage = $mpdf->ImportPage($pageNumber);
                        $mpdf->UseTemplate($importPage);
                        if (is_array($viewFile) && isset($viewFile[$i])) {
                            $mpdf->WriteHTML($this->_getView()->element($viewFile[$i], $viewVars));
                        }
                    }

                    if ($i < $pagecount) {
                        $mpdf->AddPage();
                    }
                }
            } else {
                $tplId = $mpdf->ImportPage($pagecount);
                $mpdf->SetPageTemplate($tplId);
            }
        }

        return $mpdf;
    }

    /**
     * Render a view file
     *
     * @param string|array  $viewFile Path to the View file to render or array with multiple
     * @param array         $options Options
     *                      - target
     *                          - TARGET_RETURN: Return the MPDF instance
     *                          - TARGET_BROWSER: Send the rendered PDF file to the browser
     *                          - TARGET_FILE: Save the PDF to the given file
     *                      - viewVars: Variables to pass to the $viewFile
     *                      - filename: Used with TARGET_BROWSER and TARGET_FILE
     * @return void
     */
    public function render($viewFile, array $options = [])
    {
        $options = Hash::merge([
            'target' => self::TARGET_RETURN,
            'filename' => 'pdf.pdf'
        ], $options);

        // mPDF throws lots of notices, sadly. There is no way around this.
        $oldErrorReporing = error_reporting();
        error_reporting(0);

        $mpdf = $this->_preparePdf($viewFile, $options['viewVars']);
        $options['viewVars']['mpdf'] = $mpdf;

        if (!is_array($viewFile)) {
            $mpdf->WriteHTML($this->_getView()->element($viewFile, $options['viewVars']));
        }

        switch ($options['target']) {
            case self::TARGET_RETURN:
                error_reporting($oldErrorReporing);
                return $mpdf;
                break;
            case self::TARGET_DOWNLOAD:
                $mpdf->Output($options['filename'], 'D');
                error_reporting($oldErrorReporing);
                break;
            case self::TARGET_BROWSER:
                $mpdf->Output($options['filename'], 'I');
                error_reporting($oldErrorReporing);
                break;
            case self::TARGET_FILE:
                $mpdf->Output($options['filename'], 'F');
                error_reporting($oldErrorReporing);
                break;
            case self::TARGET_BINARY:
                return $mpdf->Output('', 'S');
                error_reporting($oldErrorReporing);
                break;
            default:
                error_reporting($oldErrorReporing);
                throw new \InvalidArgumentException("{$options['target']} is not a valid target");
                break;
        }
    }
}
