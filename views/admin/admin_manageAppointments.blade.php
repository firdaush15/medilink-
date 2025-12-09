<!--admin_manageAppointments.blade.php-->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MediLink | Appointments Management</title>

    @vite(['resources/css/admin/admin_sidebar.css', 'resources/css/admin/admin_manageAppointments.css'])

</head>

<body>

    {{-- Sidebar --}}
    @include('admin.sidebar.admin_sidebar')

    <div class="main">
        <div class="header">
            <h2>Appointments Management</h2>
            <button><b>+ New Appointment</b></button>
        </div>

        <!-- Cards -->
        <div class="cards">
            <div class="card">
                <h3>Today's Appointments</h3>
                <p>{{ $todayCount ?? 0 }}</p>
            </div>
            <div class="card">
                <h3>Confirmed</h3>
                <p style="color:#004bb3;">{{ $confirmCount ?? 0 }}</p>
            </div>
            <div class="card">
                <h3>Completed</h3>
                <p style="color:#006b00;">{{ $completedCount ?? 0 }}</p>
            </div>
            <div class="card">
                <h3>Cancelled</h3>
                <p style="color:#a10000;">{{ $cancelledCount ?? 0 }}</p>
            </div>
        </div>

        <h3 class="section-title">All Appointments</h3>

        <!-- Controls -->
        <form method="GET" action="{{ route('admin.appointments') }}">
            <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; align-items: center;">
                <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                    <div class="search-container">
                        <i class="fa fa-search"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by doctor or patient name...">
                    </div>

                    <!-- Status Filter (NO PENDING) -->
                    <div class="sort-container" style="padding: 8px 10px;">
                        <label>Status:</label>
                        <select name="status" onchange="this.form.submit()">
                            <option value="">All</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>

                <div class="sort-container">
                    <label for="sortSelect">Sort by:</label>
                    <select name="sort" id="sortSelect" onchange="this.form.submit()">
                        <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Newest → Oldest</option>
                        <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Oldest → Newest</option>
                    </select>
                </div>
            </div>
        </form>

        <!-- Appointments Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($appointments as $appointment)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('Y-m-d') }}</td>
                        <td>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</td>
                        <td>{{ $appointment->patient->user->name }}</td>
                        <td>{{ $appointment->doctor->user->name }}</td>
                        <td>{{ $appointment->doctor->specialization }}</td>
                        <td>
                            @php
                            $statusClass = [
                            'completed' => 'completed',
                            'confirmed' => 'confirmed',
                            'cancelled' => 'cancelled'
                            ][strtolower($appointment->status)] ?? '';
                            @endphp
                            <span class="status {{ $statusClass }}">{{ ucfirst($appointment->status) }}</span>
                        </td>
                        <td class="actions" style="text-align:center;">
                            <button class="view">View</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center; color:#999;">No appointments found.</td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

{{-- ✅ Custom Pagination (consistent style) --}}
<div class="pagination">
    <p>
        Showing {{ $appointments->firstItem() }}–{{ $appointments->lastItem() }}
        of {{ $appointments->total() }} appointments
    </p>

    <div class="pages">
        {{-- Previous Page --}}
        @if ($appointments->onFirstPage())
            <button disabled>&laquo; Prev</button>
        @else
            <a href="{{ $appointments->previousPageUrl() }}">
                <button>&laquo; Prev</button>
            </a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($appointments->getUrlRange(1, $appointments->lastPage()) as $page => $url)
            @if ($page == $appointments->currentPage())
                <button class="active">{{ $page }}</button>
            @else
                <a href="{{ $url }}">
                    <button>{{ $page }}</button>
                </a>
            @endif
        @endforeach

        {{-- Next Page --}}
        @if ($appointments->hasMorePages())
            <a href="{{ $appointments->nextPageUrl() }}">
                <button>Next &raquo;</button>
            </a>
        @else
            <button disabled>Next &raquo;</button>
        @endif
    </div>
</div>

    </div>
</body>

</html>