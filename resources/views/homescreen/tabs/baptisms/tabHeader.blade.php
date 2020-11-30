@tabheader(['id' => $tab->getKey(), 'title' => $tab->getTitle(), 'active' => ($index == 0), 'count' => $tab->getBaptismCount()]) @endtabheader
@if($config['showRequests'])
@tabheader(['id' => $tab->getKey().'Requests', 'title' => 'Taufanfragen', 'count' => $tab->getBaptismRequestCount()]) @endtabheader
@endif
