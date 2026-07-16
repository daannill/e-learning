function switchVideoSource(source) {
    document.getElementById('video_source').value = source;

    document.querySelectorAll('.source-toggle-btn').forEach(function (btn) {
        btn.classList.toggle('active', btn.dataset.source === source);
    });

    document.getElementById('sourcePanelUrl').hidden = source !== 'youtube';
    document.getElementById('sourcePanelUpload').hidden = source !== 'video';

    if (source === 'youtube') {
        resetVideoFile();
    }
}

function triggerUploadInput(box) {
    box.querySelector('input[type="file"]').click();
}

function handleUploadBoxKeydown(event, box) {
    if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        triggerUploadInput(box);
    }
}

function handleVideoFileChange(input) {
    const file = input.files?.[0];
    if (!file) return;

    const box = input.closest('.upload-box');
    const video = box.querySelector('#videoPreview');

    box.querySelector('.upload-placeholder').hidden = true;
    box.querySelector('.upload-result').hidden = false;

    box.querySelector('#videoFileName').textContent = file.name;

    video.src = URL.createObjectURL(file);

    box.classList.add('has-image');
}

function resetVideoFile() {
    const box = document.querySelector('#sourcePanelUpload .upload-box');
    const input = box.querySelector('input[type="file"]');
    input.value = '';
    box.querySelector('.upload-placeholder').hidden = false;
    box.querySelector('.upload-result').hidden = true;
    box.classList.remove('has-image');
}

function removeVideoFile(event) {
    event.stopPropagation();
    resetVideoFile();
}

const videoUrl = document.getElementById('material_video_url');
const youtubePreview = document.getElementById('youtubePreview');

videoUrl.addEventListener('change', renderYoutubePreview);

function renderYoutubePreview() {
    const embedUrl = getYoutubeEmbedUrl(videoUrl.value);

    if (!embedUrl) {
        youtubePreview.innerHTML = `
            <svg class="icon" aria-hidden="true">
                <use href="#i-video"></use>
            </svg>
            <span class="video-preview-title">Video preview</span>
            <span class="video-preview-hint">
                Preview will appear here once a valid link is added.
            </span>
        `;
        return;
    }

    youtubePreview.innerHTML = `
        <iframe
            width="100%"
            height="350"
            src="${embedUrl}"
            frameborder="0"
            allowfullscreen
        ></iframe>
    `;
}

function getYoutubeEmbedUrl(url) {
    let id = null;

    try {
        const parsed = new URL(url);

        if (parsed.hostname.includes('youtube.com')) {
            id = parsed.searchParams.get('v');
        }

        if (parsed.hostname === 'youtu.be') {
            id = parsed.pathname.substring(1);
        }
    } catch {
        return null;
    }

    return id
        ? `https://www.youtube.com/embed/${id}`
        : null;
}

document.addEventListener('DOMContentLoaded', function () {
    const source = document.getElementById('video_source').value;

    switchVideoSource(source);

    if (source === 'youtube') {
        renderYoutubePreview();
    }
});