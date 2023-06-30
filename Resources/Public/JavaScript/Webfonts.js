/*
TYPO3/CMS/Webfonts/Webfonts
 */
define([
    'jquery',
    'TYPO3/CMS/Webfonts/WebfontsAPI',
    'TYPO3/CMS/Webfonts/ModalVC',
    'TYPO3/CMS/Webfonts/GoogleFontOptionsVC',
    'bootstrap',
    'vue',
], function ($, api) {
    'use strict';

    var webfontsApp = new Vue({
        el: '#tx-webfonts-vueapp',
        data: {
            // message: 'Hello Vue.js!',
            showModal: false,
            fonts: [],
            selectedFont: null,
            filter: ''
        },
        computed: {
            listGoogleWebfonts: function () {
                const list = [];
                for (const font of this.fonts) {
                    if (font['provider'] === 'google_webfonts') {
                        list.push(font);
                    }
                }
                return list;
            },
            listInstalledFonts: function () {
                const list = [];
                for (const font of this.fonts) {
                    if (font['installation']) {
                        list.push(font);
                    }
                }
                return list;
            }
        },
        methods: {
            unsetFilter: function() {
                this.filter = '';
            },
            passesFilter: function(family) {
                if (this.filter) {
                    return family.toLowerCase().indexOf(this.filter.toLowerCase()) !== -1
                }
                return true;
            },
            options: function (font) {
                webfontsApp.selectedFont = font;
                webfontsApp.showModal = true;
            },
            receiveFontList: function () {
                // all fonts
                api.getFontList().then(function (data) {
                    webfontsApp.fonts = [];
                    for (const font of data.payload.fonts) {
                        webfontsApp.fonts.push(font);
                    }
                }, function (error) {
                    console.log(error);
                    alert('Error while downloading / parsing the webfonts API data'); // TODO
                });
            },
            // call thiswhen some fonts have changed their state
            updateFonts: function (fonts) { // array of changed fonts
                let i = 0;
                for (const font of webfontsApp.fonts) {
                    for (const changedFont of fonts) {
                        if (changedFont.id === this.selectedFont.id) {
                            this.selectedFont = changedFont;
                        }
                        if (font.id === changedFont.id) {
                            webfontsApp.fonts.splice(i, 1, changedFont);
                            break;
                        }
                    }
                    i++;
                }
            }
        },
        created() {
            this.receiveFontList();
        },
    });
    return webfontsApp;
});
