<div class="modal fade {{ $modal_name }}" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<script>
    function generateSlug() {
        let name = document.getElementById('name').value;
        let slug = name.toLowerCase()
            .replace(/ /g, '-')
            .replace(/[^\w\u1780-\u17FF-]+/g, '');
        document.getElementById('slug').value = slug;
    }

    $('.btn_add').click(function(e) {
        var tbody = $('.tbody');
        var numRows = tbody.find("tr").length;
        $.ajax({
            type: "get",
            url: window.location.href,
            data: {
                "key": numRows
            },
            dataType: "json",
            success: function(response) {
                $(tbody).append(response.tr);
            }
        });
    });
    $(document).on('click', '.btn-edit', function() {
        $("div.{{ $modal_name }}").load($(this).data('href'), function() {
            $(this).modal('show');
        });
    });
</script>
