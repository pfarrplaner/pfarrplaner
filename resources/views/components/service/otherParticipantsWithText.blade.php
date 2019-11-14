<div id="otherParticipantsWithText">
    <div class="template" style="display:none;">
        @select(['name' => '', 'label' => '', 'items' => $users, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten'), 'id' => 'peopleTemplate']) @endpeopleselect
    </div>
    <label><span class="fa fa-users"></span> Weitere Dienste</label>
    <div class="row form-group">
        <div class="col-5"><label>Dienstbeschreibung</label></div>
        <div class="col-6"><label>Person(en)</label></div>
        </div>
    <div class="row form-group" style="display:none;" id="templateRow">
        <div class="col-5"></div>
        <div class="col-6">
        </div>
        <div class="col-1">
            <button class="btnDeleteMinistryRow btn btn-danger" title="Zeile löschen"><span class="fa fa-trash"></span></button>
        </div>
    </div>
    @if(isset($service))
        @foreach ($service->ministries() as $ministryTitle => $ministry)
            <div class="row">
                <div class="col-5">
                    @select(['name' => 'ministries['.$loop->index.'][description]', 'label' => '', 'class' => 'ministryTitleSelect', 'value' => (new \Illuminate\Support\Collection([$ministryTitle])), 'items' => $ministries, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten'), 'strings' => true, 'empty' => true]) @endselect
                </div>
                <div class="col-6">
                    @peopleselect(['name' => 'ministries['.$loop->index.'][people][]', 'label' => '', 'people' => $users, 'value' => $ministry, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten'), 'useItemId' => true]) @endpeopleselect
                </div>
                <div class="col-1">
                    <button class="btnDeleteMinistryRow btn btn-danger" title="Zeile löschen"><span class="fa fa-trash"></span></button>
                </div>
            </div>
        @endforeach
    @endif
    <div class="row form-group">
        <div class="col-5">
            @select(['name' => 'ministries['.(isset($service) ? count($service->ministries())+1 : 0).'][description]', 'label' => '', 'class' => 'ministryTitleSelect', 'items' => $ministries, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten'), 'strings' => true, 'empty' => true]) @endselect
        </div>
        <div class="col-6">
            @peopleselect(['name' => 'ministries['.(isset($service) ? count($service->ministries())+1 : 0).'][people][]', 'label' => '', 'people' => $users, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')]) @endpeopleselect
        </div>
        <div class="col-1">
            <button class="btnDeleteMinistryRow btn btn-danger" title="Zeile löschen"><span class="fa fa-trash"></span></button>
        </div>
    </div>
</div>
<div class="form-group">
    <button class="btn btn-secondary" id="btnAddMinistryRow"><span class="fa fa-plus"></span> Weiteren Dienst hinzufügen</button>
</div>
<script>
    var ctrMinistryRows = {{ isset($service) ? count($service->ministries()) : 0 }};

    function enableMinistryRows() {
        $('.btnDeleteMinistryRow').click(function(){
            $(this).parent().parent().remove();
        });

        $('.ministryTitleSelect').selectize({
            create: true,
            placeholder: 'Auswählen oder eingeben',
            render: {
                option_create: function (data, escape) {
                    return '<div class="create">' + escape(data.input) + '</div>';
                }
            },
        });
    }

    $(document).ready(function(){
        enableMinistryRows();

        $('#btnAddMinistryRow').click(function(e){
            e.preventDefault();
            ctrMinistryRows++;
            $('#otherParticipantsWithText').append(
                '<div class="row form-group ministry-row" style="display:none;" id="ministryRow'+ctrMinistryRows+'">'
                +'<div class="col-5">'
                +'<input class="form-control" type="text" name="ministries['+ctrMinistryRows+'][description]" value="" />'
                +'</div>'
                +'<div class="col-6">'
                +'<select type="form-control" name="ministries['+ctrMinistryRows+'][people][]" id="ministrySelect'+ctrMinistryRows+'" multiple placeholder="Eine oder mehrere Personen (keine Anmerkungen!)">'
                +'</select>'
                +'</div>'
                +'<div class="col-1">'
                +'<button class="btnDeleteMinistryRow btn btn-danger" title="Zeile löschen"><span class="fa fa-trash"></span></button>'
                +'</div>'
                +'</div>'
            );
            $('#ministrySelect'+ctrMinistryRows).attr('disabled', $('#peopleTemplate_input').attr('disabled'));
            $('#peopleTemplate_input option').each(function(){
                $('#ministrySelect'+ctrMinistryRows).append('<option value="'+$(this).attr('value')+'">'+$(this).html()+'</option>');
            });
            $('#ministrySelect'+ctrMinistryRows).selectize({
                create: true,
                render: {
                    option_create: function (data, escape) {
                        return '<div class="create">Neue Person anlegen: <strong>' + escape(data.input) + '</strong>&hellip;</div>';
                    }
                },
            });
            $('#ministryRow'+ctrMinistryRows).show();
            enableMinistryRows();
        });


    });
</script>