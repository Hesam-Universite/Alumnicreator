import {Controller} from "@hotwired/stimulus";
import * as toastr from "toastr";

export default class extends Controller {
    static values = {
        successes: Array,
        errors: Array,
    }

    connect() {
        this.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-bottom-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        this.successesValue.forEach((notification) => {
            this.success(notification);
        });
        this.errorsValue.forEach((notification) => {
            this.error(notification);
        });
    }

    success(notification) {
        toastr.success(notification);
    }

    error(notification) {
        toastr.error(notification);
    }
}
