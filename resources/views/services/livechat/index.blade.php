@extends('layouts.app')

@section('styles')
    <style>
        #chat .msg-img {
            text-align: right;
            padding-right: 10px;
        }

        #chat .msg-img img {
            width: 50px;
            border-radius: 25px;
        }

        #chat .msg-text {
            padding: 10px 0 0 10px;
        }

        #chat .msg-text span.text {
            background-color: lightgray;
            width: auto;
            border-radius: 5px;
            padding: 2px 5px;
            margin-bottom: 2px;
        }

        #chat .msg-text .time_date {
            font-size: .7em;
            color: darkgray;
            padding-left: 5px;
        }
    </style>
@endsection

@section('title')
    LiveChat :: @if($service->title){{ $service->title }} @else Gottesdienst am {{ $service->date->format('d.m.Y') }}, {{ $service->timeText() }} @endif
@endsection

@section('content')
    <div id="chat"></div>
@endsection

@section('scripts')
    <script>
        var timer;

        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        function refreshChat() {
            axios.get('https://dev.pfarrplaner.de/services/{{ $service->id }}/livechat/messages')
                .then((response) => {
                    console.log(response.data);

                    var html = '';
                    for (i=0; i<response.data.items.length; i++) {
                        var m = moment(response.data.items[i].snippet.publishedAt).locale('de');
                        html += '<div class="row">\n' +
                            '              <div class="col-sm-1 msg-img img-fluid"> <img src="'+response.data.items[i].authorDetails.profileImageUrl+'" alt="'+response.data.items[i].authorDetails.displayName+'"> </div>\n' +
                            '              <div class="col-sm-11 msg-text">\n' +
                            '                  <span class="text">'+response.data.items[i].snippet.displayMessage+'</span><br />\n' +
                            '                  <span class="time_date">'+capitalizeFirstLetter(m.calendar())+'</span></div>\n' +
                            '              </div>\n' +
                            '            </div>';
                    }
                    $('#chat').html(html);

                    // reset timer to the interval specified by the youtube api
                    clearInterval(timer);
                    timer = setInterval(refreshChat, max(response.data.pollingIntervalMillis, 5000));
                });
        }


        $(document).ready(function () {
            refreshChat();
            timer = setInterval(refreshChat, 1000);

        });
    </script>
@endsection
