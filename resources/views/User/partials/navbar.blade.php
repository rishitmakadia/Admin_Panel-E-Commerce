<nav class="main-header navbar navbar-expand-lg navbar-light border-bottom border-warning shadow-sm px-3"
     style="background-color: #fff;">

    <div class="container-fluid">

        <!-- Brand or Logo (Optional) -->
        <a class="navbar-brand fw-bold text-warning" href="{{ route('user.home') }}">
            Company
        </a>

        <!-- Toggle button for mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar content -->
        <div class="collapse navbar-collapse" id="navbarContent">

            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a href="{{ route('user.electronics') }}" class="btn btn-warning text-white fw-semibold px-3">
                        Shopping
                    </a>
                </li>
            </ul>

            <!-- Right links -->
            <ul class="navbar-nav ms-auto">
                <li class="nav-item me-3">
                    <button class="btn btn-outline-warning position-relative" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#cartSidebar" aria-controls="cartSidebar">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cartCount"></span>
                    </button>
                </li>
                <li class="nav-item dropdown">
                    <button class="btn btn-outline-warning dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        Welcome, {{ auth()->user()->name }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="dropdownMenuButton">
                        <li>
                            <a class="dropdown-item" href="{{ route('user.profile') }}">Profile</a>
                        </li>
                        <li>
                            <a class="dropdown-item text-danger" href="#"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Logout
                            </a>
                            <form id="logout-form" action="{{ route('user.logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
        crossorigin="anonymous"></script>
<script>
    $(document).ready(function () {
        cardCount();
    });
</script>
