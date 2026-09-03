<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Catalogue de livres</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
            @endif

            @if(auth()->user()->isAdmin())
                <a href="{{ route('books.create') }}" class="inline-block mb-6 bg-indigo-600 text-white px-4 py-2 rounded">
                    Ajouter un livre
                </a>
            @endif

            @forelse($booksParCategorie as $category)
                <div class="mb-10">
                    <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">
                        {{ $category->nom }}
                        <span class="text-sm font-normal text-gray-400">({{ $category->books->count() }} livre{{ $category->books->count() > 1 ? 's' : '' }})</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($category->books as $book)
                            <div class="bg-white rounded-lg shadow p-6 flex flex-col justify-between">
                                <div>
                                    <h4 class="text-lg font-bold text-gray-900">{{ $book->titre }}</h4>
                                    <p class="text-gray-600">{{ $book->author->nom ?? 'Auteur inconnu' }}</p>
                                </div>
                                <div class="mt-4">
                                    @if($book->disponible)
                                        <span class="text-green-800 bg-green-100 rounded px-2 py-1 text-xs">Disponible</span>
                                        <form method="POST" action="{{ route('rentals.store') }}" class="mt-3">
                                            @csrf
                                            <input type="hidden" name="book_id" value="{{ $book->id }}">
                                            <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded">Louer ce livre</button>
                                        </form>
                                    @else
                                        <span class="text-red-800 bg-red-100 rounded px-2 py-1 text-xs">Déjà loué</span>
                                    @endif

                                    @if(auth()->user()->isAdmin())
                                        <div class="mt-3 flex gap-3 text-sm">
                                            <a href="{{ route('books.edit', $book) }}" class="text-indigo-600">Modifier</a>
                                            <form method="POST" action="{{ route('books.destroy', $book) }}" onsubmit="return confirm('Supprimer ce livre ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600">Supprimer</button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="text-gray-400">Aucun livre au catalogue pour le moment.</p>
            @endforelse

        </div>
    </div>
</x-app-layout>