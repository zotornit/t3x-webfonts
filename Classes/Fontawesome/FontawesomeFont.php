<?php


namespace WEBFONTS\Webfonts\Fontawesome;


use WEBFONTS\Webfonts\Exception\WebfontsException;
use WEBFONTS\Webfonts\Font\Font;

class FontawesomeFont extends Font
{

    private string $version = '';
    private array $styles = [];
    private array $methods = [];

    /**
     * FontawesomeFont constructor.
     * @param array|null $font
     * @throws WebfontsException
     */
    public function __construct(array $font = null)
    {
        if (!is_null($font)) {
            if (!isset($font['provider']) || $font['provider'] != 'fontawesome') {
                throw new WebfontsException('Cannot load font parameter \'provider\' is missing.', 1588409875);
            }
            if (!isset($font['version'])) {
                throw new WebfontsException('Cannot load font parameter \'version\' is missing.', 1588409870);
            }

            $this->setVersion($font['version']);
            $this->setStyles(array_map('trim', explode(',', $font['styles'] ?? 'all')));
            $this->setMethods(array_map('trim', explode(',', $font['methods'] ?? 'css')));
            $this->setProvider("fontawesome");
        }
    }

    /**
     * @return array
     */
    public function getStyles(): array
    {
        return $this->styles;
    }

    /**
     * @param array $styles
     */
    public function setStyles(array $styles): void
    {
        $this->styles = $styles;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    /**
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @param array $methods
     */
    public function setMethods(array $methods): void
    {
        $this->methods = $methods;
    }
}
