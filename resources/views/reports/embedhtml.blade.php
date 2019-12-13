<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Anleitung</div>

            <div class="card-body instructions">
                <p>So bindest du die gewünschte Tabelle in eine TYPO3-Website (Gemeindebaukasten) ein:</p>
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
                    <li>Kopiere den Code auf der rechten Seite komplett in das Feld "HTML-Code":</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">HTML-Code</div>
            <div class="card-body instructions">
                <div class="form-group">
                    <textarea id="code" class="form-control" rows="10">{{ $slot }}</textarea>
                </div>
            </div>
            <div class="card-footer">
                <button id="btnCopy" class="btn btn-secondary btn-sm"><span class="fa fa-copy"></span> Code kopieren</button>
                <small id="msgCopied"></small>
            </div>
        </div>
    </div>
</div>
