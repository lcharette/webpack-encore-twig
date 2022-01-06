<?php

/*
 * Webpack Encore Twig Integration
 *
 * @link      https://github.com/lcharette/webpack-encore-twig
 * @copyright Copyright (c) 2021 Louis Charette
 * @license   https://github.com/lcharette/webpack-encore-twig/blob/main/LICENSE (MIT License)
 */

namespace Lcharette\WebpackEncoreTwig\Tests;

use Lcharette\WebpackEncoreTwig\EntrypointsTwigExtension;
use Lcharette\WebpackEncoreTwig\TagRenderer;
use PHPUnit\Framework\TestCase;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookup;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupInterface;
use Twig\Environment;
use Twig\Extension\ExtensionInterface;
use Twig\Loader\FilesystemLoader;

/**
 * Tests for WebpackEncoreTwigExtension.
 */
class EntrypointsTwigExtensionTest extends TestCase
{
    protected EntrypointLookupInterface $entryPoints;
    protected TagRenderer $tagRenderer;
    protected ExtensionInterface $extension;
    protected Environment $twig;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entryPoints = new EntrypointLookup(__DIR__ . '/entrypoints.json');
        $this->tagRenderer = new TagRenderer($this->entryPoints);
        $this->extension = new EntrypointsTwigExtension($this->entryPoints, $this->tagRenderer);

        // Create dumb Twig and test adding extension
        $loader = new FilesystemLoader();
        $this->twig = new Environment($loader);
        $this->twig->addExtension($this->extension);
    }

    public function testEncoreEntryJsFiles(): void
    {
        $expected = [
            '/assets/runtime.js',
            '/assets/vendors-node_modules_bootstrap-6feb83.js',
            '/assets/admin.js'
        ];

        $result = $this->twig->createTemplate("{{ encore_entry_js_files('admin')|join(', ') }}")->render();
        $this->assertSame(implode(', ', $expected), $result);
    }

    public function testEncoreEntryCssFiles(): void
    {
        $expected = [
            '/assets/vendors-node_modules_bootstrap-6feb83.css',
            '/assets/admin.css'
        ];

        $result = $this->twig->createTemplate("{{ encore_entry_css_files('admin')|join(', ') }}")->render();
        $this->assertSame(implode(', ', $expected), $result);
    }

    public function testEncoreEntryScriptTags(): void
    {
        $expected = [
            '<script src="/assets/runtime.js"></script>',
            '<script src="/assets/vendors-node_modules_bootstrap-6feb83.js"></script>',
            '<script src="/assets/admin.js"></script>'
        ];

        $result = $this->twig->createTemplate("{{ encore_entry_script_tags('admin') }}")->render();
        $this->assertSame(implode('', $expected), $result);
    }

    public function testEncoreEntryLinkTags(): void
    {
        $expected = [
            '<link rel="stylesheet" href="/assets/vendors-node_modules_bootstrap-6feb83.css">',
            '<link rel="stylesheet" href="/assets/admin.css">'
        ];

        $result = $this->twig->createTemplate("{{ encore_entry_link_tags('admin') }}")->render();
        $this->assertSame(implode('', $expected), $result);
    }

    public function testMissingFile(): void
    {
        $entryPoints = new EntrypointLookup(__DIR__ . '/entrypointsNotExist.json');
        $tagRenderer = new TagRenderer($entryPoints);
        $extension = new EntrypointsTwigExtension($entryPoints, $tagRenderer);

        // Create dumb Twig and test adding extension
        $loader = new FilesystemLoader();
        $twig = new Environment($loader);
        $twig->addExtension($extension);

        $this->expectException(\Twig\Error\RuntimeError::class);
        $twig->createTemplate("{{ encore_entry_link_tags('admin') }}")->render();
    }
}
