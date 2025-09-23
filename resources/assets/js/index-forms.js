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
});
