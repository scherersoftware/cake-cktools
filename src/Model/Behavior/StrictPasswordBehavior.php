<?php
namespace CkTools\Model\Behavior;

use Cake\Auth\DefaultPasswordHasher;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * StrictPassword behavior
 */
class StrictPasswordBehavior extends Behavior
{

    /**
     * Default configuration.
     *
     * @var array
     */
     protected $_defaultConfig = [
         // minimal password length
         'minPasswordLength' => 10,
         // fistname and surname are not allowed in password (case insensitive)
         'noUserName' => true,
         // at least one special char is needed in password
         'specialChars' => true,
         // at least one char in upper case is needed in password
         'upperCase' => true,
         // at least one char in lower case is needed in password
         'lowerCase' => true,
         // at least one numeric value is needed in password
         'numericValue' => true,
         // reuse of old passwords is not allowed: number of old passwords to preserve
         'oldPasswordCount' => 4
     ];

    /**
     * initialize
     * @param  array  $config config
     * @return void
     */
    public function initialize(array $config)
    {
        // set json type to last_passwords field
        $this->_table->schema()->columnType('last_passwords', 'json');
    }

    /**
     * set strict validation
     *
     * @param  Event $event Event
     * @param  Validator $validator Validator
     * @param  string  $name name
     * @return void
     */
    public function buildValidator(Event $event, Validator $validator, $name)
    {
        $this->validationStrictPassword($validator);
    }

    /**
     * strict password validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationStrictPassword(Validator $validator)
    {
        $validator
            ->add('password', [
                'minLength' => [
                    'rule' => ['minLength', $this->config('minPasswordLength')],
                    'last' => true,
                    'message' => ('validation.user.password_min_length')
                ],
            ])
            ->add('password', 'passwordFormat', [
                'rule' => function($value, $context) {
                    return $this->validFormat($value, $context);
                },
                'message' => __('validation.user.password_invalid_format')
            ])
            ->add('password', 'passwordNoUserName', [
                'rule' => function($value, $context) {
                    return $this->checkForUserName($value, $context);
                },
                'message' => __('validation.user.password_user_name_not_allowed')
            ])
            ->add('password', 'passwordUsedBefore', [
                'rule' => function($value, $context) {
                    return $this->checkLastPasswords($value, $context);
                },
                'message' => __('validation.user.password_used_before')
            ]);

        return $validator;
    }

    /**
     * check password format
     *
     * @param  string $value   password
     * @param  array $context context data
     * @return bool
     */
    public function validFormat($value, $context)
    {
        // one or more letter in upper case
        if ($this->config('upperCase') && !preg_match('/[A-Z]/', $value)) {
            return false;
        }
        // one or more letter in lower case
        if ($this->config('lowerCase') && !preg_match('/[a-z]/', $value)) {
            return false;
        }
        // one or more numeric value
        if ($this->config('numericValue') && !preg_match('/[0-9]/', $value)) {
            return false;
        }
        // one ore more special char (no digit or letter)
        if ($this->config('specialChar') && !preg_match('/[^äüößÄÜÖA-Za-z0-9]/', $value)) {
            return false;
        }

        return true;
    }

    /**
     * check for username used in password
     *
     * @param  string $value   password
     * @param  array $context context data
     * @return bool
     */
    public function checkForUserName($value, $context)
    {
        // return true if config is not set
        if (!$this->config('noUserName')) {
            return true;
        }

        // No Usernames in Context
        if (empty($context['data']['firstname']) || $context['data']['lastname']) {
            if (!empty($context['data']['id'])) {
                $user = $this->_table->get($context['data']['id']);
                $firstname = $user->firstname;
                $lastname = $user->lastname;

            } else {
                // no name to check
                return true;
            }
        } else {
            $firstname = $context['data']['firstname'];
            $lastname = $context['data']['lastname'];
        }
        // validate password
        if (strpos(strtolower($value), strtolower($firstname)) !== false) {
            return false;
        }
        if (strpos(strtolower($value), strtolower($lastname)) !== false) {
            return false;
        }

        return true;
    }

    /**
     * check for reusage of old passwords
     *
     * @param  string $value   password
     * @param  array $context context data
     * @return bool
     */
    public function checkLastPasswords($value, $context)
    {
        // return true if config is not set
        if (empty($this->config('oldPasswordCount'))) {
            return true;
        }

        // ignore on new user
        if (empty($context['data']['id'])) {
            return true;
        }
        $user = $this->_table->get($context['data']['id']);

        foreach ($user->last_passwords as $oldPasswordHash) {
            if ((new DefaultPasswordHasher)->check($value, $oldPasswordHash)) {
                return false;
            }
        }

        return true;
    }

    /**
     * BeforeSave Event to update last passwords
     *
     * @param  Event $event Event
     * @param  EntityInterface $entity  Entity
     * @return void
     */
    public function beforeSave(Event $event, EntityInterface $entity)
    {
        if (empty($this->config('oldPasswordCount')) || !is_numeric($this->config('oldPasswordCount'))) {
            return true;
        }

        $lastPasswords = $entity->last_passwords;
        if (count($lastPasswords) == $this->config('oldPasswordCount')) {
            array_shift($lastPasswords);
        }
        $lastPasswords[] = $entity->password;
        $entity->accessible('last_passwords', true);
        $this->_table->patchEntity($entity, [
            'last_passwords' => $lastPasswords
        ]);

        return true;
    }
}
