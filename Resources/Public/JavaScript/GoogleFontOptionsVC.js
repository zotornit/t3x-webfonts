define([
    'jquery',
    'TYPO3/CMS/Webfonts/WebfontsAPI',
    'TYPO3/CMS/Webfonts/Webfonts',
    'vue',
], function ($, api, webfontsApp) {
    'use strict';


    Vue.component('tx-webfonts-google-font-options-component',
        {
            template: '#tx-webfonts-google-font-options-component',
            data: function () {
                return {
                    cssLinkJQ: null,
                    selectedCharsets: [],
                    selectedVariants: [],
                    installationInProgress: false,
                    lastInstallationState: 0, // 0 nothing happened, 1 install ok, -1 error
                    installationErrorMsg: 'no_error'
                }
            },
            props: ['font'],
            computed: {},
            methods: {
                loadFontCSS: function () {
                    this.cssLinkJQ = $("<link/>", {
                        rel: "stylesheet",
                        type: "text/css",
                        href: this.font.cdn
                    }).appendTo("head");
                },
                unloadFontCSS: function () {
                    this.cssLinkJQ.remove();
                    this.cssLinkJQ = null;
                },
                getFamily: function (variant) {
                    return this.font.usage[variant]['family'];
                },
                getWeight: function (variant) {
                    return this.font.usage[variant]['weight'];
                },
                getStyle: function (variant) {
                    return this.font.usage[variant]['style'];
                },
                charsetChecked: function (subset) {
                    return this.selectedCharsets.indexOf(subset) >= 0;
                },
                variantSelected: function (variant) {
                    return this.selectedVariants.indexOf(variant) >= 0;
                },
                toggleVariant: function (variant) {
                    const p = this.selectedVariants.indexOf(variant);
                    if (p >= 0) {
                        this.selectedVariants.splice(p, 1);
                    } else {
                        this.selectedVariants.push(variant);
                    }
                },
                canDisableSubset: function (subset) {
                    return this.selectedCharsets.length === 1 && this.charsetChecked(subset)
                },
                toggleCharset: function (charset) {
                    const p = this.selectedCharsets.indexOf(charset);
                    if (p >= 0) {
                        this.selectedCharsets.splice(p, 1);
                    } else {
                        this.selectedCharsets.push(charset);
                    }
                },
                canInstall: function () {
                    return this.selectedCharsets.length >= 1 && this.selectedVariants.length >= 1;
                },
                isInstalled: function () {
                    return this.font.installation !== null && !this.lastActionWasDelete;
                },
                install: function (charsets = [], variants = []) {
                    if (!this.canInstall() || this.installationInProgress) {
                        return;
                    }

                    if (variants.length === 0) {
                        this.selectedVariants = [];
                    }
                    this.installationInProgress = true;
                    this.lastInstallationState = 0;
                    const c = this;

                    api.install(this.font, charsets, variants).then(function (data) {
                        webfontsApp.updateFonts(data.payload.fonts);
                        c.lastInstallationState = data.payload.state;
                        c.installationInProgress = false;
                    }, function (error) {
                        c.installationErrorMsg = error;
                        c.lastInstallationState = -1;
                        c.installationInProgress = false;
                    });
                }

            },

            created: function () {
                this.loadFontCSS();

                if (this.font.installation) {
                    this.selectedCharsets = Object.assign([], this.font.installation.subsets);
                    this.selectedVariants = Object.assign([], this.font.installation.variants);
                } else {
                    // need to preselect latin or first
                    if (this.font.subsets.indexOf('latin') >= 0) {
                        this.selectedCharsets.push('latin');
                    } else {
                        this.selectedCharsets.push(this.font.subsets[0]);
                    }
                }
            },
            destroyed: function () {
                this.unloadFontCSS();
            },
        }
    );

});
