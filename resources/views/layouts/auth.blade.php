<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Karhutala</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f3f5f4;
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #222;
        }

        .container {
            width: 100%;
            max-width: 420px;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,.06);
        }

        h1 {
            margin-top: 0;
        }

        .field {
            margin-bottom: 16px;
        }

        label {
            display: block;
            margin-bottom: 6px;
        }

        input {
            width: 100%;
            padding: 11px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            background: #246b45;
            color: white;
            cursor: pointer;
        }

        .error {
            color: #b42318;
            margin-bottom: 15px;
        }

        .link {
            margin-top: 18px;
            text-align: center;
        }

        /* Custom Pagination */
        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 20px;
        }

        .page-link {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            min-width: 36px;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background: white;
            color: #246b45;
            text-decoration: none;
            font-size: 14px;
        }

        .page-link:hover:not(.disabled):not(.active) {
            background: #f0f5f1;
        }

        .page-link.active {
            background: #246b45;
            color: white;
            border-color: #246b45;
        }

        .page-link.disabled {
            color: #aaa;
            cursor: not-allowed;
            background: #f5f5f5;
        }
        
    </style>
    @yield('styles')
</head>

<body>
    <main class="container">
        @yield('content')
    </main>
</body>
</html>
