<x-filament::page>
    <div class="space-y-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
            Contract History for {{ $user->name }}
        </h2>

        <div class="overflow-x-auto rounded-lg shadow border border-gray-200 dark:border-gray-700">
            <table class="w-full text-sm table-fixed">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        @foreach (['#', 'Start Contract', 'End Contract', 'Agreeement Type', 'Division'] as $header)
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-white uppercase whitespace-nowrap">
                                {{ $header }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($contracts->sortBy('start_date')->values() as $index => $contract)
                        @php
                            $today = \Carbon\Carbon::today();
                            $startDate = \Carbon\Carbon::parse($contract->start_date);
                            $endDate = \Carbon\Carbon::parse($contract->end_date);
                            $isActive = $startDate->lte($today) && $endDate->gte($today);
                        @endphp
                        <tr @class([
                            'bg-green-50 dark:bg-green-900' => $isActive,
                            'bg-white dark:bg-gray-800' => !$isActive,
                        ])>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-900 dark:text-white">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-900 dark:text-white">{{ $startDate->format('d M Y') }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-900 dark:text-white">{{ $endDate->format('d M Y') }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-900 dark:text-white">{{ $contract->agreement_type ?? '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-900 dark:text-white">{{ $user->division?->name ?? '-' }}</td>
                          
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-filament::page>
