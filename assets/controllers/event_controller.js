import {Controller} from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ['checkAllDay', 'dateStart', 'dateEnd', 'fullDateStart', 'fullDateEnd'];

    connect() {
        let checkAllDay = this.checkAllDayTarget;
        let dateStart = this.dateStartTarget;
        let dateEnd = this.dateEndTarget;
        let fullDateStart = this.fullDateStartTarget;
        let fullDateEnd = this.fullDateEndTarget;

        if(checkAllDay.checked) {
            dateStart.style.display = "none";
            dateEnd.style.display = "none";
            fullDateStart.style.display = "unset";
            fullDateEnd.style.display = "unset";
        } else {
            dateStart.style.display = "unset";
            dateEnd.style.display = "unset";
            fullDateStart.style.display = "none";
            fullDateEnd.style.display = "none";
        }

        checkAllDay.addEventListener('input', (e) => {
            if(checkAllDay.checked) {
                dateStart.style.display = "none";
                dateEnd.style.display = "none";
                fullDateStart.style.display = "unset";
                fullDateEnd.style.display = "unset";
            } else {
                dateStart.style.display = "unset";
                dateEnd.style.display = "unset";
                fullDateStart.style.display = "none";
                fullDateEnd.style.display = "none";
            }
        })
    }
}