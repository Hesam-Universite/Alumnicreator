import {Controller} from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ['foh1', 'foh2', 'fopa'];

    static values = {
        selectedFonts: Array,
        customFonts: Array
    }

    connect() {
        const selectMainTitle = this.foh1Target;
        const selectSecondTitle = this.foh2Target;
        const selectContent = this.fopaTarget;

        const selectedFonts = this.selectedFontsValue;

        const customFonts = this.customFontsValue;

        const urlToCall = 'https://www.googleapis.com/webfonts/v1/webfonts?key=' + selectedFonts[3];

        fetch(urlToCall)
            .then(function(res) {
                if (res.ok) {
                    return res.json();
                }
            })
            .then(function(typo) {
                for (let i = 0; i < 3; i++) {
                    if (i === 0) {
                        selectMainTitle.innerHTML = '';
                    } else if (i === 1) {
                        selectSecondTitle.innerHTML = '';
                    } else {
                        selectContent.innerHTML = '';
                    }

                    let selectedFont = selectedFonts[i].replace('[','').replace(']','').replace(/'/g,'').split(', ');

                    // Polices ajoutées par les administrateurs de l'instance
                    customFonts.forEach(element => {
                        const opt = document.createElement("option");
                        opt.value = `['${element.name}', '${element.fontFileName}']`;
                        opt.text = element.name;
                        if (element.name === selectedFont[0]) {
                            opt.selected = 'true';
                        }
                        if (i === 0) {
                            selectMainTitle.appendChild(opt);
                        } else if (i === 1) {
                            selectSecondTitle.appendChild(opt);
                        } else {
                            selectContent.appendChild(opt);
                        }
                    })

                    const opt = document.createElement("option");
                    opt.text = '________';
                    opt.disabled = 'true';
                    if (i === 0) {
                        selectMainTitle.appendChild(opt);
                    } else if (i === 1) {
                        selectSecondTitle.appendChild(opt);
                    } else {
                        selectContent.appendChild(opt);
                    }

                    // Polices Google Fonts
                    typo.items.forEach(element => {
                        // === Création de l'URL ===
                        let apiUrl = [];
                        apiUrl.push('https://fonts.googleapis.com/css?family=');
                        apiUrl.push(element.family.replace(/ /g, '+'));
                        let url = apiUrl.join('');
                        // ===
                        const opt = document.createElement("option");
                        opt.value = `['${element.family}', '${url}']`;
                        opt.text = element.family;
                        if (element.family === selectedFont[0]) {
                            opt.selected = 'true';
                        }
                        if (i === 0) {
                            selectMainTitle.appendChild(opt);
                        } else if (i === 1) {
                            selectSecondTitle.appendChild(opt);
                        } else {
                            selectContent.appendChild(opt);
                        }
                    })
                }
            })
            .catch(function(err) {
                // Une erreur est survenue
            });
    }
}
