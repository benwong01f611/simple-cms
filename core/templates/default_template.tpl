<html>
    <head>
        <title>{sitename}</title>
        <script src="/resources/js/bootstrap.bundle.min.js"></script>
        <link href="/resources/css/bootstrap.min.css" rel="stylesheet">
        <script src="/resources/js/jquery.js"></script>
        <link href="/resources/css/main.css" rel="stylesheet">
    </head>
    <body class="bg-dark d-block">
        <div class="container-md">
            <header class="text-center">
                <a class="fs-2 text-light nav-link d-inline-block sitename" href="/">{sitename}</a>
                <hr>
            </header>
            <ul class="nav justify-content-center">
                <li class="nav-item">
                    <a class="nav-link" href="/">{sitename}</a>
                </li>
            </ul>
            <div class="container-lg">
                {body}
            </div>
            <div class="container-lg">
                Tags: {tags}
            </div>
            <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
                <div class="col-md-4 d-flex align-items-center text-light">
                    <a href="/" class="mb-3 me-2 mb-md-0 text-muted text-decoration-none lh-1">
                        {sitename} 2021-2022
                    </a>
                </div>

                <ul class="nav col-md-4 justify-content-end list-unstyled d-flex">
                    <li class="ms-3">
                        <a class="text-muted github" href="https://github.com/">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-github" viewBox="0 0 16 16">
                                <path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.012 8.012 0 0 0 16 8c0-4.42-3.58-8-8-8z"/>
                            </svg>
                        </a>
                    </li>
                </ul>
            </footer>
        </div>
    </body>
</html>