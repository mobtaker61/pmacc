<div class="flex items-center space-x-2">
    <a href="{{ route('language.switch', 'fa') }}" class="text-sm text-gray-700 hover:text-gray-900 {{ app()->getLocale() === 'fa' ? 'font-bold' : '' }}">
        فارسی
    </a>
    <span class="text-gray-400">|</span>
    <a href="{{ route('language.switch', 'tr') }}" class="text-sm text-gray-700 hover:text-gray-900 {{ app()->getLocale() === 'tr' ? 'font-bold' : '' }}">
        Türkçe
    </a>
</div> 