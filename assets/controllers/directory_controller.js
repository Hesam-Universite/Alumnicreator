import {Controller} from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ['uploadInput', 'form'];

    connect() {
        this.uploadInputTarget.addEventListener('change', (e) => {
            this.formTarget.submit();
        });
    }

    upload() {
        this.uploadInputTarget.click();
    }
}
