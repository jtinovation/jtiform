<?php

namespace Database\Seeders;

use App\Enums\FormTypeEnum;
use App\Models\Form;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Submission;
use App\Models\SubmissionTarget;
use App\Models\Answer;
use App\Models\AnswerOption;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
  public function run(): void
  {
    // 1. Buat Form Test
    $form = Form::create([
      'code' => 'TEST-FORM-001',
      'type' => FormTypeEnum::GENERAL,
      'title' => 'Form Test untuk Development',
      'description' => 'Form khusus untuk testing semua fitur',
      'start_at' => Carbon::now()->subDays(1),
      'end_at' => Carbon::now()->addDays(30),
      'is_active' => true,
      'cover_path' => null,
      'cover_file' => null,
    ]);

    // 2. Buat 10 Pertanyaan dengan semua jenis
    $questions = [];

    // Text Questions (4 pertanyaan)
    $questions[] = Question::create([
      'question' => 'Siapa nama lengkap Anda?',
      'type' => 'text',
      'sequence' => 1,
      'is_required' => true,
      'm_form_id' => $form->id,
    ]);

    $questions[] = Question::create([
      'question' => 'Apa alamat lengkap Anda?',
      'type' => 'text',
      'sequence' => 2,
      'is_required' => true,
      'm_form_id' => $form->id,
    ]);

    $questions[] = Question::create([
      'question' => 'Apa pekerjaan Anda saat ini?',
      'type' => 'text',
      'sequence' => 3,
      'is_required' => false,
      'm_form_id' => $form->id,
    ]);

    $questions[] = Question::create([
      'question' => 'Ceritakan pengalaman kerja Anda',
      'type' => 'text',
      'sequence' => 4,
      'is_required' => false,
      'm_form_id' => $form->id,
    ]);

    // Option Questions (3 pertanyaan)
    $genderQuestion = Question::create([
      'question' => 'Apa jenis kelamin Anda?',
      'type' => 'option',
      'sequence' => 5,
      'is_required' => true,
      'm_form_id' => $form->id,
    ]);

    $ageQuestion = Question::create([
      'question' => 'Berapa usia Anda?',
      'type' => 'option',
      'sequence' => 6,
      'is_required' => true,
      'm_form_id' => $form->id,
    ]);

    $educationQuestion = Question::create([
      'question' => 'Apa tingkat pendidikan terakhir Anda?',
      'type' => 'option',
      'sequence' => 7,
      'is_required' => true,
      'm_form_id' => $form->id,
    ]);

    // Checkbox Questions (3 pertanyaan)
    $skillQuestion = Question::create([
      'question' => 'Pilih keahlian yang Anda kuasai',
      'type' => 'checkbox',
      'sequence' => 8,
      'is_required' => false,
      'm_form_id' => $form->id,
    ]);

    $hobbyQuestion = Question::create([
      'question' => 'Apa hobi Anda? (boleh pilih lebih dari satu)',
      'type' => 'checkbox',
      'sequence' => 9,
      'is_required' => false,
      'm_form_id' => $form->id,
    ]);

    $languageQuestion = Question::create([
      'question' => 'Bahasa apa saja yang Anda kuasai?',
      'type' => 'checkbox',
      'sequence' => 10,
      'is_required' => false,
      'm_form_id' => $form->id,
    ]);

    // Copy 10 soal lagi (sequence 11-20)
    // Text Questions (4 pertanyaan lagi)
    $questions[] = Question::create([
      'question' => 'Apa nomor telepon Anda?',
      'type' => 'text',
      'sequence' => 11,
      'is_required' => true,
      'm_form_id' => $form->id,
    ]);

    $questions[] = Question::create([
      'question' => 'Apa email Anda?',
      'type' => 'text',
      'sequence' => 12,
      'is_required' => true,
      'm_form_id' => $form->id,
    ]);

    $questions[] = Question::create([
      'question' => 'Ceritakan tentang diri Anda',
      'type' => 'text',
      'sequence' => 13,
      'is_required' => false,
      'm_form_id' => $form->id,
    ]);

    $questions[] = Question::create([
      'question' => 'Apa rencana karier Anda?',
      'type' => 'text',
      'sequence' => 14,
      'is_required' => false,
      'm_form_id' => $form->id,
    ]);

    // Option Questions (3 pertanyaan lagi)
    $experienceQuestion = Question::create([
      'question' => 'Berapa lama pengalaman kerja Anda?',
      'type' => 'option',
      'sequence' => 15,
      'is_required' => true,
      'm_form_id' => $form->id,
    ]);

    $maritalQuestion = Question::create([
      'question' => 'Apa status pernikahan Anda?',
      'type' => 'option',
      'sequence' => 16,
      'is_required' => true,
      'm_form_id' => $form->id,
    ]);

    $satisfactionQuestion = Question::create([
      'question' => 'Seberapa puas Anda dengan layanan kami?',
      'type' => 'option',
      'sequence' => 17,
      'is_required' => true,
      'm_form_id' => $form->id,
    ]);

    // Checkbox Questions (3 pertanyaan lagi)
    $socialMediaQuestion = Question::create([
      'question' => 'Platform media sosial apa yang Anda gunakan?',
      'type' => 'checkbox',
      'sequence' => 18,
      'is_required' => false,
      'm_form_id' => $form->id,
    ]);

    $dayQuestion = Question::create([
      'question' => 'Hari apa saja Anda tersedia untuk meeting?',
      'type' => 'checkbox',
      'sequence' => 19,
      'is_required' => false,
      'm_form_id' => $form->id,
    ]);

    $topicQuestion = Question::create([
      'question' => 'Topik apa yang Anda minati?',
      'type' => 'checkbox',
      'sequence' => 20,
      'is_required' => false,
      'm_form_id' => $form->id,
    ]);

    // 3. Buat Question Options

    // Gender Options
    $genderOptions = [];
    $genderOptions[] = QuestionOption::create([
      'answer' => 'Laki-laki',
      'sequence' => 1,
      'point' => 0,
      'm_question_id' => $genderQuestion->id,
    ]);
    $genderOptions[] = QuestionOption::create([
      'answer' => 'Perempuan',
      'sequence' => 2,
      'point' => 0,
      'm_question_id' => $genderQuestion->id,
    ]);

    // Age Options
    $ageOptions = [];
    $ageOptions[] = QuestionOption::create([
      'answer' => '18-25 tahun',
      'sequence' => 1,
      'point' => 0, // Tambahkan field point
      'm_question_id' => $ageQuestion->id,
    ]);
    $ageOptions[] = QuestionOption::create([
      'answer' => '26-35 tahun',
      'sequence' => 2,
      'point' => 0, // Tambahkan field point
      'm_question_id' => $ageQuestion->id,
    ]);
    $ageOptions[] = QuestionOption::create([
      'answer' => '36-45 tahun',
      'sequence' => 3,
      'point' => 0, // Tambahkan field point
      'm_question_id' => $ageQuestion->id,
    ]);
    $ageOptions[] = QuestionOption::create([
      'answer' => '46-55 tahun',
      'sequence' => 4,
      'point' => 0, // Tambahkan field point
      'm_question_id' => $ageQuestion->id,
    ]);

    // Education Options
    $educationOptions = [];
    $educationOptions[] = QuestionOption::create([
      'answer' => 'SMA/Sederajat',
      'sequence' => 1,
      'point' => 0, // Tambahkan field point
      'm_question_id' => $educationQuestion->id,
    ]);
    $educationOptions[] = QuestionOption::create([
      'answer' => 'Diploma',
      'sequence' => 2,
      'point' => 0, // Tambahkan field point
      'm_question_id' => $educationQuestion->id,
    ]);
    $educationOptions[] = QuestionOption::create([
      'answer' => 'Sarjana (S1)',
      'sequence' => 3,
      'point' => 0, // Tambahkan field point
      'm_question_id' => $educationQuestion->id,
    ]);
    $educationOptions[] = QuestionOption::create([
      'answer' => 'Magister (S2)',
      'sequence' => 4,
      'point' => 0, // Tambahkan field point
      'm_question_id' => $educationQuestion->id,
    ]);

    // Skill Options
    $skillOptions = [];
    $skillOptions[] = QuestionOption::create([
      'answer' => 'PHP',
      'sequence' => 1,
      'point' => 0, // Tambahkan field point
      'm_question_id' => $skillQuestion->id,
    ]);
    $skillOptions[] = QuestionOption::create([
      'answer' => 'JavaScript',
      'sequence' => 2,
      'point' => 0, // Tambahkan field point
      'm_question_id' => $skillQuestion->id,
    ]);
    $skillOptions[] = QuestionOption::create([
      'answer' => 'Python',
      'sequence' => 3,
      'point' => 0, // Tambahkan field point
      'm_question_id' => $skillQuestion->id,
    ]);
    $skillOptions[] = QuestionOption::create([
      'answer' => 'Java',
      'sequence' => 4,
      'point' => 0, // Tambahkan field point
      'm_question_id' => $skillQuestion->id,
    ]);

    // Hobby Options
    $hobbyOptions = [];
    $hobbyOptions[] = QuestionOption::create([
      'answer' => 'Membaca',
      'sequence' => 1,
      'point' => 0, // Tambahkan field point
      'm_question_id' => $hobbyQuestion->id,
    ]);
    $hobbyOptions[] = QuestionOption::create([
      'answer' => 'Olahraga',
      'sequence' => 2,
      'point' => 0, // Tambahkan field point
      'm_question_id' => $hobbyQuestion->id,
    ]);
    $hobbyOptions[] = QuestionOption::create([
      'answer' => 'Musik',
      'sequence' => 3,
      'point' => 0, // Tambahkan field point
      'm_question_id' => $hobbyQuestion->id,
    ]);
    $hobbyOptions[] = QuestionOption::create([
      'answer' => 'Traveling',
      'sequence' => 4,
      'point' => 0, // Tambahkan field point
      'm_question_id' => $hobbyQuestion->id,
    ]);

    // Language Options
    $languageOptions = [];
    $languageOptions[] = QuestionOption::create([
      'answer' => 'Bahasa Indonesia',
      'sequence' => 1,
      'point' => 0, // Tambahkan field point
      'm_question_id' => $languageQuestion->id,
    ]);
    $languageOptions[] = QuestionOption::create([
      'answer' => 'Bahasa Inggris',
      'sequence' => 2,
      'point' => 0, // Tambahkan field point
      'm_question_id' => $languageQuestion->id,
    ]);
    $languageOptions[] = QuestionOption::create([
      'answer' => 'Bahasa Mandarin',
      'sequence' => 3,
      'point' => 0, // Tambahkan field point
      'm_question_id' => $languageQuestion->id,
    ]);
    $languageOptions[] = QuestionOption::create([
      'answer' => 'Bahasa Jepang',
      'sequence' => 4,
      'point' => 0, // Tambahkan field point
      'm_question_id' => $languageQuestion->id,
    ]);

    // Experience Options
    $experienceOptions = [];
    $experienceOptions[] = QuestionOption::create([
      'answer' => 'Kurang dari 1 tahun',
      'sequence' => 1,
      'point' => 0,
      'm_question_id' => $experienceQuestion->id,
    ]);
    $experienceOptions[] = QuestionOption::create([
      'answer' => '1-3 tahun',
      'sequence' => 2,
      'point' => 0,
      'm_question_id' => $experienceQuestion->id,
    ]);
    $experienceOptions[] = QuestionOption::create([
      'answer' => '4-7 tahun',
      'sequence' => 3,
      'point' => 0,
      'm_question_id' => $experienceQuestion->id,
    ]);
    $experienceOptions[] = QuestionOption::create([
      'answer' => 'Lebih dari 7 tahun',
      'sequence' => 4,
      'point' => 0,
      'm_question_id' => $experienceQuestion->id,
    ]);

    // Marital Status Options
    $maritalOptions = [];
    $maritalOptions[] = QuestionOption::create([
      'answer' => 'Belum menikah',
      'sequence' => 1,
      'point' => 0,
      'm_question_id' => $maritalQuestion->id,
    ]);
    $maritalOptions[] = QuestionOption::create([
      'answer' => 'Menikah',
      'sequence' => 2,
      'point' => 0,
      'm_question_id' => $maritalQuestion->id,
    ]);
    $maritalOptions[] = QuestionOption::create([
      'answer' => 'Cerai',
      'sequence' => 3,
      'point' => 0,
      'm_question_id' => $maritalQuestion->id,
    ]);

    // Satisfaction Options
    $satisfactionOptions = [];
    $satisfactionOptions[] = QuestionOption::create([
      'answer' => 'Sangat tidak puas',
      'sequence' => 1,
      'point' => 0,
      'm_question_id' => $satisfactionQuestion->id,
    ]);
    $satisfactionOptions[] = QuestionOption::create([
      'answer' => 'Tidak puas',
      'sequence' => 2,
      'point' => 0,
      'm_question_id' => $satisfactionQuestion->id,
    ]);
    $satisfactionOptions[] = QuestionOption::create([
      'answer' => 'Netral',
      'sequence' => 3,
      'point' => 0,
      'm_question_id' => $satisfactionQuestion->id,
    ]);
    $satisfactionOptions[] = QuestionOption::create([
      'answer' => 'Puas',
      'sequence' => 4,
      'point' => 0,
      'm_question_id' => $satisfactionQuestion->id,
    ]);
    $satisfactionOptions[] = QuestionOption::create([
      'answer' => 'Sangat puas',
      'sequence' => 5,
      'point' => 0,
      'm_question_id' => $satisfactionQuestion->id,
    ]);

    // Social Media Options
    $socialMediaOptions = [];
    $socialMediaOptions[] = QuestionOption::create([
      'answer' => 'Instagram',
      'sequence' => 1,
      'point' => 0,
      'm_question_id' => $socialMediaQuestion->id,
    ]);
    $socialMediaOptions[] = QuestionOption::create([
      'answer' => 'Facebook',
      'sequence' => 2,
      'point' => 0,
      'm_question_id' => $socialMediaQuestion->id,
    ]);
    $socialMediaOptions[] = QuestionOption::create([
      'answer' => 'Twitter',
      'sequence' => 3,
      'point' => 0,
      'm_question_id' => $socialMediaQuestion->id,
    ]);
    $socialMediaOptions[] = QuestionOption::create([
      'answer' => 'LinkedIn',
      'sequence' => 4,
      'point' => 0,
      'm_question_id' => $socialMediaQuestion->id,
    ]);
    $socialMediaOptions[] = QuestionOption::create([
      'answer' => 'TikTok',
      'sequence' => 5,
      'point' => 0,
      'm_question_id' => $socialMediaQuestion->id,
    ]);

    // Day Options
    $dayOptions = [];
    $dayOptions[] = QuestionOption::create([
      'answer' => 'Senin',
      'sequence' => 1,
      'point' => 0,
      'm_question_id' => $dayQuestion->id,
    ]);
    $dayOptions[] = QuestionOption::create([
      'answer' => 'Selasa',
      'sequence' => 2,
      'point' => 0,
      'm_question_id' => $dayQuestion->id,
    ]);
    $dayOptions[] = QuestionOption::create([
      'answer' => 'Rabu',
      'sequence' => 3,
      'point' => 0,
      'm_question_id' => $dayQuestion->id,
    ]);
    $dayOptions[] = QuestionOption::create([
      'answer' => 'Kamis',
      'sequence' => 4,
      'point' => 0,
      'm_question_id' => $dayQuestion->id,
    ]);
    $dayOptions[] = QuestionOption::create([
      'answer' => 'Jumat',
      'sequence' => 5,
      'point' => 0,
      'm_question_id' => $dayQuestion->id,
    ]);

    // Topic Options
    $topicOptions = [];
    $topicOptions[] = QuestionOption::create([
      'answer' => 'Web Development',
      'sequence' => 1,
      'point' => 0,
      'm_question_id' => $topicQuestion->id,
    ]);
    $topicOptions[] = QuestionOption::create([
      'answer' => 'Mobile Development',
      'sequence' => 2,
      'point' => 0,
      'm_question_id' => $topicQuestion->id,
    ]);
    $topicOptions[] = QuestionOption::create([
      'answer' => 'UI/UX Design',
      'sequence' => 3,
      'point' => 0,
      'm_question_id' => $topicQuestion->id,
    ]);
    $topicOptions[] = QuestionOption::create([
      'answer' => 'Data Science',
      'sequence' => 4,
      'point' => 0,
      'm_question_id' => $topicQuestion->id,
    ]);

    // 4. Ambil User pertama untuk test
    $user = User::first();

    if (!$user) {
      $this->command->error('Tidak ada user di database. Jalankan UserSeeder terlebih dahulu.');
      return;
    }

    // 5. Buat Submission
    $submission = Submission::create([
      'm_form_id' => $form->id,
      'm_user_id' => $user->id,
      'started_at' => Carbon::now()->subMinutes(10), // Field required
      'submitted_at' => Carbon::now(),
      'status' => 'completed', // Field required
      'is_anonymous' => false, // Field required
      'is_valid' => true, // Field required
      'meta_json' => '{}', // Field required
    ]);

    // 6. Buat Submission Target
    $submissionTarget = SubmissionTarget::create([
      't_submission_id' => $submission->id,
      'target_type' => 'user',
      'target_id' => $user->id,
      'relation_id' => null, // Field required
      'target_label' => $user->name,
      'context_json' => '{}', // Field required
    ]);

    // 7. Buat Answers untuk semua pertanyaan

    // Text Answers
    Answer::create([
      't_submission_target_id' => $submissionTarget->id,
      'm_question_id' => $questions[0]->id,
      'text_value' => 'John Doe Smith',
      'm_question_option_id' => null,
      'score' => 0, // Field required (decimal)
      'checked_at' => Carbon::now(),
    ]);

    Answer::create([
      't_submission_target_id' => $submissionTarget->id,
      'm_question_id' => $questions[1]->id,
      'text_value' => 'Jl. Merdeka No. 123, Jakarta Pusat',
      'm_question_option_id' => null,
      'score' => 0, // Field required (decimal)
      'checked_at' => Carbon::now(),
    ]);

    Answer::create([
      't_submission_target_id' => $submissionTarget->id,
      'm_question_id' => $questions[2]->id,
      'text_value' => 'Software Developer',
      'm_question_option_id' => null,
      'score' => 0, // Field required (decimal)
      'checked_at' => Carbon::now(),
    ]);

    Answer::create([
      't_submission_target_id' => $submissionTarget->id,
      'm_question_id' => $questions[3]->id,
      'text_value' => 'Saya telah bekerja sebagai programmer selama 3 tahun dengan pengalaman mengembangkan aplikasi web menggunakan Laravel dan React.',
      'm_question_option_id' => null,
      'score' => 0, // Field required (decimal)
      'checked_at' => Carbon::now(),
    ]);

    // Option Answers (Radio Button)
    Answer::create([
      't_submission_target_id' => $submissionTarget->id,
      'm_question_id' => $genderQuestion->id,
      'text_value' => null,
      'm_question_option_id' => $genderOptions[0]->id, // Laki-laki
      'score' => 0, // Field required (decimal)
      'checked_at' => Carbon::now(),
    ]);

    Answer::create([
      't_submission_target_id' => $submissionTarget->id,
      'm_question_id' => $ageQuestion->id,
      'text_value' => null,
      'm_question_option_id' => $ageOptions[1]->id, // 26-35 tahun
      'score' => 0, // Field required (decimal)
      'checked_at' => Carbon::now(),
    ]);

    Answer::create([
      't_submission_target_id' => $submissionTarget->id,
      'm_question_id' => $educationQuestion->id,
      'text_value' => null,
      'm_question_option_id' => $educationOptions[2]->id, // Sarjana (S1)
      'score' => 0, // Field required (decimal)
      'checked_at' => Carbon::now(),
    ]);

    // Text Answers untuk pertanyaan copy (4 text lagi)
    Answer::create([
      't_submission_target_id' => $submissionTarget->id,
      'm_question_id' => $questions[4]->id, // nomor telepon
      'text_value' => '081234567890',
      'm_question_option_id' => null,
      'score' => 0,
      'checked_at' => Carbon::now(),
    ]);

    Answer::create([
      't_submission_target_id' => $submissionTarget->id,
      'm_question_id' => $questions[5]->id, // email
      'text_value' => 'johndoe@example.com',
      'm_question_option_id' => null,
      'score' => 0,
      'checked_at' => Carbon::now(),
    ]);

    Answer::create([
      't_submission_target_id' => $submissionTarget->id,
      'm_question_id' => $questions[6]->id, // cerita diri
      'text_value' => 'Saya adalah seorang profesional yang antusias dan selalu ingin belajar hal baru.',
      'm_question_option_id' => null,
      'score' => 0,
      'checked_at' => Carbon::now(),
    ]);

    Answer::create([
      't_submission_target_id' => $submissionTarget->id,
      'm_question_id' => $questions[7]->id, // rencana karier
      'text_value' => 'Saya berencana untuk menjadi tech lead dalam 5 tahun ke depan dan berkontribusi pada inovasi teknologi.',
      'm_question_option_id' => null,
      'score' => 0,
      'checked_at' => Carbon::now(),
    ]);

    // Option Answers untuk pertanyaan copy (3 option lagi)
    Answer::create([
      't_submission_target_id' => $submissionTarget->id,
      'm_question_id' => $experienceQuestion->id,
      'text_value' => null,
      'm_question_option_id' => $experienceOptions[2]->id, // 4-7 tahun
      'score' => 0,
      'checked_at' => Carbon::now(),
    ]);

    Answer::create([
      't_submission_target_id' => $submissionTarget->id,
      'm_question_id' => $maritalQuestion->id,
      'text_value' => null,
      'm_question_option_id' => $maritalOptions[1]->id, // Menikah
      'score' => 0,
      'checked_at' => Carbon::now(),
    ]);

    Answer::create([
      't_submission_target_id' => $submissionTarget->id,
      'm_question_id' => $satisfactionQuestion->id,
      'text_value' => null,
      'm_question_option_id' => $satisfactionOptions[3]->id, // Puas
      'score' => 0,
      'checked_at' => Carbon::now(),
    ]);

    // Checkbox Answers (Multiple Choice)

    // Skills: PHP + JavaScript
    $skillAnswer = Answer::create([
      't_submission_target_id' => $submissionTarget->id,
      'm_question_id' => $skillQuestion->id,
      'text_value' => null,
      'm_question_option_id' => null,
      'score' => 0, // Field required (decimal)
      'checked_at' => Carbon::now(),
    ]);

    AnswerOption::create([
      't_answer_id' => $skillAnswer->id,
      'm_question_option_id' => $skillOptions[0]->id, // PHP
    ]);

    AnswerOption::create([
      't_answer_id' => $skillAnswer->id,
      'm_question_option_id' => $skillOptions[1]->id, // JavaScript
    ]);

    // Hobbies: Membaca + Musik + Traveling
    $hobbyAnswer = Answer::create([
      't_submission_target_id' => $submissionTarget->id,
      'm_question_id' => $hobbyQuestion->id,
      'text_value' => null,
      'm_question_option_id' => null,
      'score' => 0, // Field required (decimal)
      'checked_at' => Carbon::now(),
    ]);

    AnswerOption::create([
      't_answer_id' => $hobbyAnswer->id,
      'm_question_option_id' => $hobbyOptions[0]->id, // Membaca
    ]);

    AnswerOption::create([
      't_answer_id' => $hobbyAnswer->id,
      'm_question_option_id' => $hobbyOptions[2]->id, // Musik
    ]);

    AnswerOption::create([
      't_answer_id' => $hobbyAnswer->id,
      'm_question_option_id' => $hobbyOptions[3]->id, // Traveling
    ]);

    // Languages: Indonesia + English
    $languageAnswer = Answer::create([
      't_submission_target_id' => $submissionTarget->id,
      'm_question_id' => $languageQuestion->id,
      'text_value' => null,
      'm_question_option_id' => null,
      'score' => 0, // Field required (decimal)
      'checked_at' => Carbon::now(),
    ]);

    AnswerOption::create([
      't_answer_id' => $languageAnswer->id,
      'm_question_option_id' => $languageOptions[0]->id, // Bahasa Indonesia
    ]);

    AnswerOption::create([
      't_answer_id' => $languageAnswer->id,
      'm_question_option_id' => $languageOptions[1]->id, // Bahasa Inggris
    ]);

    // Checkbox Answers untuk pertanyaan copy (3 checkbox lagi)

    // Social Media: Instagram + LinkedIn
    $socialMediaAnswer = Answer::create([
      't_submission_target_id' => $submissionTarget->id,
      'm_question_id' => $socialMediaQuestion->id,
      'text_value' => null,
      'm_question_option_id' => null,
      'score' => 0,
      'checked_at' => Carbon::now(),
    ]);

    AnswerOption::create([
      't_answer_id' => $socialMediaAnswer->id,
      'm_question_option_id' => $socialMediaOptions[0]->id, // Instagram
    ]);

    AnswerOption::create([
      't_answer_id' => $socialMediaAnswer->id,
      'm_question_option_id' => $socialMediaOptions[3]->id, // LinkedIn
    ]);

    // Days: Senin + Rabu + Jumat
    $dayAnswer = Answer::create([
      't_submission_target_id' => $submissionTarget->id,
      'm_question_id' => $dayQuestion->id,
      'text_value' => null,
      'm_question_option_id' => null,
      'score' => 0,
      'checked_at' => Carbon::now(),
    ]);

    AnswerOption::create([
      't_answer_id' => $dayAnswer->id,
      'm_question_option_id' => $dayOptions[0]->id, // Senin
    ]);

    AnswerOption::create([
      't_answer_id' => $dayAnswer->id,
      'm_question_option_id' => $dayOptions[2]->id, // Rabu
    ]);

    AnswerOption::create([
      't_answer_id' => $dayAnswer->id,
      'm_question_option_id' => $dayOptions[4]->id, // Jumat
    ]);

    // Topics: Web Development + UI/UX Design
    $topicAnswer = Answer::create([
      't_submission_target_id' => $submissionTarget->id,
      'm_question_id' => $topicQuestion->id,
      'text_value' => null,
      'm_question_option_id' => null,
      'score' => 0,
      'checked_at' => Carbon::now(),
    ]);

    AnswerOption::create([
      't_answer_id' => $topicAnswer->id,
      'm_question_option_id' => $topicOptions[0]->id, // Web Development
    ]);

    AnswerOption::create([
      't_answer_id' => $topicAnswer->id,
      'm_question_option_id' => $topicOptions[2]->id, // UI/UX Design
    ]);

    $this->command->info('✅ Test data berhasil dibuat!');
    $this->command->info('📋 Form: ' . $form->title);
    $this->command->info('👤 User: ' . $user->name);
    $this->command->info('🔢 Questions: 20 pertanyaan (8 text, 6 option, 6 checkbox)');
  }
}
