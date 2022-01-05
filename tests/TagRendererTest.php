<?php

/*
 * Webpack Encore Twig Integration
 *
 * @link      https://github.com/lcharette/webpack-encore-twig
 * @copyright Copyright (c) 2021 Louis Charette
 * @license   https://github.com/lcharette/webpack-encore-twig/blob/main/LICENSE (MIT License)
 */

namespace Lcharette\WebpackEncoreTwig\Tests;

use Lcharette\WebpackEncoreTwig\TagRenderer;
use PHPUnit\Framework\TestCase;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookup;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupInterface;

/**
 * Tests for TagRenderer.
 */
class TagRendererTest extends TestCase
{
    protected EntrypointLookupInterface $entryPoints;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entryPoints = new EntrypointLookup(__DIR__ . '/entrypoints.json');
    }

    public function testRenderWebpackScriptTags(): void
    {
        $expected = [
            '<script src="/assets/runtime.js"></script>',
            '<script src="/assets/vendors-node_modules_bootstrap-6feb83.js"></script>',
            '<script src="/assets/admin.js"></script>'
        ];

        $tagRenderer = new TagRenderer($this->entryPoints);
        $result = $tagRenderer->renderWebpackScriptTags('admin');
        $this->assertSame(implode('', $expected), $result);

        $this->assertSame([
            '/assets/runtime.js',
            '/assets/vendors-node_modules_bootstrap-6feb83.js',
            '/assets/admin.js'
        ], $tagRenderer->getRenderedScripts());
        $tagRenderer->reset();
        $this->assertSame([], $tagRenderer->getRenderedScripts());
    }

    public function testEncoreEntryLinkTags(): void
    {
        $expected = [
            '<link rel="stylesheet" href="/assets/vendors-node_modules_bootstrap-6feb83.css">',
            '<link rel="stylesheet" href="/assets/admin.css">'
        ];

        $tagRenderer = new TagRenderer($this->entryPoints);
        $result = $tagRenderer->renderWebpackLinkTags('admin');
        $this->assertSame(implode('', $expected), $result);

        $this->assertSame([
            '/assets/vendors-node_modules_bootstrap-6feb83.css',
            '/assets/admin.css'
        ], $tagRenderer->getRenderedStyles());
        $tagRenderer->reset();
        $this->assertSame([], $tagRenderer->getRenderedStyles());
    }

    public function testRenderWebpackScriptTagsWithIntegrity(): void
    {
        $expected = [
            '<script src="/assets/runtime.js" integrity="sha384-m8b9i"></script>',
            '<script src="/assets/vendors-node_modules_bootstrap-6feb83.js" integrity="sha384-InNED"></script>',
            '<script src="/assets/admin.js" integrity="sha384-raZek"></script>'
        ];

        $entryPoints = new EntrypointLookup(__DIR__ . '/entrypoints_integrity.json');
        $tagRenderer = new TagRenderer($entryPoints);
        $result = $tagRenderer->renderWebpackScriptTags('admin');
        $this->assertSame(implode('', $expected), $result);
    }

    public function testEncoreEntryLinkTagsWithIntegrity(): void
    {
        $expected = [
            '<link rel="stylesheet" href="/assets/vendors-node_modules_bootstrap-6feb83.css" integrity="sha384-HQdSI">',
            '<link rel="stylesheet" href="/assets/admin.css" integrity="sha384-rV+NX">'
        ];

        $entryPoints = new EntrypointLookup(__DIR__ . '/entrypoints_integrity.json');
        $tagRenderer = new TagRenderer($entryPoints);
        $result = $tagRenderer->renderWebpackLinkTags('admin');
        $this->assertSame(implode('', $expected), $result);
    }

    public function testEncoreEntryWithAttributes(): void
    {
        $expectedScript = [
            '<script src="/assets/runtime.js" crossorigin="anonymous" defer foo="123"></script>',
            '<script src="/assets/vendors-node_modules_bootstrap-6feb83.js" crossorigin="anonymous" defer foo="123"></script>',
            '<script src="/assets/admin.js" crossorigin="anonymous" defer foo="123"></script>'
        ];
        $expectedLink = [
            '<link rel="stylesheet" href="/assets/vendors-node_modules_bootstrap-6feb83.css" crossorigin="anonymous" hreflang="en" bar>',
            '<link rel="stylesheet" href="/assets/admin.css" crossorigin="anonymous" hreflang="en" bar>'
        ];

        $tagRenderer = new TagRenderer($this->entryPoints, ['foo' => 'bar']);

        // Add global, tag specific and request specific attributes
        $this->assertSame(['foo' => 'bar'], $tagRenderer->getDefaultAttributes());
        $tagRenderer->setDefaultAttributes(['crossorigin' => 'anonymous']);
        $this->assertSame(['crossorigin' => 'anonymous'], $tagRenderer->getDefaultAttributes());

        $tagRenderer->setDefaultScriptAttributes(['defer' => null]);
        $this->assertSame(['defer' => null], $tagRenderer->getDefaultScriptAttributes());

        $tagRenderer->setDefaultLinkAttributes(['hreflang' => 'en']);
        $this->assertSame(['hreflang' => 'en'], $tagRenderer->getDefaultLinkAttributes());

        // Render Script
        $result = $tagRenderer->renderWebpackScriptTags('admin', ['foo' => 123]);
        $this->assertSame(implode('', $expectedScript), $result);

        // Render link
        $result = $tagRenderer->renderWebpackLinkTags('admin', ['bar' => true]);
        $this->assertSame(implode('', $expectedLink), $result);
    }
}
