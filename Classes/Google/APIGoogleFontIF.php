<?php

declare(strict_types=1);

namespace WEBFONTS\Webfonts\Google;

interface APIGoogleFontIF extends \JsonSerializable
{
    function id(): string;

    function family(): string;

    function provider(): string;

    /**
     * @return string[]
     */
    function variants(): array;

    /**
     * @return string[]
     */
    function subsets(): array;

    function category(): string;

    function version(): string;

    function defSubset(): string;

    function defVariant(): string;

    function hasItalic(): bool;

    function hasNormal(): bool;

    function cdn(): string;


}
