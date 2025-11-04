<!DOCTYPE html>
<html>
<head>
    <title>Financial Report - Absolute Cinema</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .summary { margin: 20px 0; }
        .summary table { width: 100%; border-collapse: collapse; }
        .summary th, .summary td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .summary th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Absolute Cinema</h1>
        <h2>Financial Report</h2>
        <p>Period: {{ $period['start_date'] }} to {{ $period['end_date'] }}</p>
    </div>

    <div class="summary">
        <h3>Summary</h3>
        <table>
            <tr>
                <th>Description</th>
                <th>Amount</th>
            </tr>
            <tr>
                <td>Total Income</td>
                <td>Rp {{ number_format($summary['total_income'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Expenses</td>
                <td>Rp {{ number_format($summary['total_expenses'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Net Profit</strong></td>
                <td><strong>Rp {{ number_format($summary['net_profit'], 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>