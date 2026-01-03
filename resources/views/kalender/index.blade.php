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
    .event-item {
        border-left: 3px solid;
        padding: 8px 12px;
        margin-bottom: 8px;
        background: #f8f9fa;
        border-radius: 0 4px 4px 0;
        font-size: 0.875rem;
    }
    .event-item:last-child { margin-bottom: 0; }
    .event-item.akademik { border-left-color: #007bff; }
    .event-item.libur { border-left-color: #dc3545; }
    .event-item.ujian { border-left-color: #ffc107; }
    .event-item.pendaftaran { border-left-color: #28a745; }
    .event-item.lainnya { border-left-color: #6c757d; }
    .event-item .event-title {
        font-weight: 600;
        font-size: 0.85rem;
        line-height: 1.3;
        margin-bottom: 2px;
    }
    .event-item .event-meta {
        font-size: 0.75rem;
        color: #6c757d;
    }
    .event-card-body {
        max-height: 300px;
        overflow-y: auto;
    }
    .countdown-badge {
        font-size: 0.7rem;
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

    <div class="row">
        <!-- Sidebar: Upcoming & Today Events -->
        <div class="col-lg-4 mb-4">
            <!-- Today's Events -->
            @if($todayEvents->count() > 0)
            <div class="card mb-3">
                <div class="card-header bg-primary text-white py-2">
                    <h6 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Hari Ini <span class="badge bg-light text-primary ms-1">{{ $todayEvents->count() }}</span></h6>
                </div>
                <div class="card-body p-2 event-card-body">
                    @foreach($todayEvents->take(5) as $event)
                    <div class="event-item {{ $event->jenis }}">
                        <div class="event-title">{{ Str::limit($event->judul, 40) }}</div>
                        <div class="event-meta">
                            <span class="badge bg-{{ $event->jenis == 'akademik' ? 'primary' : ($event->jenis == 'libur' ? 'danger' : ($event->jenis == 'ujian' ? 'warning text-dark' : ($event->jenis == 'pendaftaran' ? 'success' : 'secondary'))) }}" style="font-size: 0.65rem;">{{ ucfirst($event->jenis) }}</span>
                            @if($event->tanggal_selesai && $event->tanggal_selesai != $event->tanggal_mulai)
                            <span class="ms-1">s/d {{ $event->tanggal_selesai->format('d M') }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                    @if($todayEvents->count() > 5)
                    <div class="text-center mt-2">
                        <small class="text-muted">+{{ $todayEvents->count() - 5 }} event lainnya</small>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Ongoing Events -->
            @if($ongoingEvents->count() > 0)
            <div class="card mb-3">
                <div class="card-header bg-success text-white py-2">
                    <h6 class="mb-0"><i class="bi bi-hourglass-split me-2"></i>Berlangsung <span class="badge bg-light text-success ms-1">{{ $ongoingEvents->count() }}</span></h6>
                </div>
                <div class="card-body p-2 event-card-body">
                    @foreach($ongoingEvents->take(5) as $event)
                    <div class="event-item {{ $event->jenis }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="event-title flex-grow-1">{{ Str::limit($event->judul, 35) }}</div>
                            @if($event->tanggal_selesai)
                            @php $sisaHari = now()->diffInDays($event->tanggal_selesai, false); @endphp
                            <span class="badge bg-{{ $sisaHari <= 3 ? 'danger' : 'info' }} countdown-badge ms-1">
                                {{ $sisaHari > 0 ? $sisaHari . 'h' : 'Terakhir' }}
                            </span>
                            @endif
                        </div>
                        <div class="event-meta">
                            {{ $event->tanggal_mulai->format('d M') }} - {{ $event->tanggal_selesai ? $event->tanggal_selesai->format('d M') : '-' }}
                        </div>
                    </div>
                    @endforeach
                    @if($ongoingEvents->count() > 5)
                    <div class="text-center mt-2">
                        <small class="text-muted">+{{ $ongoingEvents->count() - 5 }} event lainnya</small>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Upcoming Events -->
            <div class="card mb-3">
                <div class="card-header bg-info text-white py-2">
                    <h6 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Mendatang <span class="badge bg-light text-info ms-1">{{ $upcomingEvents->count() }}</span></h6>
                </div>
                <div class="card-body p-2 event-card-body">
                    @forelse($upcomingEvents->take(5) as $event)
                    <div class="event-item {{ $event->jenis }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="event-title flex-grow-1">{{ Str::limit($event->judul, 35) }}</div>
                            @php $daysUntil = now()->diffInDays($event->tanggal_mulai, false); @endphp
                            <span class="badge bg-{{ $daysUntil <= 7 ? 'warning text-dark' : 'secondary' }} countdown-badge ms-1">
                                {{ $daysUntil }}h
                            </span>
                        </div>
                        <div class="event-meta">
                            {{ $event->tanggal_mulai->format('d M Y') }}
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-2">
                        <i class="bi bi-calendar-x"></i>
                        <small class="d-block">Tidak ada event mendatang</small>
                    </div>
                    @endforelse
                    @if($upcomingEvents->count() > 5)
                    <div class="text-center mt-2">
                        <small class="text-muted">+{{ $upcomingEvents->count() - 5 }} event lainnya</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Main Calendar -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label for="filterTahun" class="form-label mb-1">Filter Tahun Akademik</label>
                            <select id="filterTahun" class="form-select">
                                <option value="">Semua Tahun</option>
                                @foreach($tahunAkademiks as $ta)
                                <option value="{{ $ta->id }}" {{ $ta->is_active ? 'selected' : '' }}>
                                    {{ $ta->tahun }}/{{ $ta->tahun + 1 }} - Semester {{ $ta->semester }}
                                    @if($ta->is_active) (Aktif) @endif
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <label for="filterJenis" class="form-label mb-1">Filter Jenis</label>
                            <select id="filterJenis" class="form-select">
                                <option value="">Semua Jenis</option>
                                <option value="akademik">Akademik</option>
                                <option value="libur">Libur</option>
                                <option value="ujian">Ujian</option>
                                <option value="pendaftaran">Pendaftaran</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label mb-1 d-block">Legenda</label>
                            <div class="d-flex flex-wrap">
                                <div class="legend-item"><span class="legend-color" style="background: #007bff;"></span> Akademik</div>
                                <div class="legend-item"><span class="legend-color" style="background: #dc3545;"></span> Libur</div>
                                <div class="legend-item"><span class="legend-color" style="background: #ffc107;"></span> Ujian</div>
                                <div class="legend-item"><span class="legend-color" style="background: #28a745;"></span> Pendaftaran</div>
                                <div class="legend-item"><span class="legend-color" style="background: #6c757d;"></span> Lainnya</div>
                            </div>
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
            @else
            <div class="modal-footer">
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
    var selectedJenis = '';
    
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
        height: 'auto',
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
            
            if (selectedJenis) {
                params.push('jenis=' + selectedJenis);
            }
            
            if (params.length > 0) {
                url += '?' + params.join('&');
            }
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    // Filter by jenis if selected
                    if (selectedJenis) {
                        data = data.filter(evt => evt.jenis === selectedJenis);
                    }
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
        },
        eventDidMount: function(info) {
            // Add tooltip
            info.el.setAttribute('title', info.event.title);
        }
    });
    
    calendar.render();
    
    // Filter handlers
    document.getElementById('filterTahun').addEventListener('change', function() {
        calendar.refetchEvents();
    });
    
    document.getElementById('filterJenis').addEventListener('change', function() {
        selectedJenis = this.value;
        calendar.refetchEvents();
    });
});
</script>
@endpush
