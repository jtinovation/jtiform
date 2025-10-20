<div class="col-12 col-sm-6 col-lg-3">
    <div class="card card-border-shadow-primary">
        <div class="card-body">
            <div class="d-flex align-items-center mb-2">
                <div class="avatar me-4">
                    <span class="avatar-initial rounded bg-label-primary">
                        <i class="icon-base ri ri-file-list-3-line icon-24px"></i>
                    </span>
                </div>
                <h4 class="mb-0">{{ $kpis['active_forms']['now'] }}</h4>
            </div>
            <h6 class="mb-0 fw-normal">Form Aktif</h6>
            <p class="mb-0">
                <span class="me-1 fw-medium">
                    {!! trendBadge($kpis['active_forms']['trend'] ?? 0) !!}
                </span>
                <small class="text-body-secondary">vs semester lalu</small>
            </p>
        </div>
    </div>
</div>

<div class="col-12 col-sm-6 col-lg-3">
    <div class="card card-border-shadow-primary">
        <div class="card-body">
            <div class="d-flex align-items-center mb-2">
                <div class="avatar me-4">
                    <span class="avatar-initial rounded bg-label-primary">
                        <i class="icon-base ri ri-user-3-line icon-24px"></i>
                    </span>
                </div>
                <h4 class="mb-0">{{ $kpis['valid_submissions']['now'] }}</h4>
            </div>
            <h6 class="mb-0 fw-normal">Total Responden Valid</h6>
            <p class="mb-0">
                <span class="me-1 fw-medium">
                    {!! trendBadge($kpis['valid_submissions']['trend'] ?? 0) !!}
                </span>
                <small class="text-body-secondary">vs semester lalu</small>
            </p>
        </div>
    </div>
</div>

<div class="col-12 col-sm-6 col-lg-3">
    <div class="card card-border-shadow-primary">
        <div class="card-body">
            <div class="d-flex align-items-center mb-2">
                <div class="avatar me-4">
                    <span class="avatar-initial rounded bg-label-primary">
                        <i class="icon-base ri ri-star-line icon-24px"></i>
                    </span>
                </div>
                <h4 class="mb-0">{{ $kpis['avg_score']['now'] }}</h4>
            </div>
            <h6 class="mb-0 fw-normal">Rata-rata Skor Dosen</h6>
            <p class="mb-0">
                <span class="me-1 fw-medium">
                    {!! trendBadge($kpis['avg_score']['trend'] ?? 0) !!}
                </span>
                <small class="text-body-secondary">vs semester lalu</small>
            </p>
        </div>
    </div>
</div>

<div class="col-12 col-sm-6 col-lg-3">
    <div class="card card-border-shadow-primary">
        <div class="card-body">
            <div class="d-flex align-items-center mb-2">
                <div class="avatar me-4">
                    <span class="avatar-initial rounded bg-label-primary">
                        <i class="icon-base ri ri-pie-chart-2-line icon-24px"></i>
                    </span>
                </div>
                <h4 class="mb-0">{{ $kpis['participation_pct']['now'] }}</h4>
            </div>
            <h6 class="mb-0 fw-normal">Partisipasi (%)</h6>
            <p class="mb-0">
                <span class="me-1 fw-medium">
                    {!! trendBadge($kpis['participation_pct']['trend'] ?? 0) !!}
                </span>
                <small class="text-body-secondary">vs semester lalu</small>
            </p>
        </div>
    </div>
</div>
