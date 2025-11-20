@extends('user.layouts.app')

@push('styles')
    <style>
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }

      :root {
        --primary: #2563eb;
        --primary-light: #3b82f6;
        --primary-lighter: #60a5fa;
        --accent: #0ea5e9;
        --success: #22c55e;
        --warning: #f59e0b;
        --danger: #ef4444;
        --gray-50: #fafafa;
        --gray-100: #f5f5f5;
        --gray-200: #e5e5e5;
        --gray-300: #d4d4d4;
        --gray-400: #a3a3a3;
        --gray-500: #737373;
        --gray-600: #525252;
        --gray-700: #404040;
        --gray-800: #262626;
        --gray-900: #171717;
        --shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        --shadow-lg: 0 4px 6px -1px rgba(0, 0, 0, 0.08);
      }

      body {
        font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI",
          sans-serif;
        line-height: 1.6;
        color: var(--gray-700);
        background: #ffffff;
      }

      /* Header */
      .header {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        color: var(--gray-800);
        padding: 2rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        position: sticky;
        top: 0;
        z-index: 100;
        border-bottom: 1px solid var(--gray-200);
      }

      .header-content {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
      }

      .header h1 {
        font-size: 1.5rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.5rem;
      }

      .base-url {
        background: var(--gray-100);
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-family: "Monaco", monospace;
        font-size: 0.85rem;
        color: var(--gray-700);
        border: 1px solid var(--gray-200);
      }

      /* Layout */
      .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 2rem;
      }

      /* Sidebar */
      .sidebar {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: var(--shadow);
        height: fit-content;
        position: sticky;
        top: 120px;
        border: 1px solid var(--gray-200);
      }

      .nav-section {
        margin-bottom: 1.5rem;
      }

      .nav-title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--gray-600);
        margin-bottom: 0.75rem;
      }

      .nav-item {
        padding: 0.5rem 0.75rem;
        cursor: pointer;
        font-size: 0.9rem;
        color: var(--gray-700);
        border-radius: 6px;
        transition: all 0.2s;
        margin-bottom: 0.25rem;
        border: 1px solid transparent;
      }

      .nav-item:hover {
        background: var(--gray-100);
        color: var(--primary);
        border-color: var(--gray-300);
      }

      .nav-item.active {
        background: linear-gradient(
          135deg,
          var(--primary) 0%,
          var(--accent) 100%
        );
        color: white;
        font-weight: 600;
        border-color: var(--primary);
      }

      /* Main Content */
      .main-content {
        background: white;
        border-radius: 12px;
        padding: 2.5rem;
        box-shadow: var(--shadow);
        min-height: 80vh;
        border: 1px solid var(--gray-200);
      }

      .section {
        display: none;
        animation: fadeIn 0.3s;
      }

      .section.active {
        display: block;
      }

      @keyframes fadeIn {
        from {
          opacity: 0;
          transform: translateY(10px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }

      .section-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 0.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--gray-200);
      }

      .section-subtitle {
        color: var(--gray-600);
        margin-bottom: 2rem;
        font-size: 1.1rem;
      }

      /* Cards */
      .card {
        background: var(--gray-50);
        border: 1px solid var(--gray-200);
        border-radius: 8px;
        padding: 1.5rem;
        margin: 1.5rem 0;
      }

      .card-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
      }

      .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
      }

      .badge-post {
        background: #3b82f6;
        color: white;
      }
      .badge-get {
        background: var(--success);
        color: white;
      }
      .badge-put {
        background: var(--warning);
        color: white;
      }
      .badge-delete {
        background: var(--danger);
        color: white;
      }

      .endpoint-path {
        font-family: "Monaco", monospace;
        font-size: 1rem;
        color: var(--gray-800);
        font-weight: 600;
      }

      .code-block {
        background: var(--gray-900);
        color: #e5e7eb;
        padding: 1.25rem;
        border-radius: 8px;
        font-family: "Monaco", monospace;
        font-size: 0.85rem;
        overflow-x: auto;
        line-height: 1.6;
        margin: 1rem 0;
        box-shadow: var(--shadow);
      }

      .code-block .key {
        color: #8b5cf6;
      }
      .code-block .string {
        color: #10b981;
      }
      .code-block .number {
        color: #f59e0b;
      }
      .code-block .comment {
        color: #9ca3af;
      }

      .param-grid {
        display: grid;
        gap: 0.75rem;
        margin: 1rem 0;
      }

      .param {
        background: white;
        padding: 1rem;
        border-radius: 6px;
        border-left: 3px solid var(--primary);
      }

      .param-name {
        font-weight: 700;
        color: var(--gray-900);
        font-family: "Monaco", monospace;
        font-size: 0.9rem;
      }

      .param-required {
        color: var(--danger);
        font-size: 0.75rem;
        margin-left: 0.5rem;
      }

      .param-desc {
        color: var(--gray-600);
        font-size: 0.9rem;
        margin-top: 0.25rem;
      }

      .feature-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1rem;
        margin: 1.5rem 0;
      }

      .feature-card {
        background: white;
        padding: 1.5rem;
        border-radius: 8px;
        border: 2px solid var(--gray-200);
        transition: all 0.3s;
      }

      .feature-card:hover {
        border-color: var(--primary);
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
      }

      .feature-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(
          135deg,
          var(--primary) 0%,
          var(--accent) 100%
        );
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        margin-bottom: 1rem;
      }

      .feature-title {
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 0.5rem;
      }

      .feature-desc {
        color: var(--gray-600);
        font-size: 0.9rem;
      }

      .status-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 1rem;
        margin: 1.5rem 0;
      }

      .status-code {
        padding: 1rem;
        border-radius: 8px;
        text-align: center;
        color: white;
        font-weight: 700;
      }

      .status-200 {
        background: var(--success);
      }
      .status-400 {
        background: var(--warning);
      }
      .status-401 {
        background: #f59e0b;
      }
      .status-404 {
        background: var(--danger);
      }
      .status-500 {
        background: #991b1b;
      }

      .info-box {
        background: #dbeafe;
        border-left: 4px solid #3b82f6;
        padding: 1rem;
        border-radius: 6px;
        margin: 1rem 0;
        font-size: 0.9rem;
        color: var(--gray-700);
      }

      .warning-box {
        background: #fef3c7;
        border-left: 4px solid #f59e0b;
        padding: 1rem;
        border-radius: 6px;
        margin: 1rem 0;
        font-size: 0.9rem;
        color: var(--gray-700);
      }

      h3 {
        color: var(--gray-900);
        font-size: 1.1rem;
        font-weight: 700;
        margin: 1.5rem 0 0.75rem 0;
      }

      .api-table {
        width: 100%;
        border-collapse: collapse;
        margin: 1rem 0;
      }

      .api-table th,
      .api-table td {
        padding: 0.75rem;
        text-align: left;
        border-bottom: 1px solid var(--gray-200);
      }

      .api-table th {
        background: var(--gray-100);
        font-weight: 700;
        color: var(--gray-800);
      }

      .api-table tr:hover {
        background: var(--gray-50);
      }

      /* Mobile */
      @media (max-width: 968px) {
        .container {
          grid-template-columns: 1fr;
        }

        .sidebar {
          position: relative;
          top: 0;
        }

        .header-content {
          flex-direction: column;
          gap: 1rem;
          text-align: center;
        }
      }
    </style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
               
        </div>
    </div>
</div>
@endsection
    <script>
      // Navigation handling
      document.querySelectorAll(".nav-item").forEach((item) => {
        item.addEventListener("click", function () {
          // Remove active class from all items and sections
          document
            .querySelectorAll(".nav-item")
            .forEach((i) => i.classList.remove("active"));
          document
            .querySelectorAll(".section")
            .forEach((s) => s.classList.remove("active"));

          // Add active class to clicked item
          this.classList.add("active");

          // Show corresponding section
          const sectionId = this.getAttribute("data-section");
          document.getElementById(sectionId).classList.add("active");

          // Smooth scroll to top
          window.scrollTo({ top: 0, behavior: "smooth" });
        });
      });
    </script>
@endpush