'use strict';
import Swal from 'sweetalert2';
import axios from 'axios';

document.addEventListener('DOMContentLoaded', function () {
  $('.button-swal').on('click', function () {
    const eventTitle = $(this).attr('data-title');
    const eventId = $(this).attr('data-id');

    Swal.fire({
      title: 'Apakah Anda yakin?',
      text: `Anda akan menghapus ${eventTitle}`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya, Hapus!',
      cancelButtonText: 'Tidak, Batalkan!'
    }).then(result => {
      if (result.isConfirmed) {
        axios
          .delete(`/admin/event/${eventId}`)
          .then(response => {
            Swal.mixin({
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 1000,
              timerProgressBar: true,
              didOpen: toast => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
              }
            })
              .fire({
                title: 'Data event berhasil dihapus.',
                icon: 'success'
              })
              .then(() => {
                location.reload();
              });
          })
          .catch(error => {
            console.error(error);
            Swal.mixin({
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true,
              didOpen: toast => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
              }
            }).fire({
              title: 'Terjadi kesalahan saat menghapus event.',
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
});
