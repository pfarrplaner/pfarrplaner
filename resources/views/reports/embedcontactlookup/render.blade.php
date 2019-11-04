@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">HTML-Code</div>

                    <div class="card-body instructions">
                        <h3>So bindest du die gewünschte Tabelle in eine TYPO3-Website (Gemeindebaukasten) ein</h3>
                        <ol>
                            <li>Lege ein neues Inhaltselement auf der gewünschten Seite an.
                                <br />
                                <img src="{{ asset('img/typo3/new-element.png') }}" />
                                <br />
                            </li>

                            <li>Wähle den Typ "HTML"
                                <br />
                                <img src="{{ asset('img/typo3/new-html-element.png') }}" />
                                <br />
                            </li>
                            <li>Kopiere den folgenden Code komplett in das Feld "HTML-Code":
                                <div class="form-group">
                            <textarea id="code" class="form-control" rows="10"><script src="https://code.jquery.com/jquery-3.3.1.min.js" type="text/javascript"></script>
<script defer>$(document).ready(function () {
        var url = '{{ $url }}';
        var parish;
        if (parish=localStorage.getItem('parish')) url = url + '&parish='+parish;
        fetch(url).then((res) => {return res.text();}).then((data) => {$('#{{ $randomId }}').html(data);});});
</script>
<div id="{{ $randomId }}">Bitte warten, Daten werden geladen...</div>
                            </textarea>
                                </div>
                                <div class="form-group">
                                    <button id="btnCopy" class="btn btn-secondary btn-sm"><span class="fa fa-copy"></span> Code kopieren</button>
                                    <small id="msgCopied"></small>
                                </div>
                            </li>
                        </ol>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function(){
            $('#code').click(function(){ $('#msgCopied').html(''); });
            $('#code').change(function(){ $('#msgCopied').html(''); });
            $('#code').blur(function(){ $('#msgCopied').html(''); });
            $('#code').on('keyup', function(){ $('#msgCopied').html(''); });
            $('#btnCopy').on('click', function(e){
                e.preventDefault();
                $('#code').focus();
                $('#code').select();
                document.execCommand('copy');
                $('#msgCopied').html('Der Code wurde in die Zwischenablage kopiert.');
            });

        });

    </script>
@endsection
