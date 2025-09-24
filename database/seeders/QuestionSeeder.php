<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Question;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            'Dosen Menyampaikan RPS Rencana Pembelajaran Semester (perkuliahan/praktikum/workshop) Kepada Mahasiswa Di Awal Semester',
            'Dosen Menyampaikan Tujuan/Capaian Pembelajaran, Referensi Dan Penilaian Matakuliah Kepada Mahasiswa Di Awal Semester',
            'Dosen Menetapkan Atau Menginformasikan Tata Tertib Dan Ketentuan Akademis Yang Harus Ditaati Oleh Mahasiswa',
            'Ketepatan Dosen Dalam Melaksanakan Jadwal Perkuliahan, Termasuk Untuk Mengawali Dan Mengakhiri Pertemuan',
            'Dosen Menegakkan Tata Tertib (memberikan Reward Atau Punishment) Sesuai Dengan Ketentuan Akademik',
            'Dosen Memberikan Contoh/Tauladan Terkait Penampilan Rapi Dan Sesuai Aturan Akademik',
            'Kemampuan Dosen Memberikan Motivasi Belajar Kepada Mahasiswa',
            'Media Pembelajaran (file Ppt/pdf/video, Dll) Yang Digunakan Jelas Dan Mudah Dipahami',
            'Dosen Menyampaikan Materi Dengan Cara Yang Mudah Dipahami',
            'Dosen Menggunakan Bahasa Komunikasi Yang Sopan Dan Santun Selama Perkuliahan',
        ];

        foreach ($questions as $i => $q) {
            Question::create([
                'id' => Str::uuid(),
                'question' => $q,
                'type' => 'option',
                'sequence' => $i + 1,
                'is_required' => true,
                'm_form_id' => 'isi_id_form_di_sini', 
            ]);
        }
    }
}
