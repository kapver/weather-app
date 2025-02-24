<a href="{{route('settings.show')}}">You have new weather notification for {{$cities->keys()->join(', ', ' and ')}}.</a>
@foreach($cities as $city => $item)
<b>{{$city}}</b>
@if(isset($item['pop']))
Precipitation of {{$item['type']}}: {{$item['pop_text']}}.
@endif
@if(isset($item['uvi']))
UV Index: {{$item['uvi_text']}}.
@endif
@endforeach
