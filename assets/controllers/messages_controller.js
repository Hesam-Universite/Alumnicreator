import {Controller} from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ['msgScroll'];

    connect() {
        let msgScroll = this.msgScrollTarget;

        msgScroll.scroll({
            top: msgScroll.scrollHeight,
            behavior: 'smooth'
        });
    }
}