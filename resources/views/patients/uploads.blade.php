@extends('layouts.app')
@push('styles')
<style>
    .upload-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        padding: 16px 24px;
        /* spacing inside */
        border-bottom: 1px solid #e9ecef;
        background-color: #cce5ff;
        border-radius: 20px;

        /* subtle shadow */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .upload-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #202124;
        margin: 0;
    }

    .upload-actions {
        display: flex;
        gap: 12px;
    }



    .btn-view-toggle {
        background: #f1f3f4;
        color: #5f6368;
        border: none;
        padding: 8px 12px;
        border-radius: 20px;
        font-size: 14px;
        transition: all 0.2s ease;
    }

    .btn-view-toggle:hover {
        background: #e8eaed;
        color: #202124;
    }

    .btn-view-toggle.active {
        background: #e8f0fe;
        color: #1a73e8;
    }

    /* Files Grid */
    .files-container {
        background: #eef6ffff;
        border-radius: 12px;
        padding: 16px;
        min-height: 400px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .files-header {
        display: flex;
        justify-content: space-between;
        /* left vs right */
        align-items: center;
        padding: 16px 24px;
        background-color: #cce5ff;
        border-radius: 7px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        margin-bottom: 24px;
    }

    /* Left side: title + file count together */
    .files-header .d-flex {
        display: flex;
        align-items: center;
        gap: 12px;
        /* spacing between title and count */
    }

    .files-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 16px;
        padding: 16px;
    }

    .files-list {
        display: none;
    }

    .files-list.active {
        display: block;
    }

    .files-grid.active {
        display: grid;
    }

    .file-item {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 16px;
        text-align: center;
        transition: all 0.2s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .file-item:hover {
        border-color: #1a73e8;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .file-item.uploading {
        opacity: 0.7;
        pointer-events: none;
    }

    .file-icon {
        width: 48px;
        height: 48px;
        margin: 0 auto 12px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
    }

    .file-icon.pdf {
        background: #ea4335;
    }

    .file-icon.doc {
        background: #4285f4;
    }

    .file-icon.image {
        background: #34a853;
    }

    .file-icon.excel {
        background: #0f9d58;
    }

    .file-icon.default {
        background: #9aa0a6;
    }

    .file-name {
        font-size: 13px;
        font-weight: 500;
        color: #202124;
        margin: 0 0 4px 0;
        line-height: 1.3;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .file-size {
        font-size: 11px;
        color: #5f6368;
        margin: 0;
    }

    .file-actions {
        position: absolute;
        top: 8px;
        right: 8px;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .file-item:hover .file-actions {
        opacity: 1;
    }

    .file-action-btn {
        background: rgba(0, 0, 0, 0.6);
        color: white;
        border: none;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        font-size: 12px;
        cursor: pointer;
        margin-left: 4px;
        transition: background 0.2s ease;
    }

    .file-action-btn:hover {
        background: rgba(0, 0, 0, 0.8);
    }

    /* Smooth transitions and touch-friendly defaults for buttons */
    .btn-general,
    .btn-view-toggle,
    .file-action-btn {
        transition: transform 0.12s ease, box-shadow 0.12s ease, background 0.12s ease;
        -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
        touch-action: manipulation;
    }

    .btn-general:active,
    .btn-view-toggle:active {
        transform: translateY(1px) scale(0.997);
    }

    .btn-view-toggle.active {
        box-shadow: 0 6px 18px rgba(26, 115, 232, 0.12);
    }

    /* File Viewer Modal - Simplified Bootstrap 5 Style */
    .file-viewer-modal {
        display: none;
    }

    .file-viewer-iframe {
        width: 100%;
        height: 70vh;
        border: none;
        border-radius: 0.375rem;
    }

    .file-viewer-content {
        background: #f8f9fa;
        border-radius: 0.375rem;
        padding: 20px;
        text-align: center;
        min-height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .file-viewer-embed {
        width: 100%;
        height: 70vh;
        border: none;
        border-radius: 0.375rem;
        background: white;
    }

    /* Progress Bar */
    .upload-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: #e9ecef;
        overflow: hidden;
    }

    .upload-progress-bar {
        height: 100%;
        background: #1a73e8;
        width: 0%;
        transition: width 0.3s ease;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 48px 24px;
        color: #5f6368;
    }

    .empty-state-icon {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    .empty-state-text {
        font-size: 16px;
        margin: 0;
    }

    /* List View */
    .file-list-item {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        border-bottom: 1px solid #f1f3f4;
        transition: background 0.2s ease;
    }

    .file-list-item:hover {
        background: #f8f9fa;
    }

    .file-list-icon {
        width: 32px;
        height: 32px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 16px;
        font-size: 16px;
        color: white;
    }

    .file-list-info {
        flex: 1;
        min-width: 0;
    }

    .file-list-name {
        font-size: 14px;
        font-weight: 500;
        color: #202124;
        margin: 0 0 2px 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .file-list-meta {
        font-size: 12px;
        color: #5f6368;
        margin: 0;
    }

    .file-list-actions {
        display: flex;
        gap: 8px;
    }

    @media (max-width: 768px) {

        /* Stack header and make action area wrap */
        .upload-header,
        .files-header {
            flex-direction: column;
            gap: 12px;
            align-items: stretch;
        }

        /* Ensure actions will wrap when space is limited */
        .upload-actions {
            flex-wrap: wrap;
        }

        /* Group view toggles separately so they remain compact */
        .view-toggle-group {
            display: flex;
            gap: 8px;
            align-items: center;
            flex: 0 0 auto;
        }

        /* Primary actions (Upload/Back) stack and grow when space is limited */
        .primary-actions {
            display: flex;
            gap: 8px;
            align-items: center;
            flex: 1 1 auto;
            justify-content: flex-end;
        }

        .primary-actions .btn-general {
            flex: 0 1 auto;
        }

        @media (max-width: 520px) {

            /* Keep primary actions compact on small screens (Upload behaves like Back) */
            .primary-actions {
                flex-direction: row;
                align-items: center;
                justify-content: flex-end;
                gap: 8px;
                flex: 0 0 auto;
                width: auto;
                max-width: 100%;
            }

            /* Ensure Upload is compact (no full-width stretch) */
            .primary-actions .btn-blue {
                width: auto;
                min-width: 0;
            }

            /* Keep Back compact */
            .primary-actions .back-form .btn-general {
                width: auto;
                min-width: 0;
            }

            /* allow buttons to shrink as needed */
            .btn-general {
                min-width: 0;
            }

            .view-toggle-group {
                order: -1;
                /* keep toggles on top */
            }

            /* Ensure Upload remains compact like Back even if other global rules try to stretch buttons */
            .files-header .primary-actions .btn-general,
            .files-header .primary-actions .btn-blue,
            .upload-actions .primary-actions .btn-general,
            .upload-actions .primary-actions .btn-blue {
                width: auto !important;
                max-width: none !important;
                display: inline-flex !important;
                white-space: nowrap !important;
            }
        }

        /* Compact square toggles */
        .btn-view-toggle {
            width: 44px;
            height: 44px;
            padding: 6px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* On very small screens keep Upload full-width, but prevent all btn-general from stretching */
        @media (max-width: 420px) {
            .btn-general {
                min-width: 0;
            }

            .btn-view-toggle {
                width: 40px;
                height: 40px;
            }
        }

        .files-grid {
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 12px;
            padding: 12px;
        }

        .upload-zone {
            padding: 24px 12px;
        }

        /* Move file actions into document flow on touch devices (no hover) */
        .file-actions {
            position: static;
            top: auto;
            right: auto;
            opacity: 1;
            margin-top: 8px;
            display: flex;
            justify-content: center;
            gap: 8px;
        }

        .file-item {
            padding-bottom: 48px;
            /* room for actions */
        }

        .file-action-btn {
            width: 36px;
            height: 36px;
            font-size: 14px;
            border-radius: 8px;
        }
    }
</style>
@endpush
@section('title', 'Patient Files')

@section('content')
<div class="main-content" style="padding:24px;">
    @php
    // Build a safe URL for the patient "show" page. If $patient is not present,
    // fall back to the patients index so the view won't break when rendered
    // in contexts without a patient variable.
    $patientShowUrl = isset($patient) ? route('patients.show', $patient) : route('patients.index');
    @endphp

    <div class="upload-container" data-patient-id="{{ $patient->id ?? '' }}">

        <!-- Hidden file input for upload functionality -->
        <input type="file" id="fileInput" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.xls,.xlsx" style="display: none;">

        <!-- Files Container -->
        <div class="files-container">
            <div class="files-header">
                <div class="d-flex align-items-center">
                    <h2 class="upload-title mb-0">Patient Files</h2>
                    <span id="fileCount">0 files</span>
                </div>
                <div class="upload-actions">
                    <div class="view-toggle-group" aria-hidden="true">
                        <button type="button" class="btn-view-toggle active" data-view="grid" aria-label="Grid view">
                            <i class="bi bi-grid-3x3-gap"></i>
                        </button>
                        <button type="button" class="btn-view-toggle" data-view="list" aria-label="List view">
                            <i class="bi bi-list"></i>
                        </button>
                    </div>

                    <div class="primary-actions">
                        <button type="button" class="btn-general btn-blue" onclick="document.getElementById('fileInput').click()">
                            <i class="bi bi-cloud-arrow-up"></i>
                            <span class="d-none d-md-inline"> Upload Files</span>
                        </button>


                        <button type="button"
                            class="btn-general btn-gray"
                            title="Back to Patient Details"
                            aria-label="Back to Patient Details"
                            onclick="window.location.href='{{ $patientShowUrl }}'">
                            Back <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>

                </div>
            </div>

            <!-- Grid View -->
            <div class="files-grid active" id="filesGrid">
                <!-- Files will be inserted here -->
            </div>

            <!-- List View -->
            <div class="files-list" id="filesList">
                <!-- Files will be inserted here -->
            </div>

            <!-- Empty State -->
            <div class="empty-state" id="emptyState">
                <div class="empty-state-icon">
                    <i class="bi bi-files"></i>
                </div>
                <p class="empty-state-text">No files uploaded yet</p>
            </div>
        </div>
    </div>

    <!-- File Viewer Modal - Bootstrap 5 -->
    <div class="modal fade file-viewer-modal" id="fileViewerModal" tabindex="-1" aria-labelledby="fileViewerLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fileViewerLabel">File Viewer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div id="fileViewerContent" class="file-viewer-content">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('fileInput');
        const filesGrid = document.getElementById('filesGrid');
        const filesList = document.getElementById('filesList');
        const emptyState = document.getElementById('emptyState');
        const fileCount = document.getElementById('fileCount');
        const viewToggles = document.querySelectorAll('.btn-view-toggle');
        const containerEl = document.querySelector('.upload-container');
        const patientId = containerEl ? (containerEl.dataset.patientId || null) : null; // Read patient id from data attribute

        let uploadedFiles = [];
        let currentView = 'grid';

        // View Toggle
        viewToggles.forEach(btn => {
            btn.addEventListener('click', function() {
                const view = this.dataset.view;

                viewToggles.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                if (view === 'grid') {
                    filesGrid.classList.add('active');
                    filesList.classList.remove('active');
                } else {
                    filesGrid.classList.remove('active');
                    filesList.classList.add('active');
                }

                currentView = view;
            });
        });

        // File Input Change
        fileInput.addEventListener('change', function() {
            handleFiles(this.files);
        });

        function handleFiles(files) {
            if (!patientId) {
                alert('Patient ID is required to upload files');
                return;
            }

            Array.from(files).forEach(file => {
                if (file.size > 10 * 1024 * 1024) { // 10MB limit
                    alert(`File ${file.name} is too large. Maximum size is 10MB.`);
                    return;
                }

                uploadFile(file);
            });
        }

        function uploadFile(file) {
            const fileId = 'file_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            const formData = new FormData();
            formData.append('file', file);
            formData.append('patient_id', patientId);
            formData.append('_token', '{{ csrf_token() }}');

            // Add file to UI immediately
            const fileObj = {
                id: fileId,
                name: file.name,
                size: formatFileSize(file.size),
                type: getFileType(file.name),
                uploading: true,
                progress: 0
            };

            uploadedFiles.unshift(fileObj);
            renderFiles();

            // Upload via fetch
            fetch('{{ route("patient.uploads.store") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Update file object
                    const fileIndex = uploadedFiles.findIndex(f => f.id === fileId);
                    if (fileIndex !== -1) {
                        if (data.success) {
                            uploadedFiles[fileIndex] = {
                                ...uploadedFiles[fileIndex],
                                id: data.file.id,
                                name: data.file.original_name || uploadedFiles[fileIndex].name, // Use original_name from backend
                                size: data.file.file_size ? formatFileSize(data.file.file_size) : uploadedFiles[fileIndex].size, // Use file_size from backend
                                uploading: false,
                                progress: 100,
                                url: data.file.url
                            };
                        } else {
                            uploadedFiles.splice(fileIndex, 1);
                            alert(data.message || 'Upload failed');
                        }
                        renderFiles();
                    }
                })
                .catch(error => {
                    console.error('Upload error:', error);
                    const fileIndex = uploadedFiles.findIndex(f => f.id === fileId);
                    if (fileIndex !== -1) {
                        uploadedFiles.splice(fileIndex, 1);
                        renderFiles();
                    }
                    alert('Upload failed: ' + error.message);
                });
        }

        // File upload logic removed

        function getFileType(filename) {
            const ext = filename.split('.').pop().toLowerCase();

            if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(ext)) return 'image';
            if (['pdf'].includes(ext)) return 'pdf';
            if (['doc', 'docx'].includes(ext)) return 'doc';
            if (['xls', 'xlsx'].includes(ext)) return 'excel';

            return 'default';
        }

        function getFileIcon(type) {
            const icons = {
                image: 'bi-image',
                pdf: 'bi-file-earmark-pdf',
                doc: 'bi-file-earmark-word',
                excel: 'bi-file-earmark-excel',
                default: 'bi-file-earmark'
            };

            return icons[type] || icons.default;
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function renderFiles() {
            const hasFiles = uploadedFiles.length > 0;

            fileCount.textContent = `${uploadedFiles.length} file${uploadedFiles.length !== 1 ? 's' : ''}`;
            emptyState.style.display = hasFiles ? 'none' : 'block';

            // Grid view
            filesGrid.innerHTML = uploadedFiles.map(file => `
            <div class="file-item ${file.uploading ? 'uploading' : ''}" data-file-id="${file.id}">
                <div class="file-icon ${file.type}" onclick="viewFile('${file.id}')" style="cursor:pointer;">
                    <i class="${getFileIcon(file.type)}"></i>
                </div>
                <p class="file-name" title="${file.name}" onclick="viewFile('${file.id}')" style="cursor:pointer; color:#1a73e8; text-decoration:underline;">${file.name}</p>
                <p class="file-size">${file.size}</p>
                
                ${!file.uploading ? `
                    <div class="file-actions">
                        <button class="file-action-btn" onclick="viewFile('${file.id}')" title="View">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="file-action-btn" onclick="downloadFile('${file.id}')" title="Download">
                            <i class="bi bi-download"></i>
                        </button>

<form action="{{ url('patient/uploads') }}/${file.id}" 
      method="POST" 
      class="delete-form d-inline" 
      data-delete-type="file"
      data-file-id="${file.id}">
    @csrf
    @method('DELETE')
    <button type="submit" class="file-action-btn" title="Delete">
        <i class="bi bi-trash"></i>
    </button>
</form>

                    </div>
                ` : ''}
                
                ${file.uploading ? `
                    <div class="upload-progress">
                        <div class="upload-progress-bar" style="width: ${file.progress}%"></div>
                    </div>
                ` : ''}
            </div>
        `).join('');

            // List view
            filesList.innerHTML = uploadedFiles.map(file => `
            <div class="file-list-item ${file.uploading ? 'uploading' : ''}" data-file-id="${file.id}">
                <div class="file-list-icon ${file.type}" onclick="viewFile('${file.id}')" style="cursor:pointer;">
                    <i class="${getFileIcon(file.type)}"></i>
                </div>
                <div class="file-list-info">
                    <p class="file-list-name" onclick="viewFile('${file.id}')" style="cursor:pointer; color:#1a73e8; text-decoration:underline;">${file.name}</p>
                    <p class="file-list-meta">${file.size}</p>
                </div>
                ${!file.uploading ? `
                    <div class="file-list-actions">
                        <button class="file-action-btn" onclick="viewFile('${file.id}')" title="View">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="file-action-btn" onclick="downloadFile('${file.id}')" title="Download">
                            <i class="bi bi-download"></i>
                        </button>
                        <button class="file-action-btn" onclick="deleteFile('${file.id}')" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                ` : ''}
            </div>
        `).join('');
        }

        // Global functions for file actions
        window.viewFile = function(fileId) {
            const file = uploadedFiles.find(f => f.id == fileId);
            if (file) {
                // Use Laravel's route function with proper URL generation
                const baseUrl = '{{ url("patient/uploads") }}';
                const viewUrl = `${baseUrl}/${fileId}/view`;

                // Update modal content
                document.getElementById('fileViewerLabel').textContent = file.name;

                // Get file extension to determine display method
                const fileName = file.name.toLowerCase();
                const fileExtension = fileName.split('.').pop();
                const fileViewerContent = document.getElementById('fileViewerContent');

                // Handle different file types for cleaner viewing
                if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].includes(fileExtension)) {
                    // For images, create zoomable viewer
                    fileViewerContent.innerHTML = `
                        <div style="position: relative; width: 100%; height: 70vh; overflow: hidden; background: #fff; cursor: grab; user-select: none;">
                            <img id="viewerImg" src="${viewUrl}" alt="${file.name}" 
                                 style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); 
                                        max-width: 100%; max-height: 100%; transition: transform 0.1s ease;">

                            <div id="zoomLevel" style="position: absolute; bottom: 10px; left: 10px; background: rgba(0,0,0,0.7); color: white; padding: 5px 10px; border-radius: 4px; font-size: 12px; z-index: 10;">100%</div>
                        </div>
                    `;

                    // Setup zoom and pan functionality
                    setTimeout(() => {
                        const img = document.getElementById('viewerImg');
                        const container = img.parentElement;
                        const zoomLevel = document.getElementById('zoomLevel');

                        let scale = 1;
                        let isDragging = false;
                        let startX = 0,
                            startY = 0;
                        let translateX = 0,
                            translateY = 0;

                        function updateTransform() {
                            img.style.transform = `translate(calc(-50% + ${translateX}px), calc(-50% + ${translateY}px)) scale(${scale})`;
                            zoomLevel.textContent = Math.round(scale * 100) + '%';
                        }

                        // Global zoom functions
                        window.zoomImage = function(factor) {
                            scale = Math.min(5, Math.max(0.2, scale * factor));
                            updateTransform();
                        };

                        window.resetZoom = function() {
                            scale = 1;
                            translateX = 0;
                            translateY = 0;
                            updateTransform();
                        };

                        // Mouse wheel zoom at cursor position
                        container.addEventListener('wheel', (e) => {
                            e.preventDefault();

                            const rect = container.getBoundingClientRect();
                            const mouseX = e.clientX - rect.left - rect.width / 2;
                            const mouseY = e.clientY - rect.top - rect.height / 2;

                            const oldScale = scale;
                            const zoomFactor = e.deltaY > 0 ? 0.9 : 1.1;
                            scale = Math.min(5, Math.max(0.2, scale * zoomFactor));

                            // Adjust position to zoom at cursor
                            const scaleChange = scale / oldScale;
                            translateX = translateX * scaleChange + mouseX * (1 - scaleChange);
                            translateY = translateY * scaleChange + mouseY * (1 - scaleChange);

                            updateTransform();
                        });

                        // Drag to pan
                        container.addEventListener('mousedown', (e) => {
                            isDragging = true;
                            container.style.cursor = 'grabbing';
                            startX = e.clientX - translateX;
                            startY = e.clientY - translateY;
                            e.preventDefault();
                        });

                        document.addEventListener('mousemove', (e) => {
                            if (!isDragging) return;
                            translateX = e.clientX - startX;
                            translateY = e.clientY - startY;
                            updateTransform();
                        });

                        document.addEventListener('mouseup', () => {
                            if (isDragging) {
                                isDragging = false;
                                container.style.cursor = 'grab';
                            }
                        });

                        // Touch support
                        let touchStart = null;
                        let initialDistance = 0;
                        let initialScale = 1;

                        container.addEventListener('touchstart', (e) => {
                            e.preventDefault();

                            if (e.touches.length === 1) {
                                touchStart = {
                                    x: e.touches[0].clientX - translateX,
                                    y: e.touches[0].clientY - translateY
                                };
                            } else if (e.touches.length === 2) {
                                const dx = e.touches[0].clientX - e.touches[1].clientX;
                                const dy = e.touches[0].clientY - e.touches[1].clientY;
                                initialDistance = Math.sqrt(dx * dx + dy * dy);
                                initialScale = scale;
                            }
                        });

                        container.addEventListener('touchmove', (e) => {
                            e.preventDefault();

                            if (e.touches.length === 1 && touchStart) {
                                translateX = e.touches[0].clientX - touchStart.x;
                                translateY = e.touches[0].clientY - touchStart.y;
                                updateTransform();
                            } else if (e.touches.length === 2) {
                                const dx = e.touches[0].clientX - e.touches[1].clientX;
                                const dy = e.touches[0].clientY - e.touches[1].clientY;
                                const distance = Math.sqrt(dx * dx + dy * dy);
                                scale = Math.min(5, Math.max(0.2, initialScale * (distance / initialDistance)));
                                updateTransform();
                            }
                        });

                        container.addEventListener('touchend', () => {
                            touchStart = null;
                        });

                        // Prevent image dragging
                        img.addEventListener('dragstart', (e) => e.preventDefault());

                        updateTransform();
                    }, 50);
                } else if (fileExtension === 'pdf') {
                    // For PDFs, use embed without browser controls for cleaner view
                    fileViewerContent.innerHTML = `<embed src="${viewUrl}" type="application/pdf" width="100%" height="600px" style="border: none;">`;
                } else {
                    // For other files, show download option
                    fileViewerContent.innerHTML = `
                        <div class="text-center p-4">
                            <i class="bi bi-file-earmark fs-1 text-muted mb-3"></i>
                            <h5>${file.name}</h5>
                            <p class="text-muted mb-3">This file type cannot be previewed</p>
                            <a href="${viewUrl}" class="btn btn-primary" download>
                                <i class="bi bi-download"></i> Download File
                            </a>
                        </div>
                    `;
                }

                // Show Bootstrap modal
                const modal = new bootstrap.Modal(document.getElementById('fileViewerModal'));
                modal.show();
            }
        };

        window.closeFileViewer = function() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('fileViewerModal'));
            if (modal) {
                modal.hide();
            }
            document.getElementById('fileViewerContent').innerHTML = ''; // Clear content
        };

        // Clear content when modal is hidden
        document.getElementById('fileViewerModal').addEventListener('hidden.bs.modal', function() {
            document.getElementById('fileViewerContent').innerHTML = '';
        });

        window.downloadFile = function(fileId) {
            window.open(`{{ url('patient/uploads') }}/${fileId}/download`, '_blank');
        };

        window.deleteFile = function(fileId) {
            if (!confirm('Are you sure you want to delete this file?')) return;

            fetch(`{{ url('patient/uploads') }}/${fileId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        uploadedFiles = uploadedFiles.filter(f => f.id != fileId);
                        renderFiles();
                    } else {
                        alert(data.message || 'Delete failed');
                    }
                })
                .catch(error => {
                    console.error('Delete error:', error);
                    alert('Delete failed: ' + error.message);
                });
        };

        // Load existing files
        if (patientId) {
            fetch(`{{ url('patient/uploads') }}?patient_id=${patientId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        uploadedFiles = data.files.map(file => {
                            return {
                                id: file.id,
                                name: file.original_name,
                                size: file.file_size ? formatFileSize(file.file_size) : 'Unknown',
                                type: getFileType(file.file_path),
                                uploading: false,
                                url: file.url,
                                created_at: file.created_at // make sure backend includes this
                            };
                        });

                        // Sort newest → oldest
                        uploadedFiles.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

                        renderFiles();

                    }
                })
                .catch(error => console.error('Load files error:', error));
        }

        // Initial render
        renderFiles();
    });
</script>
@endpush