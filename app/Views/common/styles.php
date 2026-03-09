<!-- Common Styles Component -->
<style>
    body {
        background-color: #f8f9fa;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        margin: 0;
        padding-bottom: 60px;
        /* Space for footer */
    }

    .italic {
        font-style: italic;
        margin-top: -10px;
    }

    .wrapper {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .main-content {
        flex: 1;
    }

    .sidebar {
        background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
        color: white;
        position: fixed;
        height: 100vh;
        width: 16.666667%;
        /* col-lg-2 width */
        padding-top: 1rem;
        overflow-y: auto;
        z-index: 1000;
    }

    /* For smaller screens */
    @media (max-width: 991.98px) {
        .sidebar {
            width: 25%;
            /* col-md-3 width */
        }
    }

    /* Hide scrollbar but keep functionality */
    .sidebar::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
    }

    .sidebar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 3px;
    }

    .sidebar::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.5);
    }

    .sidebar .nav-link {
        color: rgba(255, 255, 255, 0.8);
        padding: 0.75rem 1.5rem;
        margin: 0.25rem 0;
        border-radius: 0.5rem;
        transition: all 0.3s;
    }

    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
        color: white;
        background-color: rgba(255, 255, 255, 0.1);
    }

    .sidebar .nav-link i {
        margin-right: 0.75rem;
        width: 20px;
        text-align: center;
    }

    .main-content {
        padding: 2rem;
        margin-left: 16.666667%;
        /* col-lg-2 width */
        flex: 1;
    }

    /* For smaller screens */
    @media (max-width: 991.98px) {
        .main-content {
            margin-left: 25%;
            /* col-md-3 width */
        }
    }

    /* For mobile screens */
    @media (max-width: 767.98px) {
        .sidebar {
            width: 100%;
            height: auto;
            position: relative;
        }

        .main-content {
            margin-left: 0;
        }
    }

    .card {
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        border: none;
        transition: transform 0.3s;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .header {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        margin-bottom: 2rem;
    }

    .form-control,
    .form-select {
        border-radius: 10px;
        padding: 0.75rem;
    }

    .btn-submit {
        border-radius: 10px;
        padding: 0.75rem 2rem;
    }

    .table {
        border-radius: 10px;
        overflow: hidden;
    }

    .table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-action {
        padding: 0.25rem 0.5rem;
        margin: 0 0.1rem;
    }

    footer {
        margin-top: auto;
        position: fixed;
        bottom: 0;
        left: 16.666667%;
        /* col-lg-2 width */
        right: 0;
        z-index: 1000;
        margin-left: 0;
    }

    /* For smaller screens */
    @media (max-width: 991.98px) {
        footer {
            left: 25%;
            /* col-md-3 width */
        }
    }

    /* For mobile screens */
    @media (max-width: 767.98px) {
        footer {
            left: 0;
            margin-left: 0;
        }
    }
</style>