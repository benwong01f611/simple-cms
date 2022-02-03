<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="/resources/css/table.css" rel="stylesheet">
<div class="table-responsive">
    <table class="table table-bordered table-hover table-striped justify-content-md-center">
        <tr style="height:1px; flex-wrap: nowrap;">
            {header_row}
        </tr>
        {content_row}
    </table>
</div>
<script>
$('.table-responsive').on('show.bs.dropdown', function () {
     $('.table-responsive').css( "overflow", "inherit" );
});

$('.table-responsive').on('hide.bs.dropdown', function () {
     $('.table-responsive').css( "overflow", "auto" );
})
</script>