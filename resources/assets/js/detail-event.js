'use strict';
import Swal from 'sweetalert2';
import axios from 'axios';

document.addEventListener('DOMContentLoaded', function () {
  $('.button-swal').on('click', function () {
    const eventTitle = $(this).attr('data-title');
    const eventItemId = $(this).attr('data-id');

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
          .delete(`/admin/event/item/${eventItemId}`)
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

  $('.btn-delete-gallery').on('click', function () {
    const galleryId = $(this).attr('data-id');

    Swal.fire({
      title: 'Apakah Anda yakin?',
      text: `Anda akan menghapus gambar ini?`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya, Hapus!',
      cancelButtonText: 'Tidak, Batalkan!'
    }).then(result => {
      if (result.isConfirmed) {
        axios
          .delete(`/admin/event/gallery/${galleryId}`)
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
                title: 'Data gallery berhasil dihapus.',
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
              title: 'Terjadi kesalahan saat menghapus gallery.',
              icon: 'error'
            });
          });
      }
    });
  });
});
