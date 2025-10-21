<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Anime Search')</title>

    <!-- Bootstrap CSS (CDN) -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    >

    <style>
      body {
        background-color: #0f172a;
        color: #e2e8f0;
        font-family: system-ui, sans-serif;
      }
      .card {
        background-color: #1e293b;
        border: none;
        color: #e2e8f0;
      }
      a { text-decoration: none; color: #a5b4fc; }
    .pagination .page-link {
        background-color: #1e293b;
        border-color: #475569;
        color: #93c5fd;
    }
    .pagination .page-item.active .page-link {
        background-color: #2563eb;
        border-color: #2563eb;
        color: white;
    }
    .pagination .page-link:hover {
        background-color: #334155;
        border-color: #64748b;
    }
    </style>
  </head>
  <body>
    <nav class="navbar navbar-dark bg-dark">
      <div class="container">
        <a class="navbar-brand" href="/">Anime Catalog</a>
      </div>
    </nav>

    <div class="container py-4">
      @yield('content')
    </div>

    <!-- Bootstrap JS (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
  </body>
</html>
