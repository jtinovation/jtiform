import { ClassicEditor, Essentials, Bold, Italic, Font, Paragraph } from 'ckeditor5';

import 'ckeditor5/ckeditor5.css';

document.addEventListener('DOMContentLoaded', function () {
  ClassicEditor.create(document.querySelector('#description'), {
    licenseKey: 'GPL', // Or 'GPL'.
    plugins: [Essentials, Bold, Italic, Font, Paragraph],
    toolbar: ['undo', 'redo', '|', 'bold', 'italic', '|', 'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor']
  })
    .then(/* ... */)
    .catch(/* ... */);

    const addGalleryButton = document.querySelector('#add-gallery');
    const removeGalleryButton = document.querySelector('.remove-gallery');
    const galleryContainer = document.querySelector('#gallery-container');
    const galleryTemplate = document.querySelector('#gallery-template');

    addGalleryButton.addEventListener('click', function () {
      galleryContainer.appendChild(galleryTemplate.content.cloneNode(true));
    });

    $('body').on('click', '.remove-gallery', function () {
      $(this).closest('.gallery-item').remove();
    });
});
