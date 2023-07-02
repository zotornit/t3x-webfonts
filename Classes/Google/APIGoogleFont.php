<?php


namespace WEBFONTS\Webfonts\Google;


use ReturnTypeWillChange;
use WEBFONTS\Webfonts\Exception\WebfontsException;

class APIGoogleFont implements APIGoogleFontIF
{

    /*
     * GoogleFontVariant[]
     */
    private mixed $variants;
    private mixed $subsets;
    private mixed $id;
    private mixed $family;
    private mixed $category;
    private mixed $version;
    private mixed $defSubset;
    private mixed $defVariant;

    public function __construct($apiFontData)
    {
        $this->id = $apiFontData['id'];
        $this->family = $apiFontData['family'];
        $this->variants = $apiFontData['variants'];
        $this->subsets = $apiFontData['subsets'];
        $this->category = $apiFontData['category'];
        $this->version = $apiFontData['version'];
        $this->defSubset = $apiFontData['defSubset'];
        $this->defVariant = $apiFontData['defVariant'];
    }

    /**
     * @inheritDoc
     */
    #[ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'family' => $this->getFamily(),
            'variants' => $this->getVariants(),
            'subsets' => $this->getSubsets(),
            'category' => $this->getCategory(),
            'version' => $this->getVersion(),
            'defSubset' => $this->getDefSubset(),
            'defVariant' => $this->getDefVariant(),
            'provider' => $this->getProvider(),
            'installation' =>
                GoogleFontInstallationManager::getInstance()->installDetails($this->id, $this->getProvider()),
            'cdn' => $this->getCdn(),
            'usage' => $this->usage(),
            'fallback' => $this->fallback()
        ];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFamily(): string
    {
        return $this->family;
    }

    /**
     * @inheritDoc
     */
    public function getVariants(): array
    {
        return $this->variants;
    }

    /**
     * @inheritDoc
     */
    public function getSubsets(): array
    {
        return $this->subsets;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getDefSubset(): string
    {
        return $this->defSubset;
    }

    public function getDefVariant(): string
    {
        return $this->defVariant;
    }

    public function getProvider(): string
    {
        return 'google_webfonts';
    }

    public function getCdn(): string
    {
        // Format examples:
        // https://fonts.googleapis.com/css2?family=Roboto+Mono:ital,wght@0,100;0,300;0,400;0,500;0,700;1,100;1,300;1,400;1,500;1,700&display=swap
        // https://fonts.googleapis.com/css2?family=Roboto+Mono:ital,wght@0,100;1,100;0,300;1,300;0,400;1,400;0,500;1,500;0,700;1,700;0,900;1,900;&display=swap
        // https://fonts.googleapis.com/css2?family=Roboto:italic&subset=greek

        $loaderURLArr = [
            "https://fonts.googleapis.com/css2?",
            "family=" . str_replace(" ", "+", $this->getFamily()),
            $this->getHasItalic() ? ":ital," : "",
        ];

        // 0,100;
        $parts = [];
        foreach ($this->getVariants() as $v) {
            $p = [];

            if ($this->getHasNormal() && $this->getHasItalic()) {
                $p[] = $this->parseFontStyle($v) === 'italic' ? 1 : 0;
                $p[] = ",";
            }


            $p[] = $this->parseFontWeight($v);
            $p[] = ";";
            $parts[] = implode("", $p);
        }

        if (count($parts) > 1) {
            $loaderURLArr[] = $this->getHasItalic() ? "wght@" : ":wght@";
            // order is important!!
            sort($parts);
            $parts[count($parts) - 1] = rtrim($parts[count($parts) - 1], ";");
            $loaderURLArr = array_merge($loaderURLArr, $parts);
        }
        $loaderURLArr[] = "&display=swap";
        return implode("", $loaderURLArr);
    }

    public function getHasItalic(): bool
    {
        foreach ($this->variants as $variant) {
            if (strpos($variant, 'italic') !== false) {
                return true;
            }
        }
        return false;
    }

    public function getHasNormal(): bool
    {
        return true;
    }

    private function parseFontStyle($variantId)
    {
        if ($variantId === 'regular' || preg_match('/^\d*$/', $variantId)) {
            return 'normal';
        }
        if ($variantId === 'italic' || preg_match('/^\d*italic$/', $variantId)) {
            return 'italic';
        }
        throw new WebfontsException('Unknown $variantId: ' . $variantId);
    }

    private function parseFontWeight($variantId)
    {
        if ($variantId === 'regular' || $variantId === 'italic') {
            return 400;
        }

        if (preg_match('/^(\d{2,4})\w*$/', $variantId, $m)) {
            return $m[1];
        }
        throw new WebfontsException('Unknown $variantId: ' . $variantId);
    }

    private function usage()
    {
        $fallback = $this->fallback();
        $r = [];
        foreach ($this->getVariants() as $variant) {
            $r[$variant] = [
                'family' => '\'' . $this->getFamily() . '\', ' . $fallback,
                'style' => $this->parseFontStyle($variant),
                'weight' => $this->parseFontWeight($variant),
            ];
        }
        return $r;
    }

    private function fallback()
    {
        $cat = $this->getCategory();
        if ($cat === 'handwriting' || $cat === 'display') {
            return "cursive";
        }
        if ($cat === 'serif') {
            return "serif";
        }
        if ($cat === 'monospace') {
            return "monospace";
        }
        return 'sans-serif';
    }

}
