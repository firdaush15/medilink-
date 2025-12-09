<!--admin_manageDoctors.blade.php-->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediLink | Manage Doctors</title>
    @vite(['resources/css/admin/admin_sidebar.css', 'resources/css/admin/admin_manageDoctors.css'])
    <style>
        /* Paste your HTML mockup styles here */
    </style>
</head>

<body>

    <!-- Sidebar -->
    @include('admin.sidebar.admin_sidebar')

    <!-- Main Content -->
    <div class="main">
        <h2>Manage Doctors</h2>

        <!-- Header Cards -->
        <div class="header">
            <div class="card">
                <h3>Total Doctors</h3>
                <p>{{ $totalDoctors }}</p>
                <span>+{{ $newDoctorsThisWeek }} new this week</span>
            </div>
            <div class="card">
                <h3>Available</h3>
                <p style="color:green;">{{ $availableDoctors }}</p>
                <span>Currently active</span>
            </div>
            <div class="card">
                <h3>Unavailable</h3>
                <p style="color:red;">{{ $unavailableDoctors }}</p>
                <span>Not accepting patients</span>
            </div>
            <div class="card">
                <h3>On Leave</h3>
                <p style="color:orange;">{{ $onLeaveDoctors }}</p>
                <span>+{{ $newLeavesThisWeek }} new this week</span>
            </div>


        </div>


        <!-- Search & Filter -->
        <div class="search-container">
            <form method="GET" action="{{ route('admin.doctors') }}">
                <input type="text" name="search" placeholder="Search doctor by name" value="{{ request('search') }}">
                <button type="submit">Search</button>

                <select name="specialization" onchange="this.form.submit()">
                    <option value="">Filter by Specialization</option>
                    <option value="Cardiology" {{ request('specialization')=='Cardiology'?'selected':'' }}>Cardiology</option>
                    <option value="Neurology" {{ request('specialization')=='Neurology'?'selected':'' }}>Neurology</option>
                    <option value="Orthopedics" {{ request('specialization')=='Orthopedics'?'selected':'' }}>Orthopedics</option>
                    <option value="Pediatrics" {{ request('specialization')=='Pediatrics'?'selected':'' }}>Pediatrics</option>
                    <option value="Dermatology" {{ request('specialization')=='Dermatology'?'selected':'' }}>Dermatology</option>
                </select>

                <a href="{{ route('admin.doctors') }}"><button type="button">Clear</button></a>
            </form>

        </div>

        <!-- Doctors Table -->
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Specialization</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($doctors as $doctor)
                <tr>
                    <td>{{ $doctor->user->name ?? 'N/A' }}</td>
                    <td>{{ $doctor->specialization ?? 'General' }}</td>
                    <td>{{ $doctor->phone_number ?? 'N/A' }}</td>
                    <td class="
            {{ $doctor->availability_status == 'Available' ? 'status-active' : '' }}
            {{ $doctor->availability_status == 'On Leave' ? 'status-leave' : '' }}
            {{ $doctor->availability_status == 'Unavailable' ? 'status-unavailable' : '' }}
          ">
                        {{ $doctor->availability_status ?? 'Unknown' }}
                    </td>
                    <td>
                        <!-- Non-functional buttons for now -->
                        <button class="action-btn edit-btn">Edit</button>
                        <button class="action-btn delete-btn">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="pagination">
            <p>Showing {{ $doctors->firstItem() }}–{{ $doctors->lastItem() }} of {{ $doctors->total() }} doctors</p>
            <div class="pages">
                @if ($doctors->onFirstPage())
                <button disabled>&laquo; Prev</button>
                @else
                <a href="{{ $doctors->previousPageUrl() }}"><button>&laquo; Prev</button></a>
                @endif

                @foreach ($doctors->getUrlRange(1, $doctors->lastPage()) as $page => $url)
                @if ($page == $doctors->currentPage())
                <button class="active">{{ $page }}</button>
                @else
                <a href="{{ $url }}"><button>{{ $page }}</button></a>
                @endif
                @endforeach

                @if ($doctors->hasMorePages())
                <a href="{{ $doctors->nextPageUrl() }}"><button>Next &raquo;</button></a>
                @else
                <button disabled>Next &raquo;</button>
                @endif
            </div>
        </div>

    </div>
</body>

</html>