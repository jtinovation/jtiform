<?php

namespace Database\Seeders;

use App\Enums\FormRespondentTypeEnum;
use App\Enums\FormTypeEnum;
use App\Models\Form;
use App\Models\Question;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FormSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $form = Form::create([
      'code' => 'EVADOS_2425_GENAP',
      'type' => FormTypeEnum::LECTURE_EVALUATION,
      'cover_path' => null,
      'cover_file' => null,
      'title' => 'Evaluasi Dosen Pengampu Matakuliah Tahun Ajaran 2024 / 2025 Genap',
      'description' => 'Kuisioner ini di isi oleh mahasiswa Jurusan Teknologi Informasi yang bertujuan untuk memberikan masukan terhadap kinerja dosen pengampu matakuliah selama satu semester.',
      'is_active' => true,
      'start_at' => now(),
      'end_at' => now()->addMonth(),
      'respondents' => json_encode([
        'type' => FormRespondentTypeEnum::MAJOR->value,
        'major_id' => '0198597b-6188-70c8-91d6-08ea3d652a74',
      ]),
    ]);

    $questions = [
      'Dosen Menyampaikan RPS Rencana Pembelajaran Semester (perkuliahan/praktikum/workshop) Kepada Mahasiswa Di Awal Semester',
      'Dosen Menyampaikan Tujuan/Capaian Pembelajaran, Refrensi Dan Penilaiain Matakuliah Kepada Mahasiswa Di Awal Semester',
      'Dosen Menetapkan Atau Menginformasikan Tata Tertib Dan Ketentuan Akademis Yang Harus Ditaati Oleh Mahasiswa',
      'Ketepatan Dosen Dalam Melaksanakan Jadwal Perkuliahan, Termasuk Untuk Mengawali Dan Mengakhiri Pertemuan',
      'Dosen Menegakkan Tata Tertib (memberikan Reward Atau Punishment) Sesuai Dengan Ketentuan Akademik',
      'Dosen Memberikan Contoh/tauladan Terkait Penampilan Rapi Dan Sesuai Aturan Akademik.',
      'Kemampuan Dosen Memberikan Motivasi Belajar Kepada Mahasiswa',
      'Media Pembelajaran (file Ppt/pdf/video, Dll) Yang Digunakan Jelas Dan Mudah Dipahami',
      'Dosen Menyampaikan Materi Dengan Cara Yang Mudah Dipahami',
      'Dosen Menggunakan Bahasa Komunikasi Yang Sopan Dan Santun Selama Perkuliahan',
      'Dosen Memberikan Kesempatan Kepada Mahasiswa Bertanya Atau Menyampaikan Pendapat Dan Memberikan Tanggapan (feedback)',
      'Koordinasi Antar Dosen Dengan Tim Pengampu Matakuliah Lainnya (terkait Materi, Tugas, Kuis, UTS Dan UAS)',
      'Dosen Mengadakan Perkuliahan Pengganti Jika Jumlah Pertemuan Kurang Dari 14 Pertemuan',
      'Tugas Yang Diberikan Dosen Relevan Dan Menambah Pemahaman Mahasiswa Terhadap Materi',
      'Kesesuaian Soal UTS/UAS Dengan Materi Yang Disampaiakan',
    ];

    $options = [
      'Sangat Kurang',
      'Kurang Baik',
      'Cukup',
      'Baik',
      'Baik Sekali',
    ];

    foreach ($questions as $index => $questionText) {
      $question = Question::create([
        'question' => $questionText,
        'type' => 'option',
        'sequence' => $index + 1,
        'is_required' => true,
        'm_form_id' => $form->id,
      ]);

      foreach ($options as $indexOption => $optionText) {
        $question->options()->create([
          'answer' => $optionText,
          'sequence' => $indexOption + 1,
          'point' => $indexOption + 1,
        ]);
      }
    }
  }
}
