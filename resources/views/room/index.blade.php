<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('トーク') }}
      </h2>
  </x-slot>

  <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @foreach ($users as $user)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-4">
            <div class="p-6 text-gray-900">
                <p>{{$user->name}}</p>
                <p>エリア：{{$user->area}}</p>
            </div>
        </div>
        @endforeach
      </div>
  </div>
</x-app-layout>
