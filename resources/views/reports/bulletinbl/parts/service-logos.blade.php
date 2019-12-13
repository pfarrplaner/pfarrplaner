<span class="service-logos">
    @if($service->baptism)
        <img class="service-logo" src="{{ resource_path('img/bulletinbl/taufe.png') }}"  height="3mm"/>
    @endif
    @if($service->tags->pluck('code')->contains('familien-gd'))
        <img src="{{ resource_path('img/bulletinbl/familien-gd.png') }}"  height="3mm"/>
    @endif
    @if($service->tags->pluck('code')->contains('kirchenkaffee'))
        <img src="{{ resource_path('img/bulletinbl/kirchenkaffee.png') }}"  height="3mm"/>
    @endif
    @if($service->tags->pluck('code')->contains('musik-im-gd'))
        <img src="{{ resource_path('img/bulletinbl/musik.png') }}"  height="3mm"/>
    @endif
    @if($service->eucharist)
        <img class="service-logo" src="{{ resource_path('img/bulletinbl/abendmahl.png') }}"  height="3mm"/>
    @endif
    @if($service->tags->pluck('code')->contains('mit-posaunenchor'))
        <img src="{{ resource_path('img/bulletinbl/posaunenchor.png') }}"  height="3mm"/>
    @endif
    @if($service->tags->pluck('code')->contains('fahrdienst'))
        <img src="{{ resource_path('img/bulletinbl/fahrdienst.png') }}"  height="3mm"/>
    @endif
    @if($service->tags->pluck('code')->contains('infos-im-gemeindebrief'))
        <img src="{{ resource_path('img/bulletinbl/info.png') }}"  height="3mm"/>
    @endif
</span>
