<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan Absolute Cinema</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10px; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #333; padding-bottom: 15px; }
        .header h1 { font-size: 20px; color: #1a56db; margin-bottom: 5px; }
        .header h2 { font-size: 14px; color: #666; margin-bottom: 10px; }
        .period { font-size: 11px; color: #333; font-weight: bold; }
        
        .summary-cards { display: table; width: 100%; margin-bottom: 20px; }
        .card { display: table-cell; width: 33.33%; padding: 10px; }
        .card-inner { border: 2px solid #e5e7eb; border-radius: 8px; padding: 12px; text-align: center; }
        .card-title { font-size: 9px; color: #6b7280; text-transform: uppercase; margin-bottom: 5px; }
        .card-value { font-size: 14px; font-weight: bold; }
        .card-income { border-color: #10b981; }
        .card-income .card-value { color: #10b981; }
        .card-expense { border-color: #ef4444; }
        .card-expense .card-value { color: #ef4444; }
        .card-profit { border-color: #3b82f6; }
        .card-profit .card-value { color: #3b82f6; }
        
        .section { margin-bottom: 25px; }
        .section-title { font-size: 13px; font-weight: bold; color: #1f2937; margin-bottom: 10px; padding-bottom: 5px; border-bottom: 2px solid #e5e7eb; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f3f4f6; color: #374151; font-weight: bold; padding: 8px; text-align: left; border: 1px solid #d1d5db; font-size: 10px; }
        td { padding: 6px 8px; border: 1px solid #e5e7eb; font-size: 9px; }
        tr:nth-child(even) { background-color: #f9fafb; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-mono { font-family: 'Courier New', monospace; }
        
        .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 8px; color: #6b7280; }
        
        .stats { margin-bottom: 15px; font-size: 10px; }
        .stats span { margin-right: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎬 ABSOLUTE CINEMA</h1>
        <h2>Laporan Keuangan</h2>
        <div class="period">Periode: {{ $period['start_date'] }} - {{ $period['end_date'] }}</div>
    </div>

    <div class="summary-cards">
        <div class="card">
            <div class="card-inner card-income">
                <div class="card-title">Total Pemasukan</div>
                <div class="card-value">Rp {{ number_format($summary['total_income'], 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="card">
            <div class="card-inner card-expense">
                <div class="card-title">Total Pengeluaran</div>
                <div class="card-value">Rp {{ number_format($summary['total_expenses'], 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="card">
            <div class="card-inner card-profit">
                <div class="card-title">Keuntungan Bersih</div>
                <div class="card-value">Rp {{ number_format($summary['net_profit'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <div class="stats">
        <span><strong>Total Transaksi:</strong> {{ $summary['total_transactions'] }}</span>
        <span><strong>Rata-rata per Transaksi:</strong> Rp {{ $summary['total_transactions'] > 0 ? number_format($summary['total_income'] / $summary['total_transactions'], 0, ',', '.') : 0 }}</span>
    </div>

    @if(isset($transactions) && count($transactions) > 0)
    <div class="section">
        <div class="section-title">📝 Detail Transaksi</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">No. Order</th>
                    <th style="width: 18%;">Customer</th>
                    <th style="width: 20%;">Film</th>
                    <th style="width: 12%;">Kursi</th>
                    <th style="width: 18%;" class="text-right">Total</th>
                    <th style="width: 12%;">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $transaction)
                <tr>
                    <td class="text-mono">{{ $transaction['order_number'] }}</td>
                    <td>{{ $transaction['customer_name'] }}</td>
                    <td>{{ $transaction['film_title'] }}</td>
                    <td class="text-center">{{ $transaction['seats'] }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($transaction['total_amount'], 0, ',', '.') }}</td>
                    <td>{{ $transaction['created_at'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p><strong>Absolute Cinema</strong> - Sistem Manajemen Bioskop</p>
        <p>Laporan digenerate pada: {{ $generated_at }}</p>
        <p>Dokumen ini bersifat rahasia dan hanya untuk keperluan internal</p>
    </div>
</body>
</html>