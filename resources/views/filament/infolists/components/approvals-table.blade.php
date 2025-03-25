<div class="w-full">
    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead>
            <tr class="bg-gray-50 dark:bg-gray-700">
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Level</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actioned By</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Comments</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date/Time</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @foreach ($getState() as $approval)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                            {{ $approval->level_name }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span @class([
                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                            'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-300' => $approval->status === 'APPROVED',
                            'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-300' => $approval->status === 'REJECTED',
                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-300' => $approval->status === 'NEEDS_CLARITY',
                            'bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-300' => !in_array($approval->status, ['APPROVED', 'REJECTED', 'NEEDS_CLARITY']),
                        ])>
                            {{ $approval->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                        {{ $approval->actionedBy?->name }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">
                        {{ $approval->comments }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                        {{ $approval->created_at?->format('Y-m-d H:i:s') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
