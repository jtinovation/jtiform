@php
    use App\Enums\FormTypeEnum;
@endphp

<div class="col-12">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-2">Form Belum Diisi</h5>
            <div class="table-responsive text-nowrap my-3 mx-1">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Tipe</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach ($activeForm as $index => $form)
                            <tr>
                                <td>
                                    {{ $activeForm->firstItem() + $index }}
                                </td>
                                <td>
                                    {{ $form->code }}
                                </td>
                                <td>
                                    {{ $form->title }}
                                </td>
                                <td>
                                    {{ $form->type === FormTypeEnum::LECTURE_EVALUATION->value ? 'Evaluasi Dosen' : 'Umum' }}
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown"><i class="ri-more-2-line"></i></button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item"
                                                href="{{ route('form.fill', ['id' => $form->id]) }}"><i
                                                    class="ri-edit-line me-1"></i> Kerjakan Form</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        @if ($activeForm->isEmpty())
                            <tr>
                                <td colspan="5" class="text-center">No data available</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="col-12">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-2">Riwayat Pengisian</h5>

            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Tipe</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach ($submissions as $index => $submission)
                            <tr>
                                <td>
                                    {{ $submissions->firstItem() + $index }}
                                </td>
                                <td>
                                    {{ $submission->form->code }}
                                </td>
                                <td>
                                    {{ $submission->form->title }}
                                </td>
                                <td>
                                    {{ $submission->form->type === FormTypeEnum::LECTURE_EVALUATION->value ? 'Evaluasi Dosen' : 'Umum' }}
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown"><i class="ri-more-2-line"></i></button>
                                        <div class="dropdown-menu">
                                            @if ($submission->form->type === FormTypeEnum::GENERAL->value)
                                                <a class="dropdown-item"
                                                    href="{{ route('form.result', ['id' => $submission->form->id]) }}"><i
                                                        class="ri-edit-line me-1"></i> Lihat Detail</a>
                                            @else
                                                <a class="dropdown-item"
                                                    href="{{ route('form.result.evaluation', ['id' => $submission->form->id]) }}"><i
                                                        class="ri-edit-line me-1"></i> Lihat Detail</a>
                                            @endif
                                            <a class="dropdown-item"
                                                href="{{ route('form.proof.print', ['id' => $submission->id]) }}"
                                                target="_blank"><i class="ri-printer-line me-1"></i> Cetak Bukti</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        @if ($submissions->isEmpty())
                            <tr>
                                <td colspan="5" class="text-center">No data available</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
