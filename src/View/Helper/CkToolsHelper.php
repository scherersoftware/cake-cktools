<?php
namespace CkTools\View\Helper;

use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\Utility\Text;
use Cake\View\Helper;

class CkToolsHelper extends Helper
{
    public $helpers = ['Html', 'Form'];

    /**
     * Returns a map of countries with their translations
     *
     * @param array $countryCodes If an array of country codes is given,
     *                            a map of just these will be returned
     * @return array
     */
    public function countries(array $countryCodes = null)
    {
        if (!Configure::read('countries')) {
            Configure::load('CkTools.countries');
        }
        if ($countryCodes) {
            $countries = Configure::read('countries');
            $subset = [];
            foreach ($countryCodes as $countryCode) {
                $subset[$countryCode] = isset($countries[$countryCode]) ? $countries[$countryCode] : $countryCode;
            }
            return $subset;
        }
        return Configure::read('countries');
    }

    /**
     * Returns the translated version of the given country
     *
     * @param string $country E.g. "de", "en"
     * @return string
     */
    public function country($country)
    {
        if (!Configure::read('countries')) {
            Configure::load('CkTools.countries');
        }
        $countries = Configure::read('countries');
        return isset($countries[$country]) ? $countries[$country] : $country;
    }

    /**
     * Render a datepicker input to be processed by DatePicker.js
     *
     * @param string $field Field Name
     * @param array $options Options
     * @return string
     */
    public function datepickerInput($field, array $options = [])
    {
        $options = Hash::merge([
            'type' => 'date'
        ], $options);

        return $this->Form->input($field, $options);
    }


    /**
     * Creates a mailto link.
     *
     * @param string $email Address to send to
     * @param array $options Aside from the standard HtmlHelper::link() options, the
     *                       following keys are accepted:
     *                          - caption - if the display text should differ from the $email parameter
     *                          - body - will be urlencoded into the link
     *                          - subject - will be urlencoded into the link
     * @return string
     */
    public function mailtoLink($email, array $options = [])
    {
        $options = Hash::merge([
            'body' => null,
            'subject' => null,
            'caption' => null
        ], $options);

        $body = $options['body'];
        $subject = $options['subject'];
        $caption = $options['caption'];
        unset($options['body'], $options['subject'], $options['caption']);

        $href = 'mailto:' . $email;

        $queryData = [];
        if ($subject) {
            $queryData['subject'] = $subject;
        }
        if ($body) {
            $queryData['body'] = $body;
        }

        if (!empty($queryData)) {
            $href .= '?' . http_build_query($queryData);
        }

        if (!$caption) {
            $caption = $email;
        }
        return $this->Html->link($caption, $href, $options);
    }

    /**
     * Renders an edit button
     *
     * @param EntityInterface $entity Entity to take the ID from
     * @param array $options Config options
     * @return string
     */
    public function editButton(EntityInterface $entity, array $options = [])
    {
        $options = Hash::merge([
            'url' => null,
            'title' => __d('cktools', 'edit'),
            'icon' => 'fa fa-pencil',
            'escape' => false,
            'class' => 'btn btn-default btn-xs btn-edit'
        ], $options);

        $url = $options['url'];
        $title = $options['title'];
        $icon = $options['icon'];
        unset($options['url'], $options['title'], $options['icon']);
        if (!$url) {
            list($plugin, $controller) = pluginSplit($entity->source());
            $url = [
                'plugin' => $this->_View->request->plugin,
                'controller' => $controller,
                'action' => 'edit',
                $entity->id
            ];
        }
        if ($icon) {
            $title = '<i class="' . $icon . '"></i> ' . '<span class="button-text">' . $title . '</span>';
            $options['escape'] = false;
        }
        return $this->Html->link($title, $url, $options);
    }

    /**
     * Renders a details button
     *
     * @param EntityInterface $entity Entity to take the ID from
     * @param array $options Config options
     * @return string
     */
    public function viewButton(EntityInterface $entity, array $options = [])
    {
        $options = Hash::merge([
            'url' => null,
            'title' => __d('cktools', 'view'),
            'icon' => 'fa fa-eye',
            'class' => 'btn btn-default btn-xs btn-edit'
        ], $options);

        $url = $options['url'];
        $title = $options['title'];
        $icon = $options['icon'];
        unset($options['url'], $options['title'], $options['icon']);
        if (!$url) {
            list($plugin, $controller) = pluginSplit($entity->source());
            $url = [
                'plugin' => $this->_View->request->plugin,
                'controller' => $controller,
                'action' => 'view',
                $entity->id
            ];
        }
        if ($icon) {
            $title = '<i class="' . $icon . '"></i> ' . '<span class="button-text">' . $title . '</span>';
            $options['escape'] = false;
        }
        return $this->Html->link($title, $url, $options);
    }

