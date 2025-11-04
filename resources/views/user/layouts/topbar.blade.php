<!-- Top Bar Start -->
<div class="topbar d-print-none">
    <div class="container-fluid">
        <nav class="topbar-custom d-flex justify-content-between" id="topbar-custom">    
            
            {{-- 🔹 LEFT SECTION --}}
            <ul class="topbar-item list-unstyled d-inline-flex align-items-center mb-0">                        
                {{-- Menu toggle button --}}
                <li>
                    <button class="nav-link mobile-menu-btn nav-icon" id="togglemenu">
                        <i class="iconoir-menu"></i>
                    </button>
                </li> 

                {{-- Welcome text --}}
                <li class="mx-2 welcome-text">
                    <h5 class="mb-0 fw-semibold text-truncate">
                        Hello, {{ session('user.full_name') ?? session('user.username') ?? 'User' }}!
                    </h5>
                </li>                   

                {{-- 🔹 Product Buttons --}}
                <li class="ms-3">
                    <button class="product-btn {{ (session('active_product') ?? 1) == 1 ? 'active' : '' }}" data-product="1">
                        <span class="product-icon">🎵</span>
                        <span class="product-name">Audio Analysis</span>
                    </button>
                </li>
                <li class="ms-2">
                    <button class="product-btn {{ session('active_product') == 2 ? 'active' : '' }}" data-product="2">
                        <span class="product-icon">🎙️</span>
                        <span class="product-name">Voice Agent</span>
                    </button>
                </li>
                <li class="ms-2">
                    <button class="product-btn {{ session('active_product') == 3 ? 'active' : '' }}" data-product="3">
                        <span class="product-icon">💬</span>
                        <span class="product-name">Chatbot</span>
                    </button>
                </li>
            </ul>

            {{-- 🔹 RIGHT SECTION --}}
            <ul class="topbar-item list-unstyled d-inline-flex align-items-center mb-0">
                <li class="dropdown topbar-item">
                    <a class="nav-link dropdown-toggle arrow-none nav-icon" data-bs-toggle="dropdown" href="#" role="button"
                        aria-haspopup="false" aria-expanded="false" data-bs-offset="0,19">
                        <img src="{{ session('user.avatar') ?: asset('assets/images/default-avatar.jpg') }}" alt="" class="thumb-md rounded-circle">
                    </a>
                    <div class="dropdown-menu dropdown-menu-end p-2">
                        <div class="d-flex align-items-center dropdown-item py-2 bg-secondary-subtle">
                            <div class="flex-shrink-0">
                                <img src="{{ session('user.avatar') ?: asset('assets/images/default-avatar.jpg') }}" alt="" class="thumb-md rounded-circle">
                            </div>
                            <div class="flex-grow-1 ms-2 text-truncate align-self-center">
                                <h6 class="my-0 fw-medium text-dark fs-13">
                                    {{ session('user.full_name') ?? session('user.username') ?? 'User' }}
                                </h6>
                                <small class="text-muted mb-0">{{ session('user.email') ?? 'No email' }}</small>
                            </div>
                        </div>
                        <div class="dropdown-divider mt-0"></div>
                        <small class="text-muted px-2 pb-1 d-block">Account</small>
                        <a class="dropdown-item" href="{{ route('users.edit', session('user.id')) }}">
                            <i class="las la-cog fs-18 me-1 align-text-bottom"></i>Account Settings
                        </a>
                        <a class="dropdown-item text-danger" href="#"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="las la-power-off fs-18 me-1 align-text-bottom"></i> <b>Logout</b>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </li>
            </ul><!--end topbar-nav-->
        </nav>
    </div>
</div>
<!-- Top Bar End -->

{{-- 🔹 Lightweight Premium Styling --}}
<style>
.product-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: #fff;
    color: #64748b;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-weight: 500;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.product-btn:hover {
    border-color: #cbd5e1;
    background: #f8fafc;
    transform: translateY(-1px);
}

.product-btn.active {
    background: #0f172a;
    color: #fff;
    border-color: #0f172a;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.15);
}

.product-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.product-icon {
    font-size: 16px;
}
</style>

{{-- 🔹 Simple Script: Click → Set Session → Reload --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const productButtons = document.querySelectorAll('.product-btn');

    productButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Prevent double clicks
            if (this.disabled) return;
            
            const productId = this.dataset.product;
            
            // Disable all buttons
            productButtons.forEach(b => b.disabled = true);

            // Set session via AJAX, then reload page
            fetch("{{ route('setActiveProduct') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload page - sidebar and dashboard will render with new session
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                productButtons.forEach(b => b.disabled = false);
                alert('Error switching product. Please try again.');
            });
        });
    });
});
</script>