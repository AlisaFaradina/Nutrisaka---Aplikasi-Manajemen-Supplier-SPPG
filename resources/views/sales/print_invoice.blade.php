<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur Penjualan - {{ $sale->invoice_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            font-size: 13px;
            color: #1e293b;
            background: #f8fafc;
            padding: 20px;
        }
        .no-print-bar {
            max-width: 820px;
            margin: 0 auto 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid transparent;
        }
        .btn-primary { background: #0284c7; color: #fff; }
        .btn-white { background: #fff; color: #1e293b; border-color: #cbd5e1; }
        
        .invoice-sheet {
            max-width: 820px;
            margin: 0 auto;
            background: #ffffff;
            padding: 36px 40px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #0284c7;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }
        .brand-title {
            font-size: 22px;
            font-weight: 800;
            color: #0c192c;
            letter-spacing: -0.02em;
        }
        .brand-sub {
            font-size: 12px;
            color: #0284c7;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .brand-contact {
            font-size: 12px;
            color: #475569;
            margin-top: 4px;
            line-height: 1.4;
        }
        .invoice-tag {
            text-align: right;
        }
        .invoice-tag h2 {
            font-size: 20px;
            font-weight: 800;
            color: #0284c7;
            text-transform: uppercase;
        }
        .invoice-tag .number {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 2px;
        }
        
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }
        .meta-box h4 {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
        }
        .meta-box p {
            font-size: 13px;
            line-height: 1.5;
            color: #1e293b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        table th {
            background: #f1f5f9;
            color: #334155;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }
        table td {
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            font-size: 13px;
            color: #1e293b;
        }

        .summary-wrap {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 24px;
            margin-bottom: 30px;
        }
        .bank-info {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 14px 16px;
            font-size: 12px;
            line-height: 1.5;
        }
        .bank-info strong {
            color: #0c192c;
        }
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 6px 10px;
            border: none;
            font-size: 13px;
        }

        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            text-align: center;
            margin-top: 40px;
            padding-top: 10px;
        }
        .sign-box {
            padding: 0 20px;
        }
        .sign-line {
            height: 60px;
        }
        .sign-name {
            font-weight: 700;
            border-top: 1px solid #64748b;
            padding-top: 6px;
            color: #0f172a;
        }

        @media print {
            body { background: #fff; padding: 0; font-size: 11pt; }
            .no-print-bar { display: none !important; }
            .invoice-sheet { border: none; box-shadow: none; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print-bar">
        <a href="{{ route('sales.show', $sale) }}" class="btn btn-white">&larr; Kembali ke Detail Faktur</a>
        <button onclick="window.print()" class="btn btn-primary">
            Cetak Faktur Sekarang
        </button>
    </div>

    <div class="invoice-sheet">
        <!-- Header -->
        <div class="header">
            <div>
                <div class="brand-title">{{ $settings['supplier_name'] }}</div>
                <div class="brand-sub">{{ $settings['supplier_tagline'] }}</div>
                <div class="brand-contact">
                    {{ $settings['supplier_address'] }}<br>
                    Telp / WhatsApp: {{ $settings['supplier_phone'] }} | Email: {{ $settings['supplier_email'] }}
                </div>
            </div>
            <div class="invoice-tag">
                <h2>FAKTUR PENJUALAN</h2>
                <div class="number">{{ $sale->invoice_number }}</div>
                <div style="font-size: 11px; margin-top: 6px; color: #64748b;">
                    Tanggal: <strong>{{ $sale->sale_date->format('d/m/Y') }}</strong>
                </div>
                @if($sale->due_date)
                    <div style="font-size: 11px; color: #dc2626;">
                        Jatuh Tempo: <strong>{{ $sale->due_date->format('d/m/Y') }}</strong>
                    </div>
                @endif
            </div>
        </div>

        <!-- Meta Info -->
        <div class="meta-grid">
            <div class="meta-box">
                <h4>Ditujukan Kepada Pelanggan:</h4>
                <p>
                    <strong style="font-size: 14px; color: #0284c7;">{{ $sale->sppg->name }}</strong><br>
                    Kode SPPG: {{ $sale->sppg->code }}<br>
                    Penanggung Jawab: {{ $sale->sppg->pic_name ?: '-' }}<br>
                    Kontak: {{ $sale->sppg->phone ?: '-' }}<br>
                    Alamat: {{ $sale->sppg->address ?: '-' }}
                </p>
            </div>
            <div class="meta-box">
                <h4>Rincian Faktur:</h4>
                <p>
                    Nomor Faktur: <strong>{{ $sale->invoice_number }}</strong><br>
                    @if($sale->order)
                        Nomor Pesanan Awal: <strong>{{ $sale->order->order_number }}</strong><br>
                    @endif
                    Status Pembayaran: 
                    @if($sale->payment_status === 'lunas')
                        <strong style="color: #059669;">LUNAS</strong>
                    @elseif($sale->payment_status === 'sebagian')
                        <strong style="color: #d97706;">SEBAGIAN (ADA PIUTANG)</strong>
                    @else
                        <strong style="color: #dc2626;">BELUM DIBAYAR</strong>
                    @endif
                    <br>
                    Catatan: {{ $sale->notes ?: 'Pengiriman bahan pangan terverifikasi baik.' }}
                </p>
            </div>
        </div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 35px; text-align: center;">No</th>
                    <th>Nama Barang / Bahan Makanan</th>
                    <th>Satuan</th>
                    <th style="text-align: right;">Kuantitas</th>
                    <th style="text-align: right;">Harga Satuan (Rp)</th>
                    <th style="text-align: right;">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $idx => $item)
                    <tr>
                        <td style="text-align: center;">{{ $idx + 1 }}</td>
                        <td>
                            <strong>{{ $item->product->name }}</strong>
                            <div style="font-size: 10px; color: #64748b;">SKU: {{ $item->product->sku }}</div>
                        </td>
                        <td>{{ $item->product->unit }}</td>
                        <td style="text-align: right; font-weight: 600;">{{ $item->quantity }}</td>
                        <td style="text-align: right;">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td style="text-align: right; font-weight: 700;">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary Wrap -->
        <div class="summary-wrap">
            <div class="bank-info">
                <strong>Informasi Pembayaran / Transfer:</strong><br>
                Bank: <strong>{{ $settings['bank_name'] }}</strong><br>
                No. Rekening: <strong>{{ $settings['bank_account_number'] }}</strong><br>
                Atas Nama: <strong>{{ $settings['bank_account_name'] }}</strong><br>
                <div style="margin-top: 6px; font-size: 11px; color: #64748b;">
                    {{ $settings['invoice_footer_notes'] }}
                </div>
            </div>
            <div>
                <table class="totals-table">
                    <tr>
                        <td style="text-align: right; color: #64748b;">Subtotal:</td>
                        <td style="text-align: right; font-weight: 700;">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @if($sale->discount > 0)
                        <tr>
                            <td style="text-align: right; color: #dc2626;">Diskon:</td>
                            <td style="text-align: right; color: #dc2626; font-weight: 700;">- Rp {{ number_format($sale->discount, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    <tr style="border-top: 1px solid #cbd5e1; font-size: 14px;">
                        <td style="text-align: right; font-weight: 800;">TOTAL AKHIR:</td>
                        <td style="text-align: right; font-weight: 800; color: #0284c7;">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="text-align: right; color: #059669; font-weight: 600;">Telah Dibayar:</td>
                        <td style="text-align: right; color: #059669; font-weight: 700;">Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="border-top: 1px dashed #cbd5e1;">
                        <td style="text-align: right; color: #dc2626; font-weight: 700;">SISA PIUTANG:</td>
                        <td style="text-align: right; color: #dc2626; font-weight: 800;">Rp {{ number_format($sale->remaining_balance, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="sign-box">
                <div style="font-size: 12px; color: #475569;">Penerima / Petugas SPPG,</div>
                <div class="sign-line"></div>
                <div class="sign-name">( {{ $sale->sppg->pic_name ?: 'Petugas Dapur SPPG' }} )</div>
            </div>
            <div class="sign-box">
                <div style="font-size: 12px; color: #475569;">Hormat Kami Supplier,</div>
                <div class="sign-line"></div>
                <div class="sign-name">( {{ $settings['supplier_name'] }} )</div>
            </div>
        </div>
    </div>
</body>
</html>
