<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                Users Management
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- FILTER BAR --}}
                    <form method="GET" class="mb-6 flex flex-wrap gap-3 items-center">

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search name or email..."
                            class="rounded-md border-gray-300 text-sm"
                        >

                        <select name="role" class="rounded-md border-gray-300 text-sm">
                            <option value="">All Roles</option>
                            <option value="owner" @selected(request('role')=='owner')>Owner</option>
                            <option value="admin" @selected(request('role')=='admin')>Admin</option>
                        </select>

                        <select name="plan" class="rounded-md border-gray-300 text-sm">
                            <option value="">All Plans</option>
                            <option value="free" @selected(request('plan')=='free')>Free</option>
                            <option value="pro" @selected(request('plan')=='pro')>Pro</option>
                        </select>

                        <button class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm">
                            Filter
                        </button>

                        <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500">
                            Reset
                        </a>
                    </form>


                    {{-- BULK ACTIONS --}}
                    <div class="flex justify-between items-center mb-4 border-b pb-3">
    <div></div>

    <div class="flex justify-end gap-2">
        <button id="bulkPro" class="px-3 py-2 bg-green-600 text-white rounded-md text-sm">
            Make Pro
        </button>

        <button id="bulkFree" class="px-3 py-2 bg-gray-700 text-white rounded-md text-sm">
            Make Free
        </button>

        <button id="bulkDelete" class="px-3 py-2 bg-red-600 text-white rounded-md text-sm">
            Delete
        </button>
    </div>
</div>


                    {{-- TABLE --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">

                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3">
                                        <input type="checkbox" id="selectAll">
                                    </th>
                                    <th class="px-4 py-3 text-xs font-semibold">ID</th>
                                    <th class="px-4 py-3 text-xs font-semibold">Name</th>
                                    <th class="px-4 py-3 text-xs font-semibold">Email</th>
                                    <th class="px-4 py-3 text-xs font-semibold">Role</th>
                                    <th class="px-4 py-3 text-xs font-semibold">Plan</th>
                                    <th class="px-4 py-3 text-xs font-semibold">Created At</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200">

                                @forelse($users as $user)
                                <tr class="hover:bg-gray-50">

                                    <td class="px-4">
                                        <input
                                            type="checkbox"
                                            class="row-check"
                                            value="{{ $user->id }}"
                                        >
                                    </td>

                                    <td class="px-4 py-3 text-sm">{{ $user->id }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $user->name }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $user->email }}</td>

                                    {{-- ROLE --}}
                                    <td class="px-4 py-3 text-sm">
                                        @if($user->role === 'admin')
                                            <span class="px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-800">
                                                🔒 Admin
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-800">
                                                👑 Owner
                                            </span>
                                        @endif
                                    </td>


                                    {{-- PLAN (LIVE EDIT) --}}
                                    <td class="px-4 py-3 text-sm">
                                        @php
                                            $canEdit = $user->role !== 'admin' && auth()->id() !== $user->id;
                                        @endphp

                                        @if($canEdit)
                                            <div class="flex items-center gap-2">
                                                <select
                                                    class="js-plan-select rounded-md border-gray-300 text-sm"
                                                    data-url="{{ route('admin.users.plan.update', $user) }}"
                                                    data-prev="{{ $user->plan }}"
                                                >
                                                    <option value="free" @selected($user->plan=='free')>free</option>
                                                    <option value="pro" @selected($user->plan=='pro')>pro</option>
                                                </select>

                                                <span class="js-plan-status text-xs"></span>
                                            </div>
                                        @else
                                            <span class="px-2 py-0.5 text-xs rounded bg-gray-100">
                                                {{ $user->plan }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 text-sm">
                                        {{ optional($user->created_at)->format('d M Y') }}
                                    </td>

                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-12 text-gray-500">
                                        🔍 No users found
                                    </td>
                                </tr>
                                @endforelse
                                </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>



{{-- ================= JAVASCRIPT ================= --}}
<script>
/* ================= SINGLE PLAN LIVE SAVE ================= */
(() => {

function csrf() {
    return document.querySelector('meta[name="csrf-token"]').content;
}

async function post(url, body) {
    const r = await fetch(url,{
        method:'POST',
        headers:{
            'Content-Type':'application/json',
            'Accept':'application/json',
            'X-CSRF-TOKEN':csrf()
        },
        body:JSON.stringify(body)
    });

    return await r.json();
}

function setStatus(el,msg,color='green'){
    el.textContent = msg;
    el.className = 'text-xs ' + (color==='red'?'text-red-600':'text-green-600');
}

/* SINGLE DROPDOWN */
document.addEventListener('change', async (e)=>{
    const select = e.target.closest('.js-plan-select');
    if(!select) return;

    const status = select.closest('td').querySelector('.js-plan-status');
    const url = select.dataset.url;
    const prev = select.dataset.prev;
    const next = select.value;

    if(prev===next) return;

    select.disabled=true;
    setStatus(status,'Saving...');

    try{
        const res = await post(url,{plan:next});
        select.dataset.prev = res.plan ?? next;

        setStatus(status,'✓ Saved');
        setTimeout(()=>setStatus(status,''),2000);

    }catch{
        select.value = prev;
        setStatus(status,'Failed','red');
    }

    select.disabled=false;
});


/* ================= BULK ACTIONS ================= */

const selectAll = document.getElementById('selectAll');
const checks = ()=> [...document.querySelectorAll('.row-check:checked')].map(c=>c.value);

selectAll?.addEventListener('change',()=>{
    document.querySelectorAll('.row-check').forEach(c=>c.checked=selectAll.checked);
    if (selectAll) selectAll.indeterminate = false;
});

function syncSelectAll() {
    if (!selectAll) return;
    const boxes = [...document.querySelectorAll('.row-check')];
    const checked = boxes.filter(b => b.checked).length;
    selectAll.checked = boxes.length > 0 && checked === boxes.length;
    selectAll.indeterminate = checked > 0 && checked < boxes.length;
}

document.addEventListener('change', (e) => {
    const t = e.target;
    if (t && t.matches && t.matches('.row-check')) {
        syncSelectAll();
    }
});

syncSelectAll();

async function bulk(action){
    const ids = checks();
    if(!ids.length) return alert('Select users first');

    if(action === 'delete' && !confirm('Delete selected users?')) return;

    const res = await fetch("{{ route('admin.users.bulk') }}",{
        method:'POST',
        headers:{
            'Content-Type':'application/json',
            'Accept':'application/json',
            'X-CSRF-TOKEN':csrf()
        },
        credentials: 'same-origin',
        body:JSON.stringify({ids, action})
    });

    const data = await res.json().catch(() => null);
    if (!res.ok || !data || data.success !== true) {
        return alert((data && (data.message || data.error)) || 'Bulk action failed');
    }

    location.reload();
}

document.getElementById('bulkPro').onclick = ()=> bulk('pro');
document.getElementById('bulkFree').onclick = ()=> bulk('free');
document.getElementById('bulkDelete').onclick = ()=> bulk('delete');

})();
</script>
