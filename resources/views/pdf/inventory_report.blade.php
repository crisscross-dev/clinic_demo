<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Inventory Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Header Styles */
        table {
            border-collapse: collapse;
        }

        .header-table {
            width: 100%;
            margin-bottom: 5px;
        }

        .header-table td {
            vertical-align: middle;
        }

        .header-logo {
            width: 20%;
            text-align: center;
        }

        .header-logo img {
            width: 80px;
            height: auto;
        }

        .header-info {
            width: 60%;
            text-align: center;
        }

        .school-name {
            font-size: 14pt;
            font-weight: bold;
            color: #2c5aa0;
            margin: 0;
            padding: 0;
        }

        .school-address {
            font-size: 9pt;
            color: #2c5aa0;
            margin: 2px 0;
        }

        .school-unit {
            font-size: 10pt;
            font-weight: bold;
            color: #2c5aa0;
            margin: 5px 0 2px 0;
        }

        .header-spacer {
            width: 20%;
        }

        .date-submitted {
            text-align: right;
            font-size: 9pt;
            color: #555;
            margin: 10px 0 15px;
        }

        .divider {
            border: none;
            border-top: 3px solid #2c5aa0;
            margin: 8px 0 10px 0;
        }

        .box {
            border: 2px solid #2c5aa0;
            padding: 10px;
            margin-bottom: 12px;
        }

        .box-title {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 8px;
            border-bottom: 2px solid #2c5aa0;
            padding-bottom: 4px;
            color: #2c5aa0;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
            table-layout: fixed;
        }

        .report-table td {
            padding: 6px 8px;
            border: 1px solid #cddff5;
            vertical-align: top;
            word-wrap: break-word;
            word-break: break-word;
            overflow-wrap: break-word;
        }

        .report-table td.label {
            font-weight: 700;
            background-color: #cee7f8ff;
            color: #1e5799;
        }

        .title-report {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            color: #2c5aa0;
            margin: 15px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .title-report h1 {
            font-size: 14pt;
            font-weight: bold;
            color: #2c5aa0;
            margin: 5px 0;
        }

        .title-report h3 {
            font-size: 11pt;
            font-weight: bold;
            color: #2c5aa0;
            margin: 5px 0;
        }

        .section {
            page-break-inside: avoid;
        }
    </style>

</head>

<body>
    <!-- Header -->
    <table class="header-table">
        <tr>
            <td class="header-logo">
                <img src="{{ $logoUrl }}" alt="Logo" style="width: 100px; height: auto;">
            </td>
            <td class="header-info">
                <div class="school-name">CLINIC SYSTEM DEMO</div>
                <div class="school-address">ADDRESS OF THE CLINIC</div>
                <div class="school-unit">MEDICAL SERVICE UNIT (DEMO) </div>
            </td>
            <td class="header-spacer"></td>
        </tr>
    </table>

    <div style="text-align: center; border-top: 2px solid #207cca;"></div>
    <div class="date-submitted">
        <div><strong>Generated:</strong> {{ $generatedAt }}</div>
    </div>
    <div class="title-report">
        <h1>INVENTORY REPORT</h1>
        <h3>{{ $rangeLabel }}</h3>
    </div>

    <!-- Inventory Report: Used Items -->
    @if($includeUsed)
    <div class="section">
        <div class="box">
            <div class="box-title">USED ITEMS — DEDUCTED</div>
            <table class="report-table">
                <tr>
                    <td class="label" style="width:55%">Item Name</td>
                    <td class="label" style="width:22.5%">Total Deducted</td>
                    <td class="label" style="width:22.5%">Remaining</td>
                </tr>
                @forelse($usedRows as $row)
                <tr>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['total_deducted'] }}</td>
                    <td>{{ $row['remaining'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align:center">No items were deducted in the selected range.</td>
                </tr>
                @endforelse
            </table>
        </div>
    </div>
    @endif

    <!-- Inventory Report: Restocked Items -->
    @if($includeRestock ?? true)
    <div class="section">
        <div class="box">
            <div class="box-title">RESTOCKED — ITEMS</div>
            <table class="report-table">
                <tr>
                    <td class="label" style="width:55%">Item Name</td>
                    <td class="label" style="width:22.5%">Total Restocked</td>
                    <td class="label" style="width:22.5%">Current Stock</td>
                </tr>
                @forelse($restockRows as $row)
                <tr>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['total_restocked'] }}</td>
                    <td>{{ $row['remaining'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align:center">No items were restocked in the selected range.</td>
                </tr>
                @endforelse
            </table>
        </div>
    </div>
    @endif

    <!-- Inventory Report: Least Usage (No deductions) -->
    @if($includeUnused)
    <div class="section">
        <div class="box">
            <div class="box-title">NOT USED — NO DEDUCTIONS ITEMS</div>
            <table class="report-table">
                <tr>
                    <td class="label" style="width:70%">Item Name</td>
                    <td class="label" style="width:30%">Remaining</td>
                </tr>
                @forelse($unusedRows as $row)
                <tr>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['remaining'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" style="text-align:center">All items had some usage in the selected range.</td>
                </tr>
                @endforelse
            </table>
        </div>
    </div>
    @endif

    <!-- Out of Stock List -->
    @if($includeOut)
    <div class="section">
        <div class="box">
            <div class="box-title">OUT OF STOCK — ITEMS</div>
            <table class="report-table">
                <tr>
                    <td class="label" style="width:70%">Item Name</td>
                    <td class="label" style="width:30%">Remaining</td>
                </tr>
                @forelse($outOfStockRows as $row)
                <tr>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['remaining'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" style="text-align:center">No items are out of stock.</td>
                </tr>
                @endforelse
            </table>
        </div>
    </div>
    @endif

    <!-- Low Stock List -->
    @if($includeLow)
    <div class="section">
        <div class="box">
            <div class="box-title">LOW STOCK — ITEMS</div>
            <table class="report-table">
                <tr>
                    <td class="label" style="width:70%">Item Name</td>
                    <td class="label" style="width:30%">Remaining</td>
                </tr>
                @forelse($lowStockRows as $row)
                <tr>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['remaining'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" style="text-align:center">No items are below the low stock threshold.</td>
                </tr>
                @endforelse
            </table>
        </div>
    </div>
    @endif

    <!-- Lost & Expired Items -->
    @if($includeLostExpired ?? true)
    <div class="section">
        <div class="box">
            <div class="box-title">LOST & EXPIRED — ITEMS</div>
            <table class="report-table">
                <tr>
                    <td class="label" style="width:40%">Item Name</td>
                    <td class="label" style="width:20%">Lost</td>
                    <td class="label" style="width:20%">Expired</td>
                    <td class="label" style="width:20%">Total</td>
                </tr>
                @forelse($lostExpiredRows as $row)
                <tr>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['lost'] }}</td>
                    <td>{{ $row['expired'] }}</td>
                    <td>{{ $row['total'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align:center">No items were lost or expired in the selected range.</td>
                </tr>
                @endforelse
            </table>
        </div>
    </div>
    @endif

    <!-- Transaction Log: Restock Records -->
    @if($includeTransactionLog ?? true)
    <div class="section">
        <div class="box">
            <div class="box-title">RESTOCK LOG RECORDS</div>
            <table class="report-table">
                <tr>
                    <td class="label" style="width:25%">Performed By</td>
                    <td class="label" style="width:30%">Item Name</td>
                    <td class="label" style="width:12%">Quantity</td>
                    <td class="label" style="width:15%">Date & Time</td>
                </tr>
                @forelse($restockTransactions as $transaction)
                <tr>
                    <td style="font-size: 10px;">
                        @if($transaction->admin)
                        {{ $transaction->admin->full_name }}
                        @else
                        <span style="color:#999;">Unknown</span>
                        @endif
                    </td>
                    <td>{{ optional($transaction->item)->name ?? 'N/A' }}</td>
                    <td style="text-align:center; color:#28a745; font-weight:bold;">+{{ $transaction->quantity }}</td>
                    <td style="font-size: 10px;">{{ \Carbon\Carbon::parse($transaction->created_at)->format('M d, Y h:i A') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center">No restock transactions found in the selected range.</td>
                </tr>
                @endforelse
            </table>
        </div>
    </div>
    @endif

    <!-- Transaction Log: Deduct/Dispensed Records -->
    @if($includeTransactionLog ?? true)
    <div class="section">
        <div class="box">
            <div class="box-title">DEDUCTION LOG RECORDS</div>
            <table class="report-table">
                <tr>
                    <td class="label" style="width:22%">Performed By</td>
                    <td class="label" style="width:25%">Item Name</td>
                    <td class="label" style="width:12%">Quantity</td>
                    <td class="label" style="width:10%">Type</td>
                    <td class="label" style="width:15%">Date & Time</td>
                </tr>
                @forelse($deductTransactions as $transaction)
                <tr>
                    <td style="font-size: 10px;">
                        @if($transaction->admin)
                        {{ $transaction->admin->full_name }}
                        @else
                        <span style="color:#999;">Unknown</span>
                        @endif
                    </td>
                    <td>{{ optional($transaction->item)->name ?? 'N/A' }}</td>
                    <td style="text-align:center; color:#dc3545; font-weight:bold;">-{{ $transaction->quantity }}</td>
                    <td style="font-size: 10px; text-align:center;">{{ ucfirst($transaction->type) }}</td>
                    <td style="font-size: 10px;">{{ \Carbon\Carbon::parse($transaction->created_at)->format('M d, Y h:i A') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center">No deduct/dispensed transactions found in the selected range.</td>
                </tr>
                @endforelse
            </table>
        </div>
    </div>
    @endif

    <!-- Computer Generated Message -->
    <div style="text-align: center; margin-top: 40px; margin-bottom: 20px; border-top: 2px solid #207cca; padding-top: 15px;">
        <p style="margin: 0; font-size: 11px; color: #1e5799; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase;">This is Computer Generated</p>
        <p style="margin: 5px 0 0 0; font-size: 9px; color: #888; font-style: italic;">Clinic Demo - Medical Service Unit Demo</p>
    </div>

</body>

</html>