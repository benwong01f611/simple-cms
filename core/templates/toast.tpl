<div class="position-fixed top-0 start-50 pt-2 translate-middle-x" style="z-index: 999999">
    <div id="liveToast" style="min-width: 350px; width: max-content; max-width: 60vw;" class="toast align-items-center text-white {bg} border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
        <div class="d-flex">
            <div class="toast-body text-break text-center">
                <span class="text-center">{toastbody}</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
<script>(new bootstrap.Toast(document.getElementById("liveToast"))).show()</script>