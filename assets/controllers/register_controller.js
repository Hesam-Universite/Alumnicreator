import {Controller} from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ['promotion', 'status'];

    connect() {
        this.promotionTarget.addEventListener('change', (e) => {
            if(e.target.value < new Date().getFullYear()) {
                this.statusTarget.classList.remove('d-none');
            } else {
                this.statusTarget.classList.add('d-none');
            }
        })
    }
}