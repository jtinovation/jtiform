'use strict';
import Swal from 'sweetalert2';
import axios from 'axios';

document.addEventListener('DOMContentLoaded', function () {
  $('.button-swal').on('click', function () {
    const name = $(this).attr('data-name');
    const id = $(this).attr('data-id');

    Swal.fire({
      title: 'Apakah Anda yakin?',
      text: `Anda akan menghapus ${name}`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya, Hapus!',
      cancelButtonText: 'Tidak, Batalkan!'
    }).then(result => {
      if (result.isConfirmed) {
        axios
          .delete(`/form/${id}`)
          .then(response => {
            Swal.fire({
              title: 'Dihapus!',
              text: `${name} telah dihapus.`,
              icon: 'success'
            }).then(() => {
              location.reload();
            });
          })
          .catch(error => {
            console.error(error);
            Swal.fire({
              title: 'Error!',
              text: 'Terjadi kesalahan saat menghapus pengguna.',
              icon: 'error'
            });
          });
      }
    });
  });

  $('#search').on('keyup', function () {
    let debounceTimer;
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(function () {
      $('#form-filter').submit();
    }, 1000);
  });

  const modalEl = document.getElementById('cloneFormModal');
  if (!modalEl) return;

  modalEl.addEventListener('show.bs.modal', event => {
    const btn = event.relatedTarget;
    const formId = btn.getAttribute('data-id');
    const code = btn.getAttribute('data-code') || '';
    const title = btn.getAttribute('data-title') || '';
    const description = btn.getAttribute('data-description') || '';
    const startAt = btn.getAttribute('data-start') || '';
    const endAt = btn.getAttribute('data-end') || '';

    const form = modalEl.querySelector('form#cloneForm');
    form.setAttribute('action', `/form/${formId}/clone`);

    form.querySelector('input[name="code"]').value = `${code}-COPY`;
    form.querySelector('input[name="title"]').value = `${title} (Copy)`;
    form.querySelector('textarea[name="description"]').value = description || '';
    form.querySelector('input[name="start_at"]').value = startAt || '';
    form.querySelector('input[name="end_at"]').value = endAt || '';
  });
});
