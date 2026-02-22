         <form action="{{ route('register') }}" method="POST" class="rounded-2xl p-8 max-w-md space-y-4 bg-gradient-to-br from-indigo-600 via-indigo-700 to-indigo-800 shadow-2xl ring-1 ring-white/10 border border-white/10">
                            @csrf
                            <input type="text" name="name" placeholder="Full name" required autocomplete="name"
                                   class="w-full px-4 py-3 bg-white/15 backdrop-blur-md border border-white/25 text-white placeholder:text-white/70 rounded-xl shadow-inner focus:ring-2 focus:ring-white/40 focus:bg-white/20 focus:border-white/40 transition">
                            <input type="email" name="email" placeholder="Email" required autocomplete="email"
                                   class="w-full px-4 py-3 bg-white/15 backdrop-blur-md border border-white/25 text-white placeholder:text-white/70 rounded-xl shadow-inner focus:ring-2 focus:ring-white/40 focus:bg-white/20 focus:border-white/40 transition">
                            <div class="relative">
                                <input type="password" name="password" id="password" placeholder="Password" required autocomplete="new-password"
                                       class="w-full px-4 py-3 pr-12 bg-white/15 backdrop-blur-md border border-white/25 text-white placeholder:text-white/70 rounded-xl shadow-inner focus:ring-2 focus:ring-white/40 focus:bg-white/20 focus:border-white/40 transition">
                                <button type="button" id="hero-password-toggle" aria-label="Show password" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-white/80 hover:text-white transition rounded">
                                    <svg id="hero-password-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg id="hero-password-eye-off" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878a4.5 4.5 0 106.262 6.262M4 4l3 3m8 0l3 3m0-8l-3 3m8 0l-3 3"/></svg>
                                </button>
                            </div>
                    
                            <div id="passwordHelper" class="hidden text-xs text-white/90 space-y-1 mt-2">
                                <p>Use 10+ characters with letters &amp; numbers</p>
                            </div>
                            <div class="flex items-start gap-2 text-sm text-white/90">
    <input 
        type="checkbox" 
        name="terms" 
        required
        class="mt-1 rounded border-white/50 text-indigo-600 focus:ring-indigo-500"
    >

    <label>
        By continuing, you agree to the 
        <a href="{{ route('legal.terms') }}" class="text-indigo-200 hover:text-white underline">Terms of Service</a> 
        &
        <a href="{{ route('legal.privacy') }}" class="text-indigo-200 hover:text-white underline">Privacy Policy</a>
    </label>
</div>
         <button type="submit"
          class="w-full py-4 rounded-xl font-semibold bg-white text-indigo-700 shadow-xl hover:shadow-2xl hover:scale-[1.02] transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-white/60">
           Try it free
           </button>

           </form>