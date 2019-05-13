<div role="tabpanel" class="tab-pane fade @if($tab == 'cc') in active show @endif " id="cc">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="cc" value="1"
               id="cc-check" @cannot('gd-kinderkirche-bearbeiten') disabled @endcannot @if($service->cc) checked @endif/>
        <label class="form-check-label" for="cc">
            Parallel findet Kinderkirche statt
        </label>
    </div>
    <br />
    <div class="form-group">
        <label for="cc_location">Ort der Kinderkirche:</label>
        <input class="form-control" type="text" name="cc_location" placeholder="Leer lassen für " value="{{ $service->cc_location }}" @cannot('gd-kinderkirche-bearbeiten') disabled @endcannot />
    </div>
    <div class="form-group">
        <label for="cc_lesson">Lektion:</label>
        <input class="form-control" type="text" name="cc_lesson" value="{{ $service->cc_lesson }}" @cannot('gd-kinderkirche-bearbeiten') disabled @endcannot />
    </div>
    <div class="form-group">
        <label for="cc_staff">Mitarbeiter:</label>
        <input class="form-control" type="text" name="cc_staff" placeholder="Name, Name, ..." value="{{ $service->cc_staff }}" @cannot('gd-kinderkirche-bearbeiten') disabled @endcannot />
    </div>
</div>
