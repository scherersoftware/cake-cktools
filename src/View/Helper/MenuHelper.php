<?php
declare(strict_types = 1);
namespace CkTools\View\Helper;

use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\Utility\Text;
use Cake\View\Helper;
use Cake\View\View;

class MenuHelper extends Helper
{

    /**
     * Used helpers
     *
     * @var array
     */
    public $helpers = ['AuthActions.Auth'];

    /**
     * Default configuration
     *
     * @var array
     */
    protected $_defaultConfig = [
        'configKey' => 'menu',
        'templates' => [
            'icon' => '<i class="fa fa-:icon fa-fw"></i>',
            'item' => '<li class=":liclass"><a href=":href" class=":class">:icon :title :childrenArrow</a>:childrenContainer</li>',
            'childrenArrow' => '<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>',
            'childrenContainer' => '<ul class="treeview-menu">:children</ul>',
        ],
    ];

    /**
     * Holds the current URL as an array
     *
     * @var array
     */
    protected $_currentUrl;

    /**
     * flag indicating active status of a controller item
     *
     * @var bool
     */
    protected $_controllerActive = false;

    /**
     * flag indicating active status of a action item
     *
     * @var bool
     */
    protected $_actionActive = false;

    /**
     * Configures the instance
     *
     * @param \Cake\View\View $View CakePHP View instance
     * @param array $config helper config
     */
    public function __construct(View $View, array $config = [])
    {
        parent::__construct($View, $config);
        $this->_currentUrl = Router::parse(Router::url());
    }

    /**
     * Renders
     *
     * @param string|array $config Either the config file to load or the menu config as an array
     * @return string   rendered HTML
     */
    public function renderSidebarMenuItems($config): string
    {
        $config = $this->prepareMenuConfig($config);
        $this->setPageTitle($config);
        $out = '';
        foreach ($config as $mainData) {
            $childrenContainer = '';
            $liclass = $mainData['active'] ? 'active' : '';

            if (!empty($mainData['children'])) {
                $children = '';
                foreach ($mainData['children'] as $child) {
                    $children .= Text::insert($this->_defaultConfig['templates']['item'], [
                        'title' => $child['title'],
                        'icon' => isset($child['icon']) ? Text::insert($this->_defaultConfig['templates']['icon'], ['icon' => $child['icon']]) : '',
                        'href' => isset($child['url']) ? Router::url($child['url']) : '',
                        'childrenArrow' => '',
                        'childrenContainer' => '',
                        'liclass' => $child['active'] ? 'active' : '',
                    ]);
                }
                $childrenContainer = Text::insert($this->_defaultConfig['templates']['childrenContainer'], [
                    'children' => $children,
                ]);
                $liclass .= ' treeview';
            }
            $out .= Text::insert($this->_defaultConfig['templates']['item'], [
                'class' => $mainData['active'] ? 'active' : '',
                'liclass' => $liclass,
                'title' => '<span>' . $mainData['title'] . '</span>',
                'icon' => isset($mainData['icon']) ? Text::insert($this->_defaultConfig['templates']['icon'], ['icon' => $mainData['icon']]) : '',
                'href' => isset($mainData['url']) ? Router::url($mainData['url']) : '',
                'childrenArrow' => !empty($mainData['children']) ? $this->_defaultConfig['templates']['childrenArrow'] : '',
                'childrenContainer' => $childrenContainer,
            ]);
        }

        return $out;
    }

    /**
     * Set page title automatically based on the current menu item
     *
     * @param array $config Menu Config
     * @return void
     */
    public function setPageTitle(array $config): void
    {
        foreach ($config as $item) {
            if (isset($item['active']) && $item['active']) {
                $this->_View->assign('title', $item['title']);
                break;
            }
        }
    }

