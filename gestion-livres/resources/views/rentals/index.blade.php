<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Mes locations</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
            @endif

            <table class="w-full bg-white rounded shadow">
                <thead>
                    <tr class="text-left border-b">
                        <th class="p-4">Livre</th>
                        <th class="p-4">Emprunté le</th>
                        <th class="p-4">Retour prévu</th>
                        <th class="p-4">Statut</th>
                        <th class="p-4">Pénalité</th>
                        <th class="p-4"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rentals as $rental)
                        @php $penalite = $rental->calculerPenalite(); @endphp
                        <tr class="border-b">
                            <td class="p-4">{{ $rental->book->titre }}</td>
                            <td class="p-4">{{ $rental->date_emprunt }}</td>
                            <td class="p-4">{{ $rental->date_retour_prevue }}</td>
                            <td class="p-4">{{ $rental->date_retour_reelle ? 'Rendu' : 'En cours' }}</td>
                            <td class="p-4">
                                @if($penalite > 0)
                                    <span class="text-red-600 font-semibold">{{ $penalite }} €</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="p-4">
                                @if(!$rental->date_retour_reelle)
                                    <form method="POST" action="{{ route('rentals.return', $rental) }}">
                                        @csrf
                                        <button type="submit" class="text-sm text-indigo-600">Marquer comme rendu</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
</x-app-layout>