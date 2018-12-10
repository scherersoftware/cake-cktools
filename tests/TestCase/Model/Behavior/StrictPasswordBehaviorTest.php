<?php
declare(strict_types = 1);
namespace CkTools\Test\TestCase\Model\Behavior;

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * CkTools\Model\Behavior\StrictPasswordBehavior Test Case
 */
class StrictPasswordBehaviorTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.CkTools.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Users = TableRegistry::get('CkTools.Users');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Users);
        TableRegistry::remove('CkTools.Users');
        parent::tearDown();
    }

    /**
     * Test valid format
     *
     * @return void
     */
    public function testValidFormat(): void
    {
        $this->Users->addBehavior('CkTools.StrictPassword', [
            'userNameFields' => [
                'firstname' => 'forename',
                'lastname' => 'surname',
            ],
            'minPasswordLength' => 8,
        ]);

        $newUser = $this->Users->newEntity([
            'forename' => 'vorname',
            'surname' => 'nachname',
            'password' => 'forename',
        ]);
        
        // No uppercase
        $this->assertArrayHasKey('passwordFormat', $newUser->getError('password'));

        $data = ['password' => 'FORENAME'];
        $result = $this->Users->patchEntity($newUser, $data); // No lowercase
        $this->assertArrayHasKey('passwordFormat', $newUser->getError('password'));

        $data = ['password' => 'Forename'];
        $result = $this->Users->patchEntity($newUser, $data); // No numeric value
        $this->assertArrayHasKey('passwordFormat', $newUser->getError('password'));

        $data = ['password' => 'Forename1'];
        $result = $this->Users->patchEntity($newUser, $data);  // No special char
        $this->assertArrayHasKey('passwordFormat', $newUser->getError('password'));

        $data = ['password' => 'Forename1?'];
        $result = $this->Users->patchEntity($newUser, $data);
        $this->assertEmpty($result->getErrors());
    }

    /**
     * Test check for username
     *
     * @return void
     */
    public function testCheckForUsername(): void
    {
        $this->Users->addBehavior('CkTools.StrictPassword', [
            'userNameFields' => [
                'firstname' => 'forename',
                'lastname' => 'surname',
            ],
            'minPasswordLength' => 8,
        ]);

        $newUser = $this->Users->newEntity([
            'forename' => 'Forename2?',
            'surname' => 'Nachname2?',
            'password' => 'Forename2?',
        ]);

        $result = $this->Users->save($newUser);
        $this->assertArrayHasKey('passwordNoUserName', $newUser->getError('password'));

        $data = [
            'forename' => 'Forename2?',
            'surname' => 'Nachname2?',
            'password' => 'Nachname2?',
        ];
        $result = $this->Users->patchEntity($newUser, $data);
        $this->assertArrayHasKey('passwordNoUserName', $newUser->getError('password'));

        $data = [
            'forename' => 'Forename2?',
            'surname' => 'Nachname2?',
            'password' => 'Keinname1?',
        ];
        $result = $this->Users->patchEntity($newUser, $data);
        $this->assertEmpty($result->getErrors());
    }

    /**
     * Test check last passwords
     *
     * @return void
     */
    public function testCheckLastPasswords($value='')
    {
        $hasher = new DefaultPasswordHasher();
        $this->Users->addBehavior('CkTools.StrictPassword', [
            'userNameFields' => [
                'firstname' => 'forename',
                'lastname' => 'surname',
            ],
            'minPasswordLength' => 8,
        ]);

        $newUser = $this->Users->newEntity([
            'forename' => 'vorname',
            'surname' => 'nachname',
            'password' => $hasher->hash('Passwort1?'),
            'last_passwords' => [$hasher->hash('Passwort2?')],
        ]);

        $result = $this->Users->save($newUser);
        $this->assertEmpty($result->getErrors());

        $data = [
            'id' => $newUser->id,
            'password' => 'Passwort2?',
        ];
        
        $result = $this->Users->patchEntity($newUser, $data);
        $this->assertArrayHasKey('passwordUsedBefore', $newUser->getError('password'));

        $data = [
            'id' => $newUser->id,
            'password' => 'Passwort3?',
        ];

        $result = $this->Users->patchEntity($newUser, $data);
        $this->assertEmpty($result->getErrors());
    }
}
