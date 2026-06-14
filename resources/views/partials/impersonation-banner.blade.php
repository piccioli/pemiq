@if(session('impersonating_admin_id'))
<div class="w-full bg-yellow-400 text-yellow-900 text-sm font-medium py-2 px-4 flex items-center justify-between">
    <span>
        ⚠️ Stai impersonando <strong>{{ auth()->user()->name }}</strong>
    </span>
    <form method="POST" action="{{ route('impersonate.stop') }}">
        @csrf
        <button type="submit"
                class="underline font-semibold hover:text-yellow-700 transition-colors">
            Esci dall'impersonazione
        </button>
    </form>
</div>
@endif
