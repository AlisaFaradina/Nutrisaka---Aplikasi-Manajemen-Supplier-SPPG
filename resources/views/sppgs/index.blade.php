@extends('layouts.app')

@section('title', 'Data Pelanggan SPPG — Nutrisaka')
@section('header_title', 'Master Pelanggan SPPG')
@section('header_subtitle', 'Kelola daftar unit Satuan Pelayanan Pemenuhan Gizi yang dilayani')

@section('header_actions')
    <a href="{{ route('sppgs.create') }}" class="btn btn-primary">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        <span>+ Tambah SPPG Baru</span>
    </a>
@endsection

@section('content')
    <!-- Search & Filter Bar -->
    <div class="filter-toolbar">
        <form action="{{ route('sppgs.index') }}" method="GET" style="display: flex; gap: 10px; flex: 1; flex-wrap: wrap;">
            <div class="search-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="search" class="form-input" placeholder="Cari nama SPPG, PIC, atau kode..." value="{{ request('search') }}">
            </div>
            <select name="status" class="form-select" style="width: auto;" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
            </select>
            <button type="submit" class="btn btn-navy">Cari</button>
            @if(request('search') || request('status'))
                <a href="{{ route('sppgs.index') }}" class="btn btn-white">Reset</a>
            @endif
        </form>
    </div>

    <!-- SPPG Table -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Daftar Satuan Pelayanan Pemenuhan Gizi ({{ $sppgs->total() }})
            </div>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama SPPG</th>
                        <th>Penanggung Jawab (PIC)</th>
                        <th>Kontak Telepon / WA</th>
                        <th>Total Transaksi</th>
                        <th>Sisa Piutang</th>
                        <th>Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sppgs as $sppg)
                        <tr>
                            <td>
                                <span style="font-weight: 700; color: var(--navy-800); background: var(--bg-alt); padding: 3px 8px; border-radius: var(--radius-sm);">
                                    {{ $sppg->code }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('sppgs.show', $sppg) }}" style="font-weight: 700; color: var(--sky-600); font-size: 15px;">
                                    {{ $sppg->name }}
                                </a>
                                <div style="font-size: 12px; color: var(--text-sub); max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $sppg->address ?: 'Alamat belum diisi' }}
                                </div>
                            </td>
                            <td>{{ $sppg->pic_name ?: '-' }}</td>
                            <td>
                                @if($sppg->phone)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $sppg->phone) }}" target="_blank" style="color: var(--success); font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                        {{ $sppg->phone }}
                                    </a>
                                @else
                                    <span style="color: var(--text-light);">-</span>
                                @endif
                            </td>
                            <td>
                                <strong>Rp {{ number_format($sppg->total_sales_amount, 0, ',', '.') }}</strong>
                                <div style="font-size: 11px; color: var(--text-sub);">{{ $sppg->sales_count }} transaksi</div>
                            </td>
                            <td>
                                @if($sppg->total_debt > 0)
                                    <span class="badge badge-danger">
                                        Rp {{ number_format($sppg->total_debt, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="badge badge-success">Lunas (Rp 0)</span>
                                @endif
                            </td>
                            <td>
                                @if($sppg->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="{{ route('sppgs.show', $sppg) }}" class="btn btn-sm btn-outline">Lihat</a>
                                <a href="{{ route('sppgs.edit', $sppg) }}" class="btn btn-sm btn-white">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-light); padding: 40px;">
                                Belum ada data SPPG yang tersimpan. Klik tombol <strong>+ Tambah SPPG Baru</strong>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding: 16px 20px;">
            {{ $sppgs->links() }}
        </div>
    </div>
@endsection