    /**
     * Renders an add button
     *
     * @param string $title Link Caption
     * @param array $options Additional Options
     * @return string
     */
    public function addButton($title = null, array $options = [])
    {
        if (!$title) {
            $title = __d('cktools', 'add');
        }
        $options = Hash::merge([
            'url' => null,
            'icon' => 'fa fa-plus',
            'class' => 'btn btn-default btn-xs btn-add'
        ], $options);
        $url = $options['url'];
        if (!$url) {
            $url = ['action' => 'add'];
        }
        $icon = $options['icon'];
        unset($options['url'], $options['icon']);
        if ($icon) {
            $title = '<i class="' . $icon . '"></i> ' . '<span class="button-text">' . $title . '</span>';
            $options['escape'] = false;
        }
        return $this->Html->link($title, $url, $options);
    }

    /**
     * Renders an delete button
     *
     * @param EntityInterface $entity Entity to take the ID from
     * @param array $options Config options
     * @return string
     */
    public function deleteButton(EntityInterface $entity, array $options = [])
    {
        $options = Hash::merge([
            'url' => null,
            'title' => __d('cktools', 'delete'),
            'icon' => 'fa fa-trash-o',
            'class' => 'btn btn-danger btn-xs',
            'confirm' => __d('cktools', 'delete_confirmation'),
            'usePostLink' => false
        ], $options);

        $url = $options['url'];
        $title = $options['title'];
        $icon = $options['icon'];
        unset($options['url'], $options['title'], $options['icon']);
        if (!$url) {
            list($plugin, $controller) = pluginSplit($entity->source());
            $url = [
                'plugin' => $this->_View->request->plugin,
                'controller' => $controller,
                'action' => 'delete',
                $entity->id
            ];
        }
        if ($icon) {
            $title = '<i class="' . $icon . '"></i> ' . '<span class="button-text">' . $title . '</span>';
            $options['escape'] = false;
        }
        if ($options['usePostLink']) {
            return $this->Form->postLink($title, $url, $options);
        } else {
            return $this->Html->link($title, $url, $options);
        }
    }

    /**
     * Renders form buttons
     *
     * @return void
     */
    public function formButtons(array $options = [])
    {
        $url = ['action' => 'index'];
        $options = Hash::merge([
            'useReferer' => false,
            'cancelButton' => true,
            'saveButtonTitle' => __d('cktools', 'save'),
            'cancelButtonTitle' => __d('cktools', 'cancel')
        ], $options);

        if (!empty($options['useReferer']) && $this->request->referer() != '/') {
            $url = $this->request->referer();
        }

        $formButtons = '<div class="submit-group">';
        $formButtons .= '<hr>';
        $formButtons .= $this->Form->button($options['saveButtonTitle'], ['class' => 'btn-success']);
        if ($options['cancelButton']) {
            $formButtons .= $this->Html->link($options['cancelButtonTitle'], $url, ['class' => 'btn btn-default cancel-button', 'icon' => null]);
        }
        $formButtons .= '</div>';
        return $formButtons;
    }

    /**
     * Renders a button
     *
     * @param string $title Title to display
     * @param string|array $url URL to point to
     * @param array $options Additional Options
     * @return string
     */
    public function button($title, $url = false, array $options = [])
    {
        $options = Hash::merge([
            'icon' => 'arrow-right',
            'class' => 'btn btn-default btn-xs',
            'additionalClasses' => ''
        ], $options);

        $options['class'] .= ' ' . $options['additionalClasses'];
        if ($options['icon']) {
            $title = '<i class="fa fa-' . $options['icon'] . '"></i> ' . $title;
            $options['escape'] = false;
        }
        unset($options['additionalClasses'], $options['icon']);
        return $this->Html->link($title, $url, $options);
    }

