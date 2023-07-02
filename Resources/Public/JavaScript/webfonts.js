import {LitElement, css, html} from 'lit-element';


export class GoogleFontsListApp extends LitElement {
    static properties = {
        showOnlyInstalled: {},
        fonts: [],
        installedFonts: [],
        backendCssUrl: {},
        filterText: {},
        manageActionUrl: {},
        fontsToShow: [],
    };


    static get styles() {
        return [
            css`
              .label {
                display: inline;
                padding: .2em .6em .3em;
                font-size: 75%;
                font-weight: 700;
                line-height: 1;
                color: #fff;
                text-align: center;
                white-space: nowrap;
                vertical-align: baseline;
                border-radius: .25em;
              }

              .label.label-success {
                background-color: #79a548;
              }
            `
        ]
    }


    constructor() {
        super();
        this.showOnlyInstalled = false;
        this.fonts = [];
        this.installedFonts = [];
        this.fontsToShow = [];
        this.filterText = '';
    }

    connectedCallback() {
        super.connectedCallback();
        this.showOnlyInstalled = this.showOnlyInstalled === 'true';
        this.fonts = JSON.parse(this.fonts);
        this.installedFonts = JSON.parse(this.installedFonts);
        this.updateFontsToShow();
    }

    updateFontsToShow() {
        this.fontsToShow = [];

        const installedIds = this.installedFonts.map(m => m.id);

        for(const font of this.fonts) {
            if(this.filterText && !font.family.toLocaleLowerCase().includes(this.filterText.toLocaleLowerCase())) {
                continue;
            }
            if(this.showOnlyInstalled && !installedIds.includes(font.id)) {
                continue;
            }


            this.fontsToShow.push(font);
        }

    }

    filterTextUpdate(e) {
        this.filterText = e.target.value;
        this.updateFontsToShow();
    }


    render() {
        return html`
            <link rel="stylesheet" href="${this.backendCssUrl}">

            <div class=" toolbox">
                <div class="input-group">
                    <input type="text"
                            name="list_filter" 
                            placeholder="Filter" 
                            .value="${this.filterText}" 
                            class="form-control"
                            @keyup="${this.filterTextUpdate}"
                    />                    
                </div>
            </div>
            <br>
            
            
            <div class="table-fit">
                <table id="typo3-tx-webfonts-list" class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th data-sort-method="none" >Font Family</th>
                        <th data-sort-method="none" >Provider</th>
                        <th data-sort-method="none" >Version</th>
                        <th data-sort-method="none" >Action</th>

                    </tr>
                    </thead>
                    <tbody>

                    ${this.fontsToShow.map((font) =>
                            html`
                            
                            <tr role="row" >
                                <td>
                                    ${font.family}
                                    
                                    ${font.installation
                                        ?   html`<br>
                                                Installed                                            
                                                ${font.installation.variants.map((v) => html`
                                                    <span class="label label-success" style="margin-right: 10px;">${v}</span>
                                                `)}                                    
                                            ` 
                                        :''
                                    }
                                    
                                    
                                    
                                </td>
                                <td>${font.provider}</td>
                                <td>${font.version}</td>


                                <td>
                                    <div class="btn-group">
                                        <a href="${this.manageActionUrl.replace('REPLACE_F_ID', font.id)}" class="btn btn-primary">Manage</a>
                                    </div>
                                </td>


                            </tr>
                            `
                    )}
                    
              
                    </tbody>
                </table>
            </div>
        `;
    }
}
customElements.define('google-fonts-list-app', GoogleFontsListApp);


export class GoogleFontsManageApp extends LitElement {
    static properties = {
        font: {
            family: {},
            subsets: [],
            variants: [],
            category: {},
        },
        selectedSubsets: {},
        selectedVariants: {},
        installActionUrl: {},
        installActionUrlWithParameters: {},
        uninstallActionUrl: {},
        backendCssUrl: {},
        isInstalled: {},
        isDirty: {},
    };

    connectedCallback() {
        super.connectedCallback()
        this.font = JSON.parse(this.font);
        this.selectedSubsets = JSON.parse(this.selectedSubsets);
        this.selectedVariants = JSON.parse(this.selectedVariants);
        this.isInstalled = this.isInstalled === 'true';
        this.isDirty = false;
        this.updateInstallActionUrlWithParameters();
    }

    _subsetChanged({detail}) {
        if(detail.checked) {
            this.selectedSubsets.push(detail.subset);
        } else {
            this.selectedSubsets = this.selectedSubsets.filter(f=> f !== detail.subset);
        }
        this.isDirty = true;
        this.updateInstallActionUrlWithParameters();
    }

    _variantChanged({detail}) {
        if(detail.checked) {
            this.selectedVariants.push(detail.variant);
        } else {
            this.selectedVariants = this.selectedVariants.filter(f=> f !== detail.variant);
        }
        this.isDirty = true;
        this.updateInstallActionUrlWithParameters();
    }

    updateInstallActionUrlWithParameters() {
        this.installActionUrlWithParameters = `${this.installActionUrl}&subsets=${this.selectedSubsets.join(',')}&variants=${this.selectedVariants.join(',')}`
    }

