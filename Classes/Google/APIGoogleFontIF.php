<?php

declare(strict_types=1);

namespace WEBFONTS\Webfonts\Google;

use JsonSerializable;

interface APIGoogleFontIF extends JsonSerializable
{
    public function getId(): string;

    public function getFamily(): string;

    public function getProvider(): string;

    /**
     * @return string[]
     */
    public function getVariants(): array;

    /**
     * @return string[]
     */
    public function getSubsets(): array;

    public function getCategory(): string;

    public function getVersion(): string;

    public function getDefSubset(): string;

    public function getDefVariant(): string;

    public function getHasItalic(): bool;

    public function getHasNormal(): bool;

    public function getCdn(): string;


}
