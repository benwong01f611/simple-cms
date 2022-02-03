<header class="navbar navbar-expand-lg sticky-top navbar-dark fixed-top bg-dark flex-lg-nowrap p-0 shadow">
    <div class="navbar-brand"><a href="/" class="d-inline-block sitename nav-link text-light">{sitename}</a></div>
    <button class="navbar-toggler position-absolute d-lg-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-toggle" aria-controls="navbar-toggle" aria-expanded="false" aria-label="Toggle navigation">
        <span>
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
            </svg>
        </span>
    </button>
    <div class="collapse navbar-collapse px-3" id="navbar-toggle">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="/backend/pages" id="pageOption" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Pages
                </a>
                <ul class="dropdown-menu bg-secondary" aria-labelledby="pageOption">
                    <li><a class="dropdown-item" href="/backend/pages">Pages</a></li>
                    {url_page_edit}
                    {url_page_delete}
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="/backend/templates" id="templateOption" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Templates
                </a>
                <ul class="dropdown-menu bg-secondary" aria-labelledby="templateOption">
                    <li><a class="dropdown-item" href="/backend/templates">Templates</a></li>
                    {url_template_edit}
                    {url_template_delete}
                </ul>
            </li>
            <li class="nav-item"><a class="nav-link" href="/backend/users">Users</a></li>
            <li class="nav-item"><a class="nav-link" href="/backend/site">Site Details</a></li>
        </ul>
        <div class="align-items-right">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="/backend/user/{username}" id="userAction" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                            </svg>
                            {username}
                        </span>
                    </a>
                    <ul class="dropdown-menu bg-secondary dropdown-menu-end" aria-labelledby="userAction">
                        <li><a class="dropdown-item nav-link" href="/backend/user/{username}">User details</a></li>
                        <li><a class="dropdown-item nav-link" href="/logout">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</header>