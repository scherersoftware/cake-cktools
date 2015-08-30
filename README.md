codekanzlei CakePHP 3 Toolkit
=============================

## Setting up an Api

You can create a plugin, e.g. "Api" to include all your API controller code. If you have an Api plugin,
make sure you set 'bootstrap' => true when loading the plugin in your bootstrap.

To make sure we always return errors in the api as JSON, include this to your App's or Plugin's bootstrap.php:

    if (substr(env('REQUEST_URI'), 0, 5) === '/api/') {
        $errorHandler = new \CkTools\Error\ApiErrorHandler([
            'exceptionRenderer' => '\CkTools\Error\ApiExceptionRenderer'
        ]);
        $errorHandler->register();
    }

Load the ApiComponent in your AppController's `initialize()` callback:

    $this->loadComponent('RequestHandler');
    $this->loadComponent('CkTools.Api');

In your AppController's `beforeFilter()` callback call 

    $this->Api->setup();

In a controller, your API endpoint controller action could look like this:

    public function hello_world()
    {
        $this->request->allowMethod('get');
        return $this->Api->response(200, ApiReturnCode::SUCCESS, [
            'hello' => 'world'
        ]);
    }

This call will return an `application/json` response with the HTTP Status Code `200` and the following body:

    {"code": "success","data":{"hello":"world"}}

## Create a PDF using CakePHP Views

CkTools includes a simple wrapper for the MPDF (http://www.mpdf1.com/mpdf/index.php) library.

Usage (in this example in the controller, but you can use this wherever you want):

    use CkTools\Lib\PdfGenerator;

    $pdfGenerator = new PdfGenerator([
        'pdfSourceFile' => '/path/to/your/template.pdf' // optional
    ]);
    $pdfGenerator->render('my_element', [
        'viewVars' => [
            'foo' => 'bar'
        ],
        'target' => PdfGenerator::TARGET_BROWSER,
        'cssFile' => '/path/to/your/styles.css',
        'cssStyles' => 'body { font-size: 20px }'
    ]);

In `Template/Element/my_element.ctp` you have the passed viewVars and the $mpdf variable for manipulating the PDF.

    <?php $this->start('my_block') ?>
        <div class="foo"><?= $foo ?></div> 
    <?php $this->end() ?>

    <?php
    $mpdf->SetHeader('My Document Title');
    $mpdf->Bookmark('Start of the document');
    $mpdf->WriteHTML($this->fetch('my_block'));

    // Add a new page
    $mpdf->AddPage();
    $mpdf->WriteHTML("This is page 2");
    ?>

## TinyMCE and Moxiemanager

### TinyMCE

To use TinyMCE with Moxiemanager, a really good media manager, CkTools provides a CakePHP-friendly integration.

Be aware that you need to purchase a license for Moxiemanager on http://www.moxiemanager.com/getit/ before using it outside your dev environment!

Setup:

- Load the `CkTools.TinyMce` helper in your controller
- Load the `TinyMce` and `MoxmanPicker` Components in your JS controller
- In your app configuration, configure Moxiemanager's parameters:

```
    'CkTools' => [
        'moxiemanager' => [
            'general.license' => '<your-license-key>',
            'filesystem.rootpath' => WWW_ROOT . 'files',
            'general.language' => 'en'
        ]
    ]
```

- In your app_controller.js or respective PageController, call `this.TinyMce.initComponents()`
- In your view or layout, call `$this->TinyMce->includeAssets();`
- Add a "tinymce" class to your textarea

### File Picker

The TinyMce helper provides a wrapper for FormHelper::input() and will generate an input field with a button to choose a file from Moxiemanager. The relative path of the chosen file will be set as the value of the text field.

    $this->TinyMce->picker('field_name', ['label' => 'My File Picker']);


## License

The MIT License (MIT)

Copyright (c) 2015 scherer software

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
