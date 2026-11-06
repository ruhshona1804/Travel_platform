@if($flights->isEmpty())
    <x-empty :text="__('ui.no_flights')" />
@else
    <div class="card overflow-x-auto">
        <table class="table-x min-w-[720px]">
            <thead><tr><th>{{ __('ui.airline') }}</th><th>{{ __('ui.from') }}</th><th>{{ __('ui.departure') }}</th><th>{{ __('ui.arrival') }}</th><th>{{ __('ui.duration') }}</th><th>{{ __('ui.stops') }}</th><th>{{ __('ui.baggage') }}</th><th>{{ __('ui.price') }}</th></tr></thead>
            <tbody>@foreach($flights as $f)
                <tr><td><a class="font-semibold text-ocean-600 hover:underline dark:text-turq-300" href="{{ route('airlines.show', $f->airline) }}">{{ $f->airline->name }}</a> @if($f->airline->rating)<x-stars :value="$f->airline->rating" />@endif</td>
                <td>{{ $f->from_city }}</td><td>{{ $f->departure_time ? substr($f->departure_time, 0, 5) : '—' }}</td><td>{{ $f->arrival_time ? substr($f->arrival_time, 0, 5) : '—' }}</td>
                <td>{{ $f->durationLabel() }}</td><td>{{ $f->is_direct ? __('ui.direct') : ($f->transit ?: __('ui.connecting')) }}</td><td>{{ $f->baggage_kg ? $f->baggage_kg.' kg' : '—' }}</td>
                <td><x-price :usd="$f->est_price_usd" :verified="false" />@if($f->is_sample)<span class="badge badge-demo ml-1">{{ __('ui.sample') }}</span>@endif</td></tr>
            @endforeach</tbody>
        </table>
    </div>
    <p class="mt-2 text-xs text-slate-500">⚠️ {{ __('ui.flights_disclaimer') }}</p>
@endif
