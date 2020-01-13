@component('components.ui.card')
    @slot('cardHeader')<span class="fa fa-paperclip"></span> @if(isset($object))Angehängte Dateien @else Dateien anhängen @endif @endslot
    @if (isset($object))
        @if(count($object->attachments))
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>Datei</th>
                        <th>Größe</th>
                        <th>Typ</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($object->attachments as $attachment)
                        <tr class="attachment-row" data-route="{{ route('attachment', $attachment->id) }}" data-attachment="{{ $attachment->id }}">
                            <td><span class="fa {{ \App\Helpers\FileHelper::icon($attachment->file) }}"></span> {{ $attachment->title }}</td>
                            <td>{{ \App\Helpers\FileHelper::bytesToHuman(Storage::size($attachment->file)) }}</td>
                            <td>{{ Storage::mimeType($attachment->file) }}</td>
                            <td class="text-right">
                                <button class="btn btn-sm btn-danger btn-remove-attachment" title="Anhang entfernen" data-attachment="{{ $attachment->id }}"><span class="fa fa-trash"></span></button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p>Es gibt noch keine Dateianhänge.</p>
        @endif
    <hr />
    @endif
    <div id="newAttachments">
    </div>
    <div class="form-group">
        <button class="btn btn-secondary btn-sm" onclick="addAttachmentRow(); return false;"><span class="fa fa-plus"></span> Zeile hinzufügen</button>
    </div>
@endcomponent
