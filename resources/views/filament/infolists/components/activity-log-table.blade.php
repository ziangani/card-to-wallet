<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div class="space-y-4">
        @foreach($entry->getRecord()->activities()->latest()->get() as $activity)
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="text-gray-600">{{ $activity->created_at->diffForHumans() }}</span>
                        <span class="text-gray-800">{{ $activity->description }}</span>
                    </div>
                    @if($activity->causer)
                        <span class="text-sm text-gray-500">by {{ $activity->causer->name }}</span>
                    @endif
                </div>
                @if($activity->properties->count() > 0)
                    <div class="mt-2">
                        <div class="text-sm text-gray-600">Changes:</div>
                        <div class="mt-1 space-y-1">
                            @foreach($activity->properties['attributes'] ?? [] as $key => $value)
                                @if(isset($activity->properties['old'][$key]))
                                    <div class="flex space-x-2 text-sm">
                                        <span class="text-gray-500">{{ $key }}:</span>
                                        <span class="text-red-500 line-through">{{ $activity->properties['old'][$key] }}</span>
                                        <span class="text-green-500">{{ $value }}</span>
                                    </div>
                                @else
                                    <div class="flex space-x-2 text-sm">
                                        <span class="text-gray-500">{{ $key }}:</span>
                                        <span class="text-green-500">{{ $value }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</x-dynamic-component>
