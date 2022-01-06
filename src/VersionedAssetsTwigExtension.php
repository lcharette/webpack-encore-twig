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

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Adds Webpack Encore related function to Twig.
 *
 * Added functions :
 * - asset(string $uri) : Convert asset URI to it's versioned counterpart in `manifest.json`
 *
 * @see https://symfony.com/doc/current/frontend/encore/versioning.html#load-manifest-files
 */
final class VersionedAssetsTwigExtension extends AbstractExtension
{
    public function __construct(
        private JsonManifestInterface $manifest,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('asset', [$this->manifest, 'applyVersion']),
        ];
    }
}
