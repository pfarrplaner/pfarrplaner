@if ($street == '')
<div id="{{ $randomId }}" class="row ctype-textbox listtype-none showmobdesk-0">
    <div id="c1050561" class="col s12 bullme ">
        <div class="card-panel default">

            <p class="bodytext"><b>Ihr Ansprechpartner</b></p>
        <p class="bodytext">Damit wir einen Ansprechpartner für Sie ermitteln können, geben Sie bitte hier Ihre Adresse ein:</p>
            <div>
                <label for="street">Straße:</label>
                <select id="street">
                    @foreach ($streets as $street)
                        <option>{{ $street }}</option>
                    @endforeach
                </select><br />
                <label for="number">Hausnummer</label>
                <input id="number" value="" type="text"/>
                <button id="btnFind">Ansprechpartner finden</button>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.min.js" type="text/javascript"></script><script defer>
    $(document).ready(function(){
        $('#btnFind').click(function(){
            var url = '{{ $url }}&street='+$('#street').val()+'&number='+$('#number').val();
            if (($('#street').val() != '') && ($('number').val() != '')) {
                $('#{{ $randomId }}').html('Bitte warten, Daten werden übertragen...');
                fetch(url)
                    .then((res) => {return res.text();})
                    .then((data) => {
                        $('#{{ $randomId }}').html(data);
                    });
            }
        })

    });
</script>
@else
    <?php $parish = \App\Parish::byAddress($street, $number)->first() ?>
    <div id="{{ $randomId }}" class="row ctype-textbox listtype-none showmobdesk-0">
        <div id="c1050561" class="col s12 bullme ">
            <div class="card-panel default">

                <p class="bodytext"><b>Ihr Ansprechpartner</b></p>
                <p class="bodytext">{{ $parish->name }}</p>
            </div>
        </div>
    </div>
@endif