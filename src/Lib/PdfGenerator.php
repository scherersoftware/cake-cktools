<?php
declare(strict_types = 1);
namespace CkTools\Lib;

use Cake\Core\InstanceConfigTrait;
use Cake\Utility\Hash;
use Cake\View\View;
use InvalidArgumentException;
use Mpdf\Mpdf;
use RuntimeException;

/**
 * Wrapper for generating PDF files with MPDF using CakePHP's view files.
 *
 * @package default
 */
class PdfGenerator
{

    use InstanceConfigTrait;

    // Return the MPDF instance
    public const TARGET_RETURN = 'return';

    // send the file to the browser
    public const TARGET_BROWSER = 'browser';

    // write the PDF to a file
    public const TARGET_FILE = 'file';

    // Will return the rendered PDF's binary data
    public const TARGET_BINARY = 'binary';

    // Will download the file to the browser
    public const TARGET_DOWNLOAD = 'download';

    /**
     * Defaut config
     *
     * @var array
     */
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
            'margin_footer' => 9,
        ],
        // Path to a PDF file to render the view on
        'pdfSourceFile' => null,
        'cssFile' => null,
        'cssStyles' => null,
        'mpdfConfigurationCallback' => null,
        'view' => null,
    ];

    /**
     * Constructor
     *
     * @param array $config Instance Config
     */
    public function __construct(array $config = [])
    {
        $this->setConfig($config);
        if ($this->_config['pdfSourceFile'] && !is_readable($this->_config['pdfSourceFile'])) {
            throw new RuntimeException("pdfSourceFile {$this->_config['pdfSourceFile']} is not readable.");
        }
    }

    /**
     * Prepares a View instance with which the given view file
     * will be rendered.
     *
     * @return \Cake\View\View
     */
    protected function _getView(): View
    {
        if (!$this->getConfig('view')) {
            $view = new View();
            foreach ($this->getConfig('helpers') as $helper) {
                $view->loadHelper($helper);
            }

            $this->setConfig('view', $view);
        }

        return $this->getConfig('view');
    }

    /**
     * Instanciate and configure the MPDF instance
     *
     * @param string|array $viewFile    One or more view files
     * @param array        $viewVars    View variables
     * @return \Mpdf\Mpdf
     */
    protected function _preparePdf($viewFile, array $viewVars): Mpdf
    {
        $mpdf = new Mpdf($this->_config['mpdfSettings']);

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

                    if ($pageNumber < $pagecount) {
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
     * @param array $options Options
     *               - target
     *                   - TARGET_RETURN: Return the MPDF instance
     *                   - TARGET_BROWSER: Send the rendered PDF file to the browser
     *                   - TARGET_FILE: Save the PDF to the given file
     *               - viewVars: Variables to pass to the $viewFile
     *               - filename: Used with TARGET_BROWSER and TARGET_FILE
     * @return mixed
     */
    public function render($viewFile, array $options = [])
    {
        $options = Hash::merge([
            'target' => self::TARGET_RETURN,
            'filename' => 'pdf.pdf',
        ], $options);

        $mpdf = $this->_preparePdf($viewFile, $options['viewVars']);
        $options['viewVars']['mpdf'] = $mpdf;

        if (!is_array($viewFile)) {
            $wholeString = $this->_getView()->element($viewFile, $options['viewVars']);
            // use 10% less than pcre.backtrack_limit to account for multibyte characters
            $splitLength = ini_get('pcre.backtrack_limit') - (ini_get('pcre.backtrack_limit') / 10);
            $splitString = str_split($wholeString, $splitLength);
            foreach ($splitString as $string) {
                $mpdf->WriteHTML($string);
            }
        }

        switch ($options['target']) {
            case self::TARGET_RETURN:
                return $mpdf;
            case self::TARGET_DOWNLOAD:
                $mpdf->Output($options['filename'], 'D');
                break;
            case self::TARGET_BROWSER:
                $mpdf->Output($options['filename'], 'I');
                break;
            case self::TARGET_FILE:
                $mpdf->Output($options['filename'], 'F');
                break;
            case self::TARGET_BINARY:
                return $mpdf->Output('', 'S');
            default:
                throw new InvalidArgumentException("{$options['target']} is not a valid target");
        }
    }
}
