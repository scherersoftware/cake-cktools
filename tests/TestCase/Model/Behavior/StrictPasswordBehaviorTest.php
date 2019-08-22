<?php
declare(strict_types = 1);
namespace CkTools\Test\TestCase\Model\Behavior;

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * CkTools\Model\Behavior\StrictPasswordBehavior Test Case
 *
 * @property \Cake\ORM\Table $CkTestUsers
 */
class StrictPasswordBehaviorTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.CkTools.CkTestUsers',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->CkTestUsers = TableRegistry::getTableLocator()->get('CkTools.CkTestUsers');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->CkTestUsers);
        TableRegistry::getTableLocator()->remove('CkTools.CkTestUsers');
        parent::tearDown();
    }

    public function testMinLength(): void
    {
        $this->CkTestUsers->addBehavior('CkTools.StrictPassword', [
            'minPasswordLength' => 10,
        ]);

        $newUser = $this->CkTestUsers->newEntity([
            'firstname' => 'Mike',
            'lastname' => 'Jagger',
            'password' => 'TooShort1',
        ]);
        $this->assertArrayHasKey('minLength', $newUser->getError('password'));

        $newUser2 = $this->CkTestUsers->newEntity([
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
        $this->CkTestUsers->addBehavior('CkTools.StrictPassword', [
            'minPasswordLength' => 8,
        ]);

        // No uppercase
        $newUser = $this->CkTestUsers->newEntity([
            'firstname' => 'Mike',
            'lastname' => 'Jagger',
            'password' => 'forename',
        ]);
        $this->assertArrayHasKey('passwordFormat', $newUser->getError('password'));

        // No lowercase
        $newUser = $this->CkTestUsers->newEntity([
            'firstname' => 'Mike',
            'lastname' => 'Jagger',
            'password' => 'FORENAME',

        ]);
        $this->assertArrayHasKey('passwordFormat', $newUser->getError('password'));

        // No numeric value
        $newUser = $this->CkTestUsers->newEntity([
            'firstname' => 'Mike',
            'lastname' => 'Jagger',
            'password' => 'Forename',

        ]);
        $this->assertArrayHasKey('passwordFormat', $newUser->getError('password'));

        // No special char
        $newUser = $this->CkTestUsers->newEntity([
            'firstname' => 'Mike',
            'lastname' => 'Jagger',
            'password' => 'Forename1',

        ]);
        $this->assertArrayHasKey('passwordFormat', $newUser->getError('password'));

        // all good
        $newUser = $this->CkTestUsers->newEntity([
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
        $this->CkTestUsers->addBehavior('CkTools.StrictPassword', [
            'minPasswordLength' => 8,
        ]);

        $newUser = $this->CkTestUsers->newEntity([
            'firstname' => 'Forename2?',
            'lastname' => 'Surname2?',
            'password' => 'Forename2?',
        ]);

        $result = $this->CkTestUsers->save($newUser);
        $this->assertArrayHasKey('passwordNoUserName', $newUser->getError('password'));
        $newUser->clean();

        $data = [
            'firstname' => 'Forename2?',
            'lastname' => 'Surname2?',
            'password' => 'Surname2?',
        ];
        $result = $this->CkTestUsers->patchEntity($newUser, $data);
        $this->assertArrayHasKey('passwordNoUserName', $newUser->getError('password'));
        $newUser->clean();

        $data = [
            'firstname' => 'Forename2?',
            'lastname' => 'Surname2?',
            'password' => 'Noname98?',
        ];
        $result = $this->CkTestUsers->patchEntity($newUser, $data);
        $this->assertEmpty($result->getErrors());

        // don't apply this rule when the name is very short (default: up to 3 characters)
        $data = [
            'firstname' => 'a',
            'lastname' => 'Surname2?',
            'password' => 'abcXYZ123!?',
        ];
        $result = $this->CkTestUsers->patchEntity($newUser, $data);
        $this->assertEmpty($result->getErrors());

        $data = [
            'firstname' => 'Firstname2?',
            'lastname' => 'a',
            'password' => 'abcXYZ123!?',
        ];
        $result = $this->CkTestUsers->patchEntity($newUser, $data);
        $this->assertEmpty($result->getErrors());

        // test the limit of this config
        $data = [
            'firstname' => 'abc',
            'lastname' => 'Surname2?',
            'password' => 'abcXYZ123!?',
        ];
        $result = $this->CkTestUsers->patchEntity($newUser, $data);
        $this->assertArrayHasKey('passwordNoUserName', $newUser->getError('password'));
        $newUser->clean();

        $data = [
            'firstname' => 'Firstname2?',
            'lastname' => 'abc',
            'password' => 'abcXYZ123!?',
        ];
        $result = $this->CkTestUsers->patchEntity($newUser, $data);
        $this->assertArrayHasKey('passwordNoUserName', $newUser->getError('password'));
        $newUser->clean();

        // move limit up
        $this->CkTestUsers->getBehavior('StrictPassword')->setConfig('minUserNameLength', 5);
        // now the two tests from just before should work
        $data = [
            'firstname' => 'abc',
            'lastname' => 'Surname2?',
            'password' => 'abcXYZ123!?',
        ];
        $result = $this->CkTestUsers->patchEntity($newUser, $data);
        $this->assertEmpty($result->getErrors());

        $data = [
            'firstname' => 'Firstname2?',
            'lastname' => 'abc',
            'password' => 'abcXYZ123!?',
        ];
        $result = $this->CkTestUsers->patchEntity($newUser, $data);
        $this->assertEmpty($result->getErrors());

        // move limit down
        $this->CkTestUsers->getBehavior('StrictPassword')->setConfig('minUserNameLength', 0);
        // now the first two examples for this validation should fail
        $data = [
            'firstname' => 'a',
            'lastname' => 'Surname2?',
            'password' => 'abcXYZ123!?',
        ];
        $result = $this->CkTestUsers->patchEntity($newUser, $data);
        $this->assertArrayHasKey('passwordNoUserName', $newUser->getError('password'));
        $newUser->clean();

        $data = [
            'firstname' => 'Firstname2?',
            'lastname' => 'a',
            'password' => 'abcXYZ123!?',
        ];
        $result = $this->CkTestUsers->patchEntity($newUser, $data);
        $this->assertArrayHasKey('passwordNoUserName', $newUser->getError('password'));
        $newUser->clean();
    }

    /**
     * Test check last passwords
     *
     * @return void
     */
    public function testCheckLastPasswords(): void
    {
        $hasher = new DefaultPasswordHasher();
        $this->CkTestUsers->addBehavior('CkTools.StrictPassword', [

            'minPasswordLength' => 8,
        ]);

        $newUser = $this->CkTestUsers->newEntity([
            'firstname' => 'Mike',
            'lastname' => 'Jagger',
            'password' => $hasher->hash('Password1?'),
            'last_passwords' => [$hasher->hash('Password2?')],
        ]);

        $result = $this->CkTestUsers->save($newUser);
        $this->assertEmpty($result->getErrors());

        $data = [
            'id' => $newUser->id,
            'password' => 'Password2?',
        ];

        $result = $this->CkTestUsers->patchEntity($newUser, $data);
        $this->assertArrayHasKey('passwordUsedBefore', $newUser->getError('password'));

        $data = [
            'id' => $newUser->id,
            'password' => 'Password3?',
        ];

        $result = $this->CkTestUsers->patchEntity($newUser, $data);
        $this->assertEmpty($result->getErrors());
    }

    public function testConfigurability(): void
    {
        $this->CkTestUsers->addBehavior('CkTools.StrictPassword', [
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

        $newUser = $this->CkTestUsers->newEntity([
            'qwert' => 'Mike',
            'vbnm' => 'Jagger',
            'password' => 'foo',
        ]);
        $this->assertEmpty($newUser->getErrors());

        $newUser = $this->CkTestUsers->newEntity([
            'qwert' => 'Mike',
            'vbnm' => 'Jagger',
            'password' => 'BAR',
        ]);
        $this->assertEmpty($newUser->getErrors());

        $newUser = $this->CkTestUsers->newEntity([
            'qwert' => 'Mike',
            'vbnm' => 'Jagger',
            'password' => '123',
        ]);
        $this->assertEmpty($newUser->getErrors());

        $newUser = $this->CkTestUsers->newEntity([
            'qwert' => 'Mike',
            'vbnm' => 'Jagger',
            'password' => '?=&',
        ]);
        $this->assertEmpty($newUser->getErrors());
    }
}