    /**
     * Processes the given menu config, structures it and checks for permissions
     *
     * @param string|array $config Either the config file to load or the menu config as an array
     * @return array
     */
    public function prepareMenuConfig($config): array
    {
        if (is_string($config)) {
            Configure::load($config);
            $config = Configure::read($this->_defaultConfig['configKey']);
        }

        foreach ($config as $mainItem => &$mainData) {
            if (isset($mainData['url']) && !$this->Auth->urlAllowed($mainData['url'])) {
                unset($config[$mainItem]);
                continue;
            }

            if (!empty($mainData['children']) && !$this->_hasAllowedChildren($mainData['children'])) {
                unset($config[$mainItem]);
                continue;
            }

            if (isset($mainData['shouldRender']) && !$mainData['shouldRender']()) {
                unset($config[$mainItem]);
                continue;
            }

            $this->_isItemActive($mainData);

            $visibleChildCount = 0;
            if (!empty($mainData['children'])) {
                $activeChildCount = 0;
                $visibleChildCount = count($mainData['children']);
                foreach ($mainData['children'] as $subItem => &$subData) {
                    if (isset($subData['shouldRender']) && !$subData['shouldRender']()) {
                        unset($mainData['children'][$subItem]);
                        $visibleChildCount--;
                        continue;
                    }

                    $allowed = (!isset($subData['url']) || (isset($subData['url']) && $this->Auth->urlAllowed($subData['url'])));

                    if ($allowed) {
                        if ($this->_isItemActive($subData)) {
                            $activeChildCount++;
                        }
                    } else {
                        $visibleChildCount--;
                    }
                }

                if ($activeChildCount > 1) {
                    foreach ($mainData['children'] as $subItem => &$subData) {
                        $subData['active'] = false;
                    }
                }
            }

            // if the main item has no displayable children, remove it.
            if ($visibleChildCount === 0 && isset($config['children']) && count($config['children']) > 0) {
                unset($config[$mainItem]);
            }
        }
        unset($mainData, $subData);

        //set active status
        if (empty($this->_controllerActive)) {
            $this->_controllerActive = $this->_currentUrl['controller'];
        }
        foreach ($config as &$mainData) {
            $mainData['active'] = '';
            if (!empty($mainData['url']) && !empty($this->_actionActive)) {
                if ($mainData['url']['controller'] == $this->_controllerActive && $mainData['url']['action'] == $this->_actionActive) {
                    $mainData['active'] = true;
                }
            } elseif (!empty($mainData['url']) && $mainData['url']['controller'] == $this->_controllerActive) {
                $mainData['active'] = true;
            }
            if (!empty($mainData['children'])) {
                foreach ($mainData['children'] as &$subData) {
                    $subData['active'] = '';
                    if (!empty($subData['url']) && !empty($this->_actionActive)) {
                        if ($subData['url']['controller'] == $this->_controllerActive && $subData['url']['action'] == $this->_actionActive) {
                            $subData['active'] = true;
                            $mainData['active'] = true;
                        }
                    } elseif (!empty($subData['url']) && $subData['url']['controller'] == $this->_controllerActive) {
                        $mainData['active'] = true;
                    }
                }
            }
        }

        return $config;
    }

    /**
     * Checks if the given array of item children contains at least one
     * URL that is allowed to the current user.
     *
     * @param array $children item children
     * @return bool
     */
    protected function _hasAllowedChildren(array $children): bool
    {
        foreach ($children as $child) {
            if ($this->Auth->urlAllowed($child['url'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the given item URL should be marked active in the menu
     *
     * @param array $item Item to check
     * @return bool
     */
    protected function _isItemActive(array $item): bool
    {
        if (empty($item['url'])) {
            return false;
        }
        $current = $this->_currentUrl;
        if (!empty($item['url']['plugin'])) {
            if ($item['url']['plugin'] != $current['plugin']) {
                return false;
            }
        }
        if ($item['url']['controller'] == $current['controller'] && $item['url']['action'] == $current['action']) {
            $this->_controllerActive = $current['controller'];
            $this->_actionActive = $item['url']['action'];

            return true;
        }
        if ($item['url']['controller'] == $current['controller'] && !empty($this->_actionActive)) {
            $this->_controllerActive = $current['controller'];

            return true;
        }

        return false;
    }
}
