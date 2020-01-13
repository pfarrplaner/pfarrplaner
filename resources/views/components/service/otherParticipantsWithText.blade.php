<div id="otherParticipantsWithText">
    <div class="template" style="display:none;">
        @select(['name' => '', 'label' => '', 'items' => $users, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten'), 'id' => 'peopleTemplate'])
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
                    @select(['name' => 'ministries['.$loop->index.'][description]', 'label' => '', 'class' => 'ministryTitleSelect', 'value' => $ministryTitle, 'items' => $ministries, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten'), 'strings' => true, 'empty' => true])
                </div>
                <div class="col-6">
                    @peopleselect(['name' => 'ministries['.$loop->index.'][people][]', 'label' => '', 'people' => $users, 'value' => $ministry, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten'), 'useItemId' => true])
                </div>
                <div class="col-1">
                    <button class="btnDeleteMinistryRow btn btn-danger" title="Zeile löschen"><span class="fa fa-trash"></span></button>
                </div>
            </div>
        @endforeach
    @endif
    <div class="row form-group">
        <div class="col-5">
            @select(['name' => 'ministries['.(isset($service) ? count($service->ministries())+1 : 0).'][description]', 'label' => '', 'class' => 'ministryTitleSelect', 'items' => $ministries, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten'), 'strings' => true, 'empty' => true])
        </div>
        <div class="col-6">
            @peopleselect(['name' => 'ministries['.(isset($service) ? count($service->ministries())+1 : 0).'][people][]', 'label' => '', 'people' => $users, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')])
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
</script>