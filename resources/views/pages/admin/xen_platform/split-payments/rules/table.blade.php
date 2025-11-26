<div class="row">
    <div class="col-lg-9">
        <div class="card border shadow-sm">
            <div class="card-content mt-2">
                <div class="card-body">
                    <table class="table table-striped table-hover" id="table-split-rules">
                        <thead class="thead-dark">
                        <tr>
                            <th class="w-2p">No</th>
                            <th class="w-20p">Business Name</th>
                            <th class="w-20p">Split Rule Name</th>
                            <th class="w-15p">Split Rule ID</th>
                            <th class="w-10p">Date Created <br> <span>(GMT +7)</span></th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($splitRules as $rule)
                            <tr class="split-row"
                                data-rule='@json($rule)'
                                onclick="showSplitDetail(this)">
                                <td>{{ $splitRules->firstItem() + $loop->index }}</td>
                                <td>{{ $rule['business_name'] }}</td>
                                <td>{{ $rule['name'] }}</td>
                                <td>{{ $rule['split_rule_id'] }}</td>
                                <td>{{ $rule['created_at'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    <i class="bx bx-info-circle"></i> Tidak ada data split payment yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-between align-items-center mt-1">
                        @if ($splitRules->total() > 0)
                            <div class="pagination-summary text-muted">
                                Showing {{ $splitRules->firstItem() }} - {{ $splitRules->lastItem() }} from {{ $splitRules->total() }} split payment
                            </div>
                        @endif
                            <div class="pagination-links">
                                {{ $splitRules->appends(request()->query())->links('vendor.pagination.custom-limited') }}
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border shadow-sm">
            <div class="card-header border-bottom">
                <h6 class="mb-0 text-secondary text-bold-700">Detail Split Rule</h6>
            </div>
            <div class="card-content">
                <div class="card-body" id="split-rule-detail">
                    <div class="text-center py-3">
                        <i class="bx bx-info-circle text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mb-0">Klik salah satu split rule untuk melihat detailnya.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const formatRupiah = (number) => {
        if (number === undefined || number === null || number === '') return '-';
        const num = parseFloat(number);
        if (isNaN(num)) return '-';

        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(num);
    };

    const formatDate = (dateString) => {
        if (!dateString) return '-';
        try {
            const date = new Date(dateString);
            return new Intl.DateTimeFormat('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: 'numeric',
                minute: 'numeric',
                hour12: true,
                timeZone: 'Asia/Jakarta'
            }).format(date);
        } catch (e) {
            return dateString;
        }
    };

    function showSplitDetail(row) {
        const detailBox = document.getElementById("split-rule-detail");
        const allRows = document.querySelectorAll(".split-row");

        allRows.forEach(r => r.classList.remove('table-primary'));
        row.classList.add('table-primary');

        try {
            const rule = JSON.parse(row.dataset.rule);

            let html = `
                <div class="mt-2">
                    <h6 class="text-primary text-bold-500">${rule.name ?? 'Nama Tidak Ada'}</h6>
                    <p class="text-muted small">${rule.description ?? '-'}</p>

                    <div class="mb-1">
                        <label class="text-muted small">Split Rule ID</label>
                        <p class="text-bold-500 mb-0">${rule.split_rule_id ?? '-'}</p>
                    </div>

                    <div class="mb-1">
                        <label class="text-muted small">Tanggal Dibuat</label>
                        <p class="text-bold-500 mb-0">${formatDate(rule.created_at)}</p>
                    </div>
                </div>

                <div class="border-top">
                    <h6 class="text-primary text-bold-500 mt-2 mb-1">Route Detail</h6>
            `;

            if (rule.routes && rule.routes.length > 0) {
                rule.routes.forEach((r, i) => {
                    html += `
                        <div class="rounded p-2 border-4 border mb-3">
                            <h6 class="text-bold-500 mb-2">Route ${i + 1}</h6>
                            <div class="mb-1">
                                <label class="text-muted small">Split Amount</label>
                                <p class="text-bold-500 mb-0">${r.flet_amount != null ? formatRupiah(r.flet_amount) : `${r.percent_amount}%`}</p>
                            </div>
                            <div class="mb-1">
                                <label class="text-muted small">Reference</label>
                                <p class="text-bold-500 mb-0">${r.reference ?? '-'}</p>
                            </div>
                            <div class="mb-1">
                                <label class="text-muted small1">Account ID</label>
                                <p class="text-bold-500 mb-0">${r.destination_account_id ?? '-'}</p>
                            </div>
                            <div>
                                <label class="text-muted small">Destination Name</label>
                                <p class="text-bold-500 mb-0">${r.destination_account_name ?? '-'}</p>
                            </div>
                        </div>
                    `;
                });
            } else {
                html += `<div class="text-center py-3">
                    <i class="bx bx-info-circle text-muted" style="font-size: 1.5rem;"></i>
                    <p class="text-muted mb-0">Tidak ada route detail</p>
                </div>`;
            }

            html += `</div>`;
            detailBox.innerHTML = html;

        } catch (e) {
            console.error("Error parsing JSON data:", e);
            detailBox.innerHTML = `
                <div class="text-center py-3">
                    <i class="bx bx-error-circle text-danger" style="font-size: 2rem;"></i>
                    <p class="text-danger mb-0">Gagal memuat detail. Pastikan format data valid.</p>
                </div>`;
        }
    }
</script>