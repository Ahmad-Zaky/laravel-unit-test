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
                    <form method="POST" action="{{ route('products.store') }}">
                        
                        @csrf

                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700 @error('title') text-red-500 @enderror">Name</span>
                                <input
                                    type="text"
                                    name="name"
                                    class="block @error('name') border-red-500 @enderror w-full mt-1 rounded-md" placeholder=""
                                    value="{{old('name')}}"
                                />
                            </label>
                        
                            @error('name')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700 @error('content') text-red-500 @enderror">Price</span>
                                <input
                                    type="number"
                                    name="price"
                                    class="block @error('price') border-red-500 @enderror w-full mt-1 rounded-md" placeholder=""
                                    value="{{old('price')}}"
                                />
                            </label>
                        
                            @error('price')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <button type="submit" class="text-white bg-blue-600 rounded-lg text-sm px-5 py-2.5">Submit</button>
                    
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
