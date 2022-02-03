import Tags from "./tags.min.js";
Tags.init("select:not(.ignore-tags)", {
    clearLabel: 'Clear tag',
    allowClear: true,
    suggestionThresold: 0,
    separator: ";",
    liveServer: true,
    server: "http://127.0.0.1:8082/backend/tags"
});
// Multiple inits should not matter
Tags.init("select:not(.ignore-tags)");

// Bootstrap 5 validation script
(function () {
    'use strict'

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.querySelectorAll('.needs-validation')

    // Loop over them and prevent submission
    Array.prototype.slice.call(forms)
    .forEach(function (form) {
        form.addEventListener('submit', function (event) {
        // apply/remove invalid class
        // Array.from(form.elements).forEach(el => {
        //   console.log(el, el.checkValidity());
        // });

        if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
        }

        form.classList.add('was-validated')
        }, false)
    })
})()