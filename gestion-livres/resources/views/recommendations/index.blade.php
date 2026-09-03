<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Recommandé pour vous</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <p class="text-gray-500 mb-6">
                Basé sur vos locations précédentes, voici quelques livres qui pourraient vous plaire.
            </p>

            @if($recommandations->isEmpty())
                <p class="text-gray-400">Aucune recommandation disponible pour le moment. Louez quelques livres pour en obtenir !</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($recommandations as $book)
                        <div class="bg-white rounded-lg shadow p-6 flex flex-col justify-between border-2 border-indigo-100">
                            <div>
                                <span class="text-xs text-indigo-600 font-semibold uppercase">{{ $book->category->nom ?? '' }}</span>
                                <h4 class="text-lg font-bold text-gray-900 mt-1">{{ $book->titre }}</h4>
                                <p class="text-gray-600">{{ $book->author->nom ?? 'Auteur inconnu' }}</p>
                            </div>
                            <div class="mt-4">
                                <form method="POST" action="{{ route('rentals.store') }}">
                                    @csrf
                                    <input type="hidden" name="book_id" value="{{ $book->id }}">
                                    <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded">Louer ce livre</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>