@extends('layouts.app')

@section('title', 'Kalender Akademik')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<style>
    #calendar {
        max-width: 100%;
    }
    .fc-event {
        cursor: pointer;
    }
    .legend-item {
        display: inline-flex;
        align-items: center;
        margin-right: 15px;
        font-size: 0.875rem;
    }
    .legend-color {
        width: 15px;
        height: 15px;
        border-radius: 3px;
        margin-right: 5px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Kalender Akademik</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Kalender Akademik</li>
                </ol>
            </nav>
        </div>
        @if(Auth::user()->isAdmin())
        <div>
            <a href="{{ route('kalender.list') }}" class="btn btn-outline-primary me-2">
                <i class="bi bi-list-ul me-1"></i>Daftar Event
            </a>
            <a href="{{ route('kalender.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Event
            </a>
        </div>
        @endif
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <label for="filterTahun" class="form-label">Filter Tahun Akademik</label>
                    <select id="filterTahun" class="form-select">
                        <option value="">Semua Tahun</option>
                        @foreach($tahunAkademiks as $ta)
                        <option value="{{ $ta->id }}">{{ $ta->tahun }} - Semester {{ $ta->semester }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8 mt-3 mt-md-0">
                    <label class="form-label d-block">Legenda</label>
                    <div class="legend-item"><span class="legend-color" style="background: #007bff;"></span> Akademik</div>
                    <div class="legend-item"><span class="legend-color" style="background: #dc3545;"></span> Libur</div>
                    <div class="legend-item"><span class="legend-color" style="background: #ffc107;"></span> Ujian</div>
                    <div class="legend-item"><span class="legend-color" style="background: #28a745;"></span> Pendaftaran</div>
                    <div class="legend-item"><span class="legend-color" style="background: #6c757d;"></span> Lainnya</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- Event Detail Modal -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Jenis:</strong> <span id="eventJenis" class="badge"></span></p>
                <p><strong>Tanggal:</strong> <span id="eventDate"></span></p>
                <p id="eventDescWrapper"><strong>Deskripsi:</strong><br><span id="eventDesc"></span></p>
            </div>
            @if(Auth::user()->isAdmin())
            <div class="modal-footer">
                <a href="#" id="eventEditLink" class="btn btn-primary">
                    <i class="bi bi-pencil me-1"></i>Edit
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'id',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,dayGridWeek,listMonth'
        },
        buttonText: {
            today: 'Hari Ini',
            month: 'Bulan',
            week: 'Minggu',
            list: 'Daftar'
        },
        events: function(info, successCallback, failureCallback) {
            var tahunId = document.getElementById('filterTahun').value;
            var url = '{{ route("kalender.events") }}';
            var params = [];
            
            if (tahunId) {
                params.push('tahun_akademik_id=' + tahunId);
            } else {
                params.push('start=' + info.startStr);
                params.push('end=' + info.endStr);
            }
            
            if (params.length > 0) {
                url += '?' + params.join('&');
            }
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    successCallback(data);
                    
                    // If filtering by tahun akademik and we have events, go to first event's month
                    if (tahunId && data.length > 0) {
                        var firstEvent = data.reduce((min, evt) => evt.start < min.start ? evt : min, data[0]);
                        var eventDate = new Date(firstEvent.start);
                        var currentDate = calendar.getDate();
                        
                        // Only navigate if we're not already viewing that month
                        if (eventDate.getMonth() !== currentDate.getMonth() || 
                            eventDate.getFullYear() !== currentDate.getFullYear()) {
                            calendar.gotoDate(firstEvent.start);
                        }
                    }
                })
                .catch(error => failureCallback(error));
        },
        eventClick: function(info) {
            var event = info.event;
            document.getElementById('eventTitle').textContent = event.title;
            
            var jenisEl = document.getElementById('eventJenis');
            var jenis = event.extendedProps.jenis || 'akademik';
            jenisEl.textContent = jenis.charAt(0).toUpperCase() + jenis.slice(1);
            jenisEl.className = 'badge';
            switch(jenis) {
                case 'akademik': jenisEl.classList.add('bg-primary'); break;
                case 'libur': jenisEl.classList.add('bg-danger'); break;
                case 'ujian': jenisEl.classList.add('bg-warning', 'text-dark'); break;
                case 'pendaftaran': jenisEl.classList.add('bg-success'); break;
                default: jenisEl.classList.add('bg-secondary');
            }
            
            var startDate = event.start ? event.start.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-';
            var endDate = event.end ? new Date(event.end - 86400000).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : null;
            document.getElementById('eventDate').textContent = endDate && startDate != endDate ? startDate + ' - ' + endDate : startDate;
            
            var desc = event.extendedProps.description;
            if (desc) {
                document.getElementById('eventDescWrapper').style.display = 'block';
                document.getElementById('eventDesc').textContent = desc;
            } else {
                document.getElementById('eventDescWrapper').style.display = 'none';
            }
            
            @if(Auth::user()->isAdmin())
            document.getElementById('eventEditLink').href = '{{ url("kalender") }}/' + event.id + '/edit';
            @endif
            
            new bootstrap.Modal(document.getElementById('eventModal')).show();
        }
    });
    
    calendar.render();
    
    document.getElementById('filterTahun').addEventListener('change', function() {
        calendar.refetchEvents();
    });
});
</script>
@endpush
