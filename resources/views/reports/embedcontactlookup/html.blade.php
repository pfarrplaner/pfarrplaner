<script defer>$(document).ready(function () {
        var url = '{{ $url }}';
        var parish;
        if (parish = localStorage.getItem('parish')) url = url + '&parish=' + parish;
        fetch(url).then((res) => {
            return res.text();
        }).then((data) => {
            $('#{{ $randomId }}').html(data);
        });
    });
</script>
<div id="{{ $randomId }}">Bitte warten, Daten werden geladen...</div>
