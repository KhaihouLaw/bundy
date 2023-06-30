@extends('user.advance.components.base')

@section('styles')
    {{-- lorem --}}
@endsection
    
@section('content')
    <div class="punch-clock py-10">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="bg-white border-b border-gray-200">
                    <form method="GET" action="{{ route('punch_clock.access') }}">
                        <div class="mt-10 mb-2 flex flex-col justify-center items-center text-center py-16">
                            <span>Enter Punch Clock Instance Code</span>
                            <div class="flex flex-shrink-0 justify-center items-center rounded-2xl shadow-2xl mt-4">
                                    <input name="access_code" class="m-2 border-2 border-blue-300 rounded-xl w-80" type="password" required/>
                            </div>
                        </div>
                        <div class="flex justify-center items-center mb-10">
                            <div class="flex justify-center items-center bg-gray-300 shadow-2xl rounded-2xl p-1">
                                <button class="px-12 py-6 bg-blue-500 text-white font-black rounded-2xl" type="submit">Open Instance</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://rawgit.com/notifyjs/notifyjs/master/dist/notify.js"></script>
@endsection