<!--admin_managePatients.blade.php-->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediLink | Manage Patients</title>
    @vite(['resources/css/admin/admin_sidebar.css', 'resources/css/admin/admin_managePatients.css'])
</head>

<body>

    {{-- Sidebar --}}
    @include('admin.sidebar.admin_sidebar')

    {{-- Main Content --}}
    <div class="main">
        <h2>Manage Patients</h2>

        {{-- Search --}}
        <div class="search-container">
            <form method="GET" action="{{ route('admin.patients') }}">
                <input type="text" name="search" placeholder="Search patient by name" value="{{ request('search') }}">
                <button type="submit">Search</button>
                <a href="{{ route('admin.patients') }}" class="reset-btn">Reset</a>
            </form>
        </div>

        {{-- Table --}}
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <!--<th>Status</th> -->
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($patients as $patient)
                <tr>
                    <td>{{ $patient->user->name ?? 'N/A' }}</td>
                    <td>{{ $patient->gender ?? 'N/A' }}</td>
                    <td>{{ $patient->phone_number ?? 'N/A' }}</td>
                    <td>{{ $patient->user->email ?? 'N/A' }}</td>
                    <!-- <td class="
                        {{ $patient->status == 'Active' ? 'status-active' : '' }}
                        {{ $patient->status == 'Inactive' ? 'status-inactive' : '' }}
                        {{ $patient->status == 'Under Treatment' ? 'status-treatment' : '' }}
                    ">
                        {{ $patient->status ?? 'Unknown' }}
                    </td> -->
                    <td>
                        <a href="{{ route('admin.patients.show', $patient->patient_id) }}">
                            <button class="action-btn view-btn">View</button>
                        </a>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="pagination">
            <p>
                Showing {{ $patients->firstItem() }}–{{ $patients->lastItem() }} of {{ $patients->total() }} patients
            </p>
            <div class="pages">
                {{-- Previous Page --}}
                @if ($patients->onFirstPage())
                <button disabled>&laquo; Prev</button>
                @else
                <a href="{{ $patients->previousPageUrl() }}">
                    <button>&laquo; Prev</button>
                </a>
                @endif

                {{-- Page Numbers --}}
                @foreach ($patients->getUrlRange(1, $patients->lastPage()) as $page => $url)
                @if ($page == $patients->currentPage())
                <button class="active">{{ $page }}</button>
                @else
                <a href="{{ $url }}">
                    <button>{{ $page }}</button>
                </a>
                @endif
                @endforeach

                {{-- Next Page --}}
                @if ($patients->hasMorePages())
                <a href="{{ $patients->nextPageUrl() }}">
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