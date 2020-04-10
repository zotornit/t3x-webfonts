define([
    'jquery',
], function ($) {
    'use strict';

    return {
        getFontList: function () {
            return $.ajax({
                url: TYPO3.settings.ajaxUrls.webfonts_list
            });
        },
        options: function (font) {
            return $.ajax({
                url: TYPO3.settings.ajaxUrls.webfonts_options + '&id=' + font.id + '&provider=' + font.provider
            });
        },
        install: function (font, charsets, variants) {
            return $.ajax({
                url: TYPO3.settings.ajaxUrls.webfonts_install + '&id=' + font.id + '&provider=' + font.provider,
                method: 'post',
                data: {
                    charsets: charsets ? charsets : font.subsets[0],
                    variants: variants ? variants : font.variants[0]
                },
                dataType: "json"
            });
        }
    }
});
