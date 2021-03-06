<?php

declare(strict_types=1);

/*
 * Webpack Encore Twig Integration
 *
 * @link      https://github.com/lcharette/webpack-encore-twig
 * @copyright Copyright (c) 2021 Louis Charette
 * @license   https://github.com/lcharette/webpack-encore-twig/blob/main/LICENSE (MIT License)
 */

namespace Lcharette\WebpackEncoreTwig;

use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;

/**
 * Alias for `Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy` class.
 *
 * Represent `manifest.json` file. Alias is used for better UX.
 */
final class JsonManifest extends JsonManifestVersionStrategy implements JsonManifestInterface
{
}
