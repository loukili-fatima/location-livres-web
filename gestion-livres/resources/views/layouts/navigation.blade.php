<x-nav-link :href="route('books.index')" :active="request()->routeIs('books.index')">
    {{ __('Livres') }}
</x-nav-link>
<x-nav-link :href="route('rentals.index')" :active="request()->routeIs('rentals.index')">
    {{ __('Mes locations') }}
</x-nav-link>
<x-nav-link :href="route('recommendations.index')" :active="request()->routeIs('recommendations.index')">
    {{ __('Pour vous') }}
</x-nav-link>
<x-nav-link :href="route('ml-predictions.index')" :active="request()->routeIs('ml-predictions.index')">
    {{ __('Ma prediction') }}
</x-nav-link>
@if(auth()->user()->isAdmin())
    <x-nav-link :href="route('books.create')" :active="request()->routeIs('books.create')">
        {{ __('Ajouter un livre') }}
    </x-nav-link>
    <x-nav-link :href="route('reporting.index')" :active="request()->routeIs('reporting.index')">
        {{ __('Reporting') }}
    </x-nav-link>
@endif