    /**
     * Render a <dl>
     *
     * @param array $data Keys and Values
     * @param array $options Additional Options
     * @return string
     */
    public function definitionList($data, array $options = [])
    {
        $options = Hash::merge([
            'class' => 'dl-horizontal',
            'escape' => true
        ], $options);
        $ret = '<dl class="' . $options['class'] . '">';
        foreach ($data as $key => $value) {
            $ret .= '<dt>' . $key . '</dt>';
            $ret .= '<dd>' . (empty($value) ? '&nbsp;' : ($options['escape'] ? h($value) : $value)) . '</dd>';
        }
        $ret .= '</dl>';
        return $ret;
    }

    /**
     * Renders a nested list
     *
     * Usage:
     *     $tree = $this->Posts->find('threaded');
     *     echo $this->CkTools->nestedList($tree, '<a href="{{url}}">{{title}}</a>');
     *
     * @param array $data Nested array
     * @param string $content String template for each node
     * @param int $level Depth
     * @param array $isActiveCallback Will be passed the record
     * @return string
     */
    public function nestedList($data, $content, $level = 0, $isActiveCallback = null)
    {
        $tabs = "\n" . str_repeat("	", ($level * 2));
        $liTabs = $tabs . "	";

        $output = $tabs . '<ul>';
        foreach ($data as $n => $record) {
            $liClasses = [];
            $liContent = $content;
            if ($isActiveCallback != null) {
                $additionalArguments = !empty($isActiveCallback['arguments']) ? $isActiveCallback['arguments'] : [];
                $isActive = call_user_func_array($isActiveCallback['callback'], [&$record, $additionalArguments]);
                if ($isActive) {
                    $liClasses[] = 'active';
                }
            }

            // find the model variables
            preg_match_all("/\{\{([a-z0-9\._]+)\}\}/i", $liContent, $matches);
            if (!empty($matches)) {
                $variables = array_unique($matches[1]);
                foreach ($variables as $n => $modelField) {
                    $liContent = str_replace('{{' . $modelField . '}}', $record[$modelField], $liContent);
                }
            }
            if (!empty($record['children'])) {
                $liClasses[] = 'has-children';
            }

            $output .= $liTabs . '<li class="' . implode(' ', $liClasses) . '">' . $liContent;
            if (isset($record['children'][0])) {
                $output .= $this->nestedList($record['children'], $content, ($level + 1), $isActiveCallback);
                $output .= $liTabs . '</li>';
            } else {
                $output .= '</li>';
            }
        }
        $output .= $tabs . '</ul>';
        return $output;
    }

    /**
     * Renders a div with an onclick-hanlder, which uses the history API of the Browser
     *
     * @return string
     */
    public function historyBackButton(array $options = [])
    {
        //FIXME: Add detection for IE8 & IE9 and create fallback
        $options = Hash::merge([
            'icon' => 'fa fa-arrow-left',
            'class' => 'btn btn-default btn-xs'
        ], $options);
        return '<div class="' . $options['class'] . '" onclick="history.back()"><i class="' . $options['icon'] . '"></i> ' . __d('cktools', 'history_back_button') . '</div>';
    }

    /**
     * Display an array in human-readable format and provide options to toggle
     * its display
     *
     * @param array $data Data to display
     * @param array $options Options:
     *                       - expanded: Whether the data should be shown by default
     *                       - expandLinkText: Link text for toggling the content
     * @return string
     */
    public function displayStructuredData(array $data, array $options = [])
    {
        $options = Hash::merge([
            'expanded' => true,
            'expandLinkText' => __d('cktools', 'utility.toggle_content')
        ], $options);

        if (!$options['expanded']) {
            $id = 'dsd-' . uniqid();
            $out = $this->Html->link($options['expandLinkText'], 'javascript:', [
                'type' => 'button',
                'data-toggle' => 'collapse',
                'aria-expanded' => 'false',
                'aria-controls' => $id,
                'data-target' => '#' . $id
            ]);
            $out .= '<div class="collapse" id="' . $id . '">' . $this->arrayToUnorderedList($data) . '</div>';
        } else {
            $out = $this->arrayToUnorderedList($data);
        }
        return $out;
    }

    /**
     * Converts an array to a <ul>, recursively
     *
     * @param array $array Data
     * @return string
     */
    public function arrayToUnorderedList(array $array)
    {
        $out = '<ul>';
        foreach ($array as $key => $elem) {
            if (!is_array($elem)) {
                $out = $out . "<li><span>{$key}: " . h($elem) . "</span></li>";
            } else {
                $out = $out . "<li><span>{$key}</span>" . $this->arrayToUnorderedList($elem) . '</li>';
            }
        }
        $out = $out . '</ul>';
        return $out;
    }
}
