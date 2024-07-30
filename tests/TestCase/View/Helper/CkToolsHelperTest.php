<?php
declare(strict_types = 1);
namespace CkTools\Test\TestCase\View\Helper;

use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use CkTools\View\Helper\CkToolsHelper;

class CkToolsHelperTest extends TestCase
{
    public $fixtures = [
        'plugin.CkTools.CkTestUsers',
    ];

    public $View = null;
    public $CkToolsHelper = null;

    // Here we instantiate our helper
    public function setUp(): void
    {
        parent::setUp();
        $this->CkTestUsers = TableRegistry::getTableLocator()->get('CkTools.CkTestUsers');

        $request = new ServerRequest([
            'webroot' => '',
        ]);
        Router::reload();
        Router::setRequestContext($request);

        $this->View = $this->getMockBuilder('Cake\View\View')
            ->setMethods(['append'])
            ->setConstructorArgs([$request])
            ->getMock();
        $this->CkToolsHelper = new CkToolsHelper($this->View);
    }

    public function tearDown(): void
    {
        unset($this->CkTestUsers);
        TableRegistry::getTableLocator()->remove('CkTools.CkTestUsers');
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function iconInSpecificButtonProvider(): array
    {
        return [
            ['editButton', 'fa-pencil'],
            ['viewButton', 'fa-eye'],
            ['addButton', 'fa-plus'],
            ['deleteButton', 'fa-trash-o'],
        ];
    }

    /**
     * @dataProvider iconInSpecificButtonProvider
     * @param string $buttonFunctionName
     * @param string $defaultIcon
     * @return void
     */
    public function testIconInSpecificButtons(string $buttonFunctionName, string $defaultIcon): void
    {
        $newUser = $buttonFunctionName === 'addButton' ? 'title' : $this->CkTestUsers->newEntity([
            'firstname' => 'Mike',
            'lastname' => 'Jagger',
        ]);

        // default style & icon
        $button = $this->CkToolsHelper->$buttonFunctionName($newUser, [
            // url necessary for Router to find a route when creating the link URL
            'url' => [
                'controller' => 'tests_apps',
                'action' => 'some_method',
            ],
        ]);
        $this->assertTextContains('<i class="fa ' . $defaultIcon . '"></i>', $button);

        // manual overwrite of icon
        $button = $this->CkToolsHelper->$buttonFunctionName($newUser, [
            'icon' => 'fa fa-airplane',
            // url necessary for Router to find a route when creating the link URL
            'url' => [
                'controller' => 'tests_apps',
                'action' => 'some_method',
            ],
        ]);
        $this->assertTextContains('<i class="fa fa-airplane"></i>', $button);

        // manual overwrite of style
        $button = $this->CkToolsHelper->$buttonFunctionName($newUser, [
            'icon' => 'fal ' . $defaultIcon,
            // url necessary for Router to find a route when creating the link URL
            'url' => [
                'controller' => 'tests_apps',
                'action' => 'some_method',
            ],
        ]);
        $this->assertTextContains('<i class="fal ' . $defaultIcon . '"></i>', $button);

        // manual overwrite of icon & style
        $button = $this->CkToolsHelper->$buttonFunctionName($newUser, [
            'icon' => 'fal fa-airplane',
            // url necessary for Router to find a route when creating the link URL
            'url' => [
                'controller' => 'tests_apps',
                'action' => 'some_method',
            ],
        ]);
        $this->assertTextContains('<i class="fal fa-airplane"></i>', $button);

        // style by config, icon default
        $this->CkToolsHelper->setConfig('iconStyle', 'fal');
        $button = $this->CkToolsHelper->$buttonFunctionName($newUser, [
            // url necessary for Router to find a route when creating the link URL
            'url' => [
                'controller' => 'tests_apps',
                'action' => 'some_method',
            ],
        ]);
        $this->assertTextContains('<i class="fal ' . $defaultIcon . '"></i>', $button);
        $this->CkToolsHelper->setConfig('iconStyle', null);

        // style by config, icon manual overwrite
        $this->CkToolsHelper->setConfig('iconStyle', 'fal');
        $button = $this->CkToolsHelper->$buttonFunctionName($newUser, [
            'icon' => 'fa-airplane',
            // url necessary for Router to find a route when creating the link URL
            'url' => [
                'controller' => 'tests_apps',
                'action' => 'some_method',
            ],
        ]);
        $this->assertTextContains('<i class="fal fa-airplane"></i>', $button);
        $this->CkToolsHelper->setConfig('iconStyle', null);

        // style by config, icon & style manual overwrite
        $this->CkToolsHelper->setConfig('iconStyle', 'fal');
        $button = $this->CkToolsHelper->$buttonFunctionName($newUser, [
            'icon' => 'far fa-airplane',
            // url necessary for Router to find a route when creating the link URL
            'url' => [
                'controller' => 'tests_apps',
                'action' => 'some_method',
            ],
        ]);
        $this->assertTextContains('<i class="far fa-airplane"></i>', $button);
        $this->CkToolsHelper->setConfig('iconStyle', null);
    }

    public function testIconInButton()
    {
        $defaultIcon = 'fa-arrow-right';
        $url = [];

        // default style & icon
        $button = $this->CkToolsHelper->button('title', $url);
        $this->assertTextContains('<i class="fa ' . $defaultIcon . '"></i>', $button);

        // manual overwrite of icon
        $button = $this->CkToolsHelper->button('title', $url, [
            'icon' => 'fa fa-airplane',
        ]);
        $this->assertTextContains('<i class="fa fa-airplane"></i>', $button);

        // manual overwrite of style
        $button = $this->CkToolsHelper->button('title', $url, [
            'icon' => 'fal ' . $defaultIcon,
        ]);
        $this->assertTextContains('<i class="fal ' . $defaultIcon . '"></i>', $button);

        // manual overwrite of icon & style
        $button = $this->CkToolsHelper->button('title', $url, [
            'icon' => 'fal fa-airplane',
        ]);
        $this->assertTextContains('<i class="fal fa-airplane"></i>', $button);

        // style by config, icon default
        $this->CkToolsHelper->setConfig('iconStyle', 'fal');
        $button = $this->CkToolsHelper->button('title', $url);
        $this->assertTextContains('<i class="fal ' . $defaultIcon . '"></i>', $button);
        $this->CkToolsHelper->setConfig('iconStyle', null);

        // style by config, icon manual overwrite
        $this->CkToolsHelper->setConfig('iconStyle', 'fal');
        $button = $this->CkToolsHelper->button('title', $url, [
            'icon' => 'fa-airplane'
        ]);
        $this->assertTextContains('<i class="fal fa-airplane"></i>', $button);
        $this->CkToolsHelper->setConfig('iconStyle', null);

        // style by config, icon & style manual overwrite
        $this->CkToolsHelper->setConfig('iconStyle', 'fal');
        $button = $this->CkToolsHelper->button('title', $url, [
            'icon' => 'far fa-airplane'
        ]);
        $this->assertTextContains('<i class="far fa-airplane"></i>', $button);
        $this->CkToolsHelper->setConfig('iconStyle', null);

        /**
         * legacy way of setting the icon, omitting 'fa-' from the beginning of every icon
         */
        // without configured icon style
        $button = $this->CkToolsHelper->button('title', $url, [
            'icon' => 'airplane'
        ]);
        $this->assertTextContains('<i class="fa fa-airplane"></i>', $button);

        // with configured icon style
        $this->CkToolsHelper->setConfig('iconStyle', 'fal');
        $button = $this->CkToolsHelper->button('title', $url, [
            'icon' => 'airplane'
        ]);
        $this->assertTextContains('<i class="fal fa-airplane"></i>', $button);
        $this->CkToolsHelper->setConfig('iconStyle', null);
    }
}
