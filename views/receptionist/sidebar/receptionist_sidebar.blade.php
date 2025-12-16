<div class="sidebar">
    <h2>RECEPTIONIST PANEL</h2>
    <div class="logo">
      <img src="{{ asset('assets/logo.png') }}" alt="MediLink Logo">
    </div>
    
    <a href="{{ route('receptionist.dashboard') }}" 
       class="{{ request()->routeIs('receptionist.dashboard') ? 'active' : '' }}">
        Dashboard
    </a>
    
    <a href="{{ route('receptionist.patients.register') }}" 
       class="{{ request()->routeIs('receptionist.patients.register') ? 'active' : '' }}">
        Patient Registration
    </a>
    
    <a href="{{ route('receptionist.walk-in.create') }}" 
       class="{{ request()->routeIs('receptionist.walk-in.*') ? 'active' : '' }}">
        Walk-In Patient
    </a>
    
    <a href="{{ route('receptionist.appointments') }}" 
       class="{{ request()->routeIs('receptionist.appointments*') ? 'active' : '' }}">
        Appointments
    </a>
    
    <a href="{{ route('receptionist.check-in') }}" 
       class="{{ request()->routeIs('receptionist.check-in*') ? 'active' : '' }}">
        Check-In / Queue
    </a>
    
    <a href="{{ route('receptionist.search.advanced') }}" 
       class="{{ request()->routeIs('receptionist.search.*') ? 'active' : '' }}">
        Advanced Search
    </a>
    
    <a href="{{ route('receptionist.reminders.index') }}" 
       class="{{ request()->routeIs('receptionist.reminders.*') ? 'active' : '' }}">
        Reminders
    </a>

    <a href="{{ route('receptionist.alerts.index') }}" class="sidebar-link">
    <span class="icon">🔔</span>
    <span>Alerts</span>
    @if($unreadCount > 0)
    <span class="badge">{{ $unreadCount }}</span>
    @endif
</a>

<a href="{{ route('receptionist.checkout.index') }}" class="sidebar-link">
    <span class="icon">💰</span>
    <span>Checkout & Payment</span>
</a>
    
    <a href="{{ route('receptionist.doctor-availability') }}" 
       class="{{ request()->routeIs('receptionist.doctor-availability') ? 'active' : '' }}">
        Doctor Availability
    </a>
    
    <a href="{{ route('receptionist.messages') }}" 
       class="{{ request()->routeIs('receptionist.messages') ? 'active' : '' }}">
        Messages
    </a>
    
    <a href="{{ route('receptionist.setting') }}" 
       class="{{ request()->routeIs('receptionist.setting') ? 'active' : '' }}">
        Settings
    </a>
    
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="logout-btn">Logout</button>
    </form>
</div>

<style>
.sidebar {
    width: 220px;
    height: 100vh;
    background-color: #0a1f33;
    color: white;
    position: fixed;
    top: 0;
    left: 0;
    display: flex;
    flex-direction: column;
    padding-top: 15px;
    z-index: 1000;
}

.sidebar h2 {
    font-size: 14px;
    text-align: center;
    margin-bottom: 20px;
    letter-spacing: 1px;
}

.logo {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 20px;
}

.logo img {
    width: 200px;
    height: auto;
}

.sidebar a {
    text-decoration: none;
    color: white;
    padding: 12px 25px;
    display: block;
    font-size: 15px;
    transition: all 0.2s ease;
}

.sidebar a:hover,
.sidebar a.active {
    background-color: #1b3b5f;
    border-left: 4px solid #00aaff;
}

.logout-btn {
    width: 100%;
    text-align: left;
    background: none;
    border: none;
    color: white;
    padding: 12px 25px;
    font-size: 15px;
    cursor: pointer;
    font-family: "Poppins", sans-serif;
    transition: all 0.2s ease;
}

.logout-btn:hover {
    background-color: #1b3b5f;
    border-left: 4px solid #00aaff;
}

/* Scrollbar */
.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
}

.sidebar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 3px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
}

.sidebar a {
    text-decoration: none;
    color: white;
    padding: 12px 25px;
    display: block;
    font-size: 15px;
    transition: all 0.2s ease;
    position: relative; /* Add this */
}

/* Add this new styling */
.sidebar .sidebar-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.sidebar .sidebar-link .icon {
    font-size: 1.1rem;
}

.sidebar .sidebar-link .badge {
    position: absolute;
    right: 20px;
    background: #ef4444;
    color: white;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 0.15rem 0.45rem;
    border-radius: 10px;
    min-width: 18px;
    text-align: center;
    line-height: 1.2;
}
</style>