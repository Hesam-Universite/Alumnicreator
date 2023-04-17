import {Controller} from "@hotwired/stimulus";

import tinymce from 'tinymce';

import 'tinymce/icons/default';
import 'tinymce/themes/silver';
import 'tinymce/skins/ui/oxide/skin.css';
import 'tinymce/models/dom/model.min';

import 'tinymce/plugins/image';
import 'tinymce/plugins/link';
import 'tinymce/plugins/table';

import '../tinymce-translation/fr_FR';

export default class extends Controller {
    static targets = ['postContent'];

    connect() {
        const postContent = this.postContentTarget;

        tinymce.init({
            selector: '#' + postContent.getAttribute('id'),
            language: 'fr_FR',
            plugins: 'image link table',
            toolbar: 'image bold italic | alignleft aligncenter alignright alignjustify | link | table tabledelete',
            automatic_uploads: true,
            images_upload_url: '/administration/pages/attachment-page',
            branding: false,
            file_picker_types: 'image',
            file_picker_callback: function (cb, value, meta) {
                var input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');

                input.onchange = function () {
                    var file = this.files[0];

                    var reader = new FileReader();
                    reader.onload = function () {
                        var id = 'blobid' + (new Date()).getTime();
                        var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
                        var base64 = reader.result.split(',')[1];
                        var blobInfo = blobCache.create(id, file, base64);
                        blobCache.add(blobInfo);

                        /* call the callback and populate the Title field with the file name */
                        cb(blobInfo.blobUri(), { title: file.name });
                    };
                    reader.readAsDataURL(file);
                };

                input.click();
            }
        })
    }
}
