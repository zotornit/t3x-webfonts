<?php


namespace WEBFONTS\Webfonts\Font;


abstract class Font
{
    protected $provider = '';

    /**
     * @return string
     */
    public function getProvider(): string
    {
        return $this->provider;
    }

    /**
     * @param string $provider
     */
    public function setProvider(string $provider): void
    {
        $this->provider = $provider;
    }
}
