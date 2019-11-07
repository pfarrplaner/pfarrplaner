<div role="tabpanel" id="offerings" class="tab-pane fade @if($tab == 'offerings') in active show @endif ">
    <div class="form-group">
        <label for="offerings_counter1">Opferzähler*in 1</label>
        <input class="form-control" type="text" name="offerings_counter1" value="{{ $service->offerings_counter1 }}" @cannot('gd-opfer-bearbeiten') disabled @endcannot />
    </div>
    <div class="form-group">
        <label for="offerings_counter2">Opferzähler*in 2</label>
        <input class="form-control" type="text" name="offerings_counter2" value="{{ $service->offerings_counter2 }}" @cannot('gd-opfer-bearbeiten') disabled @endcannot />
    </div>
    <div class="form-group">
        <label for="offering_goal">Opferzweck</label>
        <input class="form-control" type="text" name="offering_goal" value="{{ $service->offering_goal }}" @cannot('gd-opfer-bearbeiten') disabled @endcannot />
    </div>
    <div class="form-group">
        <label style="display:block;">Opfertyp</label>
        <div class="form-check-inline">
            <input type="radio" name="offering_type" value="" autocomplete="off" @if($service->offering_type == '')checked @endif @cannot('gd-opfer-bearbeiten') disabled @endcannot />
            <label class="form-check-label">
                Eigener Beschluss
            </label>
        </div>
        <div class="form-check-inline">
            <input type="radio" name="offering_type" value="eO" autocomplete="off" @if($service->offering_type == 'eO')checked @endif @cannot('gd-opfer-bearbeiten') disabled @endcannot  />
            <label class="form-check-label">
                Empfohlenes Opfer
            </label>
        </div>
        <div class="form-check-inline disabled">
            <input type="radio" name="offering_type" value="PO" autocomplete="off" @if($service->offering_type == 'PO')checked @endif @cannot('gd-opfer-bearbeiten') disabled @endcannot />
            <label class="form-check-label">
                Pflichtopfer
            </label>
        </div>
    </div>
    <div class="form-group">
        <label for="offering_description">Anmerkungen zum Opfer</label>
        <input class="form-control" type="text" name="offering_description" value="{{ $service->offering_description }}" @cannot('gd-opfer-bearbeiten') disabled @endcannot />
    </div>
    <div class="form-group">
        <label for="offering_amount">Betrag</label>
        <input class="form-control" type="text" name="offering_amount" value="{{ $service->offering_amount }}" @cannot('gd-opfer-bearbeiten') disabled @endcannot />
    </div>
</div>
