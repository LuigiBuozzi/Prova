<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Items</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-gray-50 p-6 text-gray-900 dark:bg-gray-950 dark:text-gray-100">
        <main class="mx-auto max-w-2xl space-y-8">
            <h1 class="text-2xl font-semibold">Add an item</h1>

            @if (session('status'))
                <div class="rounded-md bg-green-100 px-4 py-2 text-green-800 dark:bg-green-900 dark:text-green-100">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('items.store') }}" class="space-y-4 rounded-lg bg-white p-6 shadow dark:bg-gray-900">
                @csrf

                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label for="name" class="mb-1 block text-sm font-medium">Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required
                            class="w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800">
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="color" class="mb-1 block text-sm font-medium">Color</label>
                        <input id="color" name="color" type="text" value="{{ old('color') }}" required
                            class="w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800">
                        @error('color') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="quantity" class="mb-1 block text-sm font-medium">Quantity</label>
                        <input id="quantity" name="quantity" type="number" min="0" value="{{ old('quantity') }}" required
                            class="w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800">
                        @error('quantity') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 font-medium text-white hover:bg-gray-700 dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-gray-300">
                    Add item
                </button>
            </form>

            <section>
                <h2 class="mb-3 text-lg font-semibold">Items</h2>

                @if ($items->isEmpty())
                    <p class="text-gray-500">No items yet.</p>
                @else
                    <table class="w-full overflow-hidden rounded-lg bg-white text-left shadow dark:bg-gray-900">
                        <thead class="bg-gray-100 text-sm dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-2">Name</th>
                                <th class="px-4 py-2">Color</th>
                                <th class="px-4 py-2">Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr class="border-t border-gray-200 dark:border-gray-800">
                                    <td class="px-4 py-2">{{ $item->name }}</td>
                                    <td class="px-4 py-2">{{ $item->color }}</td>
                                    <td class="px-4 py-2">
                                        {{ $item->quantity }}
                                        @if ($item->isLowStock())
                                            <span class="ml-2 rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800 dark:bg-amber-900 dark:text-amber-100">Low stock</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </section>
        </main>
    </body>
</html>
