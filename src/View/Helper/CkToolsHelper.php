<?php
namespace CkTools\View\Helper;

use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Utility\Text;
use Cake\View\Helper;

class CkToolsHelper extends Helper
{
    public $helpers = ['Form'];

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
}
