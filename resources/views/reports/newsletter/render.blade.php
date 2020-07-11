@extends('layouts.app')

@section('content')
    <table>
        <thead></thead>
        <tbody>
        @foreach($serviceList as $dayTitle => $services)
            @if(count($services))
                <tr>
                    <td colspan="3"
                        style="font-size: 12px; font-family: verdana, arial, helvetica, sans-serif; padding: 12px 0 12px 0; color: #804070;">
                        <strong>{!!  $dayTitle !!}</strong>
                    </td>
                </tr>
                @if(isset($services[0]) && ($services[0]->offering_goal!=''))
                    <tr>
                        <td colspan="3"
                            style="font-size: 12px; font-family: verdana, arial, helvetica, sans-serif; padding: 0 0 12px 0;">
                            Opfer: {{ $services[0]->offering_goal }}
                        </td>
                    </tr>
                @endif

                @foreach ($services as $service)
                    <tr>
                        <td style="font-size: 12px; font-family: verdana, arial, helvetica, sans-serif; padding: 0 30px 0 0;">
                            {{ $service->timeText(true, '.') }}</td>
                        <td style="font-size: 12px; font-family: verdana, arial, helvetica, sans-serif; padding: 0 30px 0 0;">
                            <strong>{{ $service->titleText(false, false) }}</strong> ({{ $service->locationText() }})
                        </td>
                        <td style="font-size: 12px; font-family: verdana, arial, helvetica, sans-serif; padding: 0;">
                            <strong>{{ $service->participantsText('P') }}</strong></td>
                    </tr>
                @endforeach
            @endif
        @endforeach
        </tbody>
    </table>
    <hr/>
    <textarea class="form-control" rows="20"><table>
        <thead></thead>
        <tbody>
        @foreach($serviceList as $dayTitle => $services)
            @if(count($services))
                <tr>
                <td colspan="3"
                    style="font-size: 12px; font-family: verdana, arial, helvetica, sans-serif; padding: 12px 0 12px 0; color: #804070;">
                    <strong>{!!  $dayTitle !!}</strong>
                </td>
            </tr>
                @if(isset($services[0]) && ($services[0]->offering_goal!=''))
                    <tr>
                <td colspan="3"
                    style="font-size: 12px; font-family: verdana, arial, helvetica, sans-serif; padding: 0 0 12px 0;">
                    Opfer: {{ $services[0]->offering_goal }}
                </td>
            </tr>
                @endif

                @foreach ($services as $service)
                    <tr>
                    <td style="font-size: 12px; font-family: verdana, arial, helvetica, sans-serif; padding: 0 30px 0 0;">
                        {{ $service->timeText(true, '.') }}</td>
                    <td style="font-size: 12px; font-family: verdana, arial, helvetica, sans-serif; padding: 0 30px 0 0;">
                        <strong>{{ $service->titleText(false, false) }}</strong> ({{ $service->locationText() }})
                    </td>
                    <td style="font-size: 12px; font-family: verdana, arial, helvetica, sans-serif; padding: 0;">
                        <strong>{{ $service->participantsText('P') }}</strong></td>
                </tr>
                @endforeach
            @endif
        @endforeach
        </tbody>
    </table></textarea>
@endsection
