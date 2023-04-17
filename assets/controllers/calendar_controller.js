import {Controller} from "@hotwired/stimulus";

import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';

export default class extends Controller {
    static targets = ['calendarEl'];

    static values = {
        events: Array,
    }

    connect() {
        const calendarEl = this.calendarElTarget;

        let calendar = new Calendar(calendarEl, {
            locale: 'fr', // the initial locale
            plugins: [ dayGridPlugin ],
            initialView: 'dayGridMonth',
            firstDay: 1,
            buttonText: {
                today:    'Aujourd\'hui',
                month:    'Mois',
                week:     'Semaine',
                day:      'Jour',
                list:     'Liste'
            },
            displayEventEnd: true,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth'
            },
            events: this.eventsValue,
        });

        calendar.render();
    }
}