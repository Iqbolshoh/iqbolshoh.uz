{{-- Shared confirmation for every admin listing. The caller sets `deleteUrl`,
     `deleteName` and `deleteModalOpen` on its own Alpine scope. --}}
<div x-show="deleteModalOpen" x-cloak class="fixed inset-0 z-[60]" role="dialog" aria-modal="true" style="display:none;">
    <div x-show="deleteModalOpen"
        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        @click="deleteModalOpen = false"
        class="fixed inset-0 bg-black/70 backdrop-blur-sm cursor-pointer"></div>

    <div class="flex min-h-screen items-center justify-center p-4 relative z-10">
        <div x-show="deleteModalOpen"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            class="w-full max-w-md rounded-[var(--radius-lg)] bg-[var(--bg-raised)] border border-[var(--border-strong)] shadow-2xl shadow-black/60 p-8">

            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[var(--accent-soft)] border border-[var(--accent-border)]">
                <x-lucide-trash-2 class="h-6 w-6 text-[var(--accent)]" />
            </div>

            <div class="mt-5 text-center">
                <h3 class="text-xl font-bold text-white tracking-tight">Delete {{ $what }}</h3>
                <p class="mt-2 text-sm text-[var(--text-secondary)] leading-relaxed">
                    Are you sure you want to delete
                    <span class="font-semibold text-white" x-text='"\"" + deleteName + "\""'></span>?
                    This cannot be undone.
                </p>
            </div>

            <div class="mt-7 flex flex-col sm:flex-row gap-3">
                <button type="button" @click="deleteModalOpen = false" class="btn-secondary flex-1">Cancel</button>
                <form :action="deleteUrl" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-[0.625rem] rounded-[var(--radius-md)] text-sm font-semibold text-white bg-[var(--accent)] hover:bg-[var(--accent-hover)] transition-colors shadow-lg shadow-[var(--accent-glow)] cursor-pointer">
                        <x-lucide-trash-2 class="w-4 h-4" />
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
