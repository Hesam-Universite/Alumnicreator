import {Controller} from "@hotwired/stimulus";
import * as toastr from "toastr";

export default class extends Controller {
    static values = {
        apiUrl: String,
        apiToken: String,
    }

    changeStudentRole(event) {
        const currentTarget = event.currentTarget;
        fetch(this.apiUrlValue.replace('999999', event.currentTarget.dataset.studentId), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-INTERNAL-API-TOKEN': this.apiTokenValue,
            },
            body: JSON.stringify({
                role: event.currentTarget.dataset.studentRole,
            }),
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentTarget.parentNode.parentNode.querySelectorAll('button')[0].innerHTML = data.role;
                    toastr.success('Le role de l\'étudiant a été modifié');
                } else {
                    toastr.error('Une erreur est survenue. Merci de réessayer plus tard');
                }
            });
    }
}
