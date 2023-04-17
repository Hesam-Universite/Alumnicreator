import {Controller} from "@hotwired/stimulus";

import tinymce from 'tinymce';

import 'tinymce/icons/default';
import 'tinymce/themes/silver';
import 'tinymce/skins/ui/oxide/skin.css';
import 'tinymce/models/dom/model.min';

import 'tinymce/plugins/link';

import '../tinymce-translation/fr_FR';

export default class extends Controller {
    static targets = ['postContent'];

    connect() {
        const postContent = this.postContentTarget;

        tinymce.init({
            selector: '#' + postContent.getAttribute('id'),
            language: 'fr_FR',
            plugins: 'link',
            toolbar: 'bold italic | alignleft aligncenter alignright alignjustify | link',
            automatic_uploads: true,
            branding: false,
        })
    }
}
