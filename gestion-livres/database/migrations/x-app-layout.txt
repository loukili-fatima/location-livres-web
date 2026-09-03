<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Ajouter un livre</h2>
    </x-slot>

    <div class="py-12 max-w-2xl mx-auto">
        <form method="POST" action="{{ route('books.store') }}" class="bg-white p-6 rounded shadow space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium">Titre</label>
                <input type="text" name="titre" value="{{ old('titre') }}" class="mt-1 w-full border rounded p-2">
                @error('titre') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">ISBN</label>
                <input type="text" name="isbn" value="{{ old('isbn') }}" class="mt-1 w-full border rounded p-2">
                @error('isbn') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Auteur</label>
                <select name="author_id" class="mt-1 w-full border rounded p-2">
                    @foreach($authors as $author)
                        <option value="{{ $author->id }}" {{ old('author_id') == $author->id ? 'selected' : '' }}>
                            {{ $author->nom }}
                        </option>
                    @endforeach
                </select>
                @error('author_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Catégorie</label>
                <select name="category_id" class="mt-1 w-full border rounded p-2">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->nom }}
                        </option>
                    @endforeach
                </select>
                @error('category_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Enregistrer</button>
        </form>
    </div>
</x-app-layout>