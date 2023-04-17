import {Controller} from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ['postalcode', 'selectcities', 'realfield'];

    connect() {
        this.postalcodeTarget.addEventListener('input', (e) => {
            if(this.postalcodeTarget.value.length === 5) {
                callApi(this.postalcodeTarget.value);
            }
        })

        let selectCities = this.selectcitiesTarget;
        let realfield = this.realfieldTarget;

        function callApi(postalCodeWrittenByUser) {
            fetch('https://geo.api.gouv.fr/communes?codePostal=' + postalCodeWrittenByUser)
                .then(response => response.json())
                .then(data => {
                    selectCities.innerHTML = '';
                    const opt = document.createElement("option");
                    opt.value = null;
                    opt.text = "Sélectionnez une ville";
                    selectCities.appendChild(opt);
                    data.forEach(city => {
                        const opt = document.createElement("option");
                        opt.value = city.nom;
                        opt.text = city.nom;
                        selectCities.appendChild(opt);
                    })
                });
        }

        selectCities.addEventListener('change', (e) => {
            realfield.value = selectCities.value;
        })
    }
}