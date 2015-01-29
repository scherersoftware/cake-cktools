<?php
namespace CkTools\View\Helper;

use Cake\Core\Configure;
use Cake\View\Helper;

class CkToolsHelper extends Helper
{
    public $helpers = ['Form'];

    /**
     * Returns a map of countries with their translations
     *
     * @return array
     */
    public function countries()
    {
        if (!Configure::read('countries')) {
            Configure::load('CkTools.countries');
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
}
