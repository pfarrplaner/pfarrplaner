<div role="tabpanel" id="special" class="tab-pane fade @if($tab == 'special') in active show @endif ">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="baptism" value="1"
               id="baptism" @if ($service->baptism) checked @endif @cannot('gd-taufe-bearbeiten') disabled @endcannot >
        <label class="form-check-label" for="baptism">
            Taufe(n)
        </label>
    </div>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="eucharist" value="1"
               id="eucharist" @if ($service->eucharist) checked @endif @cannot('gd-abendmahl-bearbeiten') disabled @endcannot >
        <label class="form-check-label" for="eucharist">
            Abendmahl
        </label>
    </div>
    <br />
    <div class="form-group">
        <label for="description">Anmerkungen</label>
        <input type="text" class="form-control" name="description" value="{{ $service->description }}" @canany(['gd-allgemein-bearbeiten','gd-anmerkungen-bearbeiten']) @else disabled @endcanany/>
    </div>
</div>
