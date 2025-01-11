<!-- Logo Section -->
<div class="logo mb-4 text-center w-100">
    <h3 class=" mt-4 display-5"><i class="text-prime">Link<b class="fw-bold text-prime">UP</b></i></h3>
</div>

<!-- Navigation Links -->
<nav class="nav flex-column w-100">
    <a class="nav-link2 w-100 text-center" href="{{ route('dashboard') }}">
        Dashboard
    </a>
    <hr class="w-75 mx-auto my-1 blue">
    <a class="nav-link2 w-100 text-center" href="{{route('meeting.index')}}">
        My Meetings
    </a>
    <hr class="w-75 mx-auto my-1 blue">
    <a class="nav-link2 w-100 text-center" href="{{ route('meeting.create') }}">
        Create Meeting
    </a>
    <hr class="w-75 mx-auto my-1 blue">


</nav>
