@php
use Illuminate\Support\Facades\Auth;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>POS Kasir</title>


<link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('dist/css/icons/material-design-iconic-font/css/materialdesignicons.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/kasir.css') }}">
@yield('css')
</head>
<body class="kasir-fullscreen">

<header class="kasir-header d-flex justify-content-between align-items-center px-4">
    <div class="d-flex align-items-center gap-2 text-white fs-5">
        <img src="{{ asset('assets/images/icon.ico') }}" alt="Logo" class="logo-icon"> POS KASIR
    </div>
    <div class="d-flex align-items-center gap-3 text-white">
        <span><i class="mdi mdi-account"></i> {{ Auth::user()->nama }}</span>
        <span id="kasir-date"></span>
        <span id="kasir-time"></span>
        <div id="shift-status-container" class="d-flex gap-2"></div>
        <a href="{{ url('/') }}" class="btn btn-warning btn-sm fw-bold">
            <i class="mdi mdi-arrow-left"></i> Dashboard
        </a>
    </div>
</header>

<!-- Modal Buka Shift -->
<div class="modal fade" id="openShiftModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Buka Shift</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="openShiftForm">
        <div class="modal-body">
          <label for="start_cash" class="form-label">Modal Awal (Rp)</label>
          <input type="number" name="start_cash" class="form-control" required>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Buka Shift</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Tutup Shift -->
<div class="modal fade" id="closeShiftModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tutup Shift</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="closeShiftForm">
        <div class="modal-body">
          <p id="expectedCashText" class="fw-bold">Expected Cash: Rp 0</p>
          <label for="end_cash" class="form-label">Uang Fisik di Laci (Rp)</label>
          <input type="number" name="end_cash" class="form-control" required>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Tutup Shift</button>
        </div>
      </form>
    </div>
  </div>
</div>

<main class="kasir-body">@yield('content')</main>

<script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/sweetalert/sweetalert.all.js') }}"></script>
@include('sweetalert::alert')
@yield('js')

<script>
document.addEventListener('DOMContentLoaded', function() {

    function formatRupiah(angka) { 
        return 'Rp ' + Number(angka).toLocaleString('id-ID'); 
    }

    function updateWaktu() {
        const now = new Date();
        const tgl = now.toLocaleDateString('id-ID', {year:'numeric',month:'long',day:'numeric'});
        let h = now.getHours(), m = now.getMinutes(), s = now.getSeconds();
        const ampm = h >= 12 ? 'PM' : 'AM';
        h = h%12 || 12; h = h<10?'0'+h:h; 
        m=m<10?'0'+m:m; 
        s=s<10?'0'+s:s;
        document.getElementById('kasir-date').textContent = tgl;
        document.getElementById('kasir-time').textContent = `${h}:${m}:${s} ${ampm}`;
    }
    updateWaktu(); 
    setInterval(updateWaktu,1000);

    async function checkShiftStatus() {
        try {
            const res = await fetch("{{ route('settlement.status') }}");
            const data = await res.json();
            const container = document.getElementById('shift-status-container');

            if(data.status==='open'){
                container.innerHTML = `
                    <span class="badge bg-success">
                        Shift Aktif (${new Date(data.opened_at).toLocaleTimeString()})
                    </span>
                    <button class="btn btn-danger btn-sm ms-2" onclick="showCloseShiftModal()">
                        Tutup Shift
                    </button>
                `;
            } else {
                container.innerHTML = `
                    <button class="btn btn-primary" onclick="showOpenShiftModal()">Buka Shift</button>
                `;
            }
        } catch(e){
            console.error(e);
        }
    }

    window.showOpenShiftModal = ()=>{ 
        new bootstrap.Modal(document.getElementById('openShiftModal')).show(); 
    }

    window.showCloseShiftModal = async ()=>{
        const modalEl = document.getElementById('closeShiftModal');
        const modal = new bootstrap.Modal(modalEl);

        let startCash = 0;
        let totalCash = 0;

        try{
            const res = await fetch('{{ route("settlement.status") }}');
            const data = await res.json();

            if(data.status === 'open'){
                startCash = parseFloat(data.start_cash)||0;

                const cashRes = await fetch('{{ route("settlement.totalCashShift") }}');
                const cashData = await cashRes.json();

                totalCash = parseFloat(cashData.total_cash)||0;
            }

        } catch(e){
            console.error(e);
        }

        document.getElementById('expectedCashText').textContent = 
            `Expected Cash: ${formatRupiah(totalCash)}`;

        modal.show();
    };

    checkShiftStatus();

    /*
    |--------------------------------------------------------------------------
    | OPEN SHIFT
    |--------------------------------------------------------------------------
    */
    document.getElementById('openShiftForm')?.addEventListener('submit', async e=>{
        e.preventDefault(); 

        const btn = e.target.querySelector('button[type="submit"]');
        btn.disabled = true; 
        btn.innerHTML='<span class="spinner-border spinner-border-sm"></span> Membuka...';

        try{
            const res = await fetch("{{ route('settlement.open') }}", {
                method:'POST',
                headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'},
                body:new FormData(e.target)
            });

            const result = await res.json();

            if(res.ok){

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: result.message,
                });

                bootstrap.Modal.getInstance(document.getElementById('openShiftModal')).hide();
                e.target.reset(); 
                checkShiftStatus();

            } else {
                throw new Error(result.message || 'Terjadi kesalahan');
            }

        } catch(err){
            Swal.fire({
                icon: 'error',
                title: 'Gagal membuka shift!',
                text: err.message,
            });
        } finally{
            btn.disabled = false; 
            btn.innerHTML='Buka Shift';
            document.activeElement?.blur();
        }
    });

    /*
    |--------------------------------------------------------------------------
    | CLOSE SHIFT
    |--------------------------------------------------------------------------
    */
    document.getElementById('closeShiftForm')?.addEventListener('submit', async e=>{
        e.preventDefault(); 

        const btn = e.target.querySelector('button[type="submit"]');
        btn.disabled = true; 
        btn.innerHTML='<span class="spinner-border spinner-border-sm"></span> Menutup...';

        try{
            const res = await fetch("{{ route('settlement.close') }}", {
                method:'POST',
                headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'},
                body:new FormData(e.target)
            });

            const result = await res.json();

            if(res.ok){

                Swal.fire({
                    icon: 'success',
                    title: 'Shift Ditutup',
                    text: result.message,
                });

                bootstrap.Modal.getInstance(document.getElementById('closeShiftModal')).hide();
                e.target.reset(); 
                checkShiftStatus();

                // PRINT SHIFT
                try{
                    const printRes = await fetch('{{ route("settlement.printShift") }}', {
                        method:'POST',
                        headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}
                    });

                    const shiftData = await printRes.json();

                    if(window.electronAPI){
                        window.electronAPI.printShift(shiftData);
                    }

                } catch(err){
                    console.error('Gagal panggil print shift:', err);

                    Swal.fire({
                        icon: 'warning',
                        title: 'Print Gagal',
                        text: 'Shift ditutup, tetapi tidak bisa mencetak struk.',
                    });
                }

            } else {
                throw new Error(result.message || 'Terjadi kesalahan');
            }

        } catch(err){
            Swal.fire({
                icon: 'error',
                title: 'Gagal menutup shift!',
                text: err.message,
            });
        } finally{
            btn.disabled = false; 
            btn.innerHTML='Tutup Shift';
            document.activeElement?.blur();
        }
    });

});
</script>

</body>
</html>
