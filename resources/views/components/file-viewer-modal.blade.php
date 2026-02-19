@props([
    'modalId' => 'fileViewerModal'
])

<div 
    x-data="fileViewerModal()" 
    class="fixed inset-0 z-50" 
    x-show="showModal"
    x-cloak
    @open-file-viewer.window="openModal($event.detail.url, $event.detail.name)"
>
    <!-- Backdrop -->
    <div 
        @click="closeModal()" 
        class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
        x-transition
    ></div>

    <!-- Modal Container -->
    <div class="fixed inset-0 overflow-y-auto flex items-center justify-center pointer-events-none">
        <div 
            @click.stop 
            class="bg-white rounded-lg shadow-xl w-full mx-4 max-w-4xl max-h-[90vh] flex flex-col transform transition-all pointer-events-auto"
            x-transition
        >
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900" x-text="fileName"></h3>
                <button 
                    @click="closeModal()" 
                    class="text-gray-400 hover:text-gray-500 focus:outline-none"
                >
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>

            <!-- Content -->
            <div class="flex-1 overflow-auto p-6 bg-gray-50">
                <!-- PDF Viewer -->
                <div x-show="fileType === 'pdf'" class="h-full">
                    <iframe 
                        :src="fileUrl" 
                        class="w-full rounded border border-gray-300"
                        style="min-height: 500px;"
                    ></iframe>
                </div>

                <!-- Image Viewer -->
                <div x-show="fileType === 'image'" class="flex justify-center items-center min-h-[500px]">
                    <img 
                        :src="fileUrl" 
                        :alt="fileName"
                        class="max-h-[70vh] max-w-full rounded border border-gray-300"
                    />
                </div>

                <!-- Unsupported File Type -->
                <div x-show="fileType === 'unsupported'" class="text-center py-12 min-h-[500px] flex flex-col justify-center">
                    <div class="flex justify-center mb-4 text-gray-400">
                        <x-heroicon-o-document class="w-16 h-16" />
                    </div>
                    <p class="text-gray-600 mb-4">File type not supported for preview</p>
                    <a 
                        :href="fileUrl" 
                        target="_blank" 
                        rel="noopener noreferrer"
                        class="inline-block px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
                    >
                        Download File
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function fileViewerModal() {
    return {
        showModal: false,
        fileUrl: '',
        fileName: '',
        fileType: 'unsupported',
        fileSizeText: '',

        openModal(url, name, size = null) {
            this.fileUrl = url;
            this.fileName = name;
            this.showModal = true;
            document.body.style.overflow = 'hidden';
            
            // Determine file type from URL
            const ext = (name || 'file').split('.').pop().toLowerCase();
            
            if (['pdf'].includes(ext)) {
                this.fileType = 'pdf';
            } else if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                this.fileType = 'image';
            } else {
                this.fileType = 'unsupported';
            }

            // Format file size if provided
            this.fileSizeText = size ? this.formatFileSize(size) : '';
        },

        closeModal() {
            this.showModal = false;
            this.fileUrl = '';
            this.fileName = '';
            this.fileType = 'unsupported';
            document.body.style.overflow = 'auto';
        },

        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
        }
    }
}
</script>