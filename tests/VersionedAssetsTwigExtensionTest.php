<?php

/*
 * Webpack Encore Twig Integration
 *
 * @link      https://github.com/lcharette/webpack-encore-twig
 * @copyright Copyright (c) 2021 Louis Charette
 * @license   https://github.com/lcharette/webpack-encore-twig/blob/main/LICENSE (MIT License)
 */

namespace Lcharette\WebpackEncoreTwig\Tests;

use Lcharette\WebpackEncoreTwig\VersionedAssetsTwigExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;
use Twig\Environment;
use Twig\Extension\ExtensionInterface;
use Twig\Loader\FilesystemLoader;

/**
 * Tests for VersionedAssetsTwigExtension.
 */
class VersionedAssetsTwigExtensionTest extends TestCase
{
    protected JsonManifestVersionStrategy $manifest;
    protected ExtensionInterface $extension;
    protected Environment $twig;

    protected function setUp(): void
    {
        parent::setUp();

        $this->manifest = new JsonManifestVersionStrategy(__DIR__ . '/manifest.json');
        $this->extension = new VersionedAssetsTwigExtension($this->manifest);

        // Create dumb Twig and test adding extension
        $loader = new FilesystemLoader();
        $this->twig = new Environment($loader);
        $this->twig->addExtension($this->extension);
    }

    /**
     * @dataProvider pathDataProvider
     */
    public function testFunction(string $asset, string $versioned): void
    {
        $this->assertSame($versioned, $this->manifest->applyVersion($asset));
        $result = $this->twig->createTemplate("{{ asset('" . $asset . "') }}")->render();
        $this->assertSame($versioned, $result);
    }

    /**
     * @return string[][]
     */
    public function pathDataProvider(): array
    {
        return [
            ['assets/images/cupcake.png', '/assets/images/cupcake.6714f07e.png'],
            ['assets/admin.css', '/assets/admin.css'],
            ['assets/admin.js', 'assets/admin.js'], // Not in manifest.json. Will be returned as is.
        ];
    }
}