    render() {

        const tsLink = `page.includeCSS.tx_webfonts__${this.font.id} = fileadmin/tx_webfonts/fonts/google_webfonts/${this.font.id}/import.css`;
        const cssCode = `font-family: '${this.font.family}', ${this.font.category};`;

        return html`
            <link rel="stylesheet" href="${this.backendCssUrl}">
            <div style="display:flex; justify-content: space-between;">
                <div>
                    <h1>${this.font.family}</h1>
                </div>
                <div>
                    ${this.isInstalled
                        ? html`<div style="background-color: #107c10; color:white; padding: 5px; border-radius: 5px;">installed</div>`
                        : html`<div style="background-color: #e8a33d; color:white; padding: 5px; border-radius: 5px;">not installed</div>`
                    }
                </div>
            </div>
            <hr>

            <div class="row" @subsetChanged="${this._subsetChanged}" @variantChanged="${this._variantChanged}">
                <div class="col-6">

                    <h2>Charsets</h2>

                    <div>
                        <charsets-element .available="${this.font.subsets}" .selected="${this.selectedSubsets}"></charsets-element>
                    </div>

                    <br>
                    <br>

                    <h2>Variants</h2>
                    
                    
                    <div>
                        <variants-element .available="${this.font.variants}" .selected="${this.selectedVariants}" .font="${this.font}"></variants-element>
                    </div>    
                    
                </div>
                <div class="col-6">
                    <h2>Actions</h2>
                    <div>    
                        <a href="${this.installActionUrlWithParameters}" 
                           class="btn btn-primary ${this.selectedVariants.length == 0 || this.selectedSubsets.length == 0 || (!this.isDirty && this.isInstalled) ? 'disabled' : ''}" 
                           title="${!this.isInstalled ? 'Install' : 'Update'}">${!this.isInstalled ? 'Install' : 'Update'}
                        </a>
                        <a href="${this.uninstallActionUrl}" 
                           class="btn btn-danger ${!this.isInstalled ? 'disabled' : ''}" 
                           title="Uninstall">Uninstall</a>                                                 
                    </div>

                    <br>
                    <br>


                    <h2>Usage</h2>
                                        
                    ${this.isInstalled 
                            ?  
                            html`
                                
                                <h3>TypoScript</h3>
                                <p style="font-style: italic; font-size: 10px;">Click to copy</p>
                                <code @click="${() => navigator.clipboard.writeText(tsLink)}" style="cursor:pointer;">
                                    ${tsLink}
                                </code>
                                

                                <h3>CSS</h3>
                                <code @click="${() => navigator.clipboard.writeText(cssCode)}" style="cursor:pointer;">
                                    ${cssCode}
                                </code>
                            `                            
                            :
                            html`
                            <p style="font-style: italic;">Font ist not installed</p>
                            `
                    }

                   

                </div>
            </div>      
        `;
    }
}
customElements.define('google-fonts-manage-app', GoogleFontsManageApp);


export class CharsetsItem extends LitElement {
    static properties = {
        name: {},
        checked: {}
    };

    toggleChecked() {
        this.checked = !this.checked;
        this.dispatchEvent(new CustomEvent('subsetChanged', {
            detail: {
                subset: this.name,
                checked: this.checked,
            },
            bubbles: true,
            composed: true,
        }));
    }

    render() {
        return html`
            <input type="checkbox" id="${this.name}" value="${this.name}" ?checked="${this.checked}" @click="${this.toggleChecked}" style="cursor: pointer;">
            <label for="${this.name}" style="cursor: pointer;">${this.name}</label>           
        `;
    }
}
customElements.define('charset-item', CharsetsItem);


export class CharsetsElement extends LitElement {
    static properties = {
        available: [],
        selected: [],
    };

    constructor() {
        super();
        this.available = [];
        this.selected = [];
    }
    render() {
        return html`            
            ${this.available.map((item) =>
                html`<charset-item name="${item}" .checked="${this.selected.includes(item)}"></charset-item>`
            )}
        `;
    }
}
customElements.define('charsets-element', CharsetsElement);




export class VariantsItem extends LitElement {
    static properties = {
        name: {},
        checked: {},
        font: {},
    };

    static get styles() {
        return [
            css`
              .fontbox {
                border-left: 5px solid lightgrey;
                background-color: gainsboro;
                cursor: pointer;
                padding: 5px;
                margin-bottom: 10px;
              }
              .fontbox p {
                margin-bottom: 3px;
              }
              .fontbox:hover {
                background-color: gainsboro;
              }
              .fontbox.selected {
                border-left: 5px solid #79a548;
                background-color: #f0f6e8;
              }
              
              .pill {
                background-color: #0078e6;
                color: white;
                padding: 4px;
                border-radius: 4px;
              }
            `
        ]
    }

    toggleChecked() {
        this.checked = !this.checked;
        this.dispatchEvent(new CustomEvent('variantChanged', {
            detail: {
                variant: this.name,
                checked: this.checked,
            },
            bubbles: true,
            composed: true,
        }));
    }

    render() {
        const style = this.name === 'italic' || new RegExp(`/^\d*italic$/`).test(this.name) ? 'italic' : 'normal';


        const matches = this.name.match(/^(\d{2,4})\w*$/);
        const weight = matches && matches[1] ? matches[1] : 400;

        return html`
            <div class="${this.checked ? 'fontbox selected' : 'fontbox'}" @click="${this.toggleChecked}" style="cursor: pointer;">
                <p style="margin-top: 0; font-size: 18px;
                                       font-family: ${this.font.family};
                                       font-weight: ${weight};
                                       font-style: ${style};
                                    ">
                    The quick brown fox jumps over the lazy dog.
                </p>
                <p>
                    <span class="pill">${this.name}</span>
                </p>
            </div>
        `;
    }
}
customElements.define('variant-item', VariantsItem);

export class VariantsElement extends LitElement {
    static properties = {
        available: [],
        selected: [],
        font: {},
    };

    constructor() {
        super();
        this.available = [];
        this.selected = [];
    }
    render() {
        return html`
            
            ${this.available.map((item) =>
                html`<variant-item name="${item}" .checked="${this.selected.includes(item)}" .font="${this.font}"></variant-item>`
            )}    
               
        `;
    }
}
customElements.define('variants-element', VariantsElement);

