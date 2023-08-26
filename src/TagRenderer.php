<?php

/*
 * Webpack Encore Twig Integration
 *
 * @link      https://github.com/lcharette/webpack-encore-twig
 * @copyright Copyright (c) 2021 Louis Charette
 * @license   https://github.com/lcharette/webpack-encore-twig/blob/main/LICENSE (MIT License)
 */

namespace Lcharette\WebpackEncoreTwig;

use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupInterface;
use Symfony\WebpackEncoreBundle\Asset\IntegrityDataProviderInterface;

/**
 * Render <script> and <link> HTML tags from Webpack Encore `entrypoints.json`.
 *
 * @see https://symfony.com/doc/current/frontend.html
 * @see https://github.com/symfony/webpack-encore-bundle
 * @see https://github.com/symfony/webpack-encore-bundle/blob/509cad50878e838c879743225e0e921b3b64a3f2/src/Asset/TagRenderer.php
 */
final class TagRenderer
{
    /**
     * @var string[][]
     */
    private array $renderedFiles = [];

    /**
     * @param EntrypointLookupInterface $entryPoints
     * @param mixed[]                   $defaultAttributes
     * @param mixed[]                   $defaultScriptAttributes
     * @param mixed[]                   $defaultLinkAttributes
     */
    public function __construct(
        private EntrypointLookupInterface $entryPoints,
        private array $defaultAttributes = [],
        private array $defaultScriptAttributes = [],
        private array $defaultLinkAttributes = [],
    ) {
    }

    /**
     * Render <script> tag from Webpack Encore `entrypoints.json` js entry.
     *
     * @param string  $entryName       Entry to render
     * @param mixed[] $extraAttributes Extra attributes to add to the tag
     *
     * @return string
     */
    public function renderWebpackScriptTags(string $entryName, array $extraAttributes = []): string
    {
        $scriptTags = [];
        $integrityHashes = ($this->entryPoints instanceof IntegrityDataProviderInterface) ? $this->entryPoints->getIntegrityData() : [];

        foreach ($this->entryPoints->getJavaScriptFiles($entryName) as $filename) {
            $attributes = [];
            $attributes['src'] = $filename;
            $attributes = array_merge($attributes, $this->getDefaultAttributes(), $this->defaultScriptAttributes, $extraAttributes);

            if (isset($integrityHashes[$filename])) {
                $attributes['integrity'] = $integrityHashes[$filename];
            }

            $scriptTags[] = sprintf(
                '<script %s></script>',
                $this->convertArrayToAttributes($attributes)
            );

            $this->renderedFiles['scripts'][] = $attributes['src'];
        }

        return implode('', $scriptTags);
    }

    /**
     * Render <link> tag from Webpack Encore `entrypoints.json` css entry.
     *
     * @param string  $entryName       Entry to render
     * @param mixed[] $extraAttributes Extra attributes to add to the tag
     *
     * @return string
     */
    public function renderWebpackLinkTags(string $entryName, array $extraAttributes = []): string
    {
        $scriptTags = [];
        $integrityHashes = ($this->entryPoints instanceof IntegrityDataProviderInterface) ? $this->entryPoints->getIntegrityData() : [];

        foreach ($this->entryPoints->getCssFiles($entryName) as $filename) {
            $attributes = [];
            $attributes['rel'] = 'stylesheet';
            $attributes['href'] = $filename;
            $attributes = array_merge($attributes, $this->getDefaultAttributes(), $this->defaultLinkAttributes, $extraAttributes);

            if (isset($integrityHashes[$filename])) {
                $attributes['integrity'] = $integrityHashes[$filename];
            }

            $scriptTags[] = sprintf(
                '<link %s>',
                $this->convertArrayToAttributes($attributes)
            );

            $this->renderedFiles['styles'][] = $attributes['href'];
        }

        return implode('', $scriptTags);
    }

    /**
     * @return mixed[]
     */
    public function getRenderedScripts(): array
    {
        return $this->renderedFiles['scripts'];
    }

    /**
     * @return mixed[]
     */
    public function getRenderedStyles(): array
    {
        return $this->renderedFiles['styles'];
    }

    /**
     * @return mixed[]
     */
    public function getDefaultAttributes(): array
    {
        return $this->defaultAttributes;
    }

    /**
     * @param mixed[] $defaultScriptAttributes
     */
    public function setDefaultScriptAttributes(array $defaultScriptAttributes): static
    {
        $this->defaultScriptAttributes = $defaultScriptAttributes;

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getDefaultScriptAttributes(): array
    {
        return $this->defaultScriptAttributes;
    }

    /**
     * @param mixed[] $defaultLinkAttributes
     */
    public function setDefaultLinkAttributes(array $defaultLinkAttributes): static
    {
        $this->defaultLinkAttributes = $defaultLinkAttributes;

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getDefaultLinkAttributes(): array
    {
        return $this->defaultLinkAttributes;
    }

    /**
     * @param mixed[] $defaultAttributes
     */
    public function setDefaultAttributes(array $defaultAttributes): static
    {
        $this->defaultAttributes = $defaultAttributes;

        return $this;
    }

    /**
     * Reset list of rendered files.
     */
    public function reset(): void
    {
        $this->renderedFiles = [
            'scripts' => [],
            'styles'  => [],
        ];
    }

    /**
     * Flatten array of tag attributes to a single string.
     *
     * @param mixed[] $attributesMap
     *
     * @return string
     */
    private function convertArrayToAttributes(array $attributesMap): string
    {
        // Remove attributes set specifically to false
        $attributesMap = array_filter($attributesMap, static function (mixed $value) {
            return $value !== false;
        });

        return implode(' ', array_map(
            static function (string $key, mixed $value) {
                // allows for things like defer: true to only render "defer"
                if ($value === true || $value === null || !is_scalar($value)) {
                    return $key;
                }

                return sprintf('%s="%s"', $key, htmlentities((string) $value));
            },
            array_keys($attributesMap),
            $attributesMap
        ));
    }
}
