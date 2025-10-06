@php
    // Variabel ini akan kita gunakan di seluruh template
    $courses = $report->report_details;
    $lecturerName = $report->user->name ?? 'Nama Dosen Tidak Tersedia';

    // Membuat collection dari courses dan membaginya menjadi beberapa halaman (5 mata kuliah per halaman)
    $pagedCourses = collect($courses)->chunk(5);
    $totalPages = $pagedCourses->count();
@endphp

<html>

<head>
    <style>
        /** Define the margins of your page **/
        @page {
            margin: 150px 50px 10px 50px;
        }

        header {
            position: fixed;
            width: 100%;
            top: -130px;
            left: 0px;
            right: 0px;
            height: 130px;

            /** Extra personal styles **/
            background-color: rgb(255, 255, 255);
            color: black;
            text-align: center;
            line-height: 35px;

            border-bottom-width: 2px;
            border-bottom-style: solid;
            border-bottom-color: rgb(0, 0, 0);
        }

        .column {
            float: left;
            width: 60%;
            font-size: 12px;
            line-height: 15px;
        }

        .space {
            float: left;
            width: 5%;
        }

        .ttd {
            float: left;
            width: 35%;
            font-size: 12px;
            line-height: 15px;
        }

        /* Clear floats after the columns */
        .row:after {
            clear: both;
        }

        table,
        th,
        td {
            border: 1px solid black;
            border-collapse: collapse;
            font-size: 12px;
        }

        .tdCenter {
            text-align: center;
        }
    </style>
</head>

<body>
    <header>
        <div style="float: left; margin-bottom:0px;">
            <img src="{{ public_path('assets/img/logo/polije.png') }}" style="width: 120px; height: 120px">
        </div>
        <div style="float: left; padding-left:20px; margin-top:-10px;">
            <p style="text-align: center; margin-bottom: 1px;line-height: 5px;font-size:18px;">
                KEMENTERIAN PENDIDIKAN TINGGI,
            </p>
            <p style="text-align: center; margin-bottom: 1px;line-height: 5px;font-size:18px;">
                SAINS, DAN TEKNOLOGI
            </p>
            <p style="text-align: center; margin-bottom: 1px;line-height: 5px;font-size:16px; font-weight: bold;">
                POLITEKNIK NEGERI JEMBER
            </p>
            <p style="text-align: center; margin-bottom: px;line-height: 5px;font-size:16px;">
                JURUSAN TEKNOLOGI INFORMASI
            </p>
            <p style="text-align: center; margin-bottom: 1px;line-height: 5px;font-size:16px;">
                Jalan Mastrip Kotak Pos 164 Jember Telp. (0331) 33532-34; Fax. (0331) 333531
            </p>
            <p style="text-align: center; margin-bottom: 0px;line-height: 5px;font-size:16px;">
                Email: politeknik@polije.ac.id; Laman: www.polije.ac.id
            </p>
        </div>
        </br>
    </header>
    <footer></footer>

    <main style="margin: 0px 10px 0px 10px;">

        @foreach ($pagedCourses as $coursesOnPage)
            @if ($loop->last && $totalPages > 1)
                <div style="page-break-before: always;">
            @endif
            <p style="margin-top: 20px; font-size: 12px;">
                <strong>Nama: {{ $lecturerName }}</strong>
            </p>
            <p style="margin-top: -10px; font-size: 10px;">
                <strong>Hal {{ $loop->iteration }} dari {{ $totalPages }}</strong>
            </p>

            <table width="100%">
                <thead>
                    <tr>
                        <th rowspan="2" width="5px">No</th>
                        <th rowspan="2" width="35%" style="vertical-align: middle; text-align:center;">Pertanyaan
                        </th>
                        <th colspan="{{ $coursesOnPage->count() }}" style="vertical-align: middle; text-align:center;">
                            Matakuliah</th>
                    </tr>
                    <tr>
                        @foreach ($coursesOnPage as $course)
                            <th width="{{ 60 / $coursesOnPage->count() }}">{{ $course['class'] }} <br>
                                {{ $course['course_code'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($report->form->questions as $question)
                        <tr>
                            <td class="tdCenter" width="5%">{{ $question->sequence }}</td>
                            <td width="35%">{{ $question->question }}</td>
                            @foreach ($coursesOnPage as $course)
                                @php
                                    $scoresByQuestionId = collect($course['scores'])->keyBy('question_id');
                                    $score = $scoresByQuestionId[$question->id]['score'] ?? 0;
                                @endphp
                                <td class="tdCenter">{{ number_format($score, 2) }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                    <tr>
                        <td rowspan="2"></td>
                        <td><strong>Jumlah Responden</strong></td>
                        @foreach ($coursesOnPage as $course)
                            <td class="tdCenter" style="font-weight: bold;">{{ $course['respondents'] }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td><strong>Rata - Rata Nilai Per-MataKuliah</strong></td>
                        @foreach ($coursesOnPage as $course)
                            <td class="tdCenter" style="font-weight: bold;">
                                {{ number_format($course['average_score'], 2) }}</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>

            {{-- Bagian Keterangan dan TTD hanya akan muncul di halaman terakhir --}}
            @if ($loop->last)
                <div style="width: 100%; height:30px; margin-top:10px;">
                    <div style="float: left; width: 40%; font-size: 12px;line-height: 15px;">
                        <span>Rata-Rata Nilai Keseluruhan Dosen (NKD)</span><br />
                        <span>Predikat</span>
                    </div>
                    <div style="float: left; width: 20%; font-size: 12px;line-height: 15px;">
                        <span>:
                            <strong>{{ number_format($report->overall_average_score, 2) }}</strong></span><br>
                        <span>: <strong>{{ $report->predicate }}</strong></span><br>
                    </div>
                </div>
                <br>
                <div style="width: 100%;">
                    <div class="column">
                        <span><strong>Keterangan:</strong></span><br />
                        @foreach ($courses as $course)
                            <span>{{ $course['class'] }} {{ $course['course_code'] }} :
                                {{ $course['course_name'] }}</span> <br>
                        @endforeach
                    </div>
                    <div class="space"></div>
                    <div class="ttd">
                        <span>Jember,
                            {{ \Carbon\Carbon::now()->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d F Y') }}</span><br />
                        <span>Ketua Jurusan</span><br />
                        <span>Teknologi Informasi</span>
                        <div style="height: 70px;">
                            {{-- Letakkan gambar ttd & stempel di sini jika ada --}}
                        </div>
                        <span><strong><u>Hendra Yufit Riskiawan, S.Kom, M.Cs</u></strong></span><br />
                        <span>NIP. 19830203 200604 1 003</span><br />
                    </div>
                </div>
            @endif

            @if ($loop->last && $totalPages > 1)
                {{-- penutup div <div style="page-break-before: always;"> --}}
                </div>
            @endif

            {{-- Jika ini bukan halaman terakhir, tambahkan page break --}}
            @if (!$loop->last)
                <div class="page-break"></div>
            @endif
        @endforeach
    </main>
</body>

</html>
