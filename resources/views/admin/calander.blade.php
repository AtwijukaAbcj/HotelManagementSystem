<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Calendar</title>
    @include('admin.css')
    <style>
        .calendar-page { background: #f6f8fb; min-height: 100vh; }
        .calendar-page .page-wrapper { padding-top: 80px; }
        .calendar-container { max-width: 1320px; margin: 0 auto; padding: 30px 28px 56px; }
        .calendar-card { border: 1px solid #e7ebf1; border-radius: 12px; box-shadow: 0 8px 22px rgba(15,23,42,.05); }
        .calendar-stat { border: 1px solid #e7ebf1; border-radius: 12px; background: #fff; box-shadow: 0 8px 22px rgba(15,23,42,.04); padding: 18px; }
        .calendar-stat-label { color: #667085; font-size: .74rem; letter-spacing: .05em; text-transform: uppercase; }
        .calendar-stat-value { color: #172033; font-size: 1.55rem; font-weight: 700; }
        .upcoming-item { border-bottom: 1px solid #eef1f5; padding: 14px 0; }
        .upcoming-item:last-child { border-bottom: 0; }
        #calendar { min-height: 680px; }
        .calendar-card .card-body { padding: 22px; }
        .fc { color: #344054; }
        .fc .fc-toolbar { margin-bottom: 22px; }
        .fc .fc-toolbar-title { color: #172033; font-size: 1.35rem; font-weight: 700; }
        .fc .fc-button { box-shadow: none; font-weight: 600; text-transform: capitalize; }
        .fc .fc-button-primary { background: #009f93; border-color: #009f93; border-radius: 8px; }
        .fc .fc-button-primary:hover, .fc .fc-button-primary:focus { background: #00857b; border-color: #00857b; }
        .fc .fc-button-primary:disabled { background: #8acbc5; border-color: #8acbc5; }
        .fc .fc-scrollgrid, .fc .fc-theme-standard td, .fc .fc-theme-standard th { border-color: #e4e7ec; }
        .fc .fc-col-header-cell { background: #f8fafc; padding: 10px 0; }
        .fc .fc-col-header-cell-cushion { color: #667085; font-size: .74rem; letter-spacing: .05em; text-transform: uppercase; }
        .fc .fc-daygrid-day-number { color: #475467; font-size: .82rem; padding: 8px; }
        .fc .fc-daygrid-day.fc-day-today { background: #eefaf8; }
        .fc .fc-daygrid-day:hover { background: #f8fbff; }
        .fc-event { background: #2563eb; border: 0; border-radius: 5px; padding: 2px 5px; }
        @media (max-width: 767px) { .calendar-page .page-wrapper { padding-top: 72px; } .calendar-container { padding: 24px 16px 40px; } #calendar { min-height: 560px; } .fc .fc-toolbar { display: block; } .fc .fc-toolbar-chunk { margin-bottom: 8px; } }
    </style>
</head>
<body>
<div class="main-wrapper calendar-page">
    @include('admin.header')
    @include('admin.sidebar')
    <div class="page-wrapper"><main class="calendar-container">
        <div class="d-flex justify-content-between align-items-start flex-wrap mb-4" style="gap:16px"><div><h3 class="page-title mb-1">Reservation Calendar</h3><p class="text-muted mb-0">Plan hotel events, arrivals, departures, and operational dates.</p></div><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#calendar-event-modal"><i class="fas fa-plus mr-1"></i> Add Event</button></div>
        @if(session('message'))<div class="alert alert-success calendar-card">{{ session('message') }}</div>@endif
        @if($errors->any())<div class="alert alert-danger calendar-card"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <div class="row mb-4"><div class="col-md-4 mb-3 mb-md-0"><div class="calendar-stat"><div class="d-flex justify-content-between"><span class="calendar-stat-label">Scheduled events</span><i class="fas fa-calendar-alt text-primary"></i></div><div class="calendar-stat-value mt-2">{{ $calendarStats['total'] }}</div></div></div><div class="col-md-4 mb-3 mb-md-0"><div class="calendar-stat"><div class="d-flex justify-content-between"><span class="calendar-stat-label">Today</span><i class="fas fa-sun text-warning"></i></div><div class="calendar-stat-value mt-2">{{ $calendarStats['today'] }}</div></div></div><div class="col-md-4"><div class="calendar-stat"><div class="d-flex justify-content-between"><span class="calendar-stat-label">Upcoming</span><i class="fas fa-clock text-success"></i></div><div class="calendar-stat-value mt-2">{{ $calendarStats['upcoming'] }}</div></div></div></div>
        <div class="row"><div class="col-xl-9 mb-4 mb-xl-0"><div class="card calendar-card"><div class="card-body"><div id="calendar"></div></div></div></div><div class="col-xl-3"><div class="card calendar-card"><div class="card-body"><h5 class="mb-1">Upcoming events</h5><p class="text-muted small mb-3">Next scheduled dates</p>@forelse($upcomingEvents as $event)<div class="upcoming-item"><strong class="d-block">{{ $event->event_name }}</strong><small class="text-muted">{{ $event->event_date->format('d M Y') }}</small></div>@empty<div class="text-center text-muted py-4"><i class="fas fa-calendar-plus fa-2x mb-2"></i><br>No upcoming events</div>@endforelse</div></div></div></div>
    </main></div>
</div>
<div class="modal fade" id="calendar-event-modal" tabindex="-1" role="dialog" aria-labelledby="calendar-event-title" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="calendar-event-title">Add Calendar Event</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div><form action="{{ url('/add-event') }}" method="POST">@csrf<div class="modal-body"><div class="form-group"><label>Event name</label><input name="event_name" class="form-control" required></div><div class="form-group mb-0"><label>Event date</label><input type="date" name="event_date" id="calendar-event-date" class="form-control" required></div></div><div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save Event</button></div></form></div></div></div>
@include('admin.script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'dayGridMonth',
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,listWeek' },
        initialDate: new Date(),
        navLinks: true,
        editable: false,
        eventLimit: true,
        events: @json($calendarEvents),
        dateClick: function (info) { document.getElementById('calendar-event-date').value = info.dateStr; $('#calendar-event-modal').modal('show'); }
    });
    calendar.render();
});
</script>
</body>
</html>
