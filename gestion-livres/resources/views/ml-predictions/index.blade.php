<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Votre recommandation personnalisee</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-8 text-center">
                @if ($prediction)
                    <p class="text-lg text-gray-700 mb-2">Notre modele predit que vous aimez :</p>
                    <p class="text-3xl font-bold text-indigo-600 mb-4">
                        {{ $prediction->category->nom }}
                    </p>
                    <p class="text-sm text-gray-500">
                        Confiance : {{ number_format($prediction->confiance, 2) }}%
                    </p>
                @else
                    <p class="text-gray-500">
                        Aucune prediction disponible pour le moment. Empruntez quelques livres pour que notre modele apprenne vos preferences.
                    </p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>