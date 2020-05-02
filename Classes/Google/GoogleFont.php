<?php


namespace WEBFONTS\Webfonts\Google;


use WEBFONTS\Webfonts\Exception\WebfontsException;
use WEBFONTS\Webfonts\Font\Font;

class GoogleFont extends Font
{

    private $id = '';
    private $charsets = [];
    private $variants = [];

    /**
     * GoogleFont constructor.
     */
    public function __construct(array $font = null)
    {
        if (!is_null($font)) {
            if (!isset($font['provider']) || $font['provider'] != 'google_webfonts') {
                throw new WebfontsException('Cannot load font parameter \'provider\' is missing.', 1588405875);
            }
            if (!isset($font['charsets'])) {
                throw new WebfontsException('Cannot load font parameter \'charsets\' is missing.', 1588405870);
            }
            if (!isset($font['variants'])) {
                throw new WebfontsException('Cannot load font parameter \'variants\' is missing.', 1588405871);
            }
            if (!isset($font['id'])) {
                throw new WebfontsException('Cannot load font parameter \'id\' is missing.', 1588405873);
            }

            $this->setProvider($font['provider']);
            $this->setId($font['id']);
            if (is_array($font['charsets'])) {
                $this->setCharsets(array_map('trim', $font['charsets']));
            } else {
                $this->setCharsets(array_map('trim', explode(',', $font['charsets'] ?? 'all')));

            }
            if (is_array($font['variants'])) {
                $this->setVariants(array_map('trim', $font['variants']));
            } else {
                $this->setVariants(array_map('trim', explode(',', $font['variants'] ?? 'css')));

            }
        }
    }

    /**
     * @return array
     */
    public function getCharsets(): array
    {
        return $this->charsets;
    }

    /**
     * @param array $charsets
     */
    public function setCharsets(array $charsets): void
    {
        $this->charsets = $charsets;
    }

    /**
     * @return array
     */
    public function getVariants(): array
    {
        return $this->variants;
    }

    /**
     * @param array $variants
     */
    public function setVariants(array $variants): void
    {
        $this->variants = $variants;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }
}
