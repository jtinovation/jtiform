@extends('layouts/contentNavbarLayout')

@section('title', 'Upload Tanda Tangan')

@section('vendor-script')
    @vite('resources/assets/vendor/libs/signature-pad/signature.js')
@endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">Upload Tanda Tangan</li>
        </ol>
    </nav>

    <div class="card mb-6">
        <ul class="nav nav-tabs">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#pane-upload">Upload
                    File</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#pane-draw">Tanda
                    Tangan</button></li>
        </ul>

        <div class="tab-content">
            {{-- ===== Upload File ===== --}}
            <div id="pane-upload" class="tab-pane fade show active">
                <form class="p-4" action="{{ route('signature.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Upload Tanda Tangan</label>
                        <input type="file" class="form-control" name="signature" accept=".png,.jpg,.jpeg,.gif,.svg"
                            required>
                        <div class="form-text">Disarankan PNG transparan / SVG.</div>
                    </div>
                    <button class="btn btn-primary">Simpan</button>
                </form>
            </div>

            {{-- ===== Tanda Tangan (Signature Pad) ===== --}}
            <div id="pane-draw" class="tab-pane fade">
                <form id="drawForm" class="p-4" action="{{ route('signature.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="signature_data" id="signature_data">
                    <div class="mb-2 d-flex align-items-center gap-2">
                        <label class="form-label mb-0">Tanda Tangani di area berikut</label>
                        <input id="penWidth" type="range" min="1" max="6" value="2" class="form-range"
                            style="width:120px">
                        <select id="exportFormat" class="form-select form-select-sm" style="width:auto">
                            <option value="png">PNG</option>
                            <option value="svg">SVG</option>
                        </select>
                    </div>
                    <div class="border bg-white rounded" style="height:260px;">
                        <canvas id="signatureCanvas" class="w-59 h-100" style="display:block;"></canvas>
                    </div>
                    <div class="mt-3 d-flex gap-2">
                        <button id="clearBtn" type="button" class="btn btn-outline-secondary">Clear</button>
                        <button id="saveDrawBtn" type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('page-script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function getCanvasBBox(canvas) {
                const ctx = canvas.getContext('2d');
                const {
                    width: W,
                    height: H
                } = canvas;
                const img = ctx.getImageData(0, 0, W, H).data;

                let minX = W,
                    minY = H,
                    maxX = -1,
                    maxY = -1;
                for (let y = 0; y < H; y++) {
                    for (let x = 0; x < W; x++) {
                        const a = img[(y * W + x) * 4 + 3]; // alpha channel
                        if (a !== 0) {
                            if (x < minX) minX = x;
                            if (y < minY) minY = y;
                            if (x > maxX) maxX = x;
                            if (y > maxY) maxY = y;
                        }
                    }
                }
                if (maxX === -1) return null; // kosong
                return {
                    minX,
                    minY,
                    maxX,
                    maxY,
                    width: maxX - minX + 1,
                    height: maxY - minY + 1
                };
            }

            /** Crop canvas sesuai bbox + padding (padding dalam CSS px, kita konversi ke pixel canvas/retina) */
            function cropCanvas(canvas, paddingCssPx = 8) {
                const bbox = getCanvasBBox(canvas);
                if (!bbox) return null;

                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                const pad = Math.round(paddingCssPx * ratio);

                const sx = Math.max(0, bbox.minX - pad);
                const sy = Math.max(0, bbox.minY - pad);
                const sw = Math.min(canvas.width - sx, bbox.width + pad * 2);
                const sh = Math.min(canvas.height - sy, bbox.height + pad * 2);

                const out = document.createElement('canvas');
                out.width = sw;
                out.height = sh;

                const octx = out.getContext('2d');
                // background transparan by default
                octx.drawImage(canvas, sx, sy, sw, sh, 0, 0, sw, sh);

                // Set ukuran CSS (biar preview tidak “raksasa”)
                out.style.width = Math.round(sw / ratio) + 'px';
                out.style.height = Math.round(sh / ratio) + 'px';
                return out;
            }

            /** Hitung bbox berbasis stroke data (untuk SVG viewBox) */
            function getStrokeBBox(signaturePad) {
                const data = signaturePad.toData();
                if (!data || !data.length) return null;

                let minX = Infinity,
                    minY = Infinity,
                    maxX = -Infinity,
                    maxY = -Infinity;

                data.forEach(stroke => {
                    stroke.points.forEach(p => {
                        if (p.x < minX) minX = p.x;
                        if (p.y < minY) minY = p.y;
                        if (p.x > maxX) maxX = p.x;
                        if (p.y > maxY) maxY = p.y;
                    });
                });

                if (!isFinite(minX)) return null;

                return {
                    minX,
                    minY,
                    maxX,
                    maxY,
                    width: maxX - minX,
                    height: maxY - minY
                };
            }

            /** Sesuaikan viewBox & ukuran pada SVG agar viewport pas ke tanda tangan */
            function cropSvgDataUrl(svgDataURL, bbox, paddingCssPx = 8) {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                const pad = paddingCssPx; // untuk SVG, padding di CSS px (bukan pixel canvas)
                // decode dataURL -> string SVG
                const raw = atob(svgDataURL.split(',')[1]);
                const parser = new DOMParser();
                const doc = parser.parseFromString(raw, 'image/svg+xml');
                const svg = doc.documentElement;

                // Dimensi hasil (pakai unit px viewport)
                const vbX = Math.max(0, bbox.minX - pad);
                const vbY = Math.max(0, bbox.minY - pad);
                const vbW = Math.max(1, bbox.width + pad * 2);
                const vbH = Math.max(1, bbox.height + pad * 2);

                // set viewBox, width, height
                svg.setAttribute('viewBox', `${vbX} ${vbY} ${vbW} ${vbH}`);
                svg.setAttribute('width', `${vbW}px`);
                svg.setAttribute('height', `${vbH}px`);

                // serialize kembali ke dataURL
                const serializer = new XMLSerializer();
                const updated = serializer.serializeToString(svg);
                return 'data:image/svg+xml;base64,' + btoa(updated);
            }

            const canvas = document.getElementById('signatureCanvas');
            if (!canvas) return;
            const signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgba(255,255,255,0)',
                penColor: '#000'
            });

            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                const parent = canvas.parentElement;
                const w = parent.clientWidth,
                    h = parent.clientHeight;
                const data = signaturePad.toData();
                canvas.width = Math.floor(w * ratio);
                canvas.height = Math.floor(h * ratio);
                canvas.style.width = w + 'px';
                canvas.style.height = h + 'px';
                const ctx = canvas.getContext('2d');
                ctx.scale(ratio, ratio);
                signaturePad.clear();
                signaturePad.fromData(data);
            }
            window.addEventListener('resize', resizeCanvas);
            document.querySelector('[data-bs-target="#pane-draw"]').addEventListener('shown.bs.tab', () => {
                setTimeout(() => {
                    resizeCanvas();
                    signaturePad.clear();
                }, 50);
            });

            document.getElementById('clearBtn').addEventListener('click', () => signaturePad.clear());
            document.getElementById('penWidth').addEventListener('input', e => {
                const v = Number(e.target.value || 2);
                signaturePad.minWidth = Math.max(0.5, v - 0.5);
                signaturePad.maxWidth = v;
            });

            document.getElementById('drawForm').addEventListener('submit', (e) => {
                if (signaturePad.isEmpty()) {
                    e.preventDefault();
                    alert('Tanda tangan masih kosong.');
                    return;
                }

                const fmt = (document.getElementById('exportFormat').value || 'png').toLowerCase();

                if (fmt === 'svg') {
                    // 1) ambil SVG default
                    let svgDataURL = signaturePad.toDataURL('image/svg+xml');
                    // 2) hitung bbox dari stroke lalu sesuaikan viewBox agar rapat
                    const bbox = getStrokeBBox(signaturePad);
                    if (bbox) {
                        svgDataURL = cropSvgDataUrl(svgDataURL, bbox, 8); // padding 8px
                    }
                    document.getElementById('signature_data').value = svgDataURL;
                } else {
                    // PNG: crop via pixel alpha
                    const cropped = cropCanvas(signatureCanvas, 8); // padding 8px
                    const pngDataURL = (cropped ?? signatureCanvas).toDataURL('image/png');
                    document.getElementById('signature_data').value = pngDataURL;
                }
            });
        });
    </script>
@endpush
