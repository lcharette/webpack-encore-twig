# Webpack Encore Twig Integration

[![Version](https://img.shields.io/github/v/release/lcharette/webpack-encore-twig?sort=semver)](https://github.com/lcharette/webpack-encore-twig/releases)
![PHP Version](https://img.shields.io/badge/php-%5E8.0-brightgreen)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)
[![Build](https://img.shields.io/github/actions/workflow/status/lcharette/webpack-encore-twig/Build.yml?logo=github)](https://github.com/lcharette/webpack-encore-twig/actions)
[![Codecov](https://codecov.io/gh/lcharette/webpack-encore-twig/branch/main/graph/badge.svg)](https://app.codecov.io/gh/lcharette/webpack-encore-twig/branch/main)
[![StyleCI](https://github.styleci.io/repos/444619108/shield?branch=main&style=flat)](https://github.styleci.io/repos/444619108)
[![PHPStan](https://img.shields.io/github/actions/workflow/status/lcharette/webpack-encore-twig/PHPStan.yml?label=PHPStan)](https://github.com/lcharette/webpack-encore-twig/actions/workflows/PHPStan.yml)
[![Donate](https://img.shields.io/badge/Donate-Buy%20Me%20a%20Coffee-blue.svg)](https://ko-fi.com/lcharette)

Webpack Encore Standalone Twig Functions. Allows usage of Webpack Encore in Twig Templates without Symfony. Optimized for PHP-DI style containers.

This allows you to use the `splitEntryChunks()` and `enableVersioning()` features from Webpack Encore by reading the `entrypoints.json` and `manifest.json` files.

## Installation
```
composer require lcharette/webpack-encore-twig
```

## Documentation & Usage

Whenever you run [Encore](https://symfony.com/doc/current/frontend.html), two configuration files are generated in your output folder (default location: `public/build/`): `entrypoints.json` and `manifest.json`. Each file is similar, and contains a map to the final, versioned filenames.

The first file – `entrypoints.json` – is generated when the [`splitEntryChunks()`](https://symfony.com/doc/current/frontend/encore/split-chunks.html) option is defined in your `webpack.config.js` and will be read by the `encore_entry_script_tags()` and `encore_entry_link_tags()` Twig helpers this package provides. If you're using these, then your CSS and JavaScript files will render with the new, versioned filename.

The second file - `manifest.json` - is generated when [Asset Versioning](https://symfony.com/doc/current/frontend/encore/versioning.html#load-manifest-files) option (`enableVersioning()`) is defined in your `webpack.config.js` and will be read to get the versioned filename of _other_ files, like font files or image files (though it also contains information about the CSS and JavaScript files).

Both features (`splitEntryChunks()` and `enableVersioning()`) are defined as two separate Twig Extensions (`EntrypointsTwigExtension` and `VersionedAssetsTwigExtension` respectively) in case you need/want to enable only one of the two.

### `splitEntryChunks` and `entrypoints.json`

Encore writes an `entrypoints.json` file that contains all of the files needed for each "entry". To reference entries in Twig, you need to add the `EntrypointsTwigExtension` extension to the Twig Environment. This accept `EntrypointLookup`, which itself accept the path to the `entrypoints.json`, and the `TagRenderer` (which itself accept `EntrypointLookup`).

```php
use Lcharette\WebpackEncoreTwig\EntrypointsTwigExtension;
use Lcharette\WebpackEncoreTwig\TagRenderer;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookup;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$entryPoints = new EntrypointLookup('./path/to/entrypoints.json');
$tagRenderer = new TagRenderer($entryPoints);
$extension = new EntrypointsTwigExtension($entryPoints, $tagRenderer);

// Create Twig Environment and add extension
$loader = new FilesystemLoader('./path/to/templates');
$twig = new Environment($loader);
$twig->addExtension($extension);
```

Now, to render all of the `script` and `link` tags for a specific "entry" (e.g. `entry1`), you can:

```twig
{# any template or base layout where you need to include a JavaScript entry #}

{% block javascripts %}
    {{ parent() }}

    {{ encore_entry_script_tags('entry1') }}

    {# or render a custom attribute #}
    {#
    {{ encore_entry_script_tags('entry1', attributes={
        defer: true
    }) }}
    #}
{% endblock %}

{% block stylesheets %}
    {{ parent() }}

    {{ encore_entry_link_tags('entry1') }}
{% endblock %}
```

If you want more control, you can use the `encore_entry_js_files()` and `encore_entry_css_files()` methods to get the list of files needed, then loop and create the `script` and `link` tags manually.

#### Custom Attributes on script and link Tags

Custom attributes can be added to rendered `script` or `link` in 3 different ways:

1. Via global config, using the `defaultAttributes` argument on the TagRenderer constructor, or using the setter method: 
   ```
   $tagRenderer->setDefaultAttributes(['crossorigin' => 'anonymous']);
   ``` 

1. Via specific _script_ or _link_ argument on the TagRenderer constructor, or using the setter method:
   ```
   $tagRenderer->setDefaultScriptAttributes(['defer' => null]);
   $tagRenderer->setDefaultLinkAttributes(['hreflang' => 'en']);
   ``` 

1. When rendering in Twig - see the `attributes` option in the docs above.


### `enableVersioning` and `manifest.json`

To read the manifest file to be able to link (e.g. via an `img` tag) to certain assets, you need to add the `VersionedAssetsTwigExtension` extension to the Twig Environment. This accept `JsonManifest`, which itself accept the path to the `manifest.json`.

```php 
use Lcharette\WebpackEncoreTwig\JsonManifest;
use Lcharette\WebpackEncoreTwig\VersionedAssetsTwigExtension;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$manifest = new JsonManifest('./path/to/manifest.json');
$extension = new VersionedAssetsTwigExtension($manifest);

// Create dumb Twig and test adding extension
$loader = new FilesystemLoader();
$twig = new Environment($loader);
$twig->addExtension($extension);
```

In your Twig template, just wrap each path in the Twig `asset()` function like normal:

```twig
<img src="{{ asset('build/images/logo.png') }}" alt="ACME logo">
```

### Dependency Injection & Autowiring

When using a PSR Dependency Injection Container with autowiring, like [PHP-DI](https://php-di.org), you can define `EntrypointLookup` and `JsonManifest` in your [definition factory](https://php-di.org/doc/php-definitions.html#factories) via their respective interface (`EntrypointLookupInterface` and `JsonManifestInterface`). For example : 

```php
use Lcharette\WebpackEncoreTwig\JsonManifest;
use Lcharette\WebpackEncoreTwig\JsonManifestInterface;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookup;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupInterface;

// ...

return [
    EntrypointLookupInterface::class => function () {
        return new EntrypointLookup('./path/to/entrypoints.json');
    },
    JsonManifestInterface::class => function () {
        return new JsonManifest('./path/to/manifest.json');
    },
];
```

`EntrypointsTwigExtension` and `VersionedAssetsTwigExtension` can then be injected into your other classes via the container :

```php
use Lcharette\WebpackEncoreTwig\EntrypointsTwigExtension;
use Lcharette\WebpackEncoreTwig\VersionedAssetsTwigExtension;

// ...

$extension = $container->get(EntrypointsTwigExtension::class);
$twig->addExtension($extension);

$extension = $container->get(VersionedAssetsTwigExtension::class);
$twig->addExtension($extension);
```

### Using Without Twig

Both script and link tags can be generated in vanilla PHP and without Twig using the underlying public methods on the `TagRenderer` class:

**Getting script and link tag from `entrypoints.json`:**
```php
$entryPoints = new EntrypointLookup('./path/to/entrypoints.json');
$tagRenderer = new TagRenderer($entryPoints);

// Returns the tags as string
$scriptsString = $tagRenderer->renderWebpackScriptTags('entry1');
$linksString = $tagRenderer->renderWebpackLinkTags('entry1');

// Returns the list of files as an array of strings
$jsFiles = $tagRenderer->getJavaScriptFiles('entry1');
$cssFiles = $tagRenderer->getCssFiles('entry1');
```

Same goes for the versioned assets, using the underlying public methods on the `JsonManifest` class:

**Getting versioned from `manifest.json`:**
```php 
$manifest = new JsonManifest('./path/to/manifest.json');
$path = $manifest->applyVersion('build/images/logo.png');
```

## See Also
- [Changelog](CHANGELOG.md)
- [License](LICENSE)
- [Style Guide](.github/STYLE-GUIDE.md)
- [Testing](.github/RUNNING_TESTS.md)

## References
- https://symfony.com/doc/current/frontend.html
- https://github.com/symfony/webpack-encore-bundle
