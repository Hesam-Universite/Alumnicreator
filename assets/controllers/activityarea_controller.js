import {Controller} from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ['activityarea', 'otherfield'];

    connect() {
        let currentOption = this.activityareaTarget;
        let value = currentOption.options[currentOption.selectedIndex].text;
        if(value === 'Autre') {
            this.otherfieldTarget.classList.remove('d-none');
        } else {
            this.otherfieldTarget.classList.add('d-none');
        }

        this.activityareaTarget.addEventListener('change', (e) => {
            const select = e.target;
            const value = select.options[select.selectedIndex].text;
            if(value === 'Autre') {
                this.otherfieldTarget.classList.remove('d-none');
            } else {
                this.otherfieldTarget.classList.add('d-none');
            }
        })
    }
}