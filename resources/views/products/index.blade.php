<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="row mb-4">
                        <div class="col-lg-12 margin-tb">
                            <div class="pull-right">
                                <a class="px-4 py-1 hover:text-gray-400 hover:bg-gray-700 border rounded-lg"
                                    href="{{ route('products.create') }}"> {{  __('New Product') }} +</a>
                            </div>
                        </div>
                    </div>

                    @if ($message = Session::get('success'))
                        <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md mb-4"
                            role="alert">
                            <div class="flex">
                                <div class="py-1"><svg class="fill-current h-6 w-6 text-teal-500 mr-4"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path
                                            d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z" />
                                    </svg></div>
                                <div>
                                    <p class="font-bold">{{ $message }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($message = Session::get('error'))
                        <div class="bg-red-100 border-t-4 border-red-500 rounded-b text-teal-900 px-4 py-3 shadow-md mb-4"
                            role="alert">
                            <div class="flex">
                                <div class="py-1"><svg class="fill-current h-6 w-6 text-red-500 mr-4"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path
                                            d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z" />
                                    </svg></div>
                                <div>
                                    <p class="font-bold">{{ $message }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <table class="w-full whitespace-no-wrapw-full whitespace-no-wrap">
                        <thead>
                            <tr class="text-center font-bold">
                                <td class="border px-6 py-4">Name</td>
                                <td class="border px-6 py-4">Price USD</td>
                                <td class="border px-6 py-4">Price EUR</td>
                                <td class="border px-6 py-4">Actions</td>
                            </tr>
                        </thead>
                        @forelse($products as $product)
                            <tr class="text-center">
                                <td class="border px-6 py-4">{{ $product->name }}</td>
                                <td class="border px-6 py-4">{{ number_format($product->price, 2) }}</td>
                                <td class="border px-6 py-4">{{ number_format($product->price_eur, 2) }}</td>

                                <td class="border px-6 py-4">

                                    <a href="{{ route('products.edit', $product->id) }}"
                                        class="px-4 py-1 text-sm hover:text-gray-400 hover:bg-gray-700 border rounded-lg">{{ __('Edit') }}</a>

                                    @if (authAdmin())

                                        <form method="POST" action="{{ route('products.destroy', $product->id) }}"
                                            style="display: inline-block">

                                            @csrf

                                            @method('DELETE')

                                            <a href="#"
                                                class="px-4 py-1 text-sm hover:text-gray-400 hover:bg-gray-700 border rounded-lg"
                                                onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Delete') }}</a>
                                        </form>
                                        
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="border px-6 py-4 text-center">{{ __('No products found !') }}</td>
                            </tr>
                        @endforelse
                    </table>

                    <div class="mt-2">
                        {!! $products->links() !!}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
