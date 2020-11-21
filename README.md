[![Continuous Integration](https://github.com/rfool/less.php/workflows/PHP%20Test/badge.svg)](https://github.com/rfool/less.php/actions)

[Less.php](http://lessphp.typesettercms.com)
========

This is a fork of a PHP port of the official LESS processor <http://lesscss.org>.

* [About](#about)
* [Installation](#installation)
* [Basic Use](#basic-use)
* [Source Maps](#source-maps)
* [Command Line](#command-line)
* [Integration with other projects](#integration-with-other-projects)
* [Transitioning from Leafo/lessphp](#transitioning-from-leafolessphp)
* [Credits](#credits)



About
---
The code structure of less.php mirrors that of the official processor which helps us ensure compatibility and allows for easy maintenance.

Please note, there are a few unsupported LESS features:

- Evaluation of JavaScript expressions within back-ticks (for obvious reasons).
- Definition of custom functions.


Installation
---

You can install the library with Composer or manually.

#### Composer

1. [Install Composer](https://getcomposer.org/download/)
2. Run `composer require rfool/less.php`

#### Manually From Release

Step 1. [Download a release](https://github.com/rfool/less.php/releases) and upload the PHP files to your server.

Step 2. Include the library:

```php
require_once '[path to less.php]/lib/Less/Autoloader.php';
Less_Autoloader::register();
```

Basic Use
---

#### Parsing Strings

```php
$parser = new Less_Parser();
$parser->parse( '@color: #4D926F; #header { color: @color; } h2 { color: @color; }' );
$css = $parser->getCss();
```


#### Parsing LESS Files
The parseFile() function takes two arguments:

1. The absolute path of the .less file to be parsed
2. The url root to prepend to any relative image or @import urls in the .less file.

```php
$parser = new Less_Parser();
$parser->parseFile( '/var/www/mysite/bootstrap.less', 'http://example.com/mysite/' );
$css = $parser->getCss();
```


#### Handling Invalid LESS
An exception will be thrown if the compiler encounters invalid LESS.

```php
try{
	$parser = new Less_Parser();
	$parser->parseFile( '/var/www/mysite/bootstrap.less', 'http://example.com/mysite/' );
	$css = $parser->getCss();
}catch(Exception $e){
	$error_message = $e->getMessage();
}
```


#### Parsing Multiple Sources
less.php can parse multiple sources to generate a single CSS file.

```php
$parser = new Less_Parser();
$parser->parseFile( '/var/www/mysite/bootstrap.less', '/mysite/' );
$parser->parse( '@color: #4D926F; #header { color: @color; } h2 { color: @color; }' );
$css = $parser->getCss();
```

#### Getting Info About The Parsed Files
less.php can tell you which .less files were imported and parsed.

```php
$parser = new Less_Parser();
$parser->parseFile( '/var/www/mysite/bootstrap.less', '/mysite/' );
$css = $parser->getCss();
$imported_files = $parser->allParsedFiles();
```


#### Compressing Output
You can tell less.php to remove comments and whitespace to generate minimized CSS files.

```php
$options = array( 'compress'=>true );
$parser = new Less_Parser( $options );
$parser->parseFile( '/var/www/mysite/bootstrap.less', '/mysite/' );
$css = $parser->getCss();
```

#### Getting Variables
You can use the getVariables() method to get an all variables defined and
their value in a php associative array. Note that LESS has to be previously
compiled.
```php
$parser = new Less_Parser;
$parser->parseFile( '/var/www/mysite/bootstrap.less');
$css = $parser->getCss();
$variables = $parser->getVariables();

```



#### Setting Variables
You can use the ModifyVars() method to customize your CSS if you have variables stored in PHP associative arrays.

```php
$parser = new Less_Parser();
$parser->parseFile( '/var/www/mysite/bootstrap.less', '/mysite/' );
$parser->ModifyVars( array('font-size-base'=>'16px') );
$css = $parser->getCss();
```


#### Import Directories
By default, less.php will look for @imports in the directory of the file passed to parseFile().
If you're using parse() or if @imports reside in different directories, you can tell less.php where to look.

```php
$directories = array( '/var/www/mysite/bootstrap/' => '/mysite/bootstrap/' );
$parser = new Less_Parser();
$parser->SetImportDirs( $directories );
$parser->parseFile( '/var/www/mysite/theme.less', '/mysite/' );
$css = $parser->getCss();
```


Source Maps
---
Less.php supports v3 sourcemaps

#### Inline
The sourcemap will be appended to the generated CSS file.

```php
$options = array( 'sourceMap' => true );
$parser = new Less_Parser($options);
$parser->parseFile( '/var/www/mysite/bootstrap.less', '/mysite/' );
$css = $parser->getCss();
```

#### Saving to Map File

```php
$options = array(
	'sourceMap'			=> true,
	'sourceMapWriteTo'	=> '/var/www/mysite/writable_folder/filename.map',
	'sourceMapURL'		=> '/mysite/writable_folder/filename.map',
	);
$parser = new Less_Parser($options);
$parser->parseFile( '/var/www/mysite/bootstrap.less', '/mysite/' );
$css = $parser->getCss();
```


Command line
---
An additional script has been included to use the compiler from the command line.
In the simplest invocation, you specify an input file and the compiled CSS is written to standard out:

```
$ lessc input.less > output.css
```

By using the -w flag you can watch a specified input file and have it compile as needed to the output file:

```
$ lessc -w input.less output.css
```

Errors from watch mode are written to standard out.

For more help, run `lessc --help`


Integration with other projects
---

#### Drupal 7

This library can be used as drop-in replacement of lessphp to work with [Drupal 7 less module](https://drupal.org/project/less).

How to install:

1. [Download the less.php source code](https://github.com/rfool/less.php/archive/master.zip) and unzip it so that 'lessc.inc.php' is located at 'sites/all/libraries/lessphp/lessc.inc.php'.
2. Download and install [Drupal 7 less module](https://drupal.org/project/less) as usual.
3. That's it :)

#### JBST WordPress theme

JBST has a built-in LESS compiler based on lessphp. Customize your WordPress theme with LESS.

How to use / install:

1. [Download the latest release](https://github.com/bassjobsen/jamedo-bootstrap-start-theme) copy the files to your {wordpress/}wp-content/themes folder and activate it.
2. Find the compiler under Appearance > LESS Compiler in your WordPress dashboard
3. Enter your LESS code in the text area and press (re)compile

Use the built-in compiler to:
- set any [Bootstrap](http://getbootstrap.com/customize/) variable or use Bootstrap's mixins:
	-`@navbar-default-color: blue;`
        - create a custom button: `.btn-custom {
  .button-variant(white; red; blue);
}`
- set any built-in LESS variable: for example `@footer_bg_color: black;` sets the background color of the footer to black
- use built-in mixins: - add a custom font: `.include-custom-font(@family: arial,@font-path, @path: @custom-font-dir, @weight: normal, @style: normal);`

The compiler can also be downloaded as [plugin](http://wordpress.org/plugins/wp-less-to-css/)

#### WordPress

This simple plugin will simply make the library available to other plugins and themes and can be used as a dependency using the [TGM Library](http://tgmpluginactivation.com/)

How to install:

1. Install the plugin from your WordPress Dashboard: http://wordpress.org/plugins/lessphp/
2. That's it :)


Transitioning from Leafo/lessphp
---
Projects looking for an easy transition from leafo/lessphp can use the lessc.inc.php adapter. To use, [Download the less.php source code](https://github.com/rfool/less.php/archive/master.zip) and unzip the files into your project so that the new 'lessc.inc.php' replaces the existing 'lessc.inc.php'.

Note, the 'setPreserveComments' will no longer have any effect on the compiled LESS.

Credits
---
less.php was originally ported to PHP by [Matt Agar](https://github.com/agar) and then updated by [Martin Jantošovič](https://github.com/Mordred). This fork was split off from [Wikimedias's version](https://github.com/wikimedia/less.php).

