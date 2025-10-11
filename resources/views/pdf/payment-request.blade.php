<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giấy đề nghị thanh toán</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .company-info {
            float: left;
            width: 50%;
        }

        .document-info {
            float: right;
            width: 45%;
            text-align: right;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 30px 0 20px 0;
        }

        .request-info {
            margin: 20px 0;
        }

        .request-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .request-info td {
            padding: 5px 0;
            vertical-align: top;
        }

        .request-info .label {
            width: 150px;
            font-weight: bold;
        }

        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .payment-table th,
        .payment-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        .payment-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .payment-table .description {
            text-align: left;
        }

        .payment-table .amount {
            text-align: right;
        }

        .total-row {
            font-weight: bold;
        }

        .amount-text {
            margin: 10px 0;
        }

        .signatures {
            margin-top: 40px;
        }

        .signatures table {
            width: 100%;
            border-collapse: collapse;
        }

        .signatures td {
            width: 25%;
            text-align: center;
            vertical-align: top;
            padding: 10px;
        }

        .signature-title {
            font-weight: bold;
            margin-bottom: 60px;
        }

        .signature-name {
            margin-top: 10px;
            font-weight: bold;
        }

        .signature-date {
            font-size: 10px;
            color: #666;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <div class="header clearfix">
        <div class="company-info">
            <strong>CÔNG TY TNHH CHẤN HƯNG E&C</strong><br>
            Phòng Kinh tế - Kế hoạch<br>
            Số: {{ $request->id }}/{{ date('m/Y') }}
        </div>
        <div class="document-info">
            <strong>Mẫu số: 05-TT</strong><br>
            (Ban Hành theo QĐ số 15/2006/QĐ-BTC ngày<br>
            20/03/2006 của Bộ trưởng BTC)<br><br>
            Vũng Tàu, ngày {{ date('d') }} tháng {{ date('m') }} năm {{ date('Y') }}
        </div>
    </div>

    <div class="title">
        GIẤY ĐỀ NGHỊ THANH TOÁN
    </div>

    <div class="request-info">
        <table>
            <tr>
                <td class="label">Kính gửi:</td>
                <td>- Giám đốc<br>- Phòng Tài chính Kế toán</td>
            </tr>
        </table>

        <br>

        <table>
            <tr>
                <td class="label">Họ và tên:</td>
                <td style="border-bottom: 1px solid #000; padding-bottom: 2px;">{{ $request->user->name }}</td>
            </tr>
            <tr>
                <td class="label">Bộ phận:</td>
                <td style="border-bottom: 1px solid #000; padding-bottom: 2px;">{{ $request->user->department->name ??
                    'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Nguồn tiền:</td>
                <td style="padding-right: 200px;">
                    <span style="float: right;">
                        <strong>Mã số:</strong> {{ $request->payment_code ?? "" }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <div class="request-info">
        <strong>1. Nội dung thanh toán:</strong>
    </div>

    <table class="payment-table">
        <thead>
            <tr>
                <th style="width: 40px;">STT</th>
                <th style="width: 200px;">Nội dung</th>
                <th style="width: 100px;">Số tiền chưa thuế</th>
                <th style="width: 80px;">Thuế GTGT</th>
                <th style="width: 100px;">Tổng</th>
                <th style="width: 100px;">Số hóa đơn</th>
            </tr>
        </thead>
        <tbody>
            @if($request->details && count($request->details) > 0)
            @foreach($request->details as $index => $detail)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="description">{{ $detail->description }}</td>
                <td class="amount">{{ number_format($detail->amount_before_tax, 0, ',', '.') }}</td>
                <td class="amount">{{ number_format($detail->tax_amount, 0, ',', '.') }}</td>
                <td class="amount">{{ number_format($detail->total_amount, 0, ',', '.') }}</td>
                <td>{{ $detail->invoice_number ?? '-' }}</td>
            </tr>
            @endforeach
            @else
            <tr>
                <td>1</td>
                <td class="description">{{ $request->description }}</td>
                <td class="amount">{{ number_format($request->amount, 0, ',', '.') }}</td>
                <td class="amount">-</td>
                <td class="amount">{{ number_format($request->amount, 0, ',', '.') }}</td>
                <td>-</td>
            </tr>
            @endif
            <tr class="total-row">
                <td colspan="4" style="text-align: center;"><strong>TỔNG</strong></td>
                <td class="amount"><strong>{{ number_format($request->amount, 0, ',', '.') }}</strong></td>
                <td>-</td>
            </tr>
        </tbody>
    </table>

    <div class="amount-text">
        <strong>Số tiền bằng chữ:</strong> {{ $amountInWords }}
    </div>

    <div style="margin: 20px 0;">
        (Kèm theo.............................chứng từ gốc)
    </div>

    <div class="signatures">
        <table>
            <tr>
                <td>
                    <div class="signature-title">Giám đốc</div>
                    <div class="signature-name">{{ $ceo->name ?? '' }}</div>
                </td>
                <td>
                    <div class="signature-title">Phụ trách Kế toán</div>
                    <div class="signature-name">{{ $accountant->name ?? '' }}</div>
                </td>
                <td>
                    <div class="signature-title">Phụ trách bộ phận</div>
                    <div class="signature-name">{{ $departmentHead->name ?? '' }}</div>
                </td>
                <td>
                    <div class="signature-title">Người đề nghị t.toán</div>
                    <div class="signature-name">{{ $request->user->name }}</div>
                    <div class="signature-date">{{ date('Y.m.d') }}<br>{{ date('H:i:s') }}<br>+07'00'</div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</body>

</html>