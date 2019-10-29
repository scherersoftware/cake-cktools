# CakePHP 3 cake-cktools

[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.txt)
[![Build Status](https://travis-ci.org/scherersoftware/cake-cktools.svg?branch=master)](https://travis-ci.org/scherersoftware/cake-cktools)

## Requirements

- [cake-frontend-bridge](https://github.com/scherersoftware/cake-frontend-bridge) for easy js-based interaction with the backend
- [cake-api-baselayer](https://github.com/scherersoftware/cake-api-baselayer) for REST apis
- [Font Awesome](https://fortawesome.github.io/Font-Awesome/) for icons

## Installation

#### 1. require the plugin

    $ composer require codekanzlei/cake-cktools

#### 3. Setup Appcontroller.php

**`$helpers`**

```
public $helpers =  [
	'CkTools.CkTools'
]
```

## Usage

### 1. CkToolsHelper

Being one of CkTools' core features, CkToolsHelper provides many useful functionalities, most of them aimed at generating powerful view elements such as form- and navigation buttons, often with as much as one line of code. Here's an overview of its actions. See `CkToolsHelper.php` for further details.

- `countries(array $countryCodes = null)`
Returns a map of countries with their translations. Use an array `$countryCodes` to limit the returned list according to your needs. See `/cake-cktools/config/countries.php` for a full list of country names.
@return array

- `country($country)`
Returns the translated version of the given country via `$country`, e.g. 'en'
@return string

- `function datepickerInput($field, array $options = [])`
Creates a neat datepicker input field which is very handy in  edit or add views.
@return string

- `mailtoLink($email, array $options = [])`
Creates a mailto link to the email address given in `$email`. `$options` include `'body'`, `'subject'` and `'caption'`.
@return string

- `editButton(EntityInterface $entity, array $options = [])`
Renders an edit button for given database record `$entity`. By default, this button will link to the entity's Controller's edit action but this can be overwritten using the `'url'` field of the `$options` array.
@return string

- `viewButton(EntityInterface $entity, array $options = [])`
Renders a details button for given database record `$entity`. By default, this button will link to the entity's Controller's view action but this can be overwritten using the `'url'` field of the `$options` array.
@return string

- `addButton($title = null, array $options = [])`
Renders an add button for the current Model. For example, use this in a `Users` index view file to render a button that will create a new record of type `User`
@return string

- `deleteButton(EntityInterface $entity, array $options = [])`
Renders an delete button for given database record `$entity`. You can define a custom confirm message in the `$options` array's `'confirm'` field.
@return string

- `formButtons(array $options = [])`
Renders form buttons 'Save' and 'Cancel'. `$options` include `'cancelButton'` which controls wether the cancel button will be rendered or not and `'useReferer'` which controls wether or not the referring address for the request will be returned.
@return void

- `button($title, $url = false, array $options = [])`
Renders a button. Pass an url object in the `'$url'` param to link to any Controller action you desire. `$options` include `'icon'` (use Font Awesome), `'class'`, and `'additionalClasses'`. Use Twitter Bootstrap for the latter two options.
@return string

- `definitionList($data, array $options = [])`
Renders a HTML definition list element. Param `$data` holds Keys and values in array strucutre. Use the following line in a view file to get a glimpse of the results of this action.

	```
	<?= $this->CkTools->definitionList($this->CkTools->countries()) ?>
	```
@return string

- `nestedList($data, $content, $level = 0, $isActiveCallback = null)`
Renders a nested list. The param `$data` holds the data to be displayed in associative array structure, `$content` is a String template for each node. See `CkToolsHelper.php` for an example call.
@return string

- `historyBackButton(array $options = [])`
Renders a div with an onclick handler which uses the history API of the Browser.
@return string

- `displayStructuredData(array $data, array $options = [])`
Display an array in human-readable format. The `'$options'` array includes a field `'expanded'` which, if set true renders a button that toggles display of the list.
@return string

- `arrayToUnorderedList(array $array)`
Rercursively converts an array to an unordered list.
@return string

### 2. MenuHelper

The MenuHelper aims at config-based rendering of hierarchical navigation menus in the UI. Here's an overview of its actions. See `MenuHelper.php` for further details.

- `renderSidebarMenuItems($config)`
Creates HTML markup for a menu-bar based on a given menu configuration. The config can be passed as a string or you can create a file `/config/menu.php` that holds the structure of the menu. Use this config file to define a 2-level menu structure consisting of menu items and sub-items for each of them. See `menu.php.default` in the CkTools folder for reference.
Use an additional field called `children` in any of the menu fields if you want to use sub-items.
If you chose to use a menu config file, use the following call in your default view file: `<?php echo $this->Menu->renderSidebarMenuItems('menu'); ?>`
@return string

- `prepareMenuConfig($config)`
Processes the given menu config, structures it and checks for permissions. Checks menu items as well as their children for URLs that are not allowed. Ignores menu items which have a field `'shouldrender'` with value FALSE.
@return array

- hasAllowedChildren(array $children)
Uses CakePHP's AuthComponent to check if given array of items 'children' contains at least one URL that is allowed to the current user.
@return bool

- isItemActive($item)
Compares the current URL with given menu item to determine wether it should be highlighted or not.
@return bool

#### `config.php` - a closer look
Here's an example of what `config.php` could look like, explaining more possibilities:

```
$config = [
    'menu' => [
        'dashboard' => [            // first menu item
            'title' => 'Dashboard', // title to be displayed in the menu bar
            'icon' => 'dashboard',  // Font Awesome icon
            'url' => [              // URL object
                'plugin' => false,
                'controller' => 'Dashboard',
                'action' => 'index'
            ],
         ],
        'examples' => [             // second menu item
            'title' => 'Examples',
            'hideForRoles' => [ 	// disable rendering for certain groups
                User::ROLE_USER 	// of users
            ],
            'children' => [         // field 'children'. Note that this item
                [                   // does not have an URL object.
                    'title' => 'foo',  // first child item (menu sub-item)
                    'url' => [
                        'plugin' => false,
                        'controller' => 'Examples',
                        'action' => 'actionFoo'
                    ]
                ],
                [
                    'title' => 'bar',  // second child item (menu sub-item)
                    'url' => [
                        'plugin' => false,
                        'controller' => 'Examples',
                        'action' => 'actionBar'
                    ]
                ],
                // further sub-items for menu item 'examples'
            ]
        ],
        // further menu items
```

## Create a PDF using CakePHP Views

CkTools includes a simple wrapper for the [MPDF library](http://www.mpdf1.com/mpdf/index.php). For detailed instructions on how to use it, see the github repository here: [https://github.com/mpdf/mpdf](https://github.com/mpdf/mpdf)

#### Usage
Here's an example use in a Cake Controller but you can use this feature wherever you want. See `/cake-cktools/src/Lib/PdfGenerator.php` for a closer look at the functionalities used.

```
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
```

In your Cake View file you can use the passed viewVars and the `$mpdf` variable for manipulating the PDF.

```
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
```

## Sort table fields with SortableBehavior
CkTools provides a behavior that allows manipulation of the (displayed) order of the records of a table. If you change the position of one field, the behavior will automatically change all the other fields' positions accordingly. To manipulate the positions you can use an ordinary form field or for example the Cake Frontend Bridge to enable JavaScript-based drag-and-drop interaction in the browser UI.

## Strict Passwords with StrictPasswordBehavior
Activate the strict password requirements by setting the StrictPasswordBehavior in UsersTable; default Configuration is set in this example

$this->addBehavior('CkTools.StrictPassword', [
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
]);

#### Usage
Add the SortableBehavior to the Table you want to use it in:

```
public function initialize(array $config)
{
	$this->addBehavior('CkTools.Sortable', [
	    'sortField' => 'sort',
	    'columnScope' => ['mediatype_id'],
	    'defaultOrder' => ['sort' => 'ASC']
	]);
}
```
The current 'position' of the record will be stored as an integer in the field `'sort'`. You need to have this field in your Model. If it's not called 'sort', you can configure the behavior accordingly here.

Here's what the table field you wish to allow sorting by could look like in CakePHP schema.

```
'sort' => ['type' => 'integer', 'length' => 6, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
```

## Manipulate database records with TableUtilitiesTrait
The TableUtilitiesTrait contains an action that allows for manually updating the value of a field in a table. Using `updateField()` bypasses cake validation and patching of the object. The given value is directly written to the database via a SQL query.

#### Setting up a Model
Add the Trait in **`YourTable.php`**:

```
<?php
namespace App\Model\Table;

use CkTools\Utility\TableUtilitiesTrait;

class MediaTable extends Table
{
    use TableUtilitiesTrait;
    ...
```

Now you can use the `updateField()` action to directly manipulate a record. Let's take a closer look at its parameters:

- `$primaryKey`: the primary key of the record you wish to manipulate
- `$field`: the name of the field you want to change
- `$value = null`: the new value you want to save to the database

## Handle type constants with TypeAwareTrait
Entities with type constants can be very useful, for example if you wish to grant different rights to different kinds of users such as Admins, regular Users, External users etc. With CkTools' TypeAwareTrait, setting up and using such constants becomes fast and simple.

#### Setting up a Model:
Let's prepare an example use case where you want to assign different roles to different groups of users in your application. Set up your **User.php** like so:

```
<?php
namespace App\Model\Entity;
use CkTools\Utility\TypeAwareTrait;

class User extends Entity
{
    use TypeAwareTrait;

	/**
	 * Define the type constants as needed as class constants.
	 */
	const ROLE_ADMIN = 'admin';
	const ROLE_USER = 'user';
	const ROLE_CUSTOMER = 'customer';
	/**
	 * (optional) Overwrite the actions in TypeAwareTrait according to your needs.
	 */

	public static function typeDescriptions()
	{
	    return [
	        self::ROLE_ADMIN => 'admin, has R+W access to all the things',
	        self::ROLE_USER => 'user, has W access to some things',
	        self::ROLE_CUSTOMER => 'external customer, has R-only access',
	    ];
	}

	public static function getRoles()
	{
	    return self::getTypeMap(
	        self::ROLE_ADMIN,
	        self::ROLE_USER,
	        self::ROLE_CUSTOMER
	    );
	}
```

Let's stick with the User roles example. Here's how you can access your type constants:

```
User::getRoles()
```

Using this Trait allows for simple control over which actions the currently logged in user can and cannot access. You can use your own methods for granting/denying access to Controller actions or simply use the TypeAwareTrait to prevent action buttons from being rendered in the UI.

Example: in a Cake View file for Model **Cars**:

```
<?php if(in_array($this->Auth->user('role'), [User::ROLE_ADMIN, User::ROLE_USER]): ?>
	<?= $car->price ?>
<?php else: ?>
	<span>Please sign up or create an account to see this</span
<?php endif; ?>
```

## Identify users with UserToken
CkTools provides a utility class that generates serialized, encrypted and base64-encoded for identifying users, usually for usage in an URL. This is very handy for 'forgot password' links so if you have a LoginController, using UserToken there is most advisable.

#### Usage
Here's how to use **UserToken** in any class you like:

```
use CkTools\Utility\UserToken;

// Use `'Configure'` to define a `'Security.cryptKey'` in your application.
// It must be at least 256 bits (32 bytes) long.
Configure::write('Security.cryptKey','<your_32_bytes_security_crypt_key>');

// Create a new instance of UserToken
$UserToken = new UserToken;

```

Now you can access the following actions via the `$UserToken` object:

- `getTokenForUser(User $user, $validForSeconds = null, array $additionalData = [])`
generates a base64-encoded token for given User Entity. Use the `$validForSeconds` parameter to determine how long the token is valid to use. Furthermore, you can (optionally) pass additional information in the `$additionalData` array.

- isTokenExpired($token)
based on given token's `$generated` and `$validForSeconds` attributes, this method validates wether the token is valid or not.

- decryptToken($token)
decrypts given token, allowing you to access all the data that was passed while it was created via `getTokenForUser()`.

- getUserIdFromToken($token)
decrypts given token and only returns the encrypted user_id.

See `cake-cktools/src/Utility/UserToken.php` for further information about these actions.

## InternationalizationShell

CkTools provides a helper shell for i18n related tasks.

### Update .po and .mo files from catalog

To automate the updating of .po/.mo files from available translation strings from the catalog (.pot) file, use the `updateFromCatalog` command.

This is done using the excellent [oscarotero/Gettext](https://github.com/oscarotero/Gettext) library.

#### Usage

1. Update all default.po and default.mo files found in `src/Locale/*` with translation strings from `src/Locale/default.pot`. The shell will list the files it will touch and asks for confirmation.

        bin/cake CkTools.Internationalization updateFromCatalog

2. Update all default.po and default.mo files found in `src/Locale/*` with translation strings from `src/Locale/default.pot`. The shell will overwrite files without interaction.

        bin/cake CkTools.Internationalization updateFromCatalog --overwrite

3. Update all default.po and default.mo files found in `src/Locale/*` with translation strings from `src/Locale/default.pot`. The shell will overwrite files without interaction. Also, it will strip all references, meaning the filenames and lines where the translation was used from the resulting .po and .mo files.

        bin/cake CkTools.Internationalization updateFromCatalog --overwrite --strip-references

4. Update all cake.po and cake.mo files found in `src/Locale/*` with translation strings from `src/Locale/default.pot`. The shell will list the files it will touch and asks for confirmation.

        bin/cake CkTools.Internationalization updateFromCatalog --domain cake

## PasswordHasher Shell

In case you need to renew a password in your local dev db, use this shell to genreate a hash.

        bin/cake CkTools.PasswordHasher <password-to-hash>

## SecurityComponent

CkTools provides a component to set multiple security enhancing headers.

### Security headers

Loading the `SecurityComponent` in your AppController, the Component will hook into the beforeFilter event and by default set the
- `X-XSS-Protection` header to `1; mode=block`
- `X-Content-Type-Options` header to `nosniff`

At the moment these two headers are neither configurable nor can you deactivate them, as they seem to be reasonable in every case.

Furthermore, you can configure multiple additional headers in your `app.php`.

#### Usage

Here is how a complete configuration of all additional security headers could look like in your `app.php`:

```
'CkTools' => [
    'Security' => [
        // Strict-Transport-Security header
        'HSTS' => 'max-age=31536000; includeSubDomains',

        // Content-Security-Policy, you could set a string like so
        'CSP' => "img-src 'self' blob: data:; default-src 'self' https:;"

        // or if it gets to long, as array
        'CSP' => [
            'font-src' => "'self' data:",
            'frame-ancestors' => "'self'",
            'connect-src' => "'self'",
            'img-src' => [
                "'self'",
                'blob:',
                'data:',
                'https://*.some-domain.com',
            ],
            'default-src' => [
                "'self'",
                'https:',
                'blob:'
            ]
        ],

        // bool config value to set X-Frame-Options header to "DENY" if true
        'denyFraming' => true
    ]
],
```

The X-Frame-Options header set by 'denyFraming' is superceded by the Content Security Policy's frame-ancestors directive, but as frame-ancestors is not yet supported in IE11 and older, Edge, Safari 9.1 (desktop), and Safari 9.2 (iOS), it is recommended that sites employ X-Frame-Options in addition to using CSP.

If you wish to exclude any header, simply set its value to something falsy or omit the array key completely.
