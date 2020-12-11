@extends('layouts.app')

@section('title', 'Kirche / Gottesdienstort bearbeiten')

@section('content')
    @component('components.container')
        @component('components.ui.card')
            @slot('cardFooter')
                <button id="form-location-edit-submit" type="submit" class="btn btn-primary">Speichern</button>
            @endslot
            @tabheaders
            @tabheader(['id' => 'home', 'title' => 'Allgemeines', 'active' => request()->get('tab', 'home') == 'home']) @endtabheader
            @tabheader(['id' => 'seating', 'title' => 'Sitzplätze', 'active' => request()->get('tab', 'home') == 'seating']) @endtabheader
            @endtabheaders
            @tabs
            @tab(['id' => 'home', 'active' => request()->get('tab', 'home') == 'home'])
            <form id="form-location-edit" method="post" action="{{ route('locations.update', $location->id) }}">
                @method('PATCH')
                @csrf
                @input(['name' => 'name', 'label' => 'Name', 'value' => $location->name])
                @select(['name' => 'city_id', 'label' => 'Kirchengemeinde', 'items' => $cities, 'value' => $location->city_id])
                @input(['name' => 'default_time', 'label' => 'Gottesdienst um', 'value' => substr($location->default_time, 0, 5)])
                @input(['name' => 'cc_default_location', 'label' => 'Wenn parallel Kinderkirche stattfindet, dann normalerweise hier', 'value' => $location->cc_default_location])
                @selectize(['name' => 'alternate_location_id', 'label' => 'Wenn parallel Kinderkirche stattfindet, dann normalerweise hier', 'items' => $alternateLocations, 'value' => $location->alternate_location_id, 'empty' => true])
                @input(['name' => 'at_text', 'label' => 'Ortsangabe, wenn ein Gottesdienst hier stattfindet', 'placeholder' => 'Ortsangabe, wenn ein Gottesdienst hier stattfindet', 'value' => $location->at_text])
                @input(['name' => 'general_location_name', 'label' => 'Allgemeine Ortsangabe', 'placeholder' => 'z.B.: in Tailfingen', 'value' => $location->general_location_name])
            </form>
            @endtab
            @tab(['id' => 'seating', 'active' => request()->get('tab', 'home') == 'seating'])
            <div class="form-group">
                <a class="btn btn-success"
                   href="{{ route('seatingSection.create', ['location' => $location->id]) }}"><span
                        class="fa fa-plus"></span> Neue Zone hinzufügen</a>
            </div>
            @if(count($location->seatingSections))
                @foreach($location->seatingSections->sortBy('priority') as $seatingSection)
                    <div class="row mb-2">
                        <div class="col-md-8">
                            <b>{{ $seatingSection->title }}</b> ({{ $seatingSection->seating_model->getTitle() }})
                        </div>
                        <div class="col-md-4 text-right">
                            <a class="btn btn-sm btn-success"
                               href="{{ route('seatingRow.create', ['seatingSection' => $seatingSection->id]) }}"><span
                                    class="fa fa-plus"></span> Reihe</a>
                            <a class="btn btn-sm btn-secondary"
                               href="{{ route('seatingSection.edit', $seatingSection) }}"><span
                                    class="fa fa-edit"></span></a>
                            <form class="form-inline" method="post"
                                  action="{{ route('seatingSection.destroy', $seatingSection) }}"
                                  style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger"><span class="fa fa-trash"></span></button>
                            </form>
                        </div>
                        @if(count($seatingSection->seatingRows))
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Bezeichnung</th>
                                    @if(is_a($seatingSection->seating_model, \App\Seating\RowBasedSeatingModel::class))
                                        <th>Sitzplätze</th>
                                        <th>Teilung möglich</th>
                                    @endif
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($seatingSection->seatingRows as $seatingRow)
                                    <tr>
                                        <td>{{ $seatingRow->title }}</td>
                                        @if(is_a($seatingSection->seating_model, \App\Seating\RowBasedSeatingModel::class))
                                            <td>{{ $seatingRow->seats }}</td>
                                            <td>@if($seatingRow->split)@foreach (explode(',', $seatingRow->split) as $splitKey => $split)
                                                    {{ chr(65+$splitKey) }}: {{ $split }}@if(!$loop->last), @endif
                                                @endforeach
                                                @else
                                                    --
                                                @endif
                                            </td>
                                        @endif
                                        <td class="text-right">
                                            <a class="btn btn-sm btn-secondary"
                                               href="{{ route('seatingRow.edit', $seatingRow) }}"><span
                                                    class="fa fa-edit"></span></a>
                                            <form class="form-inline" method="post"
                                                  action="{{ route('seatingRow.destroy', $seatingRow) }}"
                                                  style="display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger"><span class="fa fa-trash"></span></button>
                                            </form>

                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>

                @endforeach
            @else
                Es sind noch keine Zonen definiert.
            @endif
            @endtab
            @endtabs
        @endcomponent
    @endcomponent
@endsection

@section('scripts')
    <script>
        $(document).ready(function(){
            $('#form-location-edit-submit').click(function(){
                $('#form-location-edit').submit();
            });
        });
    </script>
@endsection
