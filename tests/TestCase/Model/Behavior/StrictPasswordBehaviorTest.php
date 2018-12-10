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
        $this->Users = TableRegistry::getTableLocator()->get('CkTools.Users');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Users);
        TableRegistry::getTableLocator()->remove('CkTools.Users');
        parent::tearDown();
    }

    public function testMinLength(): void
    {
        $this->Users->addBehavior('CkTools.StrictPassword', [
            'minPasswordLength' => 10,
        ]);

        $newUser = $this->Users->newEntity([
            'firstname' => 'Mike',
            'lastname' => 'Jagger',
            'password' => 'TooShort1',
        ]);
        $this->assertArrayHasKey('minLength', $newUser->getError('password'));

        $newUser2 = $this->Users->newEntity([
            'firstname' => 'Mike',
            'lastname' => 'Jagger',
            'password' => 'NotTooShortLuckily1',
        ]);
        $this->assertArrayNotHasKey('minLength', $newUser2->getError('password'));
    }

    /**
     * Test valid format
     *
     * @return void
     */
    public function testValidFormat(): void
    {
        $this->Users->addBehavior('CkTools.StrictPassword', [
            'minPasswordLength' => 8,
        ]);

        // No uppercase
        $newUser = $this->Users->newEntity([
            'firstname' => 'Mike',
            'lastname' => 'Jagger',
            'password' => 'forename',
        ]);
        $this->assertArrayHasKey('passwordFormat', $newUser->getError('password'));

        // No lowercase
        $newUser = $this->Users->newEntity([
            'firstname' => 'Mike',
            'lastname' => 'Jagger',
            'password' => 'FORENAME',

        ]);
        $this->assertArrayHasKey('passwordFormat', $newUser->getError('password'));

        // No numeric value
        $newUser = $this->Users->newEntity([
            'firstname' => 'Mike',
            'lastname' => 'Jagger',
            'password' => 'Forename',

        ]);
        $this->assertArrayHasKey('passwordFormat', $newUser->getError('password'));

        // No special char
        $newUser = $this->Users->newEntity([
            'firstname' => 'Mike',
            'lastname' => 'Jagger',
            'password' => 'Forename1',

        ]);
        $this->assertArrayHasKey('passwordFormat', $newUser->getError('password'));

        // all good
        $newUser = $this->Users->newEntity([
            'firstname' => 'Mike',
            'lastname' => 'Jagger',
            'password' => 'Forename1?',

        ]);
        $this->assertEmpty($newUser->getErrors());
    }

    /**
     * Test check for username
     *
     * @return void
     */
    public function testCheckForUsername(): void
    {
        $this->Users->addBehavior('CkTools.StrictPassword', [
            'minPasswordLength' => 8,
        ]);

        $newUser = $this->Users->newEntity([
            'firstname' => 'Forename2?',
            'lastname' => 'Surname2?',
            'password' => 'Forename2?',
        ]);

        $result = $this->Users->save($newUser);
        $this->assertArrayHasKey('passwordNoUserName', $newUser->getError('password'));

        $data = [
            'firstname' => 'Forename2?',
            'lastname' => 'Surname2?',
            'password' => 'Surname2?',
        ];
        $result = $this->Users->patchEntity($newUser, $data);
        $this->assertArrayHasKey('passwordNoUserName', $newUser->getError('password'));

        $data = [
            'firstname' => 'Forename2?',
            'lastname' => 'Surname2?',
            'password' => 'Noname98?',
        ];
        $result = $this->Users->patchEntity($newUser, $data);
        $this->assertEmpty($result->getErrors());
    }

    /**
     * Test check last passwords
     *
     * @return void
     */
    public function testCheckLastPasswords(): void
    {
        $hasher = new DefaultPasswordHasher();
        $this->Users->addBehavior('CkTools.StrictPassword', [

            'minPasswordLength' => 8,
        ]);

        $newUser = $this->Users->newEntity([
            'firstname' => 'Mike',
            'lastname' => 'Jagger',
            'password' => $hasher->hash('Password1?'),
            'last_passwords' => [$hasher->hash('Password2?')],
        ]);

        $result = $this->Users->save($newUser);
        $this->assertEmpty($result->getErrors());

        $data = [
            'id' => $newUser->id,
            'password' => 'Password2?',
        ];

        $result = $this->Users->patchEntity($newUser, $data);
        $this->assertArrayHasKey('passwordUsedBefore', $newUser->getError('password'));

        $data = [
            'id' => $newUser->id,
            'password' => 'Password3?',
        ];

        $result = $this->Users->patchEntity($newUser, $data);
        $this->assertEmpty($result->getErrors());
    }

    public function testConfigurability(): void
    {
        $this->Users->addBehavior('CkTools.StrictPassword', [
            'userNameFields' => [
                'firstname' => 'qwert',
                'lastname' => 'vbnm',
            ],
            'minPasswordLength' => 3,
            'specialChars' => false,
            'upperCase' => false,
            'lowerCase' => false,
            'numericValue' => false,
        ]);

        $newUser = $this->Users->newEntity([
            'qwert' => 'Mike',
            'vbnm' => 'Jagger',
            'password' => 'foo',
        ]);
        $this->assertEmpty($newUser->getErrors());

        $newUser = $this->Users->newEntity([
            'qwert' => 'Mike',
            'vbnm' => 'Jagger',
            'password' => 'BAR',
        ]);
        $this->assertEmpty($newUser->getErrors());

        $newUser = $this->Users->newEntity([
            'qwert' => 'Mike',
            'vbnm' => 'Jagger',
            'password' => '123',
        ]);
        $this->assertEmpty($newUser->getErrors());

        $newUser = $this->Users->newEntity([
            'qwert' => 'Mike',
            'vbnm' => 'Jagger',
            'password' => '?=&',
        ]);
        $this->assertEmpty($newUser->getErrors());
    }
}
