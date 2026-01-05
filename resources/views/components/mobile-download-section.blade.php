<section class="bg-gradient-to-r from-blue-50 to-blue-100 py-16 px-4 md:px-8">
    <div class="container mx-auto max-w-4xl">
        {{-- Header --}}
        <div class="text-center mb-12">
            <div class="flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900">
                    Descarga Nuestra App Móvil
                </h2>
            </div>
            <p class="text-lg text-gray-700 max-w-2xl mx-auto">
                Verifica RUT, genera números válidos y lee nuestro blog en cualquier momento, desde cualquier lugar. Disponible en iOS y Android.
            </p>
        </div>

        {{-- Features Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-white rounded-lg p-6 text-center shadow-sm">
                <div class="text-3xl mb-3">🔍</div>
                <h3 class="font-semibold text-gray-900 mb-2">Búsqueda Rápida</h3>
                <p class="text-gray-600 text-sm">Verifica RUT de personas y empresas al instante</p>
            </div>
            <div class="bg-white rounded-lg p-6 text-center shadow-sm">
                <div class="text-3xl mb-3">✨</div>
                <h3 class="font-semibold text-gray-900 mb-2">Generador RUT</h3>
                <p class="text-gray-600 text-sm">Genera RUT válidos con solo un toque</p>
            </div>
            <div class="bg-white rounded-lg p-6 text-center shadow-sm">
                <div class="text-3xl mb-3">📖</div>
                <h3 class="font-semibold text-gray-900 mb-2">Blog Completo</h3>
                <p class="text-gray-600 text-sm">Lee artículos educativos sobre RUT en Chile</p>
            </div>
        </div>

        {{-- Download Buttons --}}
        <div class="bg-white rounded-lg p-8 shadow-md">
            <div class="text-center mb-6">
                <h3 class="text-xl font-bold text-gray-900 mb-2">
                    Elige tu plataforma
                </h3>
                <p class="text-gray-600">Disponible en iOS, Android y como APK directo</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <button
                    onclick="window.location.href='/downloads/rutificador-chile.apk'"
                    class="flex items-center justify-center gap-2 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold py-4 px-6 rounded-lg transition-all transform hover:scale-105 active:scale-95"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <span>Descargar APK</span>
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                </button>

                <button
                    onclick="window.open('https://play.google.com/store/apps/details?id=com.rutificadorchile.app', '_blank')"
                    class="flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-4 px-6 rounded-lg transition-all transform hover:scale-105 active:scale-95"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <span>Google Play</span>
                </button>

                <button
                    onclick="window.open('https://apps.apple.com/cl/app/rutificador-chile/id1234567890', '_blank')"
                    class="flex items-center justify-center gap-2 bg-gradient-to-r from-gray-800 to-gray-900 hover:from-gray-900 hover:to-black text-white font-semibold py-4 px-6 rounded-lg transition-all transform hover:scale-105 active:scale-95"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>App Store</span>
                </button>
            </div>

            {{-- Info Text --}}
            <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <p class="text-sm text-gray-700">
                    <strong>✓ Gratuita:</strong> Descarga y usa la app sin costo alguno. 
                    <strong>✓ Segura:</strong> Tus datos están protegidos. 
                    <strong>✓ Rápida:</strong> Interfaz optimizada para móviles.
                </p>
            </div>
        </div>

        {{-- Requirements --}}
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg p-6">
                <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Requisitos Android
                </h4>
                <ul class="text-sm text-gray-700 space-y-2">
                    <li>✓ Android 6.0 o superior</li>
                    <li>✓ 50 MB de espacio libre</li>
                    <li>✓ Conexión a Internet</li>
                </ul>
            </div>
            <div class="bg-white rounded-lg p-6">
                <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                    <svg class="w-5 h-5 text-gray-800 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Requisitos iOS
                </h4>
                <ul class="text-sm text-gray-700 space-y-2">
                    <li>✓ iOS 13.0 o superior</li>
                    <li>✓ 100 MB de espacio libre</li>
                    <li>✓ Conexión a Internet</li>
                </ul>
            </div>
        </div>
    </div>
</section>

