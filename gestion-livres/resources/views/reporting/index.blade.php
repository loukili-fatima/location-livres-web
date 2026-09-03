<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Reporting</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Cartes de chiffres clés -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-gray-500 text-sm">Livres au catalogue</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalLivres }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-gray-500 text-sm">Livres disponibles</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $livresDisponibles }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-gray-500 text-sm">Taux de disponibilité</p>
                    <p class="text-3xl font-bold text-indigo-600">{{ $tauxDisponibilite }}%</p>
                </div>
            </div>

            <!-- Graphique locations par mois -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Locations par mois (6 derniers mois)</h3>
                <canvas id="locationsChart" height="80"></canvas>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Livres les plus loués -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Livres les plus loués</h3>
                    @if($livresPopulaires->isEmpty())
                        <p class="text-gray-400 text-sm">Aucune donnée pour le moment.</p>
                    @else
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 border-b">
                                    <th class="py-2">Livre</th>
                                    <th class="py-2 text-right">Locations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($livresPopulaires as $item)
                                    <tr class="border-b">
                                        <td class="py-2">{{ $item->book->titre ?? 'Livre supprimé' }}</td>
                                        <td class="py-2 text-right font-semibold">{{ $item->nombre_locations }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                <!-- Utilisateurs avec le plus de pénalités -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Utilisateurs avec le plus de pénalités</h3>
                    @if($penalitesParUtilisateur->isEmpty())
                        <p class="text-gray-400 text-sm">Aucune pénalité enregistrée.</p>
                    @else
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 border-b">
                                    <th class="py-2">Utilisateur</th>
                                    <th class="py-2 text-right">Pénalité totale</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($penalitesParUtilisateur as $item)
                                    <tr class="border-b">
                                        <td class="py-2">{{ $item['user']->name }}</td>
                                        <td class="py-2 text-right font-semibold text-red-600">{{ $item['total_penalite'] }} €</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <script>
        const mois = @json($locationsParMois->pluck('mois'));
        const totaux = @json($locationsParMois->pluck('total'));

        new Chart(document.getElementById('locationsChart'), {
            type: 'bar',
            data: {
                labels: mois,
                datasets: [{
                    label: 'Locations',
                    data: totaux,
                    backgroundColor: '#4f46e5'
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    </script>
</x-app-layout>