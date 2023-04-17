import {Controller} from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ['everyFooterPages', 'addPage', 'removePage'];

    connect() {
        const everyFooterPages = this.everyFooterPagesTarget;
        const everyRows = document.querySelectorAll('.footer-page');

        let addPage = this.addPageTarget;
        let removePage = this.removePageTarget;

        let numberOfNewRows = everyRows.length;

        addPage.addEventListener('click', () => {
            let row = document.createElement('div');
            row.classList.add('row');
            row.classList.add('mt-3');
            row.classList.add('align-items-center');
            row.classList.add('footer-page');
            row.setAttribute('id', `footer-page-${numberOfNewRows + 1}`);
            everyFooterPages.appendChild(row);

            let divNameInput = document.createElement('div');
            divNameInput.classList.add('col-lg-6');
            row.appendChild(divNameInput);

            let divLinkInput = document.createElement('div');
            divLinkInput.classList.add('col-lg-6');
            row.appendChild(divLinkInput);

            let inputName = document.createElement('input');
            inputName.type = 'text';
            inputName.placeholder = 'Nom de la page';
            inputName.required = true;
            inputName.name = `name-${numberOfNewRows + 1}`;
            inputName.classList.add('form-control');
            divNameInput.appendChild(inputName);

            let inputUrl = document.createElement('input');
            inputUrl.type = 'text';
            inputUrl.placeholder = 'Lien';
            inputUrl.required = true;
            inputUrl.name = `url-${numberOfNewRows + 1}`;
            inputUrl.classList.add('form-control');
            divLinkInput.appendChild(inputUrl);

            numberOfNewRows++;
        })

        removePage.addEventListener('click', () => {
            if (numberOfNewRows > 1) {
                document.getElementById(`footer-page-${numberOfNewRows}`).remove();
                numberOfNewRows--;
            }
        })
    }
}