@props(['type', 'model'])
@auth
    @php $on = $model->isFavoritedBy(auth()->user()); @endphp
    <form method="POST" action="{{ route('favorites.toggle', [$type, $model->id]) }}">@csrf
        <button class="btn btn-ghost btn-sm" aria-pressed="{{ $on ? 'true' : 'false' }}" aria-label="{{ $on ? __('ui.remove_favorite') : __('ui.add_favorite') }}">{{ $on ? '❤️' : '🤍' }} <span class="hidden sm:inline">{{ $on ? __('ui.saved_label') : __('ui.save') }}</span></button>
    </form>
@else
    <a href="{{ route('login') }}" class="btn btn-ghost btn-sm" aria-label="{{ __('ui.add_favorite') }}">🤍 <span class="hidden sm:inline">{{ __('ui.save') }}</span></a>
@endauth
