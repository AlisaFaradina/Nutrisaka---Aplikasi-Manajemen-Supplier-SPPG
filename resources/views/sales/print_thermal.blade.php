<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Kasir - {{ $sale->invoice_number }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Courier New', Courier, monospace;
            background: #f1f5f9;
            color: #000;
            padding: 20px;
        }
        .no-print {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background: #0284c7;
            color: #fff;
            text-decoration: none;
            font-family: sans-serif;
            font-size: 13px;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            border: none;
            margin: 0 4px;
        }
        .btn-white {
            background: #fff;
            color: #333;
            border: 1px solid #ccc;
        }

        .receipt-box {
            width: {{ $settings['thermal_paper_size'] === '80mm' ? '300px' : '260px' }};
            margin: 0 auto;
            background: #fff;
            padding: 12px 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            font-size: 11px;
            line-height: 1.35;
        }

        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }

        .title {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .sub {
            font-size: 10px;
            margin-bottom: 4px;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }
        .d-divider {
            border-top: 1px double #000;
            margin: 6px 0;
        }

        .row {
            display: flex;
            justify-content: space-between;
        }

        .items-list {
            margin: 6px 0;
        }
        .item-entry {
            margin-bottom: 5px;
        }

        @media print {
            body { background: #fff; padding: 0; }
            .no-print { display: none; }
            .receipt-box { width: 100%; box-shadow: none; padding: 0; margin: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <a href="{{ route('sales.show', $sale) }}" class="btn btn-white">&larr; Kembali</a>
        <button onclick="window.print()" class="btn">Cetak Struk Thermal</button>
    </div>

    <div class="receipt-box">
        <div class="center">
            <div class="title">{{ $settings['supplier_name'] }}</div>
            <div class="sub">{{ $settings['supplier_tagline'] }}</div>
            <div class="sub">{{ $settings['supplier_address'] }}</div>
            <div class="sub">Telp: {{ $settings['supplier_phone'] }}</div>
        </div>

        <div class="divider"></div>

        <div class="row">
            <span>No. Faktur</span>
            <span class="bold">{{ $sale->invoice_number }}</span>
        </div>
        <div class="row">
            <span>Tanggal</span>
            <span>{{ $sale->sale_date->format('d/m/y H:i') }}</span>
        </div>
        <div class="row">
            <span>SPPG</span>
            <span class="bold">{{ $sale->sppg->name }}</span>
        </div>
        @if($sale->sppg->pic_name)
            <div class="row">
                <span>PIC</span>
                <span>{{ $sale->sppg->pic_name }}</span>
            </div>
        @endif

        <div class="divider"></div>

        <div class="items-list">
            @foreach($sale->items as $item)
                <div class="item-entry">
                    <div class="bold">{{ $item->product->name }}</div>
                    <div class="row">
                        <span>{{ $item->quantity }} {{ $item->product->unit }} x {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                        <span class="bold">{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="divider"></div>

        <div class="row">
            <span>Subtotal</span>
            <span>Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</span>
        </div>
        @if($sale->discount > 0)
            <div class="row">
                <span>Diskon</span>
                <span>-Rp {{ number_format($sale->discount, 0, ',', '.') }}</span>
            </div>
        @endif
        <div class="row bold" style="font-size: 12px;">
            <span>TOTAL</span>
            <span>Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</span>
        </div>
        <div class="row">
            <span>Dibayar</span>
            <span>Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</span>
        </div>
        <div class="row bold">
            <span>SISA PIUTANG</span>
            <span>Rp {{ number_format($sale->remaining_balance, 0, ',', '.') }}</span>
        </div>

        <div class="divider"></div>

        <div class="row">
            <span>Status Pembayaran</span>
            <span class="bold">
                @if($sale->payment_status === 'lunas')
                    LUNAS
                @elseif($sale->payment_status === 'sebagian')
                    SEBAGIAN
                @else
                    BELUM BAYAR
                @endif
            </span>
        </div>

        @if($sale->remaining_balance > 0 && $sale->due_date)
            <div class="row">
                <span>Jatuh Tempo</span>
                <span>{{ $sale->due_date->format('d/m/Y') }}</span>
            </div>
        @endif

        <div class="d-divider"></div>

        <div class="center" style="font-size: 10px; margin-top: 8px;">
            <div>Bank: {{ $settings['bank_name'] }}</div>
            <div>No.Rek: {{ $settings['bank_account_number'] }}</div>
            <div>A.N: {{ $settings['bank_account_name'] }}</div>
            <div style="margin-top: 6px;">*** TERIMA KASIH ***</div>
            <div>Barang Diterima Baik & Lengkap</div>
        </div>
    </div>
</body>
</html>
