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
            <div class="card-header">
                <h6 class="mb-0">Detail Split Rule</h6>
            </div>
            <div class="card-content">
                <div class="card-body" id="split-rule-detail">
                    <p class="text-muted">Klik salah satu split rule untuk melihat detailnya.</p>
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
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
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
                <h6 class="fw-bold">${rule.name ?? 'Nama Tidak Ada'}</h6>
                <p class="text-muted small">${rule.description ?? '-'}</p>
                <p><strong>Split Rule ID:</strong><br>${rule.split_rule_id ?? '-'}</p>
                <p><strong>Tanggal Dibuat:</strong><br>${formatDate(rule.created_at)}</p>
                <hr>
                <h6 class="fw-bold">Route Detail</h6>
            `;

            if (rule.routes && rule.routes.length > 0) {
                rule.routes.forEach((r, i) => {
                    html += `
                        <div class="mb-3">
                            <strong>Route ${i + 1}</strong><br>
                            Split Amount: <strong>${r.flet_amount != null ? formatRupiah(r.flet_amount) : `${r.percent_amount}%`}</strong> <br>
                            Reference: ${r.reference ?? '-'} <br>
                            Account ID: ${r.destination_account_id ?? '-'} <br>
                            Destination Name: ${r.destination_account_name ?? '-'}
                        </div>
                        ${i < rule.routes.length - 1 ? '<hr class="my-2">' : ''}
                    `;
                });
            } else {
                html += `<p class="text-muted">Tidak ada route detail</p>`;
            }

            detailBox.innerHTML = html;

        } catch (e) {
            console.error("Error parsing JSON data:", e);
            detailBox.innerHTML = `<p class="text-danger">Gagal memuat detail. Pastikan format data valid.</p>`;
        }
    }
</script>