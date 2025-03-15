<x-filament-panels::page>
    <div
        class="fi-ta-ctn divide-y divide-gray-200 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-900 dark:ring-white/10">
        <div class="fi-ta-header-toolbar flex items-center justify-end gap-x-4 px-4 py-3 sm:px-6">
            <div class="flex shrink-0 items-center gap-x-4">
                <x-filament-tables::search-field/>
            </div>
        </div>
        <div class="@if(count($galleries)) grid @endif grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 p-4">
            @forelse($galleries as $gallery)
                <div class="relative bg-white rounded-lg shadow-md overflow-hidden">
                    <img src=" {{ $gallery->getFirstMediaUrl('cover-collection', 'cover') }}"
                         alt="{{ $gallery->formated_title }}" class="object-cover w-full h-48">
                    <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-90">
                        <div class="p-4 flex flex-col justify-end h-full">
                            <div class="bg-gray-950/50 p-4 rounded flex justify-between">
                                <div class="text-white">
                                    <h2 class="text-lg font-semibold mb-1">{{ $gallery->formated_title }}</h2>
                                    <div class="text-sm text-gray-200">
                                        @if (!$gallery->external)
                                        <p>{{ $gallery->getMedia('gallery-collection')->count() }} {{ __('my-gallery::gallery.files') }}</p>
                                        @else
                                            <p>{{ __('my-gallery::gallery.external') }}</p>
                                        @endif
                                        <p>{{ __('my-gallery::gallery.created') .' : '. $gallery->created_at->diffForHumans() }}</p>
                                        <p>{{ __('my-gallery::gallery.updated') .' : '. $gallery->updated_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center mt-2">
                                    {{ $this->getEditAction($gallery) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div
                    class="fi-ta-content relative divide-y divide-gray-200 overflow-x-auto dark:divide-white/10 dark:border-t-white/10">
                    <div class="fi-ta-empty-state px-6 py-12">
                        <div class="fi-ta-empty-state-content mx-auto grid max-w-lg justify-items-center text-center">
                            <div
                                class="fi-ta-empty-state-icon-ctn mb-4 rounded-full bg-gray-100 p-3 dark:bg-gray-500/20">
                                <svg class="fi-ta-empty-state-icon h-6 w-6 text-gray-500 dark:text-gray-400"
                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5"
                                     stroke="currentColor" aria-hidden="true" data-slot="icon">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M6 18 18 6M6 6l12 12"></path>
                                </svg>
                            </div>

                            <h4 class="fi-ta-empty-state-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                {{ __('my-gallery::gallery.no-items',['item' => $this->getTitle()]) }}
                            </h4>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
    @if(count($galleries))
        <x-filament::pagination
            :paginator="$galleries"
            :page-options="[12, 24, 48, 96]"
            current-page-option-property="itemsPerPage"
        />
    @endif
</x-filament-panels::page>